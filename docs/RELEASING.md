# Releasing CloneGuard Security Scanning

Releases go to WordPress.org through GitHub Actions. Nobody needs SVN on their machine.

## Workflows

| Workflow | Runs on | Does |
|---|---|---|
| `ci.yml` | every pull request; pushes to `main` and `release/**` | `php -l` on PHP 7.4, 8.2 and 8.4; checks that `blocks/seal/build` matches `blocks/seal/src`; Plugin Check on the shipped files (fails on any ERROR) |
| `deploy.yml` | a published GitHub release | copies the repo minus `.distignore` to SVN `trunk` and `tags/<version>`, and `.wordpress-org/` to SVN `assets/`, all in one commit |
| `deploy.yml` (dry run) | pull requests that change `deploy.yml` or `.distignore`; manual run (Actions → Deploy to WordPress.org → Run workflow, "Dry run" ticked) | everything except the SVN commit, under the version name `<version>-dry-run`; no secrets needed. Download the zip from the run's artifacts to see exactly what would ship |
| `wordpress-assets.yml` | pushes to `main` that change `readme.txt` or `.wordpress-org/` | updates the readme and the banner, icons and screenshots on WordPress.org without a release |

`deploy.yml` stops before SVN if the release tag, the `Version:` header, `public $version` in `main.php` and `Stable tag:` in `readme.txt` don't all match.

## One-time setup

Add the WordPress.org login as repository secrets (GitHub → Settings → Secrets and variables → Actions):

- `SVN_USERNAME` = `clonesupport`
- `SVN_PASSWORD` = the WordPress.org password for that account

Or from a terminal, typing the password when prompted (it doesn't end up in your shell history):

```sh
gh secret set SVN_USERNAME --body clonesupport
gh secret set SVN_PASSWORD
```

## Release steps

1. **Release branch.** Create `release/<version>` from `main` and merge the feature PRs into it.
2. **Version and changelog** on the release branch:
   - `main.php`: the `Version:` header and `public $version = '<version>';`
   - `readme.txt`: `Stable tag: <version>`, `Tested up to:` if a new WordPress version is out, a `= <version> =` entry under `== Changelog ==`, and an `== Upgrade Notice ==` entry if useful.
   - If `blocks/seal/src` changed: `npm run build:blocks` and commit `blocks/seal/build` (CI fails otherwise).
3. **Test.** CI must be green on the release branch. Click through the changes in wp-env (`npm run env:start`, http://localhost:8888/wp-admin).
4. **Optional dry run.** Actions → Deploy to WordPress.org → Run workflow on the release branch with "Dry run" ticked. Check the log and the zip artifact.
5. **Publish.** GitHub → Releases → Draft a new release:
   - Tag: `<version>` (or `v<version>`), **target: the release branch**
   - Title: `<version>`, notes: the changelog entry
   - Publish. `deploy.yml` runs and commits trunk, the tag and assets to SVN.
6. **Check WordPress.org** after a few minutes: the plugin page shows the new version, and the download zip matches the build.
7. **Merge the release branch into `main`.** Do this after step 5, not before: `wordpress-assets.yml` runs on `main` and stops with an error if any shipped file other than `readme.txt` differs from SVN trunk.

## Readme or asset change without a release

To change only `readme.txt` (for example `Tested up to:` after a new WordPress release) or the files in `.wordpress-org/`:

- Commit the change to `main` through a PR. `wordpress-assets.yml` pushes it to WordPress.org.
- Only change things that are true for the released version. Whatever lands on `main` goes live straight away.

## Assets

`.wordpress-org/` mirrors SVN `assets/` and is synced with `--delete`: a file removed here is removed from WordPress.org. Names follow the WordPress.org conventions: `banner-772x250.png`, `banner-1544x500.png`, `icon-128x128.png`, `icon-256x256.png`, `screenshot-<n>.png` (numbered like the `== Screenshots ==` captions in `readme.txt`).

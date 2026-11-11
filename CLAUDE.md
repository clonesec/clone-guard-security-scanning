# CloneGuard Security Scanning (WordPress plugin)

Start every session by reading `CONTEXT.md` (current state, verified facts, decisions, open questions, session log) and then `docs/PLAN.md` (step-by-step plan with checkboxes). Execute the first unchecked step whose prerequisites are met. Before ending a session, tick the step in `docs/PLAN.md` and append an entry to the Session log in `CONTEXT.md`.

Rules:
- Never commit tokens, API keys or portal credentials. Local secrets go in `.wp-env.override.json` (git-ignored).
- Phase 1 changes are minimal and line-by-line reviewable; no refactors or reformatting.
- Keep option names, widget id, menu slugs and hooks stable so existing installs upgrade without reconfiguration.
- `CONTEXT.md`, `CLAUDE.md`, `docs/`, `.github/`, `.vscode/` never ship to WordPress.org (see `.distignore`).

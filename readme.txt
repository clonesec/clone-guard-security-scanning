=== CloneGuard Security Scanning ===

Contributors: clonesupport
Tags: PCI compliance, vulnerability scanner, security scan, penetration testing, trust seal
Requires at least: 5.9
Tested up to: 7.1
Stable tag: 2.8.2
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Run PCI ASV scans, vulnerability scans and penetration tests from your WordPress dashboard, from a PCI Approved Scanning Vendor.

== Description ==
AUTOMATED PLUGIN FOR ALL OF YOUR SECURITY SCANNING NEEDS

CloneGuard Security Scanning connects your WordPress site to the Clone Systems scanning platform. Schedule scans, see your compliance status, review findings with fix guidance, and download reports without leaving WordPress.

Clone Systems is a PCI SSC Approved Scanning Vendor (ASV).

= What you can scan =

* PCI ASV compliance scans
* Vulnerability scans
* Penetration tests

= What you can do inside WordPress =

* Start, stop and schedule scans
* See your compliance status and latest results
* Review each finding with its impact and solution
* Download PDF reports
* Manage targets, schedules and email notifications

= Get started =

1. Choose a scanning plan at https://www.clone-systems.com/pricing?mode=pci
2. Install the plugin and connect your Clone Systems account.
3. Schedule your scans.
4. Review your results and download your reports.
5. Optional: display the Trust Seal on your site.

= Your discount =
WordPress users can get a discount by using a first-time order code: **WORDPRESSNEW8**

= Optional add-on: Trust Seal =

Customers with an active Clone Systems account and current scans can display a Trust Seal on their site. This is optional.

Visitors who click the Trust Seal see the public security report for your site. You can add it in four ways:

* In one click from CloneGuard Security > Overview: pick a style, size and corner, and the Trust Seal shows on every page.
* With the Trust Seal block in the block editor or Site Editor.
* With the Trust Seal widget in classic themes.
* With the shortcode `[cloneguard_seal icon="cs-secure-dark" size="md" position="relative"]` (sizes: lg, md, sm; positions: relative, left, right).

== Support ==

Our clients enjoy our 24x7 support services. We've created a stable security solution so that you can focus on your website. [Contact us](https://www.clone-systems.com/contact) for additional information and technical support.

== External services ==

This plugin connects to services run by Clone Systems, Inc.

**Scanning portal API.** The admin pages read and manage your scans, reports, vulnerabilities, schedules, targets and notifications through the API of the portal set in CloneGuard Security > Settings (by default `pciscan.clone-systems.com`). Requests are made from your server only when an administrator opens a CloneGuard Security admin page or performs an action there, or opens the WordPress Dashboard, where the CloneGuard Security widget loads your scan status. Each request sends your User Token and Key, the data of the action (for example a target's name and hosts), the plugin version and your site's address.

**Trust Seal.** Pages that show the Trust Seal block, the Trust Seal widget, the `[cloneguard_seal]` shortcode or the site-wide Trust Seal turned on in CloneGuard Security > Overview load the script `https://www.clone-systems.com/generate-seal/get-seal.js` and the seal image from www.clone-systems.com in the visitor's browser. The seal links to the security report for your site's domain at `https://www.clone-systems.com/report/?dn={your domain}`. After the page has loaded, the script also asks the WordPress.com mShots service (`s.wordpress.com/mshots`) for a screenshot of your home page, so the report can show it. No visitor data is sent by the plugin itself; the browser sends its usual request information (such as IP address and user agent) to these hosts. In the admin, the Trust Seal picker on the Overview page and the block editor preview load the seal images from www.clone-systems.com.

Clone Systems [Subscription Agreement](https://www.clone-systems.com/legal/subscription-agreement) and [Privacy Policy](https://www.clone-systems.com/privacy-policy). WordPress.com mShots is provided by Automattic: [Terms of Service](https://automattic.com/tos/) and [Privacy Policy](https://automattic.com/privacy/).

== Installation ==

1. Upload clone-guard-security-scanning to the `/wp-content/plugins` directory.
2. Log in to the WordPress admin panel and go to the plugins page. On the plugins page, activate the CloneGuard Security Scanning plugin.
3. Navigate to the CloneGuard Security > Settings page.
4. Enter your User Token and Key (if you do not have these, you will need to access them on the Clone Systems website).
5. The CloneGuard Security Scanning plugin is now fully active.
6. Navigate to the CloneGuard Security > Scans page to view your scans.

== Frequently Asked Questions ==

= Do I need a Clone Systems account? =

Yes. The plugin connects to your Clone Systems scanning account. You can choose a plan at https://www.clone-systems.com/pricing?mode=pci

= What can I scan? =

You can run PCI ASV compliance scans, vulnerability scans and penetration tests, depending on your plan.

= Is Clone Systems an approved PCI scanning vendor? =

Yes. Clone Systems is a PCI SSC Approved Scanning Vendor (ASV).

= Do I have to use the Trust Seal? =

No. The Trust Seal is optional. The scanning works without it.

= Why is my Trust Seal not showing? =

The seal shows only on domains with an active Clone Systems account and current scans.

= How do I get support? =

Support is available 24x7. Use the contact link in the Support section.

== Screenshots ==

1. Overview: your compliance status and latest reports at a glance.
2. Scans: start, stop and schedule scans, and follow their progress.
3. Reports: compliance result and findings for every scan, with downloadable PDF reports.
4. Vulnerability details: summary, impact, solution and affected asset for each finding.
5. Options: manage scan schedules, targets and email notifications.
6. Settings: connect the plugin to your Clone Systems portal account in three steps.
7. The Clone Systems portal.
8. Optional: add the Trust Seal block anywhere in the block editor or Site Editor, so visitors can open your security report.

== Changelog ==

= 2.8.2 =
* The plugin's homepage link now points to the Clone Systems plans and pricing page.

= 2.8.1 =
* Listing text updated: the description now leads with scanning, and the Trust Seal is described as an optional add-on.
* The seal block, the widget and the Overview section are now named "Trust Seal". Existing seals keep working.

= 2.8 =
* New look that matches clone-systems.com: Inter font, navy and orange colours, card layout, simpler product tabs and new icons.
* New CloneGuard Security widget on the WordPress Dashboard: compliance status, next scheduled scan and latest report. It loads after the Dashboard, so a slow portal never holds it up.
* Overview: new Trust Seal picker with a live preview. One click adds the seal to a corner of every page, or copy the shortcode to place it yourself.
* Settings: new Test connection button that checks the Portal URL, User Token and Key without saving them.
* Settings: the Portal URL is saved as a host name, even when pasted with https:// or a path.
* New Open in portal button on every plugin page, and a Help tab with the portal training videos and support links.
* Settings: updated feature list, and the WordPress discount code is shown next to Purchase a License.
* Smaller download: replaced old images with SVG icons.

= 2.7 =
* New Trust Seal block for the block editor and the Site Editor, with seal, size and position options and a preview. The widget and the shortcode keep working.
* Schedules: First Time now uses the browser's date and time picker.
* Fix: editing a schedule no longer shifts its first run time by the timezone's offset from UTC when saved.
* Updated Select2 to 4.1.0 and removed the jQuery date picker library.
* Removed jQuery Migrate warnings on the plugin's admin pages.
* Smaller download: removed images the plugin does not use.

= 2.6 =
* Tested up to WordPress 7.1.
* Compatibility fixes for PHP 8.2 to 8.4.
* Security hardening: output escaping, input sanitizing, and nonce and capability checks on all admin actions.
* The Trust Seal widget now shows the current Clone Systems Trust Seal, with seal, size and position options. Existing widgets keep their size.
* New [cloneguard_seal] shortcode to place the seal in posts and pages.
* Faster admin pages, and a clear message when the scanning portal cannot be reached.
* Updated purchase link, Settings page text and screenshots.
* Minimum requirements raised to WordPress 5.9 and PHP 7.4.

= 2.5 =
* Uploaded new Validation Report screenshot
* Tested plugin up to 6.5.2 Wordpress version

= 2.4 = 
* Made minor Overview page styling enhancements.
* Added new product screenshots to the Overview page.

= 2.3.3 = 
* Changed the Banner with a new one.

= 2.3.2 =
* Changed Plugin description texts
* Fixed icons position text in Scans Page
* Added Banner on the Wordpress Plugin Directory.

= 2.3.1 =
* Added multi-select capability to the exception submission process

= 2.3 =
* Added capability to submit vulnerability exceptions

= 2.2.2 =
* Visualization updates on vulnerabilities and overview pages

= 2.2.1 =
* My Settings fixes
* Updated images

= 2.2.0 =
* Added vulnerabilities page
* Added vulnerability details page
* Responsive functionality fixes

= 2.1.0 =
* Added overview page
* Implemented scan profiles for vulnerability management
* Implemented scan profiles for penetration testing
* Added portal URL customization for partner portals

= 2.0.0 =
* Added vulnerability scanning module 
* Added penetration testing module
* Implemented tabs functionality
* Implement bulk actions functionality on Options page

= 1.8.0 =
* Added Options page
* User can now manage schedules, targets, and notifications

= 1.5.2 =
* Added image folder

= 1.5.1 =
* Added screenshots 
* Fixed New Notification button alignment

= 1.5.0 =
* Design Improvements

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 2.8.2 =
The plugin's homepage link now points to the Clone Systems plans and pricing page.

= 2.8.1 =
Listing text updated. The seal block and widget are now named Trust Seal; existing seals keep working.

= 2.8 =
New design that matches clone-systems.com, a Dashboard widget with your scan status, a one-click Trust Seal and a Test connection button on Settings.

= 2.7 =
Adds a Trust Seal block for the block editor and Site Editor, and fixes schedule edits shifting the first run time by the timezone offset.

= 2.6 =
Tested with WordPress 7.1 and PHP 8.4. Security fixes, and the Trust Seal widget now shows the current Clone Systems Trust Seal. Requires WordPress 5.9 and PHP 7.4.

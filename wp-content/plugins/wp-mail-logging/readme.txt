=== WP Mail Logging ===
Contributors: No3x, tripflex
Donate link: http://no3x.de/web/donate
Tags: mail, email, log, logging, debug, list, store, collect, view
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.0
Tested up to: 4.6.1
Stable tag: 1.7.0

Logs each email sent by WordPress.

== Description ==

Logs each email sent by WordPress. This can be useful if you don't want to lose such mail contents. It can also be useful for debugging purposes while development.

Features of the plugin include:

 * Complete list of sent mails - view and search through the mails.
 * Zero-configuration - just install and enjoy.
 * Log rotation - decide which emails you want to keep.
 * DevOP: IP of server sent the mail
 * Developer: Boost your development performance by keeping track of sent mails.
 * Developer: Filters are provided to extend the columns.

[youtube https://www.youtube.com/watch?v=mQK6VPSV2-E]

**Follow this plugin on [GitHub](https://github.com/No3x/wp-mail-logging)**

**If you find an issue, let us know in the [Tracker](https://github.com/No3x/wp-mail-logging/issues?state=open)**

**Provide feedback and suggestions on [enhancements](https://github.com/No3x/wp-mail-logging/issues?direction=desc&labels=Enhancement%2Cenhancement&page=1&sort=created&state=open)**

== Installation ==
Just install and activate wp-mail-logging. The plugin will do the work for you! You can list all logged mails on the plugin site.


== Frequently Asked Questions ==
= How do I know the Mail was delivered? =
The logged email has been sent by WordPress but please note this does NOT mean it has been delivered. With the given functionality of WordPress you can't determine if a mail was sent successfully. 

== Screenshots ==
1. The List
2. The Detail View
3. The Settings

== Upgrade Notice ==
= 1.7.0 =
- New: Storing host IP
- Fix: passing search term for pagination
- Tweak: close modal with ESC

== Changelog ==

= 1.7.0, November 6, 2016 =
- New: logging host IP
- Fix: passing search term for pagination
- Tweak: close modal with ESC

= 1.6.2, August 7, 2016  =
- Fix: search mails

= 1.6.1, August 1, 2016  =
- Fix: delete mails

= 1.6.0, July 31, 2016  =
- New: Improved modal, added view types
- Tweak: Proper date if none set in WordPress settings
- Tweak: Updated libraries
- Tweak: Added wp_mail hook to very last priority

= 1.5.1, October 11, 2015  =
- Tweak: Fixed security issues

= 1.5.0, June 4, 2015  =
- New: Setting for date time format
- Tweak: Removed admin bar menu
- Fix: repetitive cron schedule

= 1.4.2, April 4, 2015  =
- Tweak: Library updated - settings load speed improved.

= 1.4.1, March 28, 2015  =
- Fix: Restrict submission data works now.
- Fix: Granularity of cleanup by time slider changed to 7.

= 1.4.0, December 22, 2014  =
- New: Log Rotation
- New: Search
- Tweak: Settings
- Fix: international characters are supported now
- Fix: Mandrill support

= 1.3.2, September 21, 2014  =
- Fix: HTML mails broken in previous version.

= 1.3.1, September 12, 2014  =
- Fix: angle brackets notation support (e.g. John Doe <john.doe@example.org>).

= 1.3, August 24, 2014  =
- New: clean mail listing including:
  Modal window for mail details. 
  Attachment support with appropriate icon for mime type.
- Tweak: Performance improvement
- Fix: screen option for mails per page

= 1.2, August 12, 2014  =
- New: video
- Tweak: Improved help & stability
- Fix: deletion of mails regardless of options (on update to 1.2 your mails will be deleted hopefully this happens for the last time)

= 1.1 =
- Tweak: Modified readme. 

= 1.0 =
- Initial Revision

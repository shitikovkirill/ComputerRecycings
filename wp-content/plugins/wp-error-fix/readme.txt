=== WP Error Fix ===
Contributors: vasyltech
Tags: error, warning, notice, bug, fix, hotfix, plugin, error fix, security, log
Requires at least: 3.8
Tested up to: 4.4.1
Stable tag: 3.4.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

WP Error Fix protects your image and website security by capturing errors and
providing solutions.

== Description ==

> WP Error Fix protects your image and website security. Now you can be sure you 
> make the best impression on your website visitors (no more error messages!) 
> and have peace in mind that your website is safe and secure...all without the 
> need for a developer!

One of the most difficult tasks is to maintain your website error free. An error 
free website significantly reduces the chance of being hacked or having a broken 
website. In addition, it provides a great user experience and increases conversion 
rate. With WP Error Fix, this is all possible without the need for a developer.

We make it quick, easy and there is no upfront cost. 

How does WP Error Fix takes a completely different approach to error handling? 

- First, it constantly monitors your website for any type of PHP errors and provides 
  the complete report in well organized format;
- Once Error Fix is activated, your errors are reported to our server and we analyze 
  them and provide fixes;
- Your website gets notified that there are available solutions for your errors 
  and simply with the click of a button, you apply fixes to your website.

> There are NO monthly or hidden fees. You DO NOT share any private information 
> with us (like FTP or Backend credentials). You pay only small amount for fixes
> that you select.

Give a try to WP Error Fix today and you will not be disappointed. For more 
information check FAQ section.

== Installation ==

1. Upload `wp-error-fix` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. And you are ready to go.

== Frequently Asked Questions ==

= How much does Error Fix cost? =

WP Error Fix plugin is a free of any charges. You pay nothing for monitoring 
and reporting errors. However majority of available fixes are not free. You would
have to pay a small amount (usually around 50 cents) to apply a fix.

= Who does provide the solutions? =

Our main priority is quality. That is why only experienced senior software 
developers analyze error reports and provide solutions. We do not outsource our
work and keep everything in-house. For more information about us check our website 
[vasyltech.com](http://vasyltech.com)

= How long it takes to fix an error? =

Depending on the number of error reports, it might take us up to a few days to prepare
fixes for your errors. But you can request High Priority support and we will be 
able to assist you within 24 business hours.


= Does Error Fix keep backups? =

YES. When you apply fixes to your website, original files are archived to 
wp-content/errorfix directory and grouped by the day so you can revert back any
changes if necessary.

== Screenshots ==

1. List of errors in table format
2. Pie graph of grouped errors by plugins, themes and core
3. Dashboard Widget

== Changelog ==

= 3.4.1 =
* Increased server request timeout to 20 seconds

= 3.4 =
* Added ability to send direct message to us
* Added a full year service support

= 3.3.6 =
* Extended core connector with ability to fetch a single file
* Simplified the core implementation - removed WordPress Custom category
* Added extra file verification step before patching

= 3.3.5 =
* Fixed bug #163 when storage is not read properly from the file system
* Fixed bug #184 when ZipArchive is not installed
* Show notification when Error Check is not activated 

= 3.3.4 =
* Added ability to trigger the fix check manually
* Fixed small HTML issue
* Improved UI feedback during activation
* Optimized performance for non-activated instance

= 3.3.3 =
* Fixed the bug with multisite network.

= 3.3.1 =
* Fixed the reported bug #19 when file does not exist after error triggered
* Fixed the reported bug #37 when WordPress core triggers an error

= 3.3 =
* Added Settings Tab
* Added ability to receive email notifications on errors
* Added ability to receive email notifications when fixes are available
* Simplified UI for newly installed WP Error Fix 

= 3.2 =
* Changed the error handling mechanism to core PHP error_handler
* Simplified the implementation
* Removed PHP error log parser from the Error Fix framework

= 3.1.2 =
* Moved Connect to the Error Fix framework
* Moved Cron to the Error Fix framework
* Added ability to download error log
* Fixed the bug with recognizing correct module in some specific cases

= 3.1.1 =
* Fixed the bug with Plugin Name recognition

= 3.1 =
* Refactored the core. Moved the Error Fix functionality to the mini-framework
* Added Rejected tab to explain the reason for report rejection
* Updated screenshots

= 3.0.2 =
* Fixed patcher to be compatible with PHP 5.2 version

= 3.0.1 =
* Fixed the bug with activation message
* Added Dashboard Widget with stats
* Improved backend manager implementation

= 3.0 =
* Completely from scratch implementation
* Faster and simplified core functionality
* Responsive and more intuitive UI

= 2.0 =
* Moved plugin to technical support concept

= 1.7 = 
* Fixed issue with dashboard over SSL
* Fixed PHPSnapshot bug with failed storage retrieve
* Updated Rate Us URL
* Added Preferences page
* Added Email notification functionality
* Fixed issue with PHP Core bug related to _destruct call on fatal error

= 1.6 =
* Moved logs to wp_errorlog dir. Based on Anderton feedback
* Renamed the main class
* Fixed the issue with SSL Dashboard (thank you moxojo)
* Added payment functionality
* Improved Patching mechanism
* Added custom dialog feature

= 1.5 =
* Fixed issue with Ajax failed calls. Show an error message
* Fixed CSS issue with minor actions for Chrome
* Fixed issue with not writable log and cache directories
* Added Error Status tooltip with explanation

= 1.4 =
* Removed deprecated functionality
* Fixed issue with hardcoded bootstrap path
* Added Rate Me button
* Fixed patching mechanism
* Improved (optimized) reporting mechanism
* Added internal caching mechanism for storage & queue objects
* Added additional check if content folder is writable

= 1.3 =
* Fixed Bug Report #81447
* Removed the Send Message screen
* Changed Control Panel UI
* Simplified the About section

= 1.0 =
* Initial version
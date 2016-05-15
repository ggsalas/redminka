==The Barley Editor for WordPress==

Made by [Plain](http://plainmade.com).

This WordPress plugin makes it easy to edit post and page content using the Barley Editor. For more information [visit the Barley web site](http://getbarley.com).

== Installation Instructions ==

We've made this [a video if you'd rather that](http://support.getbarley.com/hc/en-us/articles/201136323-Barley-for-WordPress-How-to-Install).

Installing Barley for WordPress is very simple. From the WordPress admin, hover over the “Plugin’s” section, and click “Add New.”

On the following screen, select “Upload”. Click “Choose file” and locate the “barley.zip” file that was downloaded on purchase. With the proper file selected, hit the “Install Now” button.

Once the plugin is installed, click Activate plugin. That's it!

== How to Use Barley for WordPress ==

We've [prepared a video to help you use Barley for WordPress](http://support.getbarley.com/hc/en-us/articles/200929958-Barley-for-WordPress-How-to-use-Barley-for-WordPress).

== License ==

See license.txt

== Changelog ==

= 1.7 =
* New: The Barley Editor can now be turned on or off by default from the Writing Settings
* New: Barley Editor can be turned on or off from the WP Admin Bar
* Removed: The Barley Editor can no longer be turned on and off from the customized "Edit This" link in themes.

= 1.6 = 
* New: Support for Advanced Custom Fields including text, text area, number, email address, and WYSIWYG field types.
* New: Support for built-in WordPress custom fields.
* New: Support for Custom Post Types.

= 1.5 =
* Update: Removed License API and Update API code for Code Canyon Release.

= 1.4.6 =
* Update: Small patch for the Origami theme.
* Update: Updated the Barley Editor to version 0.11.2 (fixes a Firefox v28 Bug)

= 1.4.5 =
* Update: Site license improvements.

= 1.4.4 =
* Update: Site license improvements.

= 1.4.3 =
* Update: Updated the Barley Editor to version 0.11.1 (fixes a small paste issue)

= 1.4.2 =
* Update: Updated the Barley Editor to version 0.11

= 1.4.1 =
* Update: Fixed an issue that blocked other plugins to be updated.
* Update: Editor to version 0.10.5
* Fix: Made the Default Category always checked if it's represented in the DB that way.

= 1.4 =
* New: Pages menu to add page to parent page, choose page template, and order.
* New: Pages can be deleted from the Admin Bar.
* Update: Barley Editor 0.10.4 includes various bug fixes and improvements.

= 1.3.1 =
* Fix: Drafts count in admin bar now shows the correct combined total for both page and post drafts.
* Update: Barley Editor 0.10.1 included.

= 1.3 =
* New: Update to version of Editor which includes new character replacement

= 1.2.4 =
* Fix: Bug found for creating new blog posts and pages.

= 1.2.3 =
* Fix: Fix for WordPress 3.6 and user capabilities erring.

= 1.2.2 =
* Fix: A fix for wp-insert plugin to not add content to the_content

= 1.2.1 =
* Fix: A fix for editing existing links in post and page content.

= 1.2 =
* New: Support for WordPress MU.
* New: Link Helper tool.
* New: Editor includes H4. Removed H1.
* New: Drafts show in Pages.
* Update: Fixes issues that some had using the WordPress HTTPS Plugin.
* Fix: Fixed PHP errors when in running WordPress in debug mode.
* Fix: Fix for Firefox 25 and 26 adding breaks at the end of titles.

= 1.1 =
* New: The categories now sort by used to un-used each time the meta toolbar is loaded.
* Update: Spanish translation added.
* Update: Submits new version numbers to API less often.
* Update: Barley for WordPress will continue to work after a license expires. A valid license is only required if the user wants to update their plugin.
* Update: Simplified Chinese translation added.
* Update: Additional logic for wrapping using in_the_loop.
* Update: Updated styles for drafts modal
* Update: Fix for Genesis Framework shortcodes.
* Update: Barley Editor to 0.7.1 - Update to initiation script and insertion tool.
* Update: Fix for "link" tag in header showing Barley information.

= 1.0.4 =
* Fix: The publish button now shows for Administrators, Editors, and Authors (on their own posts).
* Fix: A fix for padding on divs in navigation for Twenty Eleven theme.
* Update: German translation has updated terms.
* Small UI fixes.

= 1.0.3 =
* Fix: 404s for turning Barley editor off on permalinks.
* New: Fully compatible with Wordpress 3.x Roles and Capabilities.
* New: Dutch translation.

= 1.0.2 =
* Update: German translation.
* Update: More language terms that can be translated.
* Update: The Barley Editor no longer parses shortcodes.
* Update: The Barley Editor can be turned on and off in edit mode whereever a themes "edit" link is.

= 1.0.1 =
* Update: The plugin no longer calls itself beta. Whoops!
* Update: Barley for WordPress is now compatible with Facebook Meta Tags.
* Update: Plugin compatibility with "Bottom of Every Post"
* Update: Plugin compatibiltiy with JetPack Sharing.
* Update: Compatibility with Plugins outside of the WordPress directory.

= 1.0 =
* Update: No more Barley tags. Moving back to DIVs just for Firefox. Would love to figure out a solution to this for 1.0.1
* Update: Default data updates.

= 0.9.1 =
* Update: Switched to https
* Update: Updated editor to 0.5.4, which fixed a few link bugs as well as some FireFox styles.

= 0.9 =
* Update: New links on Writing Options panel for Walkthrough video and getting to Help Center.
* Update: New default data for all post formats.
* Update: Removed logo from footer of changelog.
* Several fixes and updates for production.

= 0.3.7 =
* Update: Bug fixes and improvements to the changelog design.

= 0.3.6 =
* Update: New changelog design.

= 0.3.5 =
* Update: Fix for license keys.

= 0.3.4 =
* New: Polish language translation.
* New: A simple, optional text widget to help promote Barley for WordPress.
* Update: Added some styles to fix the featured image on page load.
* Update: Tweaked styles for white text in media modal
* Update: Removed right positioning for the categories/tags insert tool
* Update: Fixed styles and JS animations for tags/categories dropdown lists
* Update: Much reduced License API code.
* Update: Invalid or blocked or inactive domains no longer load the Editor and show error in Admin.
* New: Copy in Admin area to get key, watch video, get help.
* New: Two transient API uses: barley_license_check, barley_license_version
* New: Added barley.bar declaration to the base.js file so the editor will work properly without the Lock button present.
* Update: Profiled the license API checks and brought the total number of API checks down to 4 per day per license.
* Update: Changed Transient API from 1 hour to 12 hours.
* Update: Editor Version to 0.5.0 which included removal of Rangy and some updates to the core
* Update: Editor Version to 0.5.1 which added doc.ready to the Editor init for possibly slower sites.

= 0.3.3 =
* Update: Added a method to truncate the title in the drafts model to keep on a single line and increased the width.
* Update: Slightly nicer changelog display.
* Update: Updated all links to the new Barley Help Center.
* Fix: If an older version of plugin is installed a new version will be prompted for update.

= 0.3.2 =
* Fix: Rewrite on how categories are processed and updated
* Update: Updated Editor to 0.4.0
* Update: Updated documentation and cleaned up some JS global variables.
* Update: Small copy changes.
* Update: Version information now properly reported to API.

= 0.3.1 =
* Update: Some style tweaks to the featured image display.

= 0.3 =
* Update: Drafts button in Admin Bar now shows the number of drafts. Doesn't show if less than one.
* Update: Colors on Publish and Delete button.
* Update: Tweaked the way the number of drafts is counted to use a specified WP query.
* Update: Better detection for the_loop. Ditched debug_backtrace.
* Update: Fixed some style conflicts in drafts modal.
* Update: Created a `barley_assets` folder for asset files (styles, images etc)
* Update: Upon learning more about images, I specified the correct context for WP Media Upload
* Update: The Drafts button no longer shows in Admin.
* New: Added ability to add/remove/update a featured image from the meta toolbar
* New: Rewrote the WP.Media modals to re-use the same ones for each type of media interaction.

= 0.2.6 =
* Update: Deleting a post now asks for confirmation first. Still goes to Trash.
* Update: Post drafts no longer tagged with "untagged" by default.
* Fix: Fixed a PHP bug when searching for version numbers.
* Update: Wrapping the_content and the_title now runs dead last.
* Updated: Added some more filters to the JS function to prevent un needed wrapping issues.
* Updated: Changed the YouTube API key to a specific one for Barley for WordPress
* New: Drafts Modal

= 0.2.5 =
* Update: license/version now returns true when there is no update. It's a long story.
* Update: Added jQuery loop to check for any titles wrapped inside an 'A' tag and will remove them and make them uneditable.
* Update: Added script to prevent editor being pushed down on some themes.
* Update: Several changes to default post and page data.
* Update: Link in admin to proper plugin page URL.
* Added: Added function for adding a small link to page/post edit table to "Edit with Barley"
* Update: Made tweaks to the media insertion to work better with pdf, audio and video
* Various bug fixes and code commenting.


= 0.2.4 =
* Update: Tweaked styling to the tag button to remove pixelated border
* Update: Updated styling to work on a black site with white text (and other variations)
* Update: Changed padding on tag/category context so the text doesn't bleed under the check box/button
* Fix: debug_backtrace is no longer used unless allowed
* Update: Sending version information with license/activate to track unlimited license versions

= 0.2.3 =
* Update: Added tag button on hover of the_content to edit tags/categories
* Update: Added barley-plugin-styles.css for use to add any styles to our plugin
* Update: Made hover, click interaction better with tag/category button
* Fix: Fixed a bug with the editor that caused multiple toolbars to show up
* Update: Added a delete button for posts.
* Update: Images are now resized up to fit into placeholders.

= 0.2.2 =
* Fix: A more complete style reset.
* Update: Barley Editor 0.3.1
	* Fix: Barley Editor no longer returns undefined in some cases.
* Update: Various copy changes in WordPress admin in preparation for beta.
* Update: Now uses the Staging License API.
* Fix: When the current plugin version is up-to-date no error message is shown.

= 0.2.1 =
* Fix: Pulling in Barley Editor HTML is now quicker
* Fix: Adding multiple tags to posts no longer shows a leading space

= 0.2 =
* New: Edit Categories and Tags for a post from front-end
* Fixed an error with Permalinks and the Publish button.
* Fixed a small bug where a SPAN was showing up in wp_title. But only in some themes.
* Fix: The insert tool for paragraph hovers now works.

= 0.1.9 =
* All text strings in WordPress Admin are now i18n compatible.

= 0.1.8 =
* Fix: single_post_title now works just like the_title

= 0.1.7 =
* Fix: Expired licenses no longer work. Plugin checks for updates every 24 hours.

= 0.1.6 =
* Fix: License keys no longer work for invalid domains.

= 0.1.5 =
* GPL License Added
* Editor files restructured
* Updated PHP files

= 0.1.2 - 0.1.4 =
* Boat loads of updates.

= 0.1 =
* Advanced inline editing capabilities for posts and pages, site title and site description

[Barley Website](http://getbarley.com/) | [Plain](http://plainmade.com/)

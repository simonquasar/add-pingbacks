=== Add Pingbacks ===
Contributors: simonquasar
Tags: pingbacks, manual pingbacks, comments, linkbacks, custom post types
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 5.6
Stable tag: 1.2.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Manually add pingbacks to any post, page, or custom post type in WordPress.

== Description ==

Add Pingbacks is a WordPress plugin that allows you to manually create pingbacks for your posts, pages, and custom post types. This is particularly useful when you want to:

* Add missing pingbacks that weren't automatically detected
* Manually create references between content
* Manage content relationships through pingbacks
* Add pingbacks for legacy content

= How It Works =

1. Go to **Comments > Add Pingbacks**
2. Select from any custom post type the specific post you want to add the pingback to
4. Enter the referrer's site title, URL and excerpt or content of the pingback
5. Click "Add Pingback" to create the pingback

== Installation ==

1. Upload the `add-pingbacks` directory to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to `Comments > Add Pingbacks` to start adding pingbacks

== Changelog ==

= 1.2.2 = 
Tested up to WordPress 6.8
Improved backward compatibility and incorporating better practices.

* Added text domain support for translations and `esc_html__()` for translatable strings
* Added proper checks and sanitizations to submission
* Improved error handling and security checks in AJAX handler

= 1.2.1 =
* Minimalistically refactored (one file only)

= 1.2 =
* Added support for all public post types
* Dynamic post loading
* Data sanitization

= 1.1 =
* Initial public release

== Support ==

For any issues and requests:
[github.com/simonquasar/add-pingbacks](https://github.com/simonquasar/add-pingbacks/)

== Credits ==

Initial development by [simonquasar](https://www.simonquasar.net/)

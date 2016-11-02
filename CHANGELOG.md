# Changelog

Here's a summary of changes in each update.

## Version 1.0.0 - Upcoming

* Fixed: FluxBB v1.5.9. Bug fixes and security improvements.
* Changed: New URL for 'documentation'.
* Changed: New URL for 'themes'.
* Changed: Overhauled the build process.
* Changed: Removed Ruby from build.
* Changed: Removed Compass from build.
* Changed: Removed Toolkit from build.
* Removed: meta data for apple-touch-icons/Windows tiles no longer required.

## Version 0.3.6 - 2015/10/12

* Fixed: Search form should have an ARIA role.
* Fixed: accessibilityHazard spec has changed.
* Changed: Dropped support for Internet Explorer 8.
* Changed: TXP Tips is now Textpattern Tips.
* Changed: minor CSS code optimisations.

## Version 0.3.5 - 2015/04/11

* Fixed: Search microdata errors.
* Changed: New URL for where the logo will be in new .com site.

## Version 0.3.4 - 2015/04/11

* Changed: Improvements to ARIA and microdata.
* Changed: Update high resolution media query syntax.
* Changed: Updated dependencies.
* Added: editorconfig file.
* Added: More comments in build files.

## Version 0.3.3 - 2015/04/07

* Changed: Updated dependencies.

## Version 0.3.2 - 2015/01/29

* Fixed: FluxBB v1.5.8. Fixes bugs, adds an `addons` directory for rudimentary plugin support, plus other minor improvements.
* Added: Extended microdata markup of HTML structure.
* Added: Meta `theme-color` tag.
* Changed: Performance improvements.
* Changed: Serve HTML5Shiv from CDNJS.
* Changed: Updated jQuery versions.
* Changed: Updated various build components.

## Version 0.3.1 - 2014/11/10

* Fixed: Content-Security-Policy headers were failing to load Google Web Fonts, because Google have recently changed the font serving URL.

## Version 0.3.0 - 2014/11/07

* Fixed: FluxBB v1.5.7. Fixes security vulnerabilities.
* Fixed: Double-border glitch in IE 11.
* Fixed: Prevent really long words from breaking alert box layouts.
* Fixed: Prevent IE 8 layout from collapsing too far.
* Fixed: Textile v3.5.5.
* Added: Theme source code has been extensive commented throughout.
* Added: New apple touch icon size for iOS 8.
* Changed: Speed up CSS transitions.
* Changed: Remove legacy touch icons.
* Changed: Updated various components and Grunt tasks.
* Changed: Slightly more room by removing left-padding of tables (helps reduce likelihood of layout spillage on smallest devices).
* Changed: Various minor CSS improvements and optimisations.

## Version 0.2.0 - 2014/01/11

* Fixed: FluxBB v1.5.6. Fixes account renaming, HTML5 validation and security vulnerabilities.
* Fixed: Masthead logo glitch on IE 8. Doesn't display Textpattern logo twice.
* Fixed: Main network navigation loading and content shifting.
* Fixed: Improve font loading on subsequent page visits. Move from Web Font Loader to a link tag.
* Fixed: Remove JavaScript based moving of the subscribe button; causes content shifting.
* Fixed: Off-positioned forum navigation tabs. Appeared 1px below the strip border on Chrome.
* Fixed: Don't send non-IPv4 addresses to Stop Forum Spam.
* Fixed: Sfs::isBanned() check; do not create duplicate ban records.
* Fixed: Only load HTML5 Shiv on IE 8.
* Fixed: Rare cases where there is no space rendered between forum navigation tabs.
* Fixed: Issues where wrong syntax highlighting language would be used.
* Fixed: Fix code block quoting and its escaping.
* Fixed: Font rendering issues on Chrome Mac by explicitly setting the font to Arial; sans-serif default querying fails.
* Added: Indicator to main navigation to show current section.
* Added: Textarea height grows with the content.
* Added: Setup script creates avatar directory if missing.
* Added: Move focus to the username field on the login page.
* Added: Sfs bot writes down dates with bans and the logged event.
* Added: Prevent admins from accidentally banning wide range of users by IP address.
* Added: Remove the account if flagged on the first post.
* Added: Registration form displays a CAPTCHA.
* Changed: Improve layout of quickpost form.
* Changed: Load Google Analytics protocol relatively.
* Changed: Unset Google Analytics tracking cookies when the session ends. We don't do persistent tracking.
* Changed: Re-enable category and forum arrangement management through production server's web GUI.
* Changed: Reverse SVG logic; logos default to SVG, loads PNG replacement if needed.
* Changed: Improve `:focus` styling for buttons and form elements.
* Changed: More prominent blockquote styling.
* Changed: [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) and [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2.md) coding standard compliance.
* Changed: Use CSS sprite for common icons - reduces HTTP requests and page filesize.
* Changed: Various CSS optimisations.
* Changed: Switch Share button to Google +1.
* Changed: Post quoting tool strips unneeded long-block escaping paragraph tags from between quotes.
* Changed: Quotes preserve embedded media links.
* Changed: Does't show "You're not logged in" message on mobiles.
* Changed: Stop Forum Spam validator tool doesn't ban, but only blocks and limits requests activity.
* Changed: Moved profile, register, logout and login links next to where "logged in as" message is presented.
* Changed: Send strict Content-Security-Policy headers.
* Changed: Conditionally load jQuery 2.x if supported by the client.
* Removed: Modernizr; avoided feature testing by reconstructing CSS.
* Removed: Social sharing buttons.
* Removed: Vimeo support.

## Version 0.1.0 - 2013/12/17

* Initial release.
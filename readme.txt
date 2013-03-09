=== Image Gallery Reloaded ===

Contributors: DanielSachs
Tags: gallery, default gallery, replacement, galleria, colorpicker, images, image, image gallery
Requires at least: 3.2
Tested up to: 3.5.1
Stable tag: 2.1.6

Replaces the default Wordpress gallery with a featured slideshow.

== Description ==

**A jQuery based Image Gallery Reloaded plugin replaces the default Wordpress gallery with a highly customizable slideshow and gallery.**

*   Set gallery images and thumbnail sizes
*   Customize gallery design to fit your Theme
*   Set transition effects
*   Lightbox view of the images 
*   Image panning effect
*   Custom colors via color picker to match your theme - you do not to remember all those HEX numbers
*   Autoplay
*   Extensive settings
*   Based on the default WordPress gallery


1.   Go to your post and click the "Edit Gallery" button.
2.   At the top of the popup click "Include in Gallery: All" or select individual images
3.   Click "Save all Changes"
4.   Click "Update Gallery Setting"
5.   Click "Update" in the post's Publish meta box

To see Image Gallery Reloaded (IGR) in action visit : [18elements website](http://18elements.com/tools/wordpress-image-gallery-reloaded)

Interested in contributing? Fork it on [GitHub](https://github.com/DanielSachs/image-gallery-reloaded)



== Installation ==

1. Upload `image-gallery-reloaded` directory to the `/wp-content/plugins/` directory.
2. Activate the plugin via the Plugins menu.
3. Configure your new gallery via the Settings > Image Gallery Reloaded Options.

== Frequently Asked Questions  ==

**How do I add galleries to posts?**

Remember, Image Gallery Reloaded uses the default WordPress gallery.

* Write a new Post or edit an existent.
* Add images to the post via the default Upload/Insert >Add an Image.
* On the Add an Image popup click the Gallery tab, Click Insert Gallery and save the post.


**How to display thumbnail on top?**

* Select the "Classic | thumbnail on top" theme
* If you'd like to show the thumbnails outside of the main image, set `Main Image Top Margin` height to allow the required space.


**How to add links to image descriptions and captions**

Wordpress escapes the double quotes in image descriptions and captions.
Use Single quotes: `<a href='http://my-awsome-site.com'>My Awsome Site</a>`

**Help, gallery does not display images**

The gallery displays only selected images. This feature allows you to keep some of the attached images excluded from the gallery. This also means that if no images are selected, the gallery will not show up. To fix this issue with existing galleries:


1.   Go to your post and click the "Edit Gallery" button.
2.   At the top of the popup click "Include in Gallery: All" or select individual images
3.   Click "Save all Changes"
4.   Click "Update Gallery Setting"
5.   Click "Update" in the post's Publish meta box


== Changelog ==

**2.1.6**

* [NEW] Multiple galleries on page

**2.1.5**

* [NEW]	WP 3.5+ support

**Important: Since WP has integrated the new Media library interface, which allows to select the displayeed images, the selective Gallery feature has been dropped.**

**2.1.4**

* [NEW]	Mouseover effect on thumbnail to load the main image
* [NEW]	Show or hide thumbnail strip
* [NEW]	Use custom CSS
* [NEW]	Disable selective gallery. Show all images in all galleries
* [REMOVED]	Custom Themes Support. Now uses only the built-in themes, sorry guys


**2.1.3**

* [NEW]	On Post warning when no images were added to the gallery


**2.1.2**

**UPGRADE NOTICE:** Upgrading from versions prior to 2.1.2, please visit your gallery pages and select the images you want to display in a gallery:

1.   Go to your post and click the "Edit Gallery" button.
2.   At the top of the popup click "Include in Gallery: All" or select individual images
3.   Click "Save all Changes"
4.   Click "Update Gallery Setting"
5.   Click "Update" in the post's Publish meta box

* [NEW]		Added functionality: Select specific images for the gallery / Selective gallery
* [NEW]		Added functionality: Theme support
* [NEW]		Added functionality: "Classic" theme with thumbnails on top
* [NEW]		Added functionality: Custom theme directory for user themes
* [NEW]		Added functionality: Image descriptions
* [NEW]		Added option: Toggle image Title and Description
* [NEW]		Added option: Theme selection
* [NEW]		Added option: Define main image top and bottom margins to allow thumbnails on top
* [Bug fix]	Title is cut after the first word


**2.0.1**

* [Bug fix]  Remove debugging mode for main JS library
* [Bug fix]  Use of undefined constant gallery - assumed 'gallery'

**2.0**

* Complete rewrite

**0.6**

* [Bug fix]   caption and tooltips displayed properly now
* [Bug fix]  Better Thickbox support
* Multiple bugfixes and cleanup

**0.5.6**

* [Bug fix] CSS for large galleries to form one line of images
* [Bug fix] multiple galleries on one (archive) page.

**0.5.5**

* [Bug fix] Resolved issues with the built-in jQuery
* [Bug fix] Tooltip styling reserved to IGR only
* [Bug fix] tooltip script reserved to IGR only
* Other bugfixes

**0.5.2**

* [NEW]		Styled Tooltips added;
* [NEW]		Thickbox support added;
* [NEW]		Better function handling;
* [NEW]		Styling for tooltips from the Control Panel;
* [NEW]		"Loading Gallery" massage while loading the images;
* [NEW]		Transition Effects on image load;
* [Bug fix]	function conflicts on some themes;
* [Bug fix]	jQuery loaded twice in enqueue mode;
* Other multiple bug fixes;

**0.2.4**

* [Bug fix] Fixed "Headers already sent" error

**0.2.3**

* [Bug fix] Changed improperly named files

**0.2.2**

* initial release

== Screenshots ==

1. The Setup Page
2. The Gallery
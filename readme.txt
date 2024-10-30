=== Big Cartel Wordpress Plugin === 
Contributors: lmon
Tags: bigcartel, big cartel, ecommerce, commerce, store, product
Requires at least: 2.0.2
Tested up to: 2.9.2
Stable tag: 0.010

Allows you to easily pull info from your Big Cartel Account into Pages you build on your Wordpress site...!

== Description ==
Pulls info from your Big Cartel Account into Pages on your Wordpress site. 
The Plugin allows you to create a few of the Main Big Cartel pages easily.

This is a very early version and I encourage others to contribute with ideas and code!
There are a number of Basic TODOs, so if you're interested, contact me http://sites.google.com/site/sooperinc. Also feel free to donate! http://sites.google.com/site/sooperinc/donate

Version:  0.010

DATE: 03-25-10 
 
== Installation ==

1. Upload to Plugins Directory (/wp-content/plugins/)

2. Activate

3. Go to Options page( Settings > "Big Cartel Wordpress Plugin" )

4. Fill in the Details, Confirm its working below.

5. Follow the instructions 

6. To Edit HTML Template files, go to [PLUGINDIRECTORY]/big_cartel_shop/templates. To change product list template(Home), edit "productList.tpl". To change product detail template(Product Detail), edit "productDetail.tpl"

7. Make sure jQuery is enabled by adding this like to your themes/YOURTHEME/header.php file: &lt;?php wp_enqueue_script("jquery"); ?&gt;

== Frequently Asked Questions ==

= What is BigCartel? =

= Big Cartel is www.bigcartel.com. 'Big Cartel is a simple shopping cart for artists. It's easy to use, customizable, and awesome.' Find out more at http://bigcartel.com/tour =

= Can I pull Big Cartel Cart information into my Wordpress pages? =

= No not yet. That's something I'm working on. Care to help? =

= Does your plugin require LibCurl? = 

= Why yes, it does. To be more specific, it uses LibCurl or file_get_contents to retreive XML data from the BigCartel servers =

= Can I help you improve the Plugin ? =
I'd love that. There are a number of features & options I'm entertaining, so developers, testers and all-round guinea pigs are encouraged to contact me here: http://forums.bigcartel.com/topic/bigcartel-on-wordpress?replies=3#post-4163 or here: http://sites.google.com/site/sooperinc/contact-us


== Screenshots ==

1. The options Page

2. The default Product Home Page 

3. The default Product Page 


== Changelog ==
= .010 =
 UPDATED 3-25-10
 * Allow for Image types other than JPEG

= .009 =
 New Functionality Added : Current Product
 * new function bigcartl_getCurrentProduct - get the object of the product currently loaded - after filters etc 
 * new function bigcartl_getCurrentProducts - get the object of all the products currently loaded - after filters etc 
 * new functions bigcartl_previous_post_link & bigcartl_next_post_link:
  Usage: 
  &lt;?php 
  	    //navigation for product page
		if($post->post_title == bigcartl_getPageSlug("product")){ ?&gt;
		&lt;div class="navigation shopnavigation"&gt;
			&lt;div class="alignleft"&gt;&lt;?php bigcartl_previous_post_link('&laquo; %link') ?&gt;&lt;/div&gt;
			&lt;div class="alignright"&gt;&lt;?php bigcartl_next_post_link('%link &raquo;') ?&gt;&lt;/div&gt;
		&lt;/div>
	&lt;?php } */?&gt;
		
 * new function bigcartl_getAFormattedLink - consolidates link generation
 
 UPDATED
 * bigcartl_getFormattedProducts and bigcartl_getAFormattedProductDetail to use bigcartl_getAFormattedLink
 * XPATH. Started replacing XML Loops with XPATH
 
 UPDATED 2-15-10
 * Updated shortcode.php to accept argument classname. Will add a css class to the productList DIV and the productDetail DIV
    Usage: [bigcartl show='home' classname='special'/]
    *Correspondingly Updated Templates/productDetail.tpl, templates/productList.tpl
    *Correspondingly Added global $displayClassname
    *Correspondingly Added bigcartl_setClassname
    
= .008 =
* Begun to add Category functions: bigcartl_getCategoryUrl, bigcartl_getCategories, bigcartl_getCategoryByName, bigcartl_getCategoriesForProduct, bigcartl_ProductIsInCategory. Not fully implemented, as there are performance issues related to getting product category that  need to work out
* On Admin page added new option: Home/Store Page & Product page image size. Allows user to choose small, medium or large for display on each. 
* On Admin page added new option: Indicate own CSS file, or use the Form data
* Added LightBox Gallery on Product Pages! (DISCLAIMER: This is working with my install of JQUERY but has not been tested across all! JQUERY confilcts are common in WP, but i tried to go about it the recommended way. Let Me Know. ) Need to make this optional.
* Updated lightbox CSS
* Fixed bug where wrong Plugin Versin was being displayed on Admin page

* fixed the way bigcartl_getImageSizeSource and bigcartl_getProductDefaultImage work together
* On Admin page added original Option in image sizes
* allow user to filter items by category 
	usage: bigcartl_setCategoryFilter("shirt,pants");
	also in shortcode: 
	 [bigcartl show='home' categories='cats,dogs'/] 
* Updated productList.tpl to include a tile in the HREF
* Added method: bigcartl_getCurrentProducts that:
*  
 
= .007 =
* Fixed order of FILE open functions to try from file_get_contents, curl to curl, file_get_contents

* Added JQUERY/Gallery functionality on product page. To see, use more than one image per product and it will display a thumbnail for each and clicking on the thumb will bring up a gallery

= .006 =
* Fixed issue where, if Wordpress is not installed in Document Root, the link to products will break.

= .005 =
* Added error new checking to Options page and improved existing.

* Updated HTML/CSS formatting on Options page

* Improved PHP class bigcartel.class.php to have handle errors better 

* Improved Templating by adding a Templating Class. This class could be updated to use better-known PHP template engines, if desired.

* Changed use of name to permalink for link to product page

= .004 =
* Updated paths in main file to avoid differing folder names upon installation

* Moved default CSS into templates/bigcartel.css

* Changed copy & Warnings on Options Page

= .003 =
* Externalized much of the HTML to Template files in /wp-content/plugins/big_cartel_shop/templates. To change product list template, edit "productList.tpl". To change product detail template, edit "productDetail.tpl"

* Updated default CSS

* Updated default HTML

* Added to Options page ability to add your own Product Page and Homepage names. These are required
 
* Added function to pull page URLS

* Fixed bug where add to cart url was missing

* Made image on Detail page the MEDIUM version

* Commented out "get Cart" functionality, as it is not yet working.

* Added new screenshot

= .002 =
* Added Css Field To Options. Updated Html.

= .001 =
* Created.


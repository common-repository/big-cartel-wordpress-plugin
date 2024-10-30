<?php
/*
Plugin Name: Big Cartel Wordpress Plugin
Plugin URI:   http://sites.google.com/site/sooperinc
Description: Pulls info from your Big Cartel Account into Pages on your Wordpress site . The Plugin allows you to create a few of the Main Big Cartel Pages Easily More details on the options page.
Author: Lucas Monaco
Version:  0.010
Author URI: http://sites.google.com/site/sooperinc
*/
/*  Copyright 2007-2009 Lucas Monaco  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*  
  UPDATED 12 10 09 
  UPDATED 01 04 10 
	-- use permalink instead of name for link to product page
  UPDATED 01 13 10 
	-- updated bigcartl_getPageUrl 
  UPDATED 01 14 10 
 *added Thuumbnail gallery on Product Page
 *updated bigcartl_js

  UPDATED 01-22-10 
 * added new option: detail page image size. chaged this function bigcartl_getAFormattedProductDetail
 
 UPDATED 1-26-10 
 * fixed the way bigcartl_getImageSizeSource 
 * 	and bigcartl_getProductDefaultImage work together
 * added "original Option in image sizes
 * 

 ADDED 1-26-10
 * allow user to filter items by category 
	usage: bigcartl_setCategoryFilter("shirt,pants");
	also in shortcode: 
	 [bigcartl show='home' categories='cats,dogs'/] 

 ADDED 2-1-10
 * function bigcartl_getCurrentProduct - get the object of the product currently loaded - after filters etc 
 * function bigcartl_getCurrentProducts - get the object of all the products currently loaded - after filters etc 
 * function bigcartl_previous_post_link
 * function bigcartl_next_post_link
 * function bigcartl_getAFormattedLink
 
 UPDATED
 * bigcartl_getFormattedProducts and bigcartl_getAFormattedProductDetail  to use bigcartl_getAFormattedLink
 * XPATH. Started replacing XML Loops with XPATH
 UPDATED 2-15-10
 * Updated shortcode.php to accept arguemt "classname='XXXXX'". will add a css class to the productList DIV and the productDetail DIV
    usage: [bigcartl show='home' classname='special'/]
    *Updated Templates/productDetail.tpl, templates/productList.tpl
    *Added global $displayClassname
    *Added bigcartl_setClassname
    * 
 UPDATED 3-25-10
 * Allow for Image types other than JPEG
* */

if ( ! defined( 'BCWP_PLUGIN_VERSION' ) )
	define( 'BCWP_PLUGIN_VERSION',  "0.010" );

	if ( ! defined( 'BCWP_PLUGIN_BASENAME' ) )
	define( 'BCWP_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'BCWP_PLUGIN_NAME' ) )
	define( 'BCWP_PLUGIN_NAME', trim( dirname( BCWP_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'BCWP_PLUGIN_DIR' ) )
	define( 'BCWP_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . BCWP_PLUGIN_NAME );
/*
 * 
 */
require(BCWP_PLUGIN_DIR."/shortcode.php");
require(BCWP_PLUGIN_DIR."/classes/bigcartel/bigcartel.class.php");	 
require(BCWP_PLUGIN_DIR."/classes/TemplateClass/TemplateClass.class.php");	 

/* the class */
$bigcartl = new BigCartel();
// sample URL http://MYDOMAIN.bigcartel.com/product/MYPRODUCTNAME.xml

/* 
 * Establish some base details 
 these are all established by BigCartel API's etc
 and would need to change if BigCartel Changes
*/
$bigcartl->BASEURL = "http://";
$bigcartl->BASEURL2 = ".bigcartel.com/";
$bigcartl->CART = "cart/";
$bigcartl->PRODUCT = "product/";
$bigcartl->SUFF = ".xml";
$bigcartl->CARTSUFF = "/cart.xml";
$bigcartl->STORESUFF = "store.xml";
$bigcartl->PRODUCTSSUFF = "products.xml";
/* added 1-24-10*/
$bigcartl->CATEGORYSUF = ".xml";

$bigcartl->pages = array("product" => get_option('bigcartelproductpage'),
	 "homepage"=>get_option('bigcartelstorehomepage')
);

/* the Template class */
$bigcartlTemlpate = new TemplateClass();

$sizes = array();
$sizes[] = "75";  // updated to work with jpg or gif  3-25-10
$sizes[] = "175";
$sizes[] = "300";
$sizes[] = ""; // ORIGINAL
/* ADDED 1-24-10 */
$bigcartl_DetailImageEffect = get_option('bigcarteldetailimageeffect');//"USEIMAGESWAP";// or // USELIGHTBOX";

/* ADDED 1-26-10
store user-created product filter*/
$arrcatfilters = array();

/* Added 2-15-10 
 Allow user to specify a class in the shorcode 
*/
$displayClassname = "";

/* options page */
$options_page = get_option('siteurl') . '/wp-admin/admin.php?page='. BCWP_PLUGIN_NAME.'/options.php';
/* Adds our admin options under "Options" */
function bigcartl_options_page() {
	add_options_page('Big Cartel Plugin', 'Big Cartel Wordpress Plugin', 10, BCWP_PLUGIN_NAME.'/options.php');
}

/* construct the store URL */
function bigcartl_getStoreUrl(){
	global $bigcartl; 
	bigcartl_chkBase(); 
	return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $bigcartl->STORESUFF ;
}
/* construct the products URL */
function bigcartl_getProductsUrl(){
	global $bigcartl;
	bigcartl_chkBase();
		return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $bigcartl->PRODUCTSSUFF ;
}
/* construct the product URL */
function bigcartl_getAProductUrl($val){
	global $bigcartl;
	bigcartl_chkBase();
		return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $bigcartl->PRODUCT . $val . $bigcartl->SUFF ;
}
/* construct the cart URL */
function bigcartl_getCartDataUrl(){
	global $bigcartl;
	bigcartl_chkBase();
		return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $bigcartl->CARTSUFF ;
}
/* construct the Cart URL */
function bigcartl_getCartUrl(){
	global $bigcartl;
	bigcartl_chkBase();
		return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $bigcartl->CART ;
}

/* START CATEGORY FUNCTIONS */
 
function bigcartl_getCategoryUrl($catXmlObj){
	global $bigcartl;
	bigcartl_chkBase();
	
	if(empty($catXmlObj->url)){return false;}
	
	if ($bigcartl->DEBUG ) print "bigcartl_getCategoryUrl: getting Category data: ".$catXmlObj->url."<br>";
	return $bigcartl->BASEURL . get_option('basebigcartelurl') . $bigcartl->BASEURL2 . $catXmlObj->url.$bigcartl->CATEGORYSUF ;
}
/* added 1-24-10 */
/* get all categories for store */
function bigcartl_getCategories(){
	global $bigcartl;
	return $bigcartl->getCategories() ;	
}

/* added 1-24-10 */
/* get category object by its name */
function bigcartl_getCategoryByName($catname){
	foreach (bigcartl_getCategories() as $cat){
		if($cat->name == $catname){
			return $cat;	
		}
	}
	return false;
}
/* added 1-24-10 */
/* get categories for a given product */
function bigcartl_getCategoriesForProduct($xmlProduct){
	global $bigcartl;
	$cats = array();
	// loop through each category, look for product, match by id
	if ($bigcartl->DEBUG ) print "bigcartl_getCategoriesForProduct: (".$xmlProduct->name.") <br>";
	 
	foreach (bigcartl_getCategories() as $cat){
		// load data for each
		$url = bigcartl_getCategoryUrl($cat);
		if ($bigcartl->DEBUG ) print "bigcartl_getCategoriesForProduct: calling $url <br>";
		
		if($url){ 
			$data = $bigcartl->loadACategory($url); 
			/* UPDATED 2-25-10 */
			$result = @$data->product->xpath('/products/product/id[.='. $xmlProduct->id[0] .']');
		}else{
			if ($bigcartl->DEBUG ) print "bigcartl_getCategoriesForProduct: url $url is blank<br>";
		}
		if($result[0]) { 	$cats[] = $cat;  		}
		
	}
	return $cats;	
}

/* added 1-24-10 
TODO: Update to be links
*/
function bigcartl_displayCategoryList($arrCats){
	$ret = '';
	foreach($arrCats as $cat){
		$url = bigcartl_getCategoryUrl($cat);
//		$ret .= '<span><a href="'.$url.'" title="Link to '.$cat->name.'">'.$cat->name.'</a></span> <br />';
		$ret .= '<span>'.$cat->name.'</span> <br />';
	}
	return $ret;
}

/* added 1-24-10 */
/* get categories for a given product */
function bigcartl_ProductIsInCategory($xmlProduct, $xmlCategory){
	global $bigcartl;
	if ($bigcartl->DEBUG ) print "bigcartl_ProductIsInCategory: (".$xmlProduct->name.",".$xmlCategory->name.") <br>";
	if(empty($xmlCategory->name) ){ return false; }
	 
	// load data for the cat
	$url = bigcartl_getCategoryUrl($xmlCategory);
	$data = $bigcartl->loadACategory($url); 
 
	/* UPDATED 2-1 */
	$result = @$data->product->xpath('/products/product/id[.='. $xmlProduct->id[0] .']');
	if($result[0]) {
    	return true;
	}
	
	return false;
	
}

/* 
Return the Products as XML Object 
*/
function bigcartl_getStoreProducts(){
	global $bigcartl;
	bigcartl_chkBase();
	$bigcartl->loadProducts(bigcartl_getProductsUrl());
	return $bigcartl->getProducts();
}

/* Return the A Product as XML Object */
function bigcartl_getASingleProduct($namevaluepair){
	global $bigcartl;
	$map = array("n"=>"name");
	
	if ($bigcartl->DEBUG ) print "bigcartl_getASingleProduct: namevaluepair is ".$namevaluepair ."<br>";
	
	$pair = split("=",$namevaluepair);
	$name = $map[trim($pair[0])];
	$value = trim($pair[1]);
	
	if ($bigcartl->DEBUG ) print "bigcartl_getASingleProduct: URL is ".bigcartl_getAProductUrl($value)."<br>";
		
	// make request for single item:
	$product = $bigcartl->loadAProduct( bigcartl_getAProductUrl($value) );
 	/* added 1-27-10*/
	//print "setting currentproductdata" ;
	$bigcartl->currentproductdata = $product;
 
	return $product;//$bigcartl->getAProduct($name,$value);
}

/* ADDED 2-1
get the object of the product currently loaded - after filters etc 
*/
function bigcartl_getCurrentProduct(){
	global $bigcartl; 
	return $bigcartl->currentproductdata;
}
/* ADDED 2-1
get the object of all the products currently loaded - after filters etc 
*/
function bigcartl_getCurrentProducts(){
	global $bigcartl; 
	return $bigcartl->currentproducts;
}
	
/*
 * ADDED 1-27
 * using the current product, get a link to the previous product in the xml
 */
 
function bigcartl_previous_post_link(){
	global $bigcartl;
	if ($bigcartl->DEBUG )print "bigcartl_previous_post_link: CURRENT product is ". bigcartl_getCurrentProduct()->name."<br>";
 
	$positiontosearchfor = NULL;
	$products = bigcartl_getStoreProducts();
	/* use xpath instead */
	/* HARDCODED! */
	$result = @$products->xpath('/products/product/position[.='. bigcartl_getCurrentProduct()->position[0] .']/parent::*');
	if($result[0]) {
    	$positiontosearchfor = (intval($result[0]->position)-1);
	}else{
		if ($bigcartl->DEBUG )print "bigcartl_previous_post_link: ERROR Getting a previous link (1) <br>";
		return false;
	}
		
	/* Adjust for beginning and end of list */
	// if too small
		if($positiontosearchfor < 1){ $positiontosearchfor = sizeof($products); }
	// if to big
		if($positiontosearchfor > sizeof($products) ){ $positiontosearchfor = 1;}
	/* use xpath to search in XMLObject for the position before */
	if($positiontosearchfor ){
		/* Search for <a><b><c> */
		/* HARDCODED! */
		$result = @$products->xpath('/products/product/position[.='.$positiontosearchfor.']/parent::*');
		if($result[0]) {
	    	/* templatize? */
			print bigcartl_getAFormattedLink($result[0],false,"&laquo; ".$result[0]->name);
	    	return true;
		}else{
			if ($bigcartl->DEBUG )print "bigcartl_previous_post_link: ERROR Getting a previous link  (2) <br>";
		}
	}else{
		if ($bigcartl->DEBUG )print "bigcartl_previous_post_link: ERROR Getting a previous link (3) <br>";
		return false;
	}	
}

/*
 * ADDED 1-27
 * using the current product, get a link to the next product in the xml
 */
function bigcartl_next_post_link(){
	global $bigcartl;
	if ($bigcartl->DEBUG )print "bigcartl_next_post_link: CURRENT product is ". bigcartl_getCurrentProduct()->name."<br>";
 
	$positiontosearchfor = NULL;
	$products = bigcartl_getStoreProducts();
	/* use xpath instead */
	/* HARDCODED! */
	$result = @$products->xpath('/products/product/position[.='. bigcartl_getCurrentProduct()->position[0] .']/parent::*');
	if($result[0]) {
    	$positiontosearchfor = (intval($result[0]->position)+1);
	}else{
		if ($bigcartl->DEBUG )print "bigcartl_next_post_link: ERROR Getting a next link (1) <br>";
		return false;
	}
		
	/* Adjust for beginning and end of list */
	// if too small
		if($positiontosearchfor < 1){ $positiontosearchfor = sizeof($products); }
	// if to big
		if($positiontosearchfor > sizeof($products) ){ $positiontosearchfor = 1;}
	/* use xpath to search in XMLObject for the position before */
	if($positiontosearchfor ){
		/* Search for <a><b><c> */
		/* HARDCODED! */
		$result = @$products->xpath('/products/product/position[.='.$positiontosearchfor.']/parent::*');
		if($result[0]) {
	    	/* templatize? */
			print bigcartl_getAFormattedLink($result[0],false,$result[0]->name." &raquo;");
	    	return true;
		}else{
			if ($bigcartl->DEBUG )print "bigcartl_next_post_link: ERROR Getting a next link  (2) <br>";
		}
	}else{
		if ($bigcartl->DEBUG )print "bigcartl_next_post_link: ERROR Getting a next link (3) <br>";
		return false;
	}		
}
/* 
 * OUTPUT Formaat
	TODO: MOVE 
*/
function bigcartl_getAFormattedProduct($xmlObject){
	$ret = "";
	foreach ($xmlObject as $product){
		$ret .= "<div>
		<p>name ".$product->name[0]. "</p> 
		<p>id ".$product->id[0]. "</p>
		<p>desription ".$product->description[0] ."</p> </div> ";
	}
	return $ret;
}

/* ADDED 1-26-10
finds product in category list */
function bigcartl_isInCategoryFilter($product){
	global $arrcatfilters;

	if(sizeof($arrcatfilters)<1){return true;}
	
	// if it fails any of the tests, return false
	foreach($arrcatfilters as $c){
		//print "looking at Cat: $c for prod ".$product->name[0]."<br>";
		$xmlCategory = bigcartl_getCategoryByName($c);
		if(!bigcartl_ProductIsInCategory($product, $xmlCategory)){ return false; };
	}
	return true;
}	
/* 
 * OUTPUT Formaat LIST Home Page
	TODO: MOVE 
*/
function bigcartl_getFormattedProducts($xmlObject){
	global $bigcartl , $bigcartlTemlpate, $displayClassname;
	
	$ret .= '<div class="bigcartlProdList '.$displayClassname.'" >';
	foreach ($xmlObject as $product){
		if($product->status[0]!="active"){ continue; } 
		/* FILTER ADDED 1-26-10 */
		if(!bigcartl_isInCategoryFilter($product) ){
			if ($bigcartl->DEBUG ) print "SKIPPING ".$product->name[0];
			continue;
		}else{
			//if ($bigcartl->DEBUG ) print "NOTSKIPPING ".$product->name[0];
		}
		/* 
		 * ADDED 2-1-10
		save current list of products */
		$bigcartl->currentProductsAdd($product);
		
	/*
	 * MY LAME-O Template System!
	 */		 	
		$bigcartlTemlpate->setTplFile(BCWP_PLUGIN_DIR."/templates/productList.tpl");
	
	//Set values		
		$values["pName"]	=	$product->name[0];
		$values["pImgUrl"]	=	bigcartl_getProductDefaultImage($product, get_option('bigcartelhomeimagesize') );
		$values["pDivId"]	=	"prod".str_replace(" ","", $product->name[0]);
		$values["pUrl"]		=	bigcartl_getAFormattedLink($product,true);//bigcartl_getPageUrl("product")."?n=".$product->permalink[0];
		$values["pOnSale"]	=	$bigcartl->isOnSale($product)==true ? "Yes":"No";
		$values["pPrice"]	=	$product->price[0];	 
		/* added 2-25-10*/
		$values["pDescription"]	=	 $product->description[0]; 
		/* ADDED 2-15-10*/
		$values["userClass"]=   $displayClassname;
		$values["pCategories"] = bigcartl_displayCategoryList(bigcartl_getCategoriesForProduct($product)) ;
		
		$bigcartlTemlpate->setTplValues($values);
		$bigcartlTemlpate->populateTpl();
		$ret .= $bigcartlTemlpate->getProcessedTpl();
	
	}
	$ret .="</div><!--end bigcartlProdList-->";
	
	return $ret;
}

/* 
 * OUTPUT Formaat
	TODO: MOVE 
 * return HTML String

*/
function bigcartl_getAFormattedProductDetail($product){
	global $bigcartl, $bigcartlTemlpate, $displayClassname;
	$ret = "";
 		
	if($product->status[0]!="active"){
		return ' ITEM IS NOT ACTIVE ';
	}
	
	if($bigcartl->isOnSale($product)){
		$ret .= ' ITEM IS ON SALE ' ;
	}
	
/*
 * MY LAME-O Template System!
 */	
	$bigcartlTemlpate->setTplFile(BCWP_PLUGIN_DIR."/templates/productDetail.tpl"); 		
	
	$values["bcHomePageUrl"]	=	bigcartl_getPageUrl("homepage");
	$values["pName"]	=	 $product->name[0];
	$values["pOnSale"]	=	 $bigcartl->isOnSale($product)==true ? "Yes":"No";
	$values["pPrice"]	=	 $product->price[0];
	$values["pUrl"]		=	 $product->url[0];	
	$values["pId"]		=	 $product->id[0]; 
	/*
	 * I DONT LOVE THIS
	 * ADDED 3/29 to deal with odd characters in XML
	 */
	$values["pDescription"]	=	 ereg_replace("&amp;", "&", $product->description[0])  ; //$product->description[0]; 
	$values["pPosition"]	=	 $product->position[0]; 
	$values["bcCartUrl"]	=	 bigcartl_getCartUrl();
	$values["pOptions"]	=	 bigcartl_getProductOptions($product);
	/* UPDATED 1-22-10 */
	$values["pImages"]	=	 bigcartl_getProductImages($product, get_option('bigcarteldetailimagesize') );//"MEDIUM") ;
	/* ADDED 2-15-10*/
	$values["userClass"]=   $displayClassname;
	/* added 1-23-10 
		TODO: This results in many calls to the server which slows everything down.
		Figure out a way to execute this faster.
		JSON?
		* */
	$values["pCategories"] = bigcartl_displayCategoryList(bigcartl_getCategoriesForProduct($product)) ;
		
	$bigcartlTemlpate->setTplValues($values);
	$bigcartlTemlpate->populateTpl();
	$ret .= $bigcartlTemlpate->getProcessedTpl();
		
	return $ret;
}

/* ADDED 2-1 
 * consolidate URL construction
 * return STRING
*/
function bigcartl_getAFormattedLink($xmlProduct,$bUrlOnly=false,$string=""){
	
	if($bUrlOnly==true){
		return bigcartl_getPageUrl("product")."?n=".$xmlProduct->permalink[0];
	}
	
	if($string!=""){
		$label = $string;
	}else{
		$label = $xmlProduct->name;
	}
	return '<a href="'.bigcartl_getPageUrl("product")."?n=".$xmlProduct->permalink[0].'" title="Link to '.$xmlProduct->name.'" >'.$label.'</a>';
}
/* 
 * get the custom size based on BigCartel sizes
 * return HTML String
*/
function bigcartl_getImageSizeSource($url, $size = "SMALL"){
	global $sizes;
	$suffix = "";
	//  // updated to work with jpg or gif  3-25-10
	/*if(ereg(".jpg", $url)){
		$suffix = ".jpg";	
	}elseif(ereg(".gif", $url)){
		$suffix = ".gif";	
	}else{
		$suffix = ".XXX";	
	} */
	#
	preg_match("/\.([^\.]+)$/", $url, $matches);    
	#
    $suffix = ".".$matches[1];
    
	
	$parts = explode("/",$url);
	array_pop($parts);
	$newurl = implode("/", $parts)."/"; 
	
	if($size == "SMALL"){
		return $newurl.$sizes[0].$suffix;
	}elseif($size == "MEDIUM"){
		return $newurl.$sizes[1].$suffix;	
	}elseif($size == "LARGE"){
		return $newurl.$sizes[2].$suffix;
	}elseif($size == "ORIGINAL"){
		//print "DEBUG URL: $url";
		return $url;
	}else{
		return $newurl.$sizes[0].$suffix;
	}
}

/* 
 * get the a product's image 
 * return HTML String
*/
function bigcartl_getProductDefaultImage($p, $size="SMALL"){
	//print "DEBUG bigcartl_getProductDefaultImage: ".$p->images->image->url[0] ."<br>\n";
	if(count($p->images)>0){
		return bigcartl_getImageSizeSource($p->images->image->url[0], $size) ;
	}else{
		return "no image found"; 
	}
}
/* 
 * get the product's images 
 * return HTML String
*/
function bigcartl_getProductImages($p, $size="SMALL"){
	global $bigcartl_DetailImageEffect;
	$ret = "";
	$rel = "";
	/* ADDED 2-25-10*/
	if($bigcartl_DetailImageEffect == "USELIGHTBOX"){
		$rel = 'rel="lightbox"';
	}elseif($bigcartl_DetailImageEffect == "USEIMAGESWAP" && count($p->images)>1){
		$rel = 'rel="imageswap"';
	}else{
		 
	}
	
	if(count($p->images)>0){
		$ret .= 	'
		<div id="product_thumbnails"> ';
		 
		/* UPDATED 1-22-10 */
		$count = 0;
		if(sizeof($p->images->image) == 1){
			$count = 99;
		}
		foreach($p->images->image as $o){
			//print "DEBUG bigcartl_getProductImages: ".$o->url ."<br>\n\n";
			$ret .= '
			<div class="featuredimg'.$count.'"><a href="' .
			$o->url .'" class="thumb" '.$rel.' title="Product Detail"><img src="'	.
			bigcartl_getImageSizeSource($o->url, $size) .'" alt="Image of '	.
			$p->name[0] .'" /><span class="stilt"></span></a></div>
			';
			$count++;
		}
		$ret .= ' </div> 
		';
	}
	return $ret;
}

/* 
 * get the product's options 
 * return HTML String
*/
function bigcartl_getProductOptions($p){

	$ret = "";
	if(count($p->options->option) == 1){
		$ret .= '<input type= "hidden" id="option" value="'.$p->options->option[0]->id.'" name="cart[add][id]">';
	}elseif(count($p->options->option) > 1){
		$ret .= 	'<select id="option" name="cart[add][id]">';
			//OPTIONS 			
			foreach($p->options->option as $o){
				$ret .= '<option value="'.$o->id.'">'.$o->name.'</option>';
			}
		$ret .= 	'</select> ';
	}
	return $ret;
}

/* 
  NOT currently in USE
 * */
function bigcartl_getShowCart(){
	global $bigcartl;
	$cart = $bigcartl->loadCart( bigcartl_getCartDataUrl() );
	return $cart;
}	
 
function bigcartl_getPageUrl($p = "product"){
	global $bigcartl; 
	
	if($p == "homepage"){
		return get_bloginfo('wpurl')."/".$bigcartl->getLocalPage('homepage');
		
	}else{
		return get_bloginfo('wpurl')."/".$bigcartl->getLocalPage('product');
	}
}

function bigcartl_chkPages(){
	global $bigcartl;
	
	foreach($bigcartl->pages as $i=>$p){
		if($p == ""){
			$bigcartl->wpc_exitGracefully(array(
				"msg"=>"<h3 class=\"error\">Error: Page $i is null </h3><p> </p><p> </p><p> </p>"
				//,"exeunt"=>"false"
				)
			);		
		}
	}
}

function bigcartl_getPageSlug($aproduct_pagename){
	global $bigcartl;
	return $bigcartl->getLocalPage($aproduct_pagename);
}

function bigcartl_chkBase(){
	global $bigcartl;
	if( trim(get_option('basebigcartelurl')) == "" ){
		$bigcartl->wpc_exitGracefully(array(
				"msg"=>"<h2 class=\"error\">Fatal Error: 'base url' (basebigcartelurl) is null </h2><p> </p><p> </p><p> </p>")
		);
	}
	return true;
}
/* UPDATED 1-24-10 to use new User Option for Css: File or Form data */
function bigcartl_styles() {
     
    /* What Option did the user choose: */
	$cssopt = get_option('cssoption') ;
	/* chose a file */
	
	print '	<!-- begin bigcartl css -->';
	
	if($cssopt=="filecss"){
		// open file
		print '
	<!-- filecss--> <link rel="stylesheet" href="'. get_bloginfo('stylesheet_directory').'/'.get_option(bigCartelPluginCssFile).'" type="text/css" media="screen" />
	';
	 		
	}elseif ($cssopt=="formcss"){
	/* chose to use css in the form */
		$bccss = '
		<style type="text/css" media="screen"> 
		'.  	get_option('bigCartelPluginCss') .	'
		</style>
	';
	
		/* Output $galleryscript as text for our web pages: */
		echo($bccss);
	
	}

	/* lightbox(used on Product Page) css */
	echo '<!-- end bigcartl lightbox -->
		<link rel="stylesheet" href="'.
		    ereg_replace( bigcartl_getCurrentDirectory() ,'plugins/'.dirname(BCWP_PLUGIN_BASENAME),get_bloginfo('template_directory' ))
		    .'/lightbox.css" type="text/css" media="screen" />
		<!-- end bigcartl lightbox -->';
	
		    print '<!-- begin bigcartl css -->';
		    

}

function bigcartl_getCurrentDirectory(){

	$template_directory = explode("/", get_bloginfo('template_directory'));
	$td1 = array_pop($template_directory);
	$td2 = array_pop($template_directory);
	$repl = $td2."/".$td1;
	return 	$repl;
}

function bigcartl_js(){
	// get string to clean
	 
	echo '
	<!-- begin bigcartl js --> 
		<script src="'.ereg_replace( bigcartl_getCurrentDirectory(), 'plugins/'.dirname(BCWP_PLUGIN_BASENAME),get_bloginfo('template_directory' )).'/js/lightbox.js"></script>
		<script type="text/javascript" src="'.ereg_replace( bigcartl_getCurrentDirectory(), 'plugins/'.dirname(BCWP_PLUGIN_BASENAME),get_bloginfo('template_directory' )).'/js/detailImageSwap.js"></script>
	<!-- end bigcartl js --> 
	';
}

/* ADDED 1-26-10
 * allow user to filter items by category 
	usage: bigcartl_setCategoryFilter("shirt,pants");
	also in shortcode: 
	 [bigcartl show='home' categories='cats,dogs'/] 
*/
function bigcartl_setCategoryFilter($catnames){
	global $arrcatfilters;
	$arrcatfilters = explode(",",$catnames);
}
/* */
function bigcartl_setClassname($name){
	global $displayClassname;
	$displayClassname = $name;
}

/* */
function bigcartl_testConnection(){
	global $bigcartl;
	bigcartl_chkBase();
	bigcartl_chkPages();
	return $bigcartl->testConnection();
}


function bigcartl_dieGracefully($str){
	global $bigcartl;
	$bigcartl->wpc_exitGracefully(array(
			"msg"=>"<h2 class=\"error\">Fatal Error: $str </h2><p> </p><p> </p><p> </p>")
	);		
}

add_action('wp_head', 'bigcartl_styles');
add_action('wp_head', 'bigcartl_js');
add_action('admin_menu', 'bigcartl_options_page');

/*
 * 
 * SimpleXMLElement Object ( 
 * 	[price] => 10.99 
 * 	[images] => SimpleXMLElement Object ( 
 * 		[@attributes] => Array (  [type] => array ) 
 * 		[image] => SimpleXMLElement Object ( 
 * 		[width] => 536 [height] => 471 
 * 		[url] => http://cache0.bigcartel.com/product_images/3513188/darwin08shirt.jpg ) ) 
 * 		[status] => active 
 * 		[permalink] => shirt 
 * 		[tax] => 0.0 
 * 		[name] => Shirt 
 * 		[on-sale] => true 
 * 		[url] => /product/shirt 
 * 		[id] => 425716 
 * 		[description] => There are details in here. 
 * 		[options] => SimpleXMLElement Object ( 
 * 			[@attributes] => Array ( 
 * 			[type] => array ) 
 * 			[option] => Array ( 
 * 			[0] => SimpleXMLElement Object ( 
 * 			[name] => Red 
 * 			[id] => 1691436 ) 
 * 			[1] => SimpleXMLElement Object ( 
 * 			[name] => Black 
 * 			[id] => 1691438 ) ) ) 
 * 			[position] => 1 )
 * 
 */?>

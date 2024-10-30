<?php
/*
Class Name: BigCartel

Description: This Class holds key information & data pulled from BigCartel and provides a abstraction layer for getting the data

Version:  .010

Author: Lucas Monaco
 
*/
require(BCWP_PLUGIN_DIR."/classes/wpClass/wpClass.class.php");
class BigCartel extends wpClass{
	
	// store the BigCartel url Pts 1 & 2
	var $BASEURL ;
	var $BASEURL2 ;
	//URL details
	var $CART ;
	//URL details
	var $PRODUCT ;
	//URL details
	var $SUFF ;
	// Add on for the BigCartel store URL
	var $STORESUFF ;
	// Add on for the BigCartel product URL
	var $PRODUCTSSUFF ;
	// Add on for the BigCartel cart URL
	var $CARTSUFF = "/cart.xml";
	
	// Store BigCartel store data
	var $storeData = NULL;
	// Store BigCartel product data
	var $productsData = NULL;
	var $categoriesData = NULL;
	var $currentproductdata = NULL;
	var $currentproducts = NULL;
	var $pages = array();
	/*
	 * Constructor
	 */
	function BigCartel(){
	}
	
	/*
	 * returns all store data
	 */
	function getStore(){
		return $this->storeData;
	}
	
	/*
	 * returns all product data
	UPDATED 1-24-10
	 */
	function getProducts(){
		if(!$this->productsData){  
			if ($this->DEBUG ) print " -- IN getProducts ( loading ) <br />" ;
			$this->loadStore( bigcartl_getStoreUrl() );
		} 
		return $this->productsData;
	}	
	
	function getCurrentProduct(){
		return $this->currentproductdata;
	}
	/* ADDED 2-1 
	Poplate the list of currently displayed items;
	*/
	function currentProductsAdd($product){
		if($this->currentproducts == NULL){
			$this->currentproducts = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><products type="array"></products>');	
		}
			$this->AddXMLElement($this->currentproducts, $product);
		//print "currentProductsAdd: ".sizeOf($this->currentproducts)."<br>\n";
	}
	
	/* ADDED 2-1 
		add one SImpleXmlObjec to ANother;
	*/
	function AddXMLElement(SimpleXMLElement $dest, SimpleXMLElement $source)
    {
        $new_dest = $dest->addChild($source->getName(), $source[0]);
        foreach ($source->children() as $child)
        {
            $this->AddXMLElement($new_dest, $child);
        }
    }
    
/*
	* returns all store categories
	 UPDATED 1-24-10
	*/
	function getCategories(){
		if(!$this->categoriesData){  
			$this->loadStore( bigcartl_getStoreUrl() );
		} 
		return $this->categoriesData->category;
		
	}

	/*
	 * returns  	
	*/
	function getBCPages(){
		return $this->storeData->pages;
	}	
	function getLocalPage($nm){ 
		return $this->pages[$nm];
	}
	function getName(){
		return $this->storeData->name;
	}	
	function getCurrency(){
		return $this->storeData->currency;
	}		
	function getCountry(){
		return $this->storeData->country;
	}	
	function getWebsite(){
		return $this->storeData->website;
	}		
	function getUrl(){
		return $this->storeData->url;
	}		
	function getProductsCount(){
		$val = "products-count";
		return $this->storeData->$val;
	}	

/* USED primarily on the Options page 
UPDATED 1-24-10
*/
	function testConnection(){
		$s = bigcartl_getStoreUrl();
	 	$p = bigcartl_getProductsUrl(); 
		
	 	print "<p>Store data url is <a href='".$s."'>". $s."</a></p>";
	 	print "<p>Products data url is <a href='".$p."'>". $p."</a></p>";
	 	 
	 	
	 	$this->loadStore( bigcartl_getStoreUrl() );
		 
	 	$name = $this->getName();
	 	if(!$name){
	 		$this->wpc_exitGracefully(array("msg"=>"<h2>There was an error connecting to your account. Make sure it is valid </h2>"));
	 	}else{
	 		print "<p>Store Name is <b>". $this->getName()."</b></p>";
			print "<p>Currency is <b>". $this->getCurrency()."</b></p>";;
			print "<p>Country  is <b>". $this->getCountry()."</b></p>";;
			print "<p>Main Website  is <b>". $this->getWebsite()."</b></p>";;
			print "<p>Store URL is <b>". $this->getUrl()."</b></p>";;
			print "<p>Products Count is <b>". $this->getProductsCount()."</b></p>";;
			print "<p>Categories are <br>"; 
			foreach($this->getCategories() as $i=>$cat){
			
				print "NAME: ".$cat->name."<br>";
				print "URL: ".bigcartl_getCategoryUrl($cat)."<br>";
				print "PERMALINK: ".$cat->permalink."<br>";
				print "ID: ".$cat->id."<br>"; 
				print "<br>";	
			}
			print "</p>";;
			 
			return true;
	 	}
			return false;
		
	}
/*
 *  TODO : NEED?
	
	function getCurrency(){
		return $this->storeData->currency;
	}		
	function getCountry(){
		return $this->storeData->country;
	}	
		
	function getWebsite(){
		return $this->storeData->website;
	}		
	function getUrl(){
		return $this->storeData->url;
	}		
	function getProductsCount(){
		$val = "products-count";
		return $this->storeData->$val;
	}	
*/	
	/* 
	return the 1st object found that satisfies [FALSE]
	requires
		valid xml fieldname($field)
		value($mixed)
	* 
	* */
	function getAProduct($field, $mixed){
		// search thru prods
		return $this->getAProductByFieldValue($field, $mixed);
	}
	
	/* 
	returns the  [FALSE]
	requires
		valid xml fieldname($field)
		value($mixed)
	* 
	* */
	function getAProductByFieldValue($field, $mixed){
		if ($this->DEBUG ) print "Searching in proddata for $field/$mixed <br>";
		foreach($this->productsData as $product){
			if ($this->DEBUG ) print_r($product->$field);
			if ($this->DEBUG ) print "<br>";
			if($product->$field == $mixed){
				if ($this->DEBUG ) print "Product Found <br>";
				if ($this->DEBUG ) print_r($product);
				return $product;
			}else{
				if ($this->DEBUG ) print $product->$field[0] ;
				if ($this->DEBUG ) print " doesnt equal $mixed <br>";
			}
		}
		return false;
	}

//////////////////////////////////////////////////////////	
	/* Start LOADers Area */
	function loadStore($url){ 
		if(!$this->storeData){
			if ($this->DEBUG ) print " -- IN loadStore ( loading ) <br />" ;
			$this->storeData = $this->util_loadData($url);  
		}
		
		if(!$this->storeData){ 
			print "<h2 class='error'>WPBC Fatal Error 3 : Cant load store data </h2>  ";
			return false;
		}
		/* UPDATED 1-24-10 */
		$this->categoriesData = $this->storeData->categories ;
		//
	}
	
	function loadProducts($url){
		if(!$this->productsData){
			if ($this->DEBUG ) print " -- IN loadProducts ( loading ) <br />" ;
			$this->productsData = $this->util_loadData($url);
		}
	}
	
	function loadAProduct($url){
		if ($this->DEBUG ) print " -- IN loadAProduct --- <br />" ;
		$d = $this->queryCacheGetData($url,"products");
		if($d){
			if ($this->DEBUG ) print " loadAProduct: ( using querycache ) <br />" ;
			return $d; //use $d !
		}else{
			if ($this->DEBUG ) print " loadAProduct: ( loading from url: $url ) <br />" ;
			$d = $this->util_loadData($url); //request fresh data 
			$this->queryCacheSetData($url, $d, "products");
			return $d;
		} 
		return $d;
	}
	
	function loadACategory($url){
		$d = $this->queryCacheGetData($url,"categories");
		if($d){
			if ($this->DEBUG ) print "loadACategory: using already loaded data for ( $url ) <br />" ;
			return $d; //use $d !
		}else{
			$d = $this->util_loadData($url); //request fresh data 
			$this->queryCacheSetData($url, $d, "categories");
			if ($this->DEBUG ) print "loadACategory: using NEW! data for ( $url ) <br />" ;
			return $d;
		}
		//$d = $this->util_loadData($url);
		//return $d;
	}
	
	function loadCart($url){
		$d = $this->util_loadData($url);
		return $d;
	}
	
	/*function loadCategories($url){
		$this->categoriesData = $this->util_loadData($url);
	}*/
	/* End LOADers Area */
	
//////////////////////////////////////////////////////////	
	/*
	 * GET DATA FROM BIGCARTEL
	 * USE CURL, or can be updated to use file_get_contents ( on some servers )
	 */ 
	function util_loadData($url){
		$content = "";
		$errype = "";
		
		// make sure curl is installed 
		if ($this->DEBUG ) print " -- IN util_loadData ( $url ) <br />" ;
		if (function_exists('curl_init') ) {
			// initialize a new curl resource
			$ch = @curl_init();
			
			// set the url to fetch
			@curl_setopt($ch, CURLOPT_URL, $url);
			
			// don't give me the headers just the content
			@curl_setopt($ch, CURLOPT_HEADER, 0);
			
			// return the value instead of printing the response to browser
			@curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			// use a user agent to mimic a browser
			@curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
			
			$content = curl_exec($ch);
			// remember to always close the session and free all resources
			@curl_close($ch);
			if($content ==""){ $errype="CURL ERROR";}
		
		}elseif (function_exists('file_get_contents') ) {
			// un '@' this to see what happenes with 404's etc 
			$content = @file_get_contents($url);
			if($content ==""){ $errype="F_G_C ERROR";}
		} else {
			$this->wpc_exitGracefully(array(
				"msg"=>"<h2 class='error'>WPBC Fatal Error 1 : <a href=\"http://php.net/manual/en/book.curl.php\">CURL library</a> is not installed</h2> Please Install and then try again !!<p> </p><p> </p><p> </p>")
			);
			$errype = " NO CURL/F_G_C";
			
		} 
		if($content != ""){
			/* 
			ATTEMPT TO ESCAPE BAD CHARACTERS: 	3-25-10 
			TODO Find a better solution
			*/
			$content = ereg_replace("&", htmlspecialchars("&"), $content);    
			 
			if (function_exists('simplexml_load_string') ) {
				if($result = @simplexml_load_string($content) ){
					return $result;
				}else{
					$this->wpc_exitGracefully(array(
					"msg"=>"<h2 class='error'>WPBC Fatal Error 3.5 : <p>There was a problem requesting the info for a product named '".$_GET['n']."' </p><p> </p><p> </p> ")
				);
				}
			}else{
				$this->wpc_exitGracefully(array(
					"msg"=>"<h2 class='error'>WPBC Fatal Error 4 : <a href=\"http://php.net/manual/en/function.simplexml-load-string.php\">simplexml_load_string function </a> is not installed</h2> Please Install and then try again !!<p> </p><p> </p><p> </p> ")
				);
			}
		}else{
			print "<h2 class='error'>WPBC Fatal Error 2 :  Error. No Content for this url: $url</h2> Please try again !!
			<p> $errype </p>
			<p> </p>
			<p> </p> ";
			return false;
		}
	}
	
/*
 * TODO: Return true or false base on Product's "on-sale" value
 * 
 */
	function isOnSale($p){
		//	$onsale = "on-sale";
		//	print "sale: ";
		//	print_r($p->)."<br>";
		//	if($p->$onsale[0]=="false"){
				return false; 
		//	}
		//		return true;
	}

} // end of class
?>
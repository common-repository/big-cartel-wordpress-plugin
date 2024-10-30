<?php
/* 
 * Short Code Handler for BigCartel Shop Plugin 

	USAGE:
	bigcartl_shortcode_handler($atts, $content=null, $code="") {
	atts[] can be 
		show can be
			home|product|cart

* */
function bigcartl_shortcode_handler($atts, $content=null, $code="") {
   global $post; 
   // $atts    ::= array of attributes
   // $content ::= text within enclosing form of shortcode element
   // $code    ::= the shortcode found, when == callback name
   // examples: [my-shortcode]
   //           [my-shortcode/]
   //           [my-shortcode show='home']
   //           [my-shortcode foo='bar'/]
   //           [my-shortcode]content[/my-shortcode]
   //           [my-shortcode foo='bar']content[/my-shortcode]
   
	extract( shortcode_atts( array(
      'attr_1' => 'attribute 1 default',
      'attr_2' => 'attribute 2 default',
      // ...etc
      ), $atts ) );
	$ret = "";
	$catnames = "";
	$style = "style='border:2px #FFCCCC solid;'";
	
	/*
	 * ADDED 1-26-10
	 */
	
	if(isset($atts['categories'])){
		$catnames = $atts['categories'];
		bigcartl_setCategoryFilter(trim($catnames));
	}
	
	/*
	 * ADDED 2-15-10
	 */
	if(isset($atts['classname'])){
		bigcartl_setClassname(trim($atts['classname']));
	}else{
		bigcartl_setClassname(trim($atts['show']));
	}
	
	if(isset($atts['show'])){
    	if($atts['show']== "home"){ 
    		//print "showing all";
    		$ret = bigcartl_getFormattedProducts(bigcartl_getStoreProducts());
    		
  	  	}else if($atts['show']== "aproduct"){
  	  		// example url /product/?n=Shirt
  	  		// example: http://MYSTORENAME.bigcartel.com/product/shirt.xml
    		// $ret = "showing a product: ".$_SERVER['REQUEST_URI'];
  	  		$s = split("\?", $_SERVER['REQUEST_URI'] );
  	  		$ret .= bigcartl_getAFormattedProductDetail( bigcartl_getASingleProduct( $s[1] ) );

  	  	}else if($atts['show'] == "cart"){
  	  		
  	  		// NOT WORKING 
  	  		//print_r( bigcartl_getShowCart() );
  	  		$ret = "<div $style>Sorry but the BigCartel Wordpress Plugin isn't handling Cart Requests at this time</div>";
  	  		
  	  	}else{
  	  		
  	  		$ret = "<div $style>Sorry but the BigCartel Wordpress Plugin doesnt know how to handle a request of type:";
  	  		$ret .= $atts['show']."<br />";
  	  		$ret .= "Full Debug Output: ";
  	  		$ret .= "<br />Atts: ".implode("<br />", $atts);
  	  		if($content!=null){$ret .= "<br />Content: ".implode("<br />", $content); }
  	  		if(trim($code)!=""){$ret .= "<br />Code:  $code"; }
  	  		$ret .= "</div>";
  	  		
  	  	}    
    }     
	return $ret; 
}
add_shortcode('bigcartl', 'bigcartl_shortcode_handler');
?>
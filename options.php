<?php
/*
  Big Cartel Wordpress Plugin
  Lucas Monaco
*/
/*
 * 1-23-10
	ADDED ability to switch image sizes on Home & Product Detail pages
 * 1-24-10
 * 	ADDED Option to indicate own CSS file instead of  CSS in Form
 * 1-26-10
 * 	ADDED additional Image size option: Original
 * 	ADDED additional checking for css option to default to form 
 *  *  */
$location = $options_page; // Form Action URI

//load up app default front end CSS
$defaultcss = file_get_contents(BCWP_PLUGIN_DIR."/templates/bigcartel.css");
//load up app default admin CSS
$optionspagecss = file_get_contents(BCWP_PLUGIN_DIR."/options.css");

print $optionspagecss;

// Establish a Default
$thecss = $defaultcss;
// Gather the user's CSS, if any
$custcss = get_option('bigCartelPluginCss');

// IF the user's CSS is not the same as default and is not blank
// use the user's
if ( ($custcss != $defaultcss) && ($custcss != "")){
	$thecss = $custcss;
}
?>
<!-- added 1-23-10 -->
<script type="text/javascript">
<!--
// Show and Hide the areas of the form based on User Choice
  function showFileCss(){
 	 document.getElementById('bigCartelPluginCss').disabled=true;
 	 document.getElementById('bigCartelPluginCssFile').disabled=false;
  }
  function showFormCss(){
 	 document.getElementById('bigCartelPluginCss').disabled=false;
 	 document.getElementById('bigCartelPluginCssFile').disabled=true; 
  }
  function formSubmit(elm){
  	// a little form validation
	if(document.getElementById('filecss').checked && document.getElementById('bigCartelPluginCssFile').value==''){
  		alert('If you want to use your own file, you need to enter a file name. '); 
  		document.getElementById('bigCartelPluginCssFile').style.borderColor='#FF0000';
  	}else{
  		elm.submit();
  	}
  }
//-->
</script>

	<div class="wrap">
		<div style="display: block; float:left;text-align: left;">
			<h2>Big Cartel Plugin Configuration - version <?php print BCWP_PLUGIN_VERSION ?></h2>
	  		<h3>Don't have  Big Cartel Account? </h3> <a href="http://www.bigcartel.com/" target="_new">Get One</a> its free! 
		</div>
		<!-- ADDED Donate Button --> 
		<div style="margin-top:65px;margin-left:100px;display: block; float:left;text-align: left;">
			<a target="_blank" rel="nofollow" imageanchor="1" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=4999Q73S39RPW&lc=US&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">
			<img border="0" src="https://www.paypal.com/en_US/i/btn/btn_donateCC_LG.gif" style="width: 122px; height: 47px;"/>
			</a>
		</div>
		<div class="clearer"></div>

		<div id="HowTo">
	  	<h2>How To Get Started</h2>
	  	<ol>
	  		<li>Fill out your BigCartel data below</li>
	  		<li>Click Save ( 'update options' button below )</li>
	  		<li>Confirm Your Data <a href="#proof">Below</a></li>
	  		<li>Create a Home Page for your store (ex: a Page called "home-page").<br />
	  		Put this code here 
	  		<pre> 	 [bigcartl show='home'/] </pre> to show the Big Cartel home page, which displays all the products by default
	  		<br />Or try <pre> [bigcartl show='home' categories='cats'/]  </pre> if you want to only show the 'cats' category of products
	  		</li>
	  		<li>Create a Product Page for your store (ex: a Page called "product").<br />
	  		Put this code here
	  		<pre> 	 [bigcartl show='aproduct'/] </pre> to show a product linked from the home page list
			</li> 
	  	</ol> 
	 </div>
	 
	 <h2>Fill Out These Options</h2> 
		 <form method="post" action="options.php" id="optionsform"><?php wp_nonce_field('update-options'); ?>
			<fieldset name="general_options" class="wpbc_options">
		       	<div>
		       		<b>Base Url</b><br/>
		       		<input type="text" name="basebigcartelurl" value="<?php echo get_option('basebigcartelurl'); ?>" /> 
		       		(the subdomian of bigcartel.com on Big Cartel eg: mystore.bigcartel.com)
		       	</div>
		       	<div id="wpbc_options_css">
					<p><b>CSS: </b>(optional) </p>
				      	<?php 
					// ADDED 1-24-10. Allow user to chose to use in-form css or own file
					$cssopt = get_option('cssoption');    ?> 	
				      <div>
				       		<input type="radio" <?php if($cssopt=="formcss" || $cssopt=="" || !isset($cssopt) || !$cssopt ){?> checked <?php }?> onclick="showFormCss()" name="cssoption" value="formcss" id="formcss"/> Use This CSS
					       	<input type="radio" <?php if($cssopt=="filecss"){?> checked <?php }?> onclick="showFileCss()" name="cssoption" value="filecss" id="filecss"/> Use Your Own CSS File
					       	</div>
				       	
		       		<div id="wpbc_options_css_form">
				       	<?php if($thecss == $defaultcss && $cssopt=="formcss"){ ?>
				       		  <p><i>Using pre=loaded CSS </i> </p>
				       	<?php } ?>
				       	<textarea name="bigCartelPluginCss" id="bigCartelPluginCss"><?php print $thecss  ?> </textarea>
				      	<div class="clearer"></div>
			      	</div>
			      	
			       	<div id="wpbc_options_css_file">
			       		 File Location: <?php print bloginfo('stylesheet_directory'); ?>/<input type="text" value="<?php echo get_option('bigCartelPluginCssFile')?>" name="bigCartelPluginCssFile" id="bigCartelPluginCssFile" />
			       	</div>
					<div class="clearer"></div>
	       	  	</div>
	
				<div>
		       		<br/> <b>User defined Urls</b> REQUIRED!! <br />
		       		<b>Products Home Page</b> 
		       		<input type="text" name="bigcartelstorehomepage" value="<?php echo get_option('bigcartelstorehomepage'); ?>" /><br>
		       		(slug of an actual page you've created in Wordpress eg: "homepage" )<br />
		       		<b>Product Page</b> 
		       		<input type="text" name="bigcartelproductpage" value="<?php echo get_option('bigcartelproductpage'); ?>" /><br>
		       		(slug of an actual page you've created in Wordpress eg: "product" )
		       		<br/><br/>
		       	</div>
				<!-- ADDED ability to switch image sizes on Home & Product Detail pages -->
			  <div><b>Image Sizes to Use:</b> (optional) <br/>
					<?php $s = get_option('bigcarteldetailimagesize'); ?> 
					<ul>
					<li>
						Product Detail Page  
						<select name="bigcarteldetailimagesize">
							<option value="SMALL" <?php if($s == "SMALL"){?> selected <?php }?>> SMALL </option>
							<option value="MEDIUM" <?php if($s == "MEDIUM"){?> selected <?php }?>> MEDIUM </option>
							<option value="LARGE" <?php if($s == "LARGE"){?> selected <?php }?>> LARGE </option>
							<option value="ORIGINAL" <?php if($s == "ORIGINAL"){?> selected <?php }?>> ORIGINAL </option>
						</select>
					</li>
					<?php $s3 = get_option('bigcarteldetailimageeffect'); ?> 
					<li> Product Detail JS Image Effect
						<select name="bigcarteldetailimageeffect">
							<option value="NONE" <?php if($s3 == "NONE"){?> selected <?php }?>> NONE </option>
							<option value="USEIMAGESWAP" <?php if($s3 == "USEIMAGESWAP"){?> selected <?php }?>> USEIMAGESWAP </option>
							<option value="USELIGHTBOX" <?php if($s3 == "USELIGHTBOX"){?> selected <?php }?>> USELIGHTBOX </option>
							 
						</select>
					</li>
					<?php $s2 = get_option('bigcartelhomeimagesize'); ?> 
					<li>
						Home/Store Page  
						<select name="bigcartelhomeimagesize">
							<option value="SMALL" <?php if($s2 == "SMALL"){?> selected <?php }?>> SMALL </option>
							<option value="MEDIUM" <?php if($s2 == "MEDIUM"){?> selected <?php }?>> MEDIUM </option>
							<option value="LARGE" <?php if($s2 == "LARGE"){?> selected <?php }?>> LARGE </option>
							<option value="ORIGINAL" <?php if($s2 == "ORIGINAL"){?> selected <?php }?>> ORIGINAL </option>
						</select>
					</li>
				</ul>
						 
				</div>

		       	<input type="hidden" name="action" value="update" />
		        <input type="hidden" name="page_options" value="basebigcartelurl,bigCartelPluginCss,bigcartelproductpage,bigcartelstorehomepage,bigcarteldetailimagesize,bigcartelhomeimagesize,cssoption,bigCartelPluginCssFile,bigcarteldetailimageeffect" />
		
			</fieldset>
			<p class="submit"><input type="button" onclick="formSubmit(this.form)" id="submitter" name="Submit" value="<?php _e('Update Options') ?>" /></p>
		 </form>      

	  
     
      <h2>Proof it's working:</h2>
      <div id="proof"><a name="proof">&nbsp;</a>
		
	 	<?php  if( bigcartl_testConnection() ){ ?>
	 		<p id="success"> No errors above? Then you're Good to Go! <a href="<?php print bigcartl_getPageUrl("homepage") ?>">Store Home</a> </p>
	 	<?php  }else{ ?>
	 		You've Got Errors.
	 	<?php  } ?>
	 	<br/>&nbsp;<br/>
	 </div>
	 	<br/>&nbsp;<br/>
	     
<script type="text/javascript">
<?php //show/hide form areas based on user choices
	if($cssopt=="formcss"){?>  
	showFormCss();
<?php 
	}elseif($cssopt=="filecss"){?>  
	showFileCss();
<?php }?>
</script>	

</div>
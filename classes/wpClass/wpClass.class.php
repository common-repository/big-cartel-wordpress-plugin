<?php
/*
Class Name: Generic WordPress Class

Description: This Class 

Version:  .001

Author: Lucas Monaco
 
*/ 

class wpClass{
	
	// for developing etc. will affect output 
	var $DEBUG = false; 
	var $cacheObject = array();
	
	function wpClass(){
		
	}
	
	/* enable caching of data by named key & collection */
	function queryCacheSetData($key, $data, $collectionName){
		$key = urlencode($key);
		
		if( !$this->cacheObject[$collectionName] ){
			$this->cacheObject[$collectionName] = array("A");
			if ($this->DEBUG ){
				print "queryCacheSetData done: creating new Collection $collectionName :<br><pre>";
				//print_r($this->cacheObject);
				print "</pre><br>";
			}
		}
		if ($this->DEBUG ) print "queryCacheSetData adding to Collection $collectionName / $key <br>";
		
		$this->cacheObject[$collectionName][$key] = $data;
		if ($this->DEBUG ){
			print "queryCacheSetData done: setting data for $key :<br><pre>";
			//print_r($this->cacheObject[$collectionName]);
			print "</pre><br>";
		}
		return;
	}
	/* look for existing data of same name
	returns data or false
	usage
	$d = queryCacheGetData()
	if($d){
		use $d !
	}else{
		$d = request fresh data 
		queryCache($key, $d, $collectionName)
	}
	*/
	function queryCacheGetData($key, $collectionName){
		
		$key = urlencode($key);
		
		if($this->cacheObject[$collectionName][$key]){
			return $this->cacheObject[$collectionName][$key];
		}else{
			if($this->cacheObject[$collectionName]){
				if($this->cacheObject[$collectionName][$key]){
					return $this->cacheObject[$collectionName][$key]; 				
				}else{
					if ($this->DEBUG ){ print "queryCacheGetData: dont see $key in <pre>";
						//print_r($this->cacheObject[$collectionName]);
						print "</pre><br>";
					}
					return false;
				}
			}else{
				if ($this->DEBUG ){ print "queryCacheGetData: dont see $collectionName in <pre>";
					//print_r($this->cacheObject);
					print "</pre><br>";
				}			
				return false;
			}
		}
	}
	
	function wpc_exitGracefully($args = null){
		
		//print "wpc_exitGracefully: ".;
		print $args["msg"] ;
		
		
		if($args["exeunt"] && $args["exeunt"]=='false'){  
			return true;
		}else{
			print "Exiting Now.";
			exit;
		}
	}
}

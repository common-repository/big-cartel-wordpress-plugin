<?php 

class TemplateClass{
	
	var $tpl;
	var $tplcontents;
	var $tplcontents_processed;
	var $values;
	
	function TemplateClass(){
 	
	}
	
	function setTplFile($filename){
		if(file_exists($filename)){
			$this->tpl = $filename;
			$this->tplcontents = $this->getContents($this->tpl);
		}else{
			
			print get_class(). " file $filename doesn't' exist ";
			return false;
		}
	}
	
	function setTplValues($values){
		$this->values = $values;
	}
	
	function populateTpl(){
		
		$this->tplcontents_processed = $this->tplcontents;
		
		foreach($this->values as $key=>$val){
			$this->tplcontents_processed = ereg_replace("##$key", "$val", $this->tplcontents_processed);
			//print "REPLACING ##$key with $val <br>";
		}
		//print "<textarea>$this->tplcontents_processed</textarea>";
	}
	
	function getProcessedTpl(){
		return $this->tplcontents_processed;
	}
	
	function getContents($filename){
		if (function_exists('file_get_contents') ) {
				// un '@' this to see what happenes with 404's etc 
				return @file_get_contents($filename);
		}else{
				print get_class(). " Required function 'file_get_contents' doesn't exist ";
				exit;
		}
	}
}

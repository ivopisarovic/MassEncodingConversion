<?php 
/*
* MassEncodingConversion, v1.0
* (c)2014 Ivo Pisařovic, http://pisarovic.cz
*
* Converts all text files to UTF-8.
* http://github.com/fajnweb/MassEncodingConversion
*
* **!** It is strongly recommended to **backup** all files before running this script. Sometimes it deleted the whole file if a very special character is found. 
* **!** All files must be either in the entered source encoding or in UTF-8, **not mixed up** with other encodings. 
* **!** If you have too many files, *max_execution_time* can be reached. Try running it again, it should be faster. 
*
*/

class MassEncodingConversion{
	
	private $MULTIBYTE_FUNCTIONS=array(
		"mail("=>"mb_send_mail(",
		"strlen("=>"mb_strlen(",
		"strpos("=>"mb_strpos(",
		"strrpos("=>"mb_strrpos(",
		"substr("=>"mb_substr(",
		"strtolower("=>"mb_strtolower(",
		"strtoupper("=>"mb_strtoupper(",
		"stripos("=>"mb_stripos(",
		"strripos("=>"mb_strripos(",
		"strstr("=>"mb_strstr(",
		"stristr("=>"mb_stristr(",
		"strrchr("=>"mb_strrchr(",
		"substr_count("=>"mb_substr_count(",
		"ereg("=>"mb_ereg(",
		"eregi("=>"mb_eregi(",
		"ereg_replace("=>"mb_ereg_replace(",
		"eregi_replace("=>"mb_eregi_replace(",
		"split("=>"mb_split("
	);
	
	private $PROBLEMATIC=array(
		"strtr"=>"do not use a string as 'from' parameter, use an array of pairs instead", 
		"set names"=>"warning: check name settings in db connection",
		"iconv"=>"check iconv()",
		$this->sourceEncoding=>"source encoding name ".$this->sourceEncoding." occured, check it",
		$this->targetEncoding=>"target encoding name ".$this->targetEncoding." occured, check it"
	);
	
	private 
		$sourceEncoding,
		$targetEncoding='UTF-8',
		$exclude=array(); 
	
	public function __construct($sourceEncoding){
		$this->sourceEncoding=$sourceEncoding;
	}
	
	public function setExcluded($files){
		foreach($files as $file){
			$this->exclude[]=realpath($file);
		}
	}
	
	public function run($defaultDir="."){
		$this->scan(realpath($defaultDir));
	}	
	
	private function isTextFile($path){
		$mime=mime_content_type($path);
		$mime=explode("/",$mime);
		return $mime[0]=="text";
	}
	
	private function isEncoded($text){
		return mb_detect_encoding($text, $this->targetEncoding, true);
	}
	
	/// Detects if code contains some functions that have problems with UTF-8
	private function detectProblems($text){
		$pattern="/";
		foreach($this->PROBLEMATIC as $k=>$v){
			$pattern.="(".$k.")|";
		}
		$pattern=substr($pattern,0,-1)."/i";
		preg_match_all($pattern,$text,$matches);
		$problems="";
		foreach($matches as $m){
			foreach($m as $v){
				if($v!="")
					$problems.=$v.", ";
			}
		}
		if($problems!="") 
			echo 'Possible problems: '.$problems;
	}
	
	private function convert($path){
		$source=file_get_contents($path);
		
		$ext=strrpos(".",$path);
		$ext=strtolower(substr($path,$ext+1));
		
		//echo $path;
		if($this->isEncoded($source))
			echo "Already in ".$this->targetEncoding.": ".$path."<br>";
		else{
			
			//convert charset
			$converted=iconv($this->sourceEncoding, $this->targetEncoding.'//TRANSLIT', $source);
			
			//replace byte functions with multibyte variations
			if(strtoupper($this->targetEncoding)=="UTF-8" and $ext=="php")
				$converted=str_replace( 
					array_keys($this->MULTIBYTE_FUNCTIONS), 
					$this->MULTIBYTE_FUNCTIONS, 
					$converted 
				);
			
			//save
			file_put_contents($path,$converted);
			
			//log
			//file_put_contents("MassEncodingConversion.log","Last converted: ".$path);
			echo "Converted: ".$path." ";
			if(strtoupper($this->targetEncoding)=="UTF-8")
				$this->detectProblems($source);
			echo "<br>";
			
		}
	}
	
	private function processFile($dir,$file){
		$path=$dir."/".$file;
		
		if($file!="." and $file!=".." and !in_array($path,$this->exclude)){
			
			if(is_dir($path))
				$this->scan($path);
			
			elseif($this->isTextFile($path) and $path!=__FILE__) //to not convert itself
				$this->convert($path);
			
		}
	}
	
	private function scan($dir){
		$files=scandir($dir);
		foreach($files as $file){
			$this->processFile($dir,$file);
		}
	}		   
}

//INIT
$from='windows-1250';
$defaultDirectory='..'; //current directory

// The constructor requires the source encoding name. 
$a=new MassEncodingConversion($from); 

//You can set excluded directories and files like this:
$a->setExcluded(array("foto","soubory","./_data")); 

//And run scanning in the entered directory
$a->run($defaultDirectory);

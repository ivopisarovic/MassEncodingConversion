MassEncodingConversion
======================

**PHP script to convert all text files to a new encoding. It replaces problematic string functions (e.g. substr is replacedby mb_substr) and show warnings for next possible problems (e.g. using iconv). **

* **!** It is strongly recommended to **backup** all files before running this script. Sometimes it deleted the whole file if a very special character occured. 
* **!** All files must be either in the entered source encoding or in UTF-8, **not mixed up** with other encodings. 
* **!** If you have too many files, *max_execution_time* can be reached. Try running it again, it should be faster. Last converted file is logged in *MassEncodingConversion.log* in the script directory. 

###Default example
Converts all text files in the **current** directory from *windows-1250* 
to *UTF-8*, excluding files in directories *foto*, *soubory* and *_data*. 
```
$from='windows-1250';
$defaultDirectory='..'; //current directory

// The constructor requires the source encoding name. 
$a=new MassEncodingConversion($from); 

//You can set excluded directories and files like this:
$a->setExcluded(array("foto","soubory","./_data")); 

//And run scanning in the entered directory
$a->run($defaultDirectory);
```

MassEncodingConversion
======================

**PHP script to convert all text files to a new encoding.**

* **!** It is strongly recommended to **backup** all files before running this script.
* **!** All files must be either in the entered source encoding or in the target encoding, **not mixed up** with other encodings.  
* **!** If you have too many files, *max_execution_time* can be reached. Try running it again, it should be faster. Last converted file is logged in *MassEncodingConversion.log*. 

###Default example
Converts all text files in the **current** directory from *windows-1250* 
to *UTF-8*, excluding files in directories *soubory* and *_data*. 
```
$from='windows-1250';
$to='UTF-8';

// The constructor requires source and target encoding name. 
$a=new MassEncodingConversion($from,$to); 

//You can set excluded directories and files like this:
$a->setExcluded(array("soubory","./_data")); 

//And run scanning the current directory
$a->run();
```

###Example of scanning in a different directory
Converts all text files in the **entered** directory from *windows-1250* 
to *UTF-8*, excluding files in directories *soubory* and *_data*. 
```
$from='windows-1250';
$to='UTF-8';
$directory="data/cms/";

// The constructor requires source and target encoding name.
$a=new MassEncodingConversion($from,$to); 

//You can set excluded directories and files like this:
$a->setExcluded(array("soubory","./_data")); 

//And run scanning in the entered directory
$a->run($directory);
```

<?php


require_once('Exception.php');
require_once('Guid.php');
require_once('Language.php');
require_once('Database.php');

require_once('Word.php');
require_once('Book.php');
require_once('Chapter.php');
require_once('Verse.php');
require_once('Bible.php');


class Bible_Base
{
    protected $gettableProperties = array();
    protected $settableProperties = array();
    
    public function throwException($message, $code, $previous){
	throw new Bible_Exception($message, $code, $previous);
    }
    
    public function __toString(){
	return parent::__toString();
    }
    
    public function __set($keyname, $value){
	if(in_array($keyname, $this->settableProperties)){
	    return $this->$keyname = $value;
	}else{
	    throw new Bible_Exception('Can not write to the property: '.get_class($this).'->'.$keyname);
	}
    }
    
    public function __get($keyname=null){
	if(in_array($keyname, $this->gettableProperties)){
	    return $this->$keyname;
	}else{
	    throw new Bible_Exception('Can not read from the property: '.get_class($this).'->'.$keyname);
	}
	
    }
    
}
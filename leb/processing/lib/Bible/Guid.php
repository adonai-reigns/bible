<?php

class Bible_Guid
{
    protected $_object = null;
    protected $guid = null;
    
    public function __construct(){
	$this->guid = self::create_guid();
    }
    
    public function __toString(){
	return (string) $this->guid;
    }
    
    public static function create_guid(){
	if(function_exists('com_create_guid')===true){
	    return trim(com_create_guid(), '{}');
	}
	return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }
    
}
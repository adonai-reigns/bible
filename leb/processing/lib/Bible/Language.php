<?php

class Bible_Language extends Bible_Base
{
    protected $iso;
    protected $lang;
    protected $displayName;
    
    public function __construct($iso, $lang, Bible_Word $displayName){
	$this->iso = $iso;
	$this->lang = $lang;
	$this->displayName = $displayName;
    }
    
    protected $gettableProperties = array(
	'iso',
	'lang',
	'displayName'
    );
    
    public function __toString(){
	return (string) $this->iso;
    }
    
}


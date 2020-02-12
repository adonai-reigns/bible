<?php

class Bible_Database{
    
    private $dbHandle;
    private $initted = false;
    private $dbPath;
    private $bibles = array();
    private static $instances = array();
    private static $currentDatabaseHandle = null;
    
    public static function setCurrentDatabaseHandle($dbHandle){
	self::$currentDatabaseHandle = $dbHandle;
    }
    
    public function __get($name){
	if(in_array($name, array(
	    // valid readable properties
	    'dbHandle'
	))){
	    return $this->$name;
	}
    }
    
    public static function start($dbPath, $dbHandle=null){
	if($dbHandle===null){
	    throw new Bible_Exception('$dbHandle must be specified when starting a database connection!');
	}
	self::$instances[$dbHandle] = new self($dbPath, $dbHandle);
	if(self::$currentDatabaseHandle === null){
	    self::$currentDatabaseHandle = $dbHandle;
	}
	return self::getInstance($dbHandle);
    }
    
    public function __construct($dbPath, $dbHandle){
	if(!is_writable($dbPath)){
	    throw new Bible_Exception('dbPath must be writable!');
	}
	if(!is_dir($dbPath)){
	    throw new Bible_Exception('dbPath must be a directory! ');
	}
	$this->dbPath = $dbPath.DIRECTORY_SEPARATOR.'Bible_Database';
	if(!is_dir($this->dbPath)){
	    mkdir($this->dbPath);
	}
	$this->dbHandle = $dbHandle;
	$this->init();
	
    }
    
    public static function getCurrentInstance(){
	return self::getInstance();
    }
   
    public static function getInstance($dbHandle=null){
	if($dbHandle === null){
	    if(self::$currentDatabaseHandle === null){
		throw new Exception('Cannot get a database without a valid handle');
	    }
	    return self::getInstance(self::$currentDatabaseHandle);
	}
	if(!self::$instances[$dbHandle] instanceof self){
	    throw new Bible_Exception('Bible_Database must be initialised before attempting to use it! (Use Bible_Database::start()).');
	}
	return self::$instances[$dbHandle];
    }
    
    public function connect(Bible $bible){
	if(!$this->initted){
	    throw new Bible_Exception('Bible_Database must be initted before connecting');
	}
	
	if(!file_exists($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bible->version.'.db.phps')){
	    // this bible version has never been saved before - it is completely new
	    touch($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bible->version.'.db.phps');
	    mkdir($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bible->version);
	    foreach($bible->getBooks() as $book){
		if(!file_exists($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bible->version.DIRECTORY_SEPARATOR.$book.'.db.phps')){
		    touch($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bible->version.DIRECTORY_SEPARATOR.$book.'.db.phps');
		}
	    }
	    
	}else{
	    // we shall load (unserialize) the saved instance of the corresponding Bible object from the database
	    $bible = $this->readBible($bible->version);
	    
	}
	
    }
    
    public function getBible($bibleVersion, $bibleLanguage){
	if(!$this->hasLoadedBible($bibleVersion)){
	    $this->loadBible($bibleVersion, $bibleLanguage);
	}
	return $this->bibles[$bibleVersion];
    }
    
    private function hasLoadedBible($bibleVersion){
	return array_key_exists($bibleVersion, $this->bibles);
    }
    
    private function loadBible($bibleVersion, $bibleLanguage){
	$serialized = @file_get_contents($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bibleVersion.'.db.phps');
	$bible = unserialize($serialized);
	if(!$bible instanceof Bible){
	    $bible = new Bible($bibleVersion, new Bible_Word($bibleVersion), $bibleLanguage);
	}
	$bible->setDBO($this);
	$this->bibles[$bibleVersion] = $bible;
	return $this->bibles[$bibleVersion];
    }
    
    public function writeBible($bibleVersion, $serialized){
	file_put_contents($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bibleVersion.'.db.phps', $serialized);
    }
    
    /**
     * The only purpose of saving this file, is to assist garbage collection in future - detecting words that have become redundant.
     * Bible_Word objects are serialized and unserialized automatically whenever they are embedded by the owner object that is storing them.
     * There is no need to save and unsave them manually.
     * @param type $wordGuid
     * @param type $json
     */
    public function writeWord($wordGuid, $json){
	
	file_put_contents($this->dbPath.DIRECTORY_SEPARATOR.'words'.DIRECTORY_SEPARATOR.$wordGuid.'.db.phps', $json);
    }
    
    public function getBook($bookSystemName, $bibleVersion){
	if(array_key_exists($bibleVersion, $this->bibles)){
	    $book = $this->getBible($bibleVersion)->getBook($bookSystemName);
	    if($book instanceof Bible_Book){
		return $book;
	    }
	}
	$book = $this->loadBook($bookSystemName, $bibleVersion);
	return $book;
    }
    
    private function loadBook($bookSystemName, $bibleVersion){
	$serialized = file_get_contents($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bibleVersion.DIRECTORY_SEPARATOR.$bookSystemName.'.db.phps');
	$book = unserialize($serialized);
	return $book;
    }
    
    
    public function writeBook($bibleVersion, $bookSystemName, $serialized){
	file_put_contents($this->dbPath.DIRECTORY_SEPARATOR.'bibles'.DIRECTORY_SEPARATOR.$bibleVersion.DIRECTORY_SEPARATOR.$bookSystemName.'.db.phps', $serialized);
    }
    
    
    
    private function init(){
	
	if(!is_dir($this->dbPath)){
	    throw new Bible_Exception('$dbPath is invalid! .. directory does not exist. ('.$this->dbPath.')');
	}
	if(!is_writable($this->dbPath)){
	    throw new Bible_Exception('$dbPath is a directory but it is not writable. ('.$this->dbPath.')');
	}
	if(!is_dir($this->dbPath.DIRECTORY_SEPARATOR.'bibles')){
	    mkdir($this->dbPath.DIRECTORY_SEPARATOR.'bibles');
	}
	if(!is_dir($this->dbPath.DIRECTORY_SEPARATOR.'words')){
	    mkdir($this->dbPath.DIRECTORY_SEPARATOR.'words');
	}
	$this->initted = true;
    }
    
}

<?php

class Bible extends Bible_Base
{
    private $bibleDBO;
    
    protected $version;
    protected $title;
    protected $shortDescription;
    protected $longDescription;
    protected $preface;
    protected $author;
    protected $createdTimestamp;
    protected $updatedTimestamp;
    protected $language;
    protected $books = array();
    
    const OLD_TESTAMENT = 'OT';
    const NEW_TESTAMENT = 'NT';
    
    const RENDER_FORMAT_PLAIN = 'plain';
    const RENDER_FORMAT_HTML = 'html';
    const RENDER_FORMAT_LATEX = 'latex';
    const RENDER_FORMAT_SQL = 'sql';
    
    protected $settableProperties = array(
	'title',
	'shortDescription',
	'longDescription',
	'updatedTimestamp',
	'preface',
	'author',
    );
    protected $gettableProperties = array(
	'version',
	'title',
	'shortDescription',
	'longDescription',
	'createdTimestamp',
	'updatedTimestamp',
	'preface',
	'author',
	'language'
    );
    
    
    public static function init($dbPath){
	Bible::$database = new Bible_Database($dbPath);
    }
    
    public function __construct(string $bibleVersion, Bible_Word $title, Bible_Language $language){
	$this->version = $bibleVersion;
	$this->title = $title;
	$this->language = $language;
	$this->createdTimestamp = time();
    }
    
    public function setDBO(Bible_Database $bibleDBO){
	$this->bibleDBO = $bibleDBO;
    }
    
    public function sortBooks($newSortOrder){
	
    }
    
    public function __sleep(){
	
	return array(
	    'version',
	    'title',
	    'shortDescription',
	    'longDescription',
	    'preface',
	    'author',
	    'createdTimestamp',
	    'updatedTimestamp',
	    'language',
	    'books',
	);
	
	
	
    }
    
    public function __wake(){

    }
    
    public function save(){
	// writes the bible to the database
	
	if($this->version === null){
	    throw new Bible_Exception('Bible cannot be saved until it has been created');
	}
	$this->updatedTimestamp = time();
	
	// make a copy that will be serialized and stored
	$bible = $this;
	
	Bible_Database::setCurrentDatabaseHandle($this->bibleDBO->dbHandle);
	
	// write the serialized object to the filesystem
	$serilializedBible = serialize($bible);
	
	$this->bibleDBO->writeBible($this->version, $serilializedBible);
	
    }
    
    /**
     * Obtain an array of the books as Bible_Book objects.
     * Optional parameter so you can select only specific books
     */
    public function getBooks($systemNames=null){
	if(is_null($systemNames)){
	    $books = $this->books;
	}else{
	    $books = array();
	    foreach($this->books as $book){
		if(in_array($book->systemName, $systemNames)){
		    // set it into the return value array, sorted in the same order as the caller has specified
		    $books[array_search($book->systemName, $systemNames)] = $book;
		}
	    }
	}
	return $books;
    }
    
    
    
    public function getBook($bookSystemName){
	$books = $this->getBooks(array($bookSystemName));
	if(count($books) < 1 || !$books[0] instanceof Bible_Book){
	    return null;
	}else{
	    return $books[0];
	}
    }
    
    
    
    public function addBook($testament, string $bookSystemName, Bible_Word $displayName, Bible_Word $shortDisplayName){
	if($this->getBook($bookSystemName) === null){
	    
	    if(!in_array($testament, array(
		self::NEW_TESTAMENT, self::OLD_TESTAMENT
	    ))){
		throw new Bible_Exception('Book Testament must follow the pattern convention specified. See Bible::OLD_TESTAMENT for an example.');
	    }
	    
	    $this->books[] = new Bible_Book($this, $testament, $bookSystemName, $displayName, $shortDisplayName);
	    
	}else{
	    // can't add a book that has already been added - silently fail
	}
	
	return $this->getBook($bookSystemName);
	
    }
    
}






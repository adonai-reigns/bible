<?php

class Bible_Book extends Bible_Base
{
    protected $_bible;
    protected $systemName;
    protected $displayName;
    protected $shortDisplayName;
    protected $language;
    protected $chapters = array();
    protected $testament; // 0 = old testament, 1 = new testament
    protected $createdTimestamp;
    protected $updatedTimestamp;
    
    protected $settableProperties = array(
	'displayName',
	'shortDisplayName'
    );
    
    protected $gettableProperties = array(
	'systemName',
	'displayName',
	'shortDisplayName',
	'language'
    );
    
    public function __construct(Bible &$bible, $testament, String $systemName, Bible_Word $displayName, Bible_Word $shortDisplayName){
	$this->_bible = &$bible;
	$this->language = $this->_bible->language;
	$this->systemName = $systemName;
	$this->displayName = $displayName;
	$this->shortDisplayName = $shortDisplayName;
	$this->testament = (int) (bool) $testament;
	$this->createdTimestamp = time();
	$this->updatedTimestamp = time();
    }
    
    public function render($format='plain'){
	
	switch($format){
	    case Bible::RENDER_FORMAT_PLAIN:
	    default:
		ob_start();
		echo $this->displayName.PHP_EOL;
		foreach($this->chapters as $chapter){
		    echo 'chapter'.PHP_EOL;
		    echo $chapter->render($format);
		}
		$renderedText = ob_get_contents();
		ob_end_clean();
		return $renderedText;
		break;
	    case Bible::RENDER_FORMAT_LATEX:
		ob_start();
		echo '\biblebook{'.Bible_Word::plaintTextToLatex($this->displayName).'}'.PHP_EOL.PHP_EOL;
		foreach($this->chapters as $chapter){
		    echo $chapter->render($format);
		}
		$renderedText = ob_get_contents();
		ob_end_clean();
		return $renderedText;
		break;
	    case Bible::RENDER_FORMAT_HTML:
		
		break;
	}
    }
    
    public function __toString(){
	return (string) $this->displayName;
    }
    
    public function getChapters(){
	$chapters = $this->chapters;
	return $chapters;
    }
    
    public function addChapter($chapterNum){
	$chapterKey = ((int) $chapterNum - 1);
	$this->chapters[$chapterKey] = new Bible_Book_Chapter($chapterNum, $this);
	
	return $this->chapters[$chapterKey];
    }
    
    public function setChapters($chapters){
	foreach($chapters as $chapter){
	    if(!$chapter instanceof Bible_Book_Chapter){
		throw new Bible_Exception('All chapters must be instances of Bible_Chapter');
	    }
	}
	$this->chapters = $chapters;
    }
    
    
    public function &_getBible(){
	return $this->_bible;
    }
    /*
    public function __sleep(){
	// prevents recursion
	$this->_bible = array(
	    'version' => $this->_bible->version,
	    'language' => $this->_bible->language
	);
	return array(
	    '_bible',
	    'systemName',
	    'displayName',
	    'shortDisplayName',
	    'language',
	    'chapters',
	    'testament',
	    'createdTimestamp',
	    'updatedTimestamp',
	);
    }
    
    public function __wakeup(){
	
	$this->_bible = Bible_Database::getInstance()->getBible($this->_bible['version'], $this->_bible['language']);
    }
    */
    public function save(){
	$this->updatedTimestamp = time();
	
	// make a copy that will be serialized and stored
	$book = $this;
	
	// write the serialized object to the filesystem
	$serilialized = serialize($book);
	self::$database->writeBook($this->_bible->version, $this->systemName, $serilialized);
	
    }
    
    
}
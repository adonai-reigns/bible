<?php

class Bible_Book_Chapter extends Bible_Base
{
    protected $_bible;
    protected $_book;
    protected $language;
    protected $number = null;
    protected $verses = array();
    protected $plainChapterHeading;
    protected $richChapterHeading;
    
    
    public function __construct($number, $book){
	$this->number = $number;
	$this->_book = $book;
	$this->_bible = &$this->_book->_getBible();
	$this->language = $this->_bible->language;
    }
    
    protected $settableProperties = array(
	
    );
    
    protected $gettableProperties = array(
	'number'
    );
    
    public function getVerses(){
	$verses = $this->verses;
	return $verses;
    }
    
    public function addVerse($verseNumber, $plainText){
	$verseKey = ((int) $verseNumber - 1);
	$verse = new Bible_Book_Chapter_Verse($this, $verseNumber, $plainText);
	$this->verses[$verseKey] = $verse;
	return $verse;
    }
    
    public function setVerses($verses){
	foreach($verses as $verse){
	    if(!$verse instanceof Bible_Book_Chapter_Verse){
		throw new Bible_Exception('All verses must be instances of Bible_Chapter_Verse');
	    }
	}
	$this->verses = $verses;
    }
    
    
    public function setChapterHeading(Bible_Word $plainText, Bible_Word $richText){
	$this->plainChapterHeading = $plainText;
	$this->richChapterHeading = $richText;
    }
    
    
    public function render($format='plain'){
	
	switch($format){
	    case Bible::RENDER_FORMAT_PLAIN:
	    default:
		ob_start();
		echo 'Chapter '.$this->number.PHP_EOL;
		if(!empty($this->plainChapterHeading)){
		    echo Bible_Word::cleanupPlainText($this->plainChapterHeading).PHP_EOL;
		}
		foreach($this->verses as $verse){
		    echo $verse->render($format);
		}
		echo PHP_EOL;
		$renderedText = ob_get_contents();
		ob_end_clean();
		return $renderedText;
		break;
	    case Bible::RENDER_FORMAT_LATEX:
		ob_start();
		echo '\begin{biblechapter} % '.$this->_book->displayName.' '.$this->number.PHP_EOL;
		if(!empty($this->plainChapterHeading)){
		    $verses = $this->verses;
		    
		    // @TODO: really do not want to overwrite an existing heading, if it exists on the verse...
		    $verses[0]->setHeading($this->plainChapterHeading, $this->richChapterHeading);
		    foreach($verses as $verse){
			echo $verse->render($format).PHP_EOL;
		    }
		    
		}else{
		    foreach($this->verses as $verse){
			echo $verse->render($format).PHP_EOL;
		    }
		}
		echo '\end{biblechapter}'.PHP_EOL.PHP_EOL;
		$renderedText = ob_get_contents();
		ob_end_clean();
		return $renderedText;
		break;
	    case Bible::RENDER_FORMAT_HTML:
		
		break;
	}
    }
    
    
    public function &_getBook(){
	return $this->_book;
    }
    
    
    public function &_getBible(){
	return $this->_bible;
    }
    
    
}
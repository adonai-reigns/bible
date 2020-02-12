<?php

class Bible_Book_Chapter_Verse extends Bible_Base
{
    
    protected $number;
    
    protected $headingPlainText;
    protected $headingRichText;
    
    protected $plainText = '';
    protected $richText;
    
    protected $_chapter;
    protected $_book;
    protected $_bible;
    protected $language;
    protected $paragraphId = null;
    
    protected $settableProperties = array(
	'plainText',
	'richText',
	'paragraphId'
    );
    protected $gettableProperties = array(
	'plainText',
	'richText',
	'language',
	'paragraphId'
    );
    
    
    public function __construct(&$chapter, int $verseNumber, String $plainText, $richText=null) {
	$this->number = $verseNumber;
	$this->plainText = $plainText;
	$this->richText = $richText;
	$this->_chapter = $chapter;
	$this->_book = $this->_chapter->_getBook();
	$this->_bible = $this->_book->_getBible();
	$this->language = $this->_bible->language;
    }
    
    public function &getChapter(){
	return $this->_chapter;
    }
    public function &getBook(){
	return $this->_book;
    }
    public function &getBible(){
	return $this->_bible;
    }
    
    
    public function setHeading(Bible_Word $plainText, Bible_Word $richText){
	$this->headingPlainText = $plainText;
	$this->richText = $richText;
    }
    
    
    public function render($format='plain'){
	
	switch($format){
	    case Bible::RENDER_FORMAT_PLAIN:
	    default:
		ob_start();
		$plainText = Bible_Word::cleanupPlainText($this->plainText);
		
		
		echo $this->number.' '.$plainText.PHP_EOL;
		$renderedText = ob_get_contents();
		ob_end_clean();
		return $renderedText;
		break;
	    case Bible::RENDER_FORMAT_LATEX:
		ob_start();
		if($this->headingPlainText !== null){
		    echo '\verseWithHeading{'.Bible_Word::plaintTextToLatex($this->headingPlainText).'} '.Bible_Word::plaintTextToLatex($this->plainText);
		}else{
		    echo '\verse '.Bible_Word::plaintTextToLatex($this->plainText);
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
	if($this->richText instanceof Bible_Word){
	    return (string) $this->richText;
	}else{
	    return (string) $this->plainText;
	}
    }
    
}


<?php

class Bible_Word extends Bible_Base
{
    private $guid;
    private $plainText = '';
    private $richText = '';
    private $isSymbol = false;
    private $language = null;
    private $strongsId = null;
    private $flags = array('function'=>null);
    private $placeholderWords = array();
    private $owners = array();
    private $createdTimestamp;
    private $updatedTimestamp;
    private $defaultDisplayMode = 1;
    
    // the display modes
    const DISPLAY_MODE_RICH = 2;
    const DISPLAY_MODE_PLAIN = 1;
    const DISPLAY_MODE_HIDDEN = 0;
    
    
    protected $gettableProperties = array(
	'guid',
	'isSymbol',
	'defaultDisplayMode'
    );
    
    protected $settableProperties = array(
	'language',
	
    );
    
    
    public function __construct($plainText){
	// disabled - we want to reuse words, which maybe it will be a disaster, let's see!
	//$this->guid = new Bible_Guid();
	
	$this->guid = md5($plainText);
	
	$this->plainText = $plainText;
	$this->createdTimestamp = time();
    }
    
    public static function cleanupPlainText($text){
	// remove notes
	$plainText = preg_replace('/(:\|N\|.*\|N\|:)/', '', $text);

	// remove "idiom" and "supplied" markups
	$plainText = str_replace(array('|S|:', '|I|:', ':|I|', ':|S|'), '', $plainText);
	
	// remove italics and bold markups
	$plainText = str_replace(array('|i|:', '|b|:', ':|i|', ':|b|'), '', $plainText);
	
	// remove verse title token
	$plainText = str_replace(':|VT|:', '', $plainText);
	
	return $plainText;
    }
    
    
    
    
    public static function plainTextToHTML($text){
	
	// strip note ids that have been used for latex
	$text = preg_replace('/:\|\|ln[A-Z]{3,3}\|\|:/U', '', $text);
	
	// strip notes that are not for print
	$text = preg_replace('/:\|N\|:\|NP\|:.*\|N\|:/U', '', $text);

	// "Important notes", "idiom" and "supplied" markups
	$tagSearches = array(
	    ':|I|:|S|',
	    '|b|:',
	    '|i|:', 
	    '|IN|:',
	    '|S|:',
	    '|I|:',
	    '|N|:',
	    ':|b|', 
	    ':|i|',
	    ':|IN|',
	    ':|S|',
	    ':|I|', 
	    ':|N|',
	    ':|VT|:',
	    "\r",
	    "\n"
	);
	
	$tagReplacements = array(
	    '<span class="italic"><span class="supplied">',
	    '</span>',
	    '</span>',
	    '</span>',
	    '</span>',
	    '</span>',
	    '</span>',
	    '<span class="bold">',
	    '<span class="italic">',
	    '<span class="note important">', 
	    '<span class="supplied">',
	    '<span class="idiom">', // @TODO: better choice of markup for idioms
	    '<span class="note">',
	    '',
	    ' ',
	    ' '
	);
	
	return str_replace($tagSearches, $tagReplacements, $text);

    }
    
    public static function plainTextToLatex($text){
	// remove notes  // @TODO: move notes into the footer
	$plainText = $text;

	$footnotes = array();
	
	$notes = array();
	preg_match_all('/:\|N\|(.*)\|N\|:/U', $plainText, $notes);
	
	foreach($notes[0] as $noteKey=>$noteValue){
	    
	    if(substr($notes[1][$noteKey], 0, 6) === ':|NP|:'){
		// we don't print these notes
		
		$plainText = str_replace($noteValue, '', $plainText);
		
	    }elseif(strpos($notes[1][$noteKey], ':||ln') !== false){
		// using fixfoot LaTeX package to consolidate repetitive footnotes
		// extract the unique id for the handle
		$noteParts = explode('||:', $notes[1][$noteKey]);
		$noteParts[0] = substr($noteParts[0], 3);
		$noteParts[1] = str_replace('||:', '', $noteParts[1]);

		// adds a unique tag name for use with the LaTeX package "fixfoot"
		$plainText = str_replace($noteValue, '\\'.$noteParts[0].'{}', $plainText);
		
		// we pass the footnote content to the calling function, so the caller can consolidate all footnotes
		$footnotes[$noteParts[0]] = $noteParts[1];

	    }else{
		// normal footnote
		$plainText = str_replace($noteValue, '\lebnote{'.$notes[1][$noteKey].'}', $plainText);
		
	    }
	}
	

	// "Important notes", "idiom" and "supplied" markups
	$tagSearches = array(
	    ':|I|:|S|',
	    '|b|:',
	    '|i|:', 
	    '|IN|:',
	    '|S|:',
	    '|I|:',
	    ':|b|', 
	    ':|i|',
	    ':|IN|',
	    ':|S|',
	    ':|I|', 
	    ':|VT|:'
	);
	
	$tagReplacements = array(
	    '\textit{',
	    '}',
	    '}',
	    '}',
	    '',
	    '}',
	    '\textbf{',
	    '\textit{',
	    '\textit{', // @TODO: better choice of markup for important notes
	    '', // @TODO: better choice of markup for supplied contexts
	    '\textit{', // @TODO: better choice of markup for idioms
	    ''
	);
	
	$plainText = str_replace($tagSearches, $tagReplacements, $plainText);
	
	foreach($footnotes as $footnoteKey=>$footnoteValue){
	    $footnotes[$footnoteKey] = str_replace($tagSearches, $tagReplacements, $footnoteValue);
	}
	
	return array(
	    'footnotes' => $footnotes,
	    'text' => $plainText
	);
    }
    
    public function __sleep(){
	$json = json_encode($this);
	Bible_Database::getCurrentInstance()->writeWord($this->guid, $json);
	
	return array(
	    'guid',
	    'plainText',
	    'richText',
	    'isSymbol',
	    'language',
	    'strongsId',
	    'flags',
	    'placeholderWords',
	    'owners',
	    'createdTimestamp',
	    'updatedTimestamp',
	    'defaultDisplayMode'
	);
	
    }
    
    public function setRichText($richText, $placeholderWords){
	$this->richText = $richText;
	
	// this is precisely where the storage memory-leak can occur. 
	// It is easier to schedule garbage collection than to calculate whether words are becoming redundant by this indiscriminate overwriting of placeholder records.
	// It is possible that every Bible_Word that is an item of the placeholder array, might be reused and referenced by other owners. Since that is possible,
	// we do not go to all the trouble of consulting those owner objects at run time. We will schedule garbage collection to do that time consuming work once in a while.
	
	foreach($placeholderWords as $placeholderWordKey=>$placeholderWord){
	    $placeholderWord->addOwner($this);
	    $placeholderWords[$placeholderWordKey] = $placeholderWord;
	}
	
	$this->placeholderWords = $placeholderWords;
    }
    
    
    public function setPlainText($plainText){
	$this->plainText = (string) $plainText;
    }
    
    public function setDefaultDisplayMode($displayMode){
	if(!in_array($displayMode, array(
	    self::DISPLAY_MODE_HIDDEN, self::DISPLAY_MODE_PLAIN, self::DISPLAY_MODE_RICH
	))){
	    throw new Bible_Exception('Display mode must conform to the values of the class constants. See Bible_Word::DISPLAY_MODE_* for options.');
	}
	$this->defaultDisplayMode = $displayMode;
    }
    
    public function setOwner($owner){
	// this is to assist with garbage collection. One Bible_Word object may belong to multiple owner objects. 
	// It is possible that words will be disassociated from their owner objects but not being deleted, and will thus become redundant.
	// We may thus loop through all Bible_Word instances and if they are no longer referred to by any owners, we shall remove them.
	$ownerToken = $this->constructOwnerToken($owner);
	if(!in_array($ownerToken, $this->owners)){
	    $this->owners[] = $ownerToken;
	}
    }
    
    private function constructOwnerToken($owner){
	// the purpose of the owner token is to assist with garbage collection. It must contain sufficient information to reconstruct the owner object, 
	// and it must be useful as a string array key.
	
	$ownerToken = '';
	
	$ownerClassname = get_class($owner);
	switch($ownerClassname){
	    case 'Bible_Word':
		// it is used as a placeholder replacement in rich text
		$ownerToken = $ownerClassname.':'.$owner->guid;
		break;
	    case 'Bible_Verse':
		$ownerToken = $ownerClassname.':'.$owner->getBible()->version.':'.$owner->getBook()->systemName.':'.$owner->getChapter()->number.':'.$owner->number;
		break;
	    case 'Bible_Book':
		$ownerToken = $ownerClassname.':'.$owner->getBible()->version.':'.$owner->getBook()->systemName.':'.$this->getFlag('function');
		break;
	    case 'Bible':
		$ownerToken = $ownerClassname.':'.$owner->getBible()->version.':'.$this->getFlag('function');
		break;
	    case 'Bible_Language':
		$ownerToken = $ownerClassname.':'.$this->iso.':'.$this->getFlag('function');
		break;
	}
    }

    public static function loadByGuid($guid){
	foreach(Bible::$database->words as $word){
	    if($word->guid === $guid){
		return $word;
	    }
	}
	// not found
	return null;
    }
    
    
    public function getFlag($flagName='function'){
	return $this->flags[$flagName];
    }
    
    public function setFlag($flagName='function', $value){
	$this->flags[$flagName] = $value;
    }
    
    
    public function toString(int $displayMode=-1){
	if(!in_array($displayMode, array(
	    self::DISPLAY_MODE_HIDDEN, self::DISPLAY_MODE_PLAIN, self::DISPLAY_MODE_RICH
	))){
	    $displayMode = $this->defaultDisplayMode;
	}
	switch($displayMode){
	    case self::DISPLAY_MODE_HIDDEN:
		return '';
		
	    case self::DISPLAY_MODE_PLAIN:
		return (string) $this->plainText;
		
	    case self::DISPLAY_MODE_RICH:
	    default:
		return (string) $this->richText;
	}
	
	
    }
    
    public function __toString(){
	return $this->toString();
    }
    
}
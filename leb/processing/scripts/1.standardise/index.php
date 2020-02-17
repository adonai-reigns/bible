<?php


// cannot call this except through the index page
defined('BIBLE_IMPORT_SCRIPT_EXEC') or die();

define('BIBLE_IMPORT_SCRIPT_EXEC_DIR', realpath(__DIR__));
define('BIBLE_IMPORT_SCRIPT_SOURCE_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'0.raw'));
define('BIBLE_IMPORT_SCRIPT_DEST_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'1.standardise'));
define('BIBLE_IMPORT_SCRIPT_OT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'OT'));
define('BIBLE_IMPORT_SCRIPT_NT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'NT'));


// we need to map the shorthand booknames that are used in the XML, to correspond to our system book names
$bookSystemNamesOTMap = array(
    'Ge' => 'genesis',
    'Ex' => 'exodus',
    'Le' => 'leviticus',
    'Nu' => 'numbers',
    'Dt' => 'deuteronomy',
    'Jos' => 'joshua',
    'Jdg' => 'judges',
    'Ru' => 'ruth',
    '1 Sa' => '1samuel',
    '2 Sa' => '2samuel',
    '1 Ki' => '1kings',
    '2 Ki' => '2kings',
    '1 Ch' => '1chronicles',
    '2 Ch' => '2chronicles',
    'Ezr' => 'ezra',
    'Ne' => 'nehemiah',
    'Es' => 'esther',
    'Job' => 'job',
    'Ps' => 'psalms',
    'Pr' => 'proverbs',
    'Ec' => 'ecclesiastes',
    'So' => 'songofsolomon',
    'Is' => 'isaiah',
    'Je' => 'jeremiah',
    'La' => 'lamentations',
    'Eze' => 'ezekiel',
    'Da' => 'daniel',
    'Ho' => 'hosea',
    'Joe' => 'joel',
    'Am' => 'amos',
    'Ob' => 'obadiah',
    'Jon' => 'jonah',
    'Mic' => 'micah',
    'Na' => 'nahum',
    'Hab' => 'habakkuk',
    'Zep' => 'zephaniah',
    'Hag' => 'haggai',
    'Zec' => 'zechariah',
    'Mal' => 'malachi',
);


$bookSystemNamesNTMap = array(
    'Mt' => 'matthew',
    'Mk' => 'mark',
    'Lk' => 'luke',
    'Jn' => 'john',
    'Ac' => 'acts',
    'Ro' => 'romans',
    '1 Co' => '1corinthians',
    '2 Co' => '2corinthians',
    'Ga' => 'galatians',
    'Eph' => 'ephesians',
    'Php' => 'philippians',
    'Col' => 'colossians',
    '1 Th' => '1thessalonians',
    '2 Th' => '2thessalonians',
    '1 Ti' => '1timothy',
    '2 Ti' => '2timothy',
    'Titus' => 'titus',
    'Phm' => 'philemon',
    'Heb' => 'hebrews',
    'Jas' => 'james',
    '1 Pe' => '1peter',
    '2 Pe' => '2peter',
    '1 Jn' => '1john',
    '2 Jn' => '2john',
    '3 Jn' => '3john',
    'Jud' => 'jude',
    'Re' => 'revelation',
);




$sourceXML = file_get_contents(BIBLE_IMPORT_SCRIPT_SOURCE_DIR.DIRECTORY_SEPARATOR.'LEB.xml');


// we perform some crude operations on the full text before beginning operations as XML object

// do not need to give title and name, as such a big name already denotes the title
$sourceXML = str_replace('Yahweh God', 'Yahweh', $sourceXML);

// Translate the name to English, rather than transliterate
//$sourceXML = str_replace('Yahweh', 'Jehovah', $sourceXML);
$sourceXML = str_replace('Yahweh', 'Adonai', $sourceXML);




// remove empty paragraphs
$sourceXML = str_replace("<p />\r\n", '', $sourceXML);


// there are some very special notes that are denoted by special characters. Most fonts can't support those characters, we will need to create a new macro for them
$sourceXML = str_replace('〚', ':|IN|', $sourceXML);
$sourceXML = str_replace('〛', '|IN|:', $sourceXML);

// paragraphs and lists are treated the same, in our view. (LEB's XML uses ul's to denote quotations)
// we will transform every ul into a paragraph
$sourceXML = str_replace('<ul>', '<p class="quote">', $sourceXML);
$sourceXML = str_replace('</ul>', '</p>', $sourceXML);

// now, inside quotes, the LEB's XML has denoted indentations by two classes of li
// let us remove those specifications because we will configure the styles using LaTeX patterns
$sourceXML = preg_replace('/<li[0-9]>|<\/li[0-9]>/', '', $sourceXML);




// fix some nesting typos that are in the original source before we begin
$sourceXML = str_replace(array(
    '<i>mishpat,</i>',
    '<i>tsedaqah,</i>',
    '<i>tsa`aqah,</i>',
    '<i>ned,</i>',
    '<i>Qere)</i>'
), array(
    '<i>mishpat</i>,',
    '<i>tsedaqah</i>,',
    '<i>tsa`aqah</i>,',
    '<i>ned</i>,',
    '<i>Qere</i>)'
), $sourceXML, $reps);



// replace note markers with non-xml markups
$sourceXML = str_replace('<note tag="NOT_FOR_PRINT">', ':|N|:|NP|:', $sourceXML);
$sourceXML = str_replace('<note>A quotation from ', ':|N|:|NP|:A quotation from ', $sourceXML);
$sourceXML = str_replace('<note>This verse contains a quotation from ', ':|N|:|NP|:This verse contains a quotation from ', $sourceXML);
$sourceXML = str_replace('<note>This verse contains quotations from ', ':|N|:|NP|:This verse contains quotations from ', $sourceXML);
$sourceXML = str_replace('<note>The second half of this verse contains a quotation from ', ':|N|:|NP|:The second half of this verse contains a quotation from ', $sourceXML);
$sourceXML = str_replace('<note>Verses <cite title="BibleLEB2:Mt 12:18–21">18–21</cite> are a quotation from ', ':|N|:|NP|:Verses 18–21 are a quotation from ', $sourceXML);
$sourceXML = str_replace('<note>Verses <cite title="BibleLEB2:Ac 7:6–7">6–7</cite> are a quotation from ', ':|N|:|NP|:Verses 6–7 are a quotation from ', $sourceXML);
$sourceXML = str_replace('<note>Verses <cite title="BibleLEB2:Ro 3:15–17">15–17</cite> are a quotation from ', ':|N|:|NP|:Verses 15–17 are a quotation from ', $sourceXML);
$sourceXML = str_replace('<note>Verses <cite title="BibleLEB2:Ro 3:10–12">10–12</cite> are a quotation from ', ':|N|:|NP|:Verses 10–12 are a quotation from ', $sourceXML);



$sourceXML = str_replace('<note>', ':|N|', $sourceXML);
$sourceXML = str_replace('</note>', '|N|:', $sourceXML);


// replace idiom markers with non-xml markups
$sourceXML = str_replace('<idiom-start />', ':|I|', $sourceXML);
$sourceXML = str_replace('<idiom-end />', '|I|:', $sourceXML);


// replace supplied markers with non-xml markups
$sourceXML = str_replace('<supplied>', ':|S|', $sourceXML);
$sourceXML = str_replace('</supplied>', '|S|:', $sourceXML);



//// replace pericope markers with paragraph tags
$sourceXML = str_replace('<pericope>', '<p class="pericope">', $sourceXML);
$sourceXML = str_replace('</pericope>', '</p>', $sourceXML);


// replace italics markers with non-xml markups
$sourceXML = str_replace('<i>', ':|i|', $sourceXML);
$sourceXML = str_replace('</i>', '|i|:', $sourceXML);


// replace bold markers with non-xml markups (these are only found three times, in notes).
$sourceXML = str_replace('<b>', ':|b|', $sourceXML);
$sourceXML = str_replace('</b>', '|b|:', $sourceXML);






// write a copy of this source code, that is useful while developing
file_put_contents(BIBLE_IMPORT_SCRIPT_DEST_DIR.DIRECTORY_SEPARATOR.'LEB-prepared.xml', $sourceXML);

$doc = new DOMDocument();
$doc->loadXML($sourceXML);



// we perform some blanket operations on the full text before beginning operations on individual books









// ready to go!

// create a database
$bibleVersion = 'LEB';
$bibleLanguage = new Bible_Language('en_US', 'en', new Bible_Word('English (US)'));
$bibleDb = Bible_Database::start(BIBLE_IMPORT_SCRIPT_DEST_DIR, 'standardise');
$dbBible = $bibleDb->getBible($bibleVersion, $bibleLanguage);

$docBooks = $doc->getElementsByTagName('book');

$numBooks = 0;

foreach($docBooks as $docBook){
    $bookId = $docBook->getAttribute('id');
    
    if(array_key_exists($bookId, $bookSystemNamesOTMap)){
	$OTNT = Bible::OLD_TESTAMENT;
	$bookSystemName = $bookSystemNamesOTMap[$bookId];
	$bookSystemConfig = $bookSystemNamesOT[$bookSystemName];
    }else if(array_key_exists($bookId, $bookSystemNamesNTMap)){
	$OTNT = Bible::NEW_TESTAMENT;
	$bookSystemName = $bookSystemNamesNTMap[$bookId];
	$bookSystemConfig = $bookSystemNamesNT[$bookSystemName];
    }else{
	throw new Exception('The book "'.$bookId.'" doesnt have a configuration!');
    }
    
    $numBooks++;
    
    /* verify that book name our mappings are complete
    if(array_key_exists($bookId, $bookSystemNamesOTMap)){
	if(!in_array($bookSystemNamesOTMap[$bookId], $bookSystemNamesOT)){
	    echo "error: it is not found in the book OT array: $bookId\n";
	}
    }elseif(array_key_exists($bookId, $bookSystemNamesNTMap)){
	if(!in_array($bookSystemNamesNTMap[$bookId], $bookSystemNamesNT)){
	    echo "error: it is not found in the book NT array: $bookId\n";
	}
    }else{
	echo "Error! .. Could not find mapping for book name $bookId\n";
    }*/
    
    
    
	
    $dbBook = $dbBible->addBook($OTNT, $bookSystemName, new Bible_Word($bookSystemConfig['displayName']), new Bible_Word($bookSystemConfig['shortDisplayName']));
	
    $docChapters = $docBook->getElementsByTagName('chapter');
    
    $docChaptersArray = array();
    
    foreach($docChapters as $docChapter){
	$docChaptersArray[] = $docChapter;
    }
    
    $singleChapterBook = false;
    
    if(count($docChaptersArray) < 1){
	// this happens when there is a book having only one chapter, so they do not count the chapter. Eg Jude, 2 John or 3 John.
	
	$singleChapterBook = true;

	// the book node needs to look like a chapter node. The only difference will be the tag name - but we don't use the tag name after this.
	$docBook->setAttribute('id', $bookId.' 1');
	
	// so our book element is going to be our sole chapter element!
	$docChaptersArray[] = $docBook;
    }
    
    $pericopeHeading = null;
    
    
    
    foreach($docChaptersArray as $docChapterKey=>$docChapter){
	
	
	// extract the chapter number
	$chapterNumber = str_replace($bookId.' ', '', $docChapter->getAttribute('id'));
	
	// add a chapter object to the book object
	$dbChapter = $dbBook->addChapter($chapterNumber);
	
	
	
	// we will add a class name to every paragraph to show that it is a default paragraph type
	$paragraphs = $docChapter->getElementsByTagName('p');
	
	foreach($paragraphs as $paragraphKey=>$paragraph){
	    if($paragraph->getAttribute('class') === 'pericope'){
		$pericopeHeading = $paragraph;
		continue;
	    }
	    
	    // break the paragraph into verses
	    
	    // we need to replace verse numbers with token pattern, because XML does not use markup for individual verse elements
	    $verseNumbersList = $paragraph->getElementsByTagName('verse-number');
	    $verseNumbers = array();
	    foreach($verseNumbersList as $verseNumber){
		
		if($singleChapterBook){
		    // the verse numbers do not include the chapter number when it is a book with only one chapter. 
		    // it's easy to fix though..
		    $verseNumber->setAttribute('id', str_replace($bookId.' ', $bookId.' 1:', $verseNumber->getAttribute('id')));
		}
		
		$verseNumbers[] = $verseNumber;
	    }
	    
	    if(count($verseNumbers)<1){
		// this paragraph has content that belongs to the previous verse
		$paragraphPlainText = trim($paragraph->nodeValue);

		// strip tab characters from the plain text
		$paragraphPlainText = preg_replace('/\t/', '', $paragraphPlainText);

		
		$dbVerse->plainText = $dbVerse->plainText.' '.$paragraphPlainText;
	    }
	    
	    // remove verse number tags that are only a chapter number
	    foreach($verseNumbers as $verseNumberKey=>$verseNumber){
		if(!strstr($verseNumber->getAttribute('id'), ':')){
		    $verseNumber->parentNode->removeChild($verseNumber);
		    unset($verseNumbers[$verseNumberKey]);
		}
	    }
	    
	    // some verse numbers are denoting the heading instead of a verse. This only happens in the psalms.
	    foreach($verseNumbers as $verseNumberKey=>$verseNumber){
		if(strstr($verseNumber->getAttribute('id'), ':title')){
		    // yes, this is one of them! -- we have a special hack for these ones, seeing as the verse number is zero!
		    // when we detect that the string pattern exists in the beginning of a verse, we shall recognise it as a heading rather thana verse.
		    // let's assign the verse number as zero, to conform with normal verse processing.
		    $verseNumber->textContent = ":|0|::|VT|:";
		}
	    }
	    
	    // regenerate array indices
	    $verseNumbers = array_values($verseNumbers);
	    
	    foreach($verseNumbers as $verseNumberKey=>$verseNumber){
		// replace verse numbers with token pattern
		if($verseNumber->textContent !== ':|0|::|VT|:'){ // exclude operating on verse titles (eg: many of the psalms have them)
		    $verseNumber->textContent = ":|{$verseNumber->textContent}|:";
		}
	    }
	    
	    // explode the paragraph into verses by the tokens available
	    $verses = preg_split('/:\|[0-9]*\|:/', $paragraph->nodeValue);
	    
	    // if the first entry is an empty string, remove it
	    if(empty(trim($verses[0]))){
		unset($verses[0]);
	    }
	    $verses = array_values($verses);
	    
	    // need to get the verse numbers too
	    $verseNumbers = array();
	    preg_match_all('/:\|[0-9]*\|:/', $paragraph->nodeValue, $verseNumbers);
	    
	    foreach($verseNumbers[0] as $verseNumberKey=>$verseNumber){

		$verseNumber = (int) preg_replace('/[^0-9]/', '', $verseNumber);

		    $versePlainText = trim($verses[$verseNumberKey]);

		    // strip tab characters from the plain text
		    $versePlainText = preg_replace('/\t/', '', $versePlainText);

		    $verseRichText = null;// @TODO


		if($verseNumber < 1){
		    // it's a title = it gets special treatment (only used in psalms as headings)
		    $dbChapter->setChapterHeading(new Bible_Word($versePlainText), new Bible_Word($verseRichText));


		}else{
		    // it's a normal verse

		    $dbVerse = $dbChapter->addVerse($verseNumber, $versePlainText, $verseRichText);
		    $dbVerse->paragraphId = $paragraphKey;

		    if($pericopeHeading != null){
			// this verse should have a custom heading attached to it
			$dbVerse->setHeading(new Bible_Word($pericopeHeading->nodeValue), new Bible_Word($pericopeHeading->nodeValue));

			// reset the pericope
			$pericopeHeading = null;
		    }


		}

	    }

	    
	}// looping paragraphs
	
    
    }// looping chapters
    
    
    switch($OTNT){
	case Bible::NEW_TESTAMENT:
	    file_put_contents(BIBLE_IMPORT_SCRIPT_NT_TEX_DIR.DIRECTORY_SEPARATOR.$bookSystemName.'.tex', $dbBook->render(Bible::RENDER_FORMAT_LATEX));
	    break;
	case Bible::OLD_TESTAMENT:
	    file_put_contents(BIBLE_IMPORT_SCRIPT_OT_TEX_DIR.DIRECTORY_SEPARATOR.$bookSystemName.'.tex', $dbBook->render(Bible::RENDER_FORMAT_LATEX));
	    break;
    }


    echo '\input{leb/content/'.$OTNT.'/'.$bookSystemName.'.tex}\flushcolsend'.PHP_EOL;
    
    
    
    
}// looping books



$dbBible->save();










<?php


// cannot call this except through the index page
defined('BIBLE_IMPORT_SCRIPT_EXEC') or die();

define('BIBLE_IMPORT_SCRIPT_EXEC_DIR', realpath(__DIR__));
define('BIBLE_IMPORT_SCRIPT_SOURCE_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'0.renderlatex'));
define('BIBLE_IMPORT_SCRIPT_DEST_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'0.renderlatex'));
define('BIBLE_IMPORT_SCRIPT_OT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'old-testament'));
define('BIBLE_IMPORT_SCRIPT_NT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'new-testament'));
define('BIBLE_IMPORT_SCRIPT_OT_SQL_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'OT'));
define('BIBLE_IMPORT_SCRIPT_NT_SQL_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'NT'));
define('BIBLE_IMPORT_SCRIPT_FN_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'));


$tmpLebDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.SQLite3.tmp';
$tmpFnDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.commentaries.SQLite3.tmp';
$tmpOTDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'IHOT+.SQLite3.tmp';
$tmpNTDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'iESVTH+.SQLite3.tmp';

copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.SQLite3', $tmpLebDbPath);
copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.commentaries.SQLite3', $tmpFnDbPath);
copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'IHOT+.SQLite3', $tmpOTDbPath);
copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'iESVTH+.SQLite3', $tmpNTDbPath);



// create our database handles
$lebDb = new SQLite3($tmpLebDbPath);
$fnDb = new SQLite3($tmpFnDbPath);
$otDB = new SQLite3($tmpOTDbPath);
$ntDB = new SQLite3($tmpNTDbPath);


// do not need to give title and name, as such a big name already denotes the title


// Translate the name to English, rather than transliterate
//$q = "UPDATE verses SET text = REPLACE(text, 'Yahweh', 'Jehovah')";
$q = "UPDATE verses SET text = REPLACE(text, 'Yahweh', 'Adonai')";
$lebDb->query($q);
//$q = "UPDATE stories SET title = REPLACE(text, 'Yahweh', 'Jehovah')";
$q = "UPDATE stories SET title = REPLACE(title, 'Yahweh', 'Adonai')";
$lebDb->query($q);
//$q = "UPDATE commentaries SET text = REPLACE(text, 'Yahweh', 'Jehovah')";
$q = "UPDATE commentaries SET text = REPLACE(text, 'Yahweh', 'Adonai')";
$fnDb->query($q);


// strip unwanted tags
$q = "UPDATE verses SET text = REPLACE(text, '[', '')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, ']', '')";
$lebDb->query($q);


// LaTeX markup begins


// headings that are inside verses
$q = "UPDATE verses SET text = REPLACE(text, ' <e>', '\\innerVerseHeading{')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '<e>', '\\innerVerseHeading{')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '</e> ', '}')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '</e>', '}')";
$lebDb->query($q);




// write the footnotes file

// detect repetitive footnotes and reuse the footnote 
$fixedFootnotes = array();
$fnDb->query("ALTER TABLE commentaries ADD COLUMN consolidated_id VARCHAR");

$footnotesResult = $fnDb->query("SELECT * FROM commentaries");

while($footnote = $footnotesResult->fetchArray(SQLITE3_ASSOC)){
    continue;
    $footnoteValue = preg_replace('/<n.*>/U', '', $footnote['text']);
    $footnoteValue = str_replace('</n>', '', $footnoteValue);
    $footnoteValue = preg_replace('/<a.*>/U', '', $footnoteValue);
    $footnoteValue = str_replace('</a>', '', $footnoteValue);
    $footnoteValue = preg_replace('/Here “(.*)” is supplied because the (.*) \(“(.*)”\) .* been translated as (.*)/U', '“$1” supplied because $2 “$3” translated as $4', $footnoteValue);
    $footnoteValue = preg_replace('/^Here\s(.*)/U', '$1', $footnoteValue);
    $footnoteValue = preg_replace('/^“the”;\s?(.*)/U', '“the”: $1', $footnoteValue);
    $origFootnoteTextEscaped = SQLite3::escapeString($footnote['text']);
    
    $nearFootnotesQuery = "SELECT COUNT(*) as talley FROM commentaries WHERE book_number = {$footnote['book_number']} AND chapter_number_from  = {$footnote['chapter_number_from']} AND text = '{$origFootnoteTextEscaped}' AND consolidated_id IS NULL";
    $nearFootnotesResult = $fnDb->query($nearFootnotesQuery);
    $nearFootnote = $nearFootnotesResult->fetchArray(SQLITE3_ASSOC);
    
    if($nearFootnote['talley'] > 1){
	// yes, this is a repetitive footnote 
	$fixedFootnotesId = count($fixedFootnotes);
	$fixedFootnotes[$fixedFootnotesId] = $footnoteValue;
	$fnDb->query("UPDATE commentaries SET consolidated_id = {$fixedFootnotesId} WHERE book_number = {$footnote['book_number']} AND chapter_number_from = {$footnote['chapter_number_from']} AND text = '{$origFootnoteTextEscaped}' ");
    }
    
    $newFootnoteTextEscaped = SQLite3::escapeString($footnoteValue);
    
    $fnDb->query("UPDATE commentaries SET text = '{$newFootnoteTextEscaped}' WHERE book_number = {$footnote['book_number']} AND chapter_number_from = {$footnote['chapter_number_from']} AND text = '{$origFootnoteTextEscaped}' ");
    
}

$footnoteFile = '';
foreach($fixedFootnotes as $footnoteKey=>$footnoteValue){
    // strip hyperlinks
    
    $footnoteFile .= '\DeclareFixedFootnote{\ln'.integer_to_word($footnoteKey).'}{'.$footnoteValue.'}%'.PHP_EOL;
}
//file_put_contents(BIBLE_IMPORT_SCRIPT_FN_TEX_DIR.DIRECTORY_SEPARATOR.'footnotes.tex', $footnoteFile);




$booksResult = $lebDb->query("SELECT * FROM books");

while($book = $booksResult->fetchArray(SQLITE3_ASSOC)){
    
    $bookNumber = $book['book_number'];
    
    $OTNT = $latexFilepath = ($bookNumber < 470) ? 'OT' : 'NT';
    
    $latexFileContent = '\biblebook{'.$book['long_name'].'}'.PHP_EOL;
    
    $latexFilename = $book['short_name'].'.tex';
    
    switch($OTNT){
	case 'OT':
	    $latexFilepath = BIBLE_IMPORT_SCRIPT_OT_TEX_DIR . DIRECTORY_SEPARATOR . $latexFilename;
	    $verseDb = $otDB;
	    $storiesDb = $lebDb;
	    break;
	case 'NT':
	    $latexFilepath = BIBLE_IMPORT_SCRIPT_NT_TEX_DIR . DIRECTORY_SEPARATOR . $latexFilename;
	    $verseDb = $ntDB;  
	    $storiesDb = $ntDB;
	    break;
    }
    
    // loop over each chapter in this book
    $chaptersResult = $verseDb->query("SELECT DISTINCT chapter FROM verses WHERE book_number = '{$bookNumber}' ORDER BY chapter ASC");
    
    while($chapter = $chaptersResult->fetchArray(SQLITE3_ASSOC)){
	$chapterNumber = $chapter['chapter'];
	
	$latexFileContent .= '\begin{biblechapter}% '.$book['long_name'].' '.$chapterNumber.PHP_EOL;
	
	$versesResult = $verseDb->query("SELECT * FROM verses WHERE book_number = {$bookNumber} AND chapter = {$chapterNumber} ORDER BY verse ASC");
	
	while($verse = $versesResult->fetchArray(SQLITE3_ASSOC)){
	    
	    // does this verse have a heading?
	    $headingResult = $storiesDb->query("SELECT * FROM stories WHERE book_number = {$bookNumber} AND chapter = {$chapterNumber} AND verse = {$verse['verse']} ORDER BY order_if_several ASC");
	    
	    // just use the first result for the verse heading. Verses already have subheadings embedded in the text.
	    $headingRows = $headingResult->fetchArray(SQLITE3_ASSOC);
	    
	    if($headingRows !== false){
		$verseCommand = '\verseWithHeading{'.$headingRows['title'].'}';
	    }else{
		$verseCommand = '\verse';
	    }
	    
	    // process the verse content
	    $verseContent = $verse['text'];
	    
	    // process footnote tags
	    $footnotes = array();
	    preg_match_all('/<f>([0-9]*)<\/f>/', $verseContent, $footnotes);
	    
	    foreach($footnotes[1] as $footnoteMarker){
		continue;
		$nearFootnotesQuery = "SELECT * FROM commentaries WHERE book_number = {$bookNumber} AND chapter_number_from = {$chapterNumber} AND verse_number_from = {$verse['verse']} AND marker = '[{$footnoteMarker}]'";
		$nearFootnotesResult = $fnDb->query($nearFootnotesQuery);
		$nearFootnote = $nearFootnotesResult->fetchArray(SQLITE3_ASSOC);
		
		if(!is_array($nearFootnote)){
		    $verseContent = str_replace('<f>'.$footnoteMarker.'</f>', 'Not found: ['.$footnoteMarker.'}', $verseContent);
		}elseif($nearFootnote['consolidated_id'] !== null){
		    $verseContent = str_replace('<f>'.$footnoteMarker.'</f>', '\ln'.integer_to_word($nearFootnote['consolidated_id']).'{}', $verseContent);
		}else{
		    // footnote should be processed as normal
		    $verseContent = str_replace("<f>{$footnoteMarker}</f>", '\lebnote{'.$nearFootnote['text'].'}', $verseContent);
		}
	    }
	    
	    
	    // strip Strong's numbers
	    $verseContent = preg_replace('/<S>(.*)<\/S>/U', '', $verseContent);
	    
	    // strip word contexts
	    $verseContent = preg_replace('/<m>(.*)<\/m>/U', '', $verseContent);
	    
	    // process Jesus' words tags
	    $verseContent = preg_replace('/<J>(.*)<\/J>/U', '\JesusWords{$1}', $verseContent);
	    
	    // some Jesus' words tags are not closed, it is a bug in the database
	    $verseContent = preg_replace('/<J>(.*)$/', '\JesusWords{$1}', $verseContent);
	    
	    // strip unwanted tags
	    $verseContent = preg_replace('/<n>(.*)<\/n>/U', '', $verseContent);
	    
	    while(strstr($verseContent, '•') !== false){
		$verseContent = str_replace('•', ' ', $verseContent);
	    }
	    
	    while(strstr($verseContent, '  ') !== false){
		$verseContent = str_replace('  ', ' ', $verseContent);
	    }
	    
	    $verseContent = trim($verseContent);
	    
	    $latexFileContent .= $verseCommand.'{'.$verseContent.'}%'.PHP_EOL;
	    
	    
	}// end looping verses
	
	$latexFileContent .= '\end{biblechapter}%'.PHP_EOL;
	
    }// end looping chapters
    
    // write the file content to the file
    file_put_contents($latexFilepath, $latexFileContent);
    
    // output to the screen, the relative latex filepath
    switch($OTNT){
	case 'OT':
	    echo '\input{int/content/old-testament/'.$book['short_name'].'.tex}\flushcolsend<br />';
	    break;
	case 'NT':
	    echo '\input{int/content/new-testament/'.$book['short_name'].'.tex}\flushcolsend<br />';
	    break;
    }
    
    
}// end looping books



unlink($tmpLebDbPath);
unlink($tmpFnDbPath);







function integer_to_word($val){
    $val = base_convert($val, 10, 26);
    $digits = str_split($val);
    foreach($digits as $k=>$digit){
	if(is_numeric($digit)){
	    $digits[$k] = chr($digit+97);
	}else{
	    $digits[$k] = chr(ord($digit)+10);
	}
    }
    return strtoupper(implode('', $digits));
}


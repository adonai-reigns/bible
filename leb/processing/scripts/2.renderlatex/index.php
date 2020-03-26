<?php


// cannot call this except through the index page
defined('BIBLE_IMPORT_SCRIPT_EXEC') or die();

define('BIBLE_IMPORT_SCRIPT_EXEC_DIR', realpath(__DIR__));
define('BIBLE_IMPORT_SCRIPT_SOURCE_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'2.renderlatex'));
define('BIBLE_IMPORT_SCRIPT_DEST_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'2.renderlatex'));
define('BIBLE_IMPORT_SCRIPT_OT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'old-testament'));
define('BIBLE_IMPORT_SCRIPT_NT_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'new-testament'));
define('BIBLE_IMPORT_SCRIPT_OT_SQL_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'OT'));
define('BIBLE_IMPORT_SCRIPT_NT_SQL_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sql'.DIRECTORY_SEPARATOR.'NT'));
define('BIBLE_IMPORT_SCRIPT_FN_TEX_DIR',  realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'content'));


$tmpLebDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.SQLite3.tmp';
$tmpFnDbPath = BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.commentaries.SQLite3.tmp';

copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.SQLite3', $tmpLebDbPath);
copy(BIBLE_IMPORT_SCRIPT_SOURCE_DIR . DIRECTORY_SEPARATOR . 'LEB.commentaries.SQLite3', $tmpFnDbPath);


// create our database handles
$lebDb = new SQLite3($tmpLebDbPath);
$fnDb = new SQLite3($tmpFnDbPath);

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
$q = "UPDATE verses SET text = REPLACE(text, ' <f>', '<f>')";
$lebDb->query($q);
//$q = "UPDATE verses SET text = REPLACE(text, '</f> ', '</f>')";
//$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '.</f>', '</f>.')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, ',</f>', '</f>,')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '</f>”', '”</f>')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '<t>', '')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '</t>', '')";
$lebDb->query($q);

$q = "UPDATE verses SET text = REPLACE(text, '<pb/>', '')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '<i>', '')";
$lebDb->query($q);
$q = "UPDATE verses SET text = REPLACE(text, '</i>', '')";
$lebDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, '<i>', '\\textit{')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, '</i>', '}')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'Literally ', '') WHERE text LIKE 'Literally %'";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'indicates the noun is singular and occurs with the definite article', 'Singular with Definite Article')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'The noun lacks the definite article and is taken as a proper noun in this context', 'No definite article, assumed proper noun')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'Here the direct object is supplied from context in the English translation', 'supplied from English context')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, '*Here the direct object is supplied from context in the English translation', 'supplied from English context')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, '*The imperfect tense has been translated as ingressive here', 'Imperfect tense')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'The imperfect tense has been translated as ingressive here', 'Imperfect tense as ingressive')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'A quotation from', 'from')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'A shortened form of “Adonai”', 'A shortened form of “Yahweh”')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'Here “and” is supplied because the previous participle (“answered”) has been translated as a finite verb', '“and” supplied because previous participle (“answered”) translated as finite verb')";
$fnDb->query($q);
$q = "UPDATE commentaries SET text = REPLACE(text, 'The direct object is supplied from context in the English translation', 'supplied from English context')";
$fnDb->query($q);


$q = "DELETE FROM commentaries WHERE text LIKE 'The Hebrew Bible counts the superscription as the first verse of the psalm%'";
$fnDb->query($q);
/*
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”'";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Canaanite”'";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hittite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Perizzite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hivite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jebusite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amramite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Izharite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hebronite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Uzzielite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Kohathite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Levite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Mahlite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Mushite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Gershonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amalekite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Simeonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Kenite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Perezite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hamulite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Tolaite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Punite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jashubite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shimronite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Seredite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Elonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jahleelite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Zebulunite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Makirite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Gileadite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Iezerite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Helekite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Asrielite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shechemite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shemidaite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hepherite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shuthelahite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Tahanite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Bekerite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Palluite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hezronite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Carmite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Reubenite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Nemuelite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jaminite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jakinite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Zerahite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shaulite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Zephonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Haggite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Oznite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Arodite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Erite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Arelite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shelanite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Eranite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Ahiramite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shuphamite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Huphamite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Naamite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shumhamite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Imnahite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Ishvite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Beriahite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Heberite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Gershonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Kohathite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Merarite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Malkielite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jahzeelite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Libnite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Hebronite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Gunite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Jezerite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Shillemite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Mahlite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Mushite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Korahite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Gadite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Maacathite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Ashdotite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Ashkelonite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Arkite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Japhletite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Sidonian”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);






$q = "DELETE FROM commentaries WHERE text = 'Hebrew “chariot”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “wonder”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “human”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “circumcisions”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
$q = "DELETE FROM commentaries WHERE text = 'Hebrew “Amorite”";
$fnDb->query($q);
*/

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
file_put_contents(BIBLE_IMPORT_SCRIPT_FN_TEX_DIR.DIRECTORY_SEPARATOR.'footnotes.tex', $footnoteFile);




$booksResult = $lebDb->query("SELECT * FROM books");

while($book = $booksResult->fetchArray(SQLITE3_ASSOC)){
    
    $bookNumber = $book['book_number'];
    
    $OTNT = $latexFilepath = ($bookNumber < 470) ? 'OT' : 'NT';
    
    $latexFileContent = '\biblebook{'.$book['long_name'].'}'.PHP_EOL;
    
    $latexFilename = $book['short_name'].'.tex';
    
    switch($OTNT){
	case 'OT':
	    $latexFilepath = BIBLE_IMPORT_SCRIPT_OT_TEX_DIR . DIRECTORY_SEPARATOR . $latexFilename;
	    break;
	case 'NT':
	    $latexFilepath = BIBLE_IMPORT_SCRIPT_NT_TEX_DIR . DIRECTORY_SEPARATOR . $latexFilename;
	    break;
    }
    
    // loop over each chapter in this book
    $chaptersResult = $lebDb->query("SELECT DISTINCT chapter FROM verses WHERE book_number = '{$bookNumber}' ORDER BY chapter ASC");
    
    while($chapter = $chaptersResult->fetchArray(SQLITE3_ASSOC)){
	$chapterNumber = $chapter['chapter'];
	
	$latexFileContent .= '\begin{biblechapter}% '.$book['long_name'].' '.$chapterNumber.PHP_EOL;
	
	$versesResult = $lebDb->query("SELECT * FROM verses WHERE book_number = {$bookNumber} AND chapter = {$chapterNumber} ORDER BY verse ASC");
	
	while($verse = $versesResult->fetchArray(SQLITE3_ASSOC)){
	    
	    // does this verse have a heading?
	    $headingResult = $lebDb->query("SELECT * FROM stories WHERE book_number = {$bookNumber} AND chapter = {$chapterNumber} AND verse = {$verse['verse']} ORDER BY order_if_several ASC");
	    
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
		$nearFootnotesQuery = "SELECT * FROM commentaries WHERE book_number = {$bookNumber} AND chapter_number_from = {$chapterNumber} AND verse_number_from = {$verse['verse']} AND marker = '[{$footnoteMarker}]'";
		$nearFootnotesResult = $fnDb->query($nearFootnotesQuery);
		$nearFootnote = $nearFootnotesResult->fetchArray(SQLITE3_ASSOC);
		
		if(!is_array($nearFootnote)){
		    $verseContent = str_replace('<f>'.$footnoteMarker.'</f>', '', $verseContent);
		}elseif($nearFootnote['consolidated_id'] !== null){
		    $verseContent = str_replace('<f>'.$footnoteMarker.'</f>', '\ln'.integer_to_word($nearFootnote['consolidated_id']).'{}', $verseContent);
		}else{
		    // footnote should be processed as normal
		    $verseContent = str_replace("<f>{$footnoteMarker}</f>", '\lebnote{'.$nearFootnote['text'].'}', $verseContent);
		}
	    }
	    
	    
	    // process Jesus' words tags
	    $verseContent = preg_replace('/<J>(.*)<\/J>/U', '\JesusWords{$1}', $verseContent);
	    
	    // some Jesus' words tags are not closed, it is a bug in the database
	    $verseContent = preg_replace('/<J>(.*)$/', '\JesusWords{$1}', $verseContent);
	    
	    // strip unwanted tags
	    $verseContent = preg_replace('/<n>(.*)<\/n>/U', '', $verseContent);
	    
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
	    echo '\input{leb/content/old-testament/'.$book['short_name'].'.tex}\flushcolsend<br />';
	    break;
	case 'NT':
	    echo '\input{leb/content/new-testament/'.$book['short_name'].'.tex}\flushcolsend<br />';
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


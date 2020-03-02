<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// cannot call this except through the index page
defined('BIBLE_IMPORT_SCRIPT_EXEC') or die();


// it takes a long time to execute this script
set_time_limit(60*8);


$startTime = time();



define('BIBLE_IMPORT_SCRIPT_EXEC_DIR', realpath(__DIR__));
define('BIBLE_IMPORT_SCRIPT_SOURCE_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'0.raw'));
define('BIBLE_IMPORT_SCRIPT_DEST_DIR', realpath(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'sources'.DIRECTORY_SEPARATOR.'1.indexnotes'));


$sourceXML = file_get_contents(BIBLE_IMPORT_SCRIPT_SOURCE_DIR.DIRECTORY_SEPARATOR.'LEB.xml');


// shorten the notes length

// remove all occurrences of "Literally" to shorten footnotes length
$sourceXML = preg_replace('/>Literally, /', '>', $sourceXML);
$sourceXML = preg_replace('/>Literally /', '>', $sourceXML);

// ditto "A quotation from"
$sourceXML = preg_replace('/>A quotation from /', '>', $sourceXML);

// strip out all notes which say that the imperfect tense has been generated
$sourceXML = preg_replace('/<note.*>The imperfect tense has been translated as.*<\/note>/U', '', $sourceXML);




// make a unique index for each note
$allNotes = array();

$sourceLength = strlen($sourceXML);
preg_match_all('/<note[^>]*>(.*)<\/note>/U', $sourceXML, $allNotes);



// we will operate only on notes where the repeats are within a proximity that is a page's length 
// (LaTeX fixfooter package has a heavy performance penalty)
$fixedfootNotes = array(array(), array());

$allNotesFrequencies = array_count_values($allNotes[0]);

$i = 0;


/*
$lsPos = 0;
while(($thePs = strpos($sourceXML, '</note>', $lsPos)) !== false){
    echo $lsPos.' nope on '.$thePs.PHP_EOL;
    $lsPos = $thePs+1;
    $i++;
    if($i>100){break;}
}
exit;
 * 
 */


// if two identical occurrences of a note is found within this number of characters, we catch it here
$proximityLimit = 1900; 

foreach($allNotes[0] as $allNotesKey=>$allNotesValue){
    $i++;
    if($allNotesFrequencies[$allNotesValue] < 2){
	// skip notes that occur only once - use normal footer call for those ones
	continue;
    }
    
    $hasProximity = false;
    $lastPos = 0;
    
    while (($thisPos = strpos($sourceXML, $allNotesValue, $lastPos))!== false) {
	if($thisPos - $lastPos < $proximityLimit){
	    $hasProximity = true;
	}
	$lastPos = $thisPos+1;
	if($lastPos >= $sourceLength){
	    break;
	}
    }
    
    if($hasProximity){
	$fixedfootNotes[0][$allNotesKey] = $allNotes[0][$allNotesKey];
	$fixedfootNotes[1][$allNotesKey] = $allNotes[1][$allNotesKey];
	
    }
    
    if($i % 50 === 0){
	//echo round(100/count($allNotes[0])*$i, 1).'% at '.(time()-$startTime).' secs'.PHP_EOL;
    }
    
}


//echo count($allNotes[0]).' vs '.count($allNotes[1]);exit;

$fixedfootNotesIndexes = array();
$nextIndex = 'AAA';

$i = 0;
$totalCount = count($fixedfootNotes[0]);
foreach($fixedfootNotes[0] as $fixedfootNotesKey=>$fixedfootNotesValue){
    $noteIndex = array_search($fixedfootNotesValue, $fixedfootNotesIndexes);
    
    if($noteIndex===false){
	$noteIndex = $nextIndex;
	$allNotesIndexes[$nextIndex] = $fixedfootNotes[1][$fixedfootNotesKey];
	$nextIndex++;
    }
    
    $sourceXML = preg_replace(
	    '/(<note[^>]*>)'.preg_quote($fixedfootNotes[1][$fixedfootNotesKey], '/').'<\/note>/U', 
	    '$1:||ln'.$noteIndex.'||:'.$fixedfootNotes[1][$fixedfootNotesKey].'</note>', 
	    $sourceXML
    );
    
    $i++;
    
    if($i % 50 === 0){
	// debugging so we know how many have been processed if the script times out
	echo round(100/$totalCount*$i, 1).'% ('.$i.' of '.$totalCount.') at '.(time()-$startTime).' seconds'.PHP_EOL;
	
    }
    
}
/*
$notesIndexSearches = array();
$notesIndexReplacements = array();

$i = 0;
foreach($allNotesIndexes as $allNotesIndexesKey=>$allNotesIndexesValue){
    $sourceXML = preg_replace('/(<note[^>]*>)'.preg_quote($allNotesIndexesValue, '/').'<\/note>/U', '$1:||fn'.$allNotesIndexesKey.'nf||:'.$allNotesIndexesValue.'</note>', $sourceXML);
    continue;
    
    $notesIndexSearches[$i] = $allNotesIndexesValue;
    $notesIndexReplacements[$i] = '<note footnote-id="'.$allNotesIndexesKey.'"'.substr($allNotesIndexesValue, 5);
    $i++;
}

//$sourceXML = str_replace($notesIndexSearches, $notesIndexReplacements, $sourceXML);
*/

// write a copy of this source code to a new file, for the next step in the processing
file_put_contents(BIBLE_IMPORT_SCRIPT_DEST_DIR.DIRECTORY_SEPARATOR.'LEB.xml', $sourceXML);


echo round(100/$totalCount*$i, 1).'% ('.$i.' of '.$totalCount.') at '.(time()-$startTime).' seconds'.PHP_EOL;






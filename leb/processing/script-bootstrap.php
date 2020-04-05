<?php

// cannot call this except through the index page
defined('BIBLE_IMPORT_SCRIPT_EXEC') or die('it fails');

ini_set('display_errors', 1);
error_reporting(E_ALL);


// Old Testament
$bookSystemNamesOT = array(
'genesis' => array(
    'systemName' => 'genesis',
    'displayName' => 'Genesis',
    'shortDisplayName' => 'Gen.'
),
'exodus' => array(
    'systemName' => 'exodus',
    'displayName' => 'Exodus',
    'shortDisplayName' => 'Exod.'
),
'leviticus' => array(
    'systemName' => 'leviticus',
    'displayName' => 'Leviticus',
    'shortDisplayName' => 'Lev.'
),
'numbers' => array(
    'systemName' => 'numbers',
    'displayName' => 'Numbers',
    'shortDisplayName' => 'Num.'
),
'deuteronomy' => array(
    'systemName' => 'deuteronomy',
    'displayName' => 'Deuteronomy',
    'shortDisplayName' => 'Deut.'
),
'joshua' => array(
    'systemName' => 'joshua',
    'displayName' => 'Joshua',
    'shortDisplayName' => 'Josh.'
),
'judges' => array(
    'systemName' => 'judges',
    'displayName' => 'Judges',
    'shortDisplayName' => 'Judg.'
),
'ruth' => array(
    'systemName' => 'ruth',
    'displayName' => 'Ruth',
    'shortDisplayName' => 'Ruth'
),
'1samuel' => array(
    'systemName' => '1samuel',
    'displayName' => '1 Samuel',
    'shortDisplayName' => '1 Sam.'
),
'2samuel' => array(
    'systemName' => '2samuel',
    'displayName' => '2 Samuel',
    'shortDisplayName' => '2 Sam.'
),
'1kings' => array(
    'systemName' => '1kings',
    'displayName' => '1 Kings',
    'shortDisplayName' => '1 Kgs'
),
'2kings' => array(
    'systemName' => '2kings',
    'displayName' => '2 Kings',
    'shortDisplayName' => '2 Kgs'
),
'1chronicles' => array(
    'systemName' => '1chronicles',
    'displayName' => '1 Chronicles',
    'shortDisplayName' => '1 Chr'
),
'2chronicles' => array(
    'systemName' => '2chronicles',
    'displayName' => '2 Chronicles',
    'shortDisplayName' => '2 Chr'
),
'ezra' => array(
    'systemName' => 'ezra',
    'displayName' => 'Ezra',
    'shortDisplayName' => 'Ezra'
),
'nehemiah' => array(
    'systemName' => 'nehemiah',
    'displayName' => 'Nehemiah',
    'shortDisplayName' => 'Neh.'
),
'esther' => array(
    'systemName' => 'esther',
    'displayName' => 'Esther',
    'shortDisplayName' => 'Esth.'
),
'job' => array(
    'systemName' => 'job',
    'displayName' => 'Job',
    'shortDisplayName' => 'Job'
),
'psalms' => array(
    'systemName' => 'psalms',
    'displayName' => 'Psalms',
    'shortDisplayName' => 'Ps.'
),
'proverbs' => array(
    'systemName' => 'proverbs',
    'displayName' => 'Proverbs',
    'shortDisplayName' => 'Prov.'
),
'ecclesiastes' => array(
    'systemName' => 'ecclesiastes',
    'displayName' => 'Ecclesiastes',
    'shortDisplayName' => 'Eccl.'
),
'songofsolomon' => array(
    'systemName' => 'songofsolomon',
    'displayName' => 'Song of Solomon',
    'shortDisplayName' => 'Song'
),
'isaiah' => array(
    'systemName' => 'isaiah',
    'displayName' => 'Isaiah',
    'shortDisplayName' => 'Isa.'
),
'jeremiah' => array(
    'systemName' => 'jeremiah',
    'displayName' => 'Jeremiah',
    'shortDisplayName' => 'Jer.'
),
'lamentations' => array(
    'systemName' => 'lamentations',
    'displayName' => 'Lamentations',
    'shortDisplayName' => 'Lam.'
),
'ezekiel' => array(
    'systemName' => 'ezekiel',
    'displayName' => 'Ezekiel',
    'shortDisplayName' => 'Ezek.'
),
'daniel' => array(
    'systemName' => 'daniel',
    'displayName' => 'Daniel',
    'shortDisplayName' => 'Dan.'
),
'hosea' => array(
    'systemName' => 'hosea',
    'displayName' => 'Hosea',
    'shortDisplayName' => 'Hos.'
),
'joel' => array(
    'systemName' => 'joel',
    'displayName' => 'Joel',
    'shortDisplayName' => 'Joel'
),
'amos' => array(
    'systemName' => 'amos',
    'displayName' => 'Amos',
    'shortDisplayName' => 'Amos'
),
'obadiah' => array(
    'systemName' => 'obadiah',
    'displayName' => 'Obadiah',
    'shortDisplayName' => 'Obad.'
),
'jonah' => array(
    'systemName' => 'jonah',
    'displayName' => 'Jonah',
    'shortDisplayName' => 'Jonah'
),
'micah' => array(
    'systemName' => 'micah',
    'displayName' => 'Micah',
    'shortDisplayName' => 'Mic.'
),
'nahum' => array(
    'systemName' => 'nahum',
    'displayName' => 'Nahum',
    'shortDisplayName' => 'Nah.'
),
'habakkuk' => array(
    'systemName' => 'habakkuk',
    'displayName' => 'Habakkuk',
    'shortDisplayName' => 'Hab.'
),
'zephaniah' => array(
    'systemName' => 'zephaniah',
    'displayName' => 'Zephaniah',
    'shortDisplayName' => 'Zeph.'
),
'haggai' => array(
    'systemName' => 'haggai',
    'displayName' => 'Haggai',
    'shortDisplayName' => 'Hag.'
),
'zechariah' => array(
    'systemName' => 'zechariah',
    'displayName' => 'Zechariah',
    'shortDisplayName' => 'Zech.'
),
'malachi' => array(
    'systemName' => 'malachi',
    'displayName' => 'Malachi',
    'shortDisplayName' => 'Mal.'
)
);



// New Testament
$bookSystemNamesNT = array(
'matthew' => array(
    'systemName' => 'matthew',
    'displayName' => 'Matthew',
    'shortDisplayName' => 'Matt.'
),
'mark' => array(
    'systemName' => 'mark',
    'displayName' => 'Mark',
    'shortDisplayName' => 'Mark'
),
'luke' => array(
    'systemName' => 'luke',
    'displayName' => 'Luke',
    'shortDisplayName' => 'Luke'
),
'john' => array(
    'systemName' => 'john',
    'displayName' => 'John',
    'shortDisplayName' => 'John'
),
'acts' => array(
    'systemName' => 'acts',
    'displayName' => 'Acts',
    'shortDisplayName' => 'Acts'
),
'romans' => array(
    'systemName' => 'romans',
    'displayName' => 'Romans',
    'shortDisplayName' => 'Rom.'
),
'1corinthians' => array(
    'systemName' => '1corinthians',
    'displayName' => '1 Corinthians',
    'shortDisplayName' => '1 Cor.'
),
'2corinthians' => array(
    'systemName' => '2corinthians',
    'displayName' => '2 Corinthians',
    'shortDisplayName' => '2 Cor.'
),
'galatians' => array(
    'systemName' => 'galatians',
    'displayName' => 'Galatians',
    'shortDisplayName' => 'Gal.'
),
'ephesians' => array(
    'systemName' => 'ephesians',
    'displayName' => 'Ephesians',
    'shortDisplayName' => 'Eph.'
),
'philippians' => array(
    'systemName' => 'philippians',
    'displayName' => 'Philippians',
    'shortDisplayName' => 'Phil.'
),
'colossians' => array(
    'systemName' => 'colossians',
    'displayName' => 'Colossians',
    'shortDisplayName' => 'Col.'
),
'1thessalonians' => array(
    'systemName' => '1thessalonians',
    'displayName' => '1 Thessalonians',
    'shortDisplayName' => '1 Thess.'
),
'2thessalonians' => array(
    'systemName' => '2thessalonians',
    'displayName' => '2 Thessalonians',
    'shortDisplayName' => '2 Thess.'
),
'1timothy' => array(
    'systemName' => '1timothy',
    'displayName' => '1 Timothy',
    'shortDisplayName' => '1 Tim.'
),
'2timothy' => array(
    'systemName' => '2timothy',
    'displayName' => '2 Timothy',
    'shortDisplayName' => '2 Tim.'
),
'titus' => array(
    'systemName' => 'titus',
    'displayName' => 'Titus',
    'shortDisplayName' => 'Tit.'
),
'philemon' => array(
    'systemName' => 'philemon',
    'displayName' => 'Philemon',
    'shortDisplayName' => 'Philem.'
),
'hebrews' => array(
    'systemName' => 'hebrews',
    'displayName' => 'Hebrews',
    'shortDisplayName' => 'Heb.'
),
'james' => array(
    'systemName' => 'james',
    'displayName' => 'James',
    'shortDisplayName' => 'Jam.'
),
'1peter' => array(
    'systemName' => '1peter',
    'displayName' => '1 Peter',
    'shortDisplayName' => '1 Pet.'
),
'2peter' => array(
    'systemName' => '2peter',
    'displayName' => '2 Peter',
    'shortDisplayName' => '2 Pet.'
),
'1john' => array(
    'systemName' => '1john',
    'displayName' => '1 John',
    'shortDisplayName' => '1 John'
),
'2john' => array(
    'systemName' => '2john',
    'displayName' => '2 John',
    'shortDisplayName' => '2 John'
),
'3john' => array(
    'systemName' => '3john',
    'displayName' => '3 John',
    'shortDisplayName' => '3 John'
),
'jude' => array(
    'systemName' => 'jude',
    'displayName' => 'Jude',
    'shortDisplayName' => 'Jude'
),
'revelation' => array(
    'systemName' => 'revelation',
    'displayName' => 'Revelation',
    'shortDisplayName' => 'Rev.'
)
);



// Apocrypha
$bookSystemNamesAP = array(
'ecclesiasticus' => array(
    'systemName' => 'ecclesiasticus',
    'displayName' => 'Ecclesiasticus',
    'shortDisplayName' => 'Ecclus.'
),
'wisdom' => array(
    'systemName' => 'wisdom',
    'displayName' => 'Wisdom',
    'shortDisplayName' => 'Wisd.'
),
'1maccabees' => array(
    'systemName' => '1maccabees',
    'displayName' => '1 Maccabees',
    'shortDisplayName' => '1 Macc.'
),
'2maccabees' => array(
    'systemName' => '2maccabees',
    'displayName' => '2 Maccabees',
    'shortDisplayName' => '2 Macc.'
),
'tobit' => array(
    'systemName' => 'tobit',
    'displayName' => 'Tobit',
    'shortDisplayName' => 'Tobit'
),
'belandthedragon' => array(
    'systemName' => 'belandthedragon',
    'displayName' => 'Beland the Dragon',
    'shortDisplayName' => 'Bel & Dr.'
),
'judith' => array(
    'systemName' => 'judith',
    'displayName' => 'Judith',
    'shortDisplayName' => 'Jud.'
),
'restofesther' => array(
    'systemName' => 'restofesther',
    'displayName' => 'Rest of Esther',
    'shortDisplayName' => 'Rest of Esth.'
),
'baruch' => array(
    'systemName' => 'baruch',
    'displayName' => 'Baruch',
    'shortDisplayName' => 'Bar.'
),
'epistleofjeremy' => array(
    'systemName' => 'epistleofjeremy',
    'displayName' => 'Epistle of Jeremy',
    'shortDisplayName' => 'Ep. of Jer.'
),
'prayerofmanasseh' => array(
    'systemName' => 'prayerofmanasseh',
    'displayName' => 'Prayer of Manasseh',
    'shortDisplayName' => 'Pr. of Man.'
),
'songofthethreechildren' => array(
    'systemName' => 'songofthethreechildren',
    'displayName' => 'Song of the Three Children',
    'shortDisplayName' => 'S. of III Ch.'
),
'storyofsusanna' => array(
    'systemName' => 'susanna',
    'displayName' => 'Susanna',
    'shortDisplayName' => 'Sus'
),
'1esdras' => array(
    'systemName' => '1esdras',
    'displayName' => '1 Esdras',
    'shortDisplayName' => '1 Esd.'
),
'2esdras' => array(
    'systemName' => '2esdras',
    'displayName' => '2 Esdras',
    'shortDisplayName' => '2 Esd.'
)
);



// Pseudepigrapha
$bookSystemNamesPS = array(
'jubilees' => array(
    'systemName' => 'jubilees',
    'displayName' => 'Jubilees',
    'shortDisplayName' => 'Jub.'
),
'enoch' => array(
    'systemName' => 'enoch',
    'displayName' => 'Enoch',
    'shortDisplayName' => 'Enoch'
),
'secondenoch' => array(
    'systemName' => 'secondenoch',
    'displayName' => 'Second Eenoch',
    'shortDisplayName' => '2 Enoch'
),
'gospelofbarnabus' => array(
    'systemName' => 'gospelofbarnabus',
    'displayName' => 'Gospel of Barnabus',
    'shortDisplayName' => 'Barn.'
),
'gospelofjudas' => array(
    'systemName' => 'gospelofjudas',
    'displayName' => 'Gospel of Judas',
    'shortDisplayName' => 'Judas'
),
'gospelofpeter' => array(
    'systemName' => 'gospelofpeter',
    'displayName' => 'Gospel of Peter',
    'shortDisplayName' => 'Pet.'
),
'3maccabees' => array(
    'systemName' => '3maccabees',
    'displayName' => '3 Maccabees',
    'shortDisplayName' => '3 Macc.'
),
'4maccabees' => array(
    'systemName' => '4maccabees',
    'displayName' => '4 Maccabees',
    'shortDisplayName' => '4 Macc.'
),
'2baruch' => array(
    'systemName' => '2baruch',
    'displayName' => '2 Baruch',
    'shortDisplayName' => '2 Bar.'
),
'3baruch' => array(
    'systemName' => '3baruch',
    'displayName' => '3 Baruch',
    'shortDisplayName' => '3 Bar.'
),
'letterofaristeas' => array(
    'systemName' => 'letterofaristeas',
    'displayName' => 'Letter of Aristeas',
    'shortDisplayName' => 'Arist.'
),
'lifeofadamandeve' => array(
    'systemName' => 'lifeofadamandeve',
    'displayName' => 'Life of Adam and Eve',
    'shortDisplayName' => 'A. & E.'
),
'ascensionofisaiah' => array(
    'systemName' => 'ascensionofisaiah',
    'displayName' => 'Ascension of Isaiah',
    'shortDisplayName' => 'As. of Isa.'
),
'psalmsofsolomon' => array(
    'systemName' => 'psalmsofsolomon',
    'displayName' => 'Psalms of Solomon',
    'shortDisplayName' => 'Ps. of Sol.'
),
'sibyllineoracles' => array(
    'systemName' => 'sibyllineoracles',
    'displayName' => 'Sibylline Oracles',
    'shortDisplayName' => 'Sib.'
),
'testamentsofthetwelvepatriarchs' => array(
    'systemName' => 'twelvepatriarchs',
    'displayName' => 'Testaments of the Twelve Patriarchs',
    'shortDisplayName' => 'Patr.'
)
);










































// 20200211 - https://stackoverflow.com/questions/775904/how-can-i-change-the-name-of-an-element-in-dom
function changeDOMElementNodeName($node, $name) {
    $newnode = $node->ownerDocument->createElement($name);
    foreach ($node->childNodes as $child){
        $child = $node->ownerDocument->importNode($child, true);
        $newnode->appendChild($child);
    }
    foreach ($node->attributes as $attrName => $attrNode) {
        $newnode->setAttribute($attrName, $attrNode);
    }
    $node->parentNode->replaceChild($newnode, $node);
    return $newnode;
}




































































/*

$bookSystemNamesOT = array(
    'genesis', 'exodus', 'leviticus', 'numbers', 'deuteronomy', 'joshua', 'judges', 'ruth', '1samuel', '2samuel', '1kings', '2kings',
    '1chronicles', '2chronicles', 'ezra', 'nehemiah', 'esther', 'job', 'psalms', 'proverbs', 'ecclesiastes', 'songofsolomon', 'isaiah',
    'jeremiah', 'lamentations', 'ezekiel', 'daniel', 'hosea', 'joel', 'amos', 'obadiah', 'jonah', 'micah', 'nahum', 'habakkuk', 'zephaniah',
    'haggai', 'zechariah', 'malachi'
);

$bookSystemNamesNT = array(
    'matthew', 'mark', 'luke', 'john', 'acts', 'romans', '1corinthians', '2corinthians', 'galatians', 'ephesians', 'philippians', 'colossians',
    '1thessalonians', '2thessalonians', '1timothy', '2timothy', 'titus', 'philemon', 'hebrews', 'james', '1peter', '2peter', '1john', '2john',
    '3john', 'jude', 'revelation'
);

$bookSystemNamesAP = array(
    'ecclesiasticus', 'wisdom', '1maccabees', '2maccabees', 'tobit', 'belandthedragon', 'judith', 'restofesther',
    'baruch', 'epistleofjeremy', 'prayerofmanasseh', 'songofthethreechildren', 'storyofsusanna', '1esdras', '2esdras'
);

$bookSystemNamesPS = array(
    'jubilees', 'enoch', 'secondenoch', 'gospelofbarnabus', 'gospelofjudas', 'gospelofpeter', '3maccabees', '4maccabees', '2baruch', '3baruch',
    'letterofaristeas', 'lifeofadamandeve', 'ascensionofisaiah', 'psalmsofsolomon', 'sibyllineoracles', 'testamentsofthetwelvepatriarchs'
);


*/




/*
echo '<pre>';
echo '$bookSystemNamesOT = array('."\n";
foreach($bookSystemNamesOT as $k=>$v){
    
    if(is_numeric(substr($v, 0, 1))){
	$ucName = substr($v, 0, 1).' '.ucfirst(substr($v, 1));
    }else{
	$ucName = ucfirst($v);
    }
    
    if(is_numeric(substr($v, 0, 1))){
	$ucNameShort = substr($v, 0, 1).' '.ucfirst(substr($v, 1, 3)).'.';
    }else{
	$ucNameShort = ucfirst(substr($v, 0, 3)).'.';
    }
    
    echo 
"'{$v}' => array(
    'systemName' => '{$v}',
    'displayName' => '{$ucName}',
    'shortDisplayName' => '{$ucNameShort}'
),
";
}
echo ');'."\n";
echo '</pre>';





echo '<pre>';
echo '$bookSystemNamesNT = array('."\n";
foreach($bookSystemNamesNT as $k=>$v){
    
    if(is_numeric(substr($v, 0, 1))){
	$ucName = substr($v, 0, 1).' '.ucfirst(substr($v, 1));
    }else{
	$ucName = ucfirst($v);
    }
    
    if(is_numeric(substr($v, 0, 1))){
	$ucNameShort = substr($v, 0, 1).' '.ucfirst(substr($v, 1, 3)).'.';
    }else{
	$ucNameShort = ucfirst(substr($v, 0, 3)).'.';
    }
    
    echo 
"'{$v}' => array(
    'systemName' => '{$v}',
    'displayName' => '{$ucName}',
    'shortDisplayName' => '{$ucNameShort}'
),
";
}
echo ');'."\n";
echo '</pre>';







echo '<pre>';
echo '$bookSystemNamesAP = array('."\n";
foreach($bookSystemNamesAP as $k=>$v){
    
    if(is_numeric(substr($v, 0, 1))){
	$ucName = substr($v, 0, 1).' '.ucfirst(substr($v, 1));
    }else{
	$ucName = ucfirst($v);
    }
    
    if(is_numeric(substr($v, 0, 1))){
	$ucNameShort = substr($v, 0, 1).' '.ucfirst(substr($v, 1, 3)).'.';
    }else{
	$ucNameShort = ucfirst(substr($v, 0, 3)).'.';
    }
    
    echo 
"'{$v}' => array(
    'systemName' => '{$v}',
    'displayName' => '{$ucName}',
    'shortDisplayName' => '{$ucNameShort}'
),
";
}
echo ');'."\n";
echo '</pre>';






echo '<pre>';
echo '$bookSystemNamesDC = array('."\n";
foreach($bookSystemNamesDC as $k=>$v){
    
    if(is_numeric(substr($v, 0, 1))){
	$ucName = substr($v, 0, 1).' '.ucfirst(substr($v, 1));
    }else{
	$ucName = ucfirst($v);
    }
    
    if(is_numeric(substr($v, 0, 1))){
	$ucNameShort = substr($v, 0, 1).' '.ucfirst(substr($v, 1, 3)).'.';
    }else{
	$ucNameShort = ucfirst(substr($v, 0, 3)).'.';
    }
    
    echo 
"'{$v}' => array(
    'systemName' => '{$v}',
    'displayName' => '{$ucName}',
    'shortDisplayName' => '{$ucNameShort}'
),
";
}
echo ');'."\n";
echo '</pre>';





 * 
 * 
 */




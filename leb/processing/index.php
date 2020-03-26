<?php

// disabled by default
//exit('Disabled for safety on line '.__LINE__.' of '.__FILE__);

set_time_limit(60*10);


define('BIBLE_IMPORT_SCRIPT_EXEC', 1);
require_once('script-bootstrap.php');


$thisDir = __DIR__;
$scriptsDir = $thisDir.DIRECTORY_SEPARATOR.'scripts';
$sourcesDir = $thisDir.DIRECTORY_SEPARATOR.'sources';

function reset_script($cmdHandle=null){
    global $scriptsConfig, $sourcesDir;
    
    
    if($cmdHandle===-1){
	// destroy all the processes
	$targetDirs = [];
	array_walk($scriptsConfig, function($config){
	    $targetDirs[] = $config['handle'];
	});
	while($file = readdir($sourcesDir)){
	    if(in_array($file, $targetDirs) && is_dir($sourcesDir.DIRECTORY_SEPARATOR.$file)){
		rmdir($dirname);
	    }
	}
    }else{
	$destroying = false;
	foreach($scriptsConfig as $config){
	    if($config['handle'] === $cmdHandle){
		$destroying = true;
	    }
	    if($destroying){
		if(is_dir($sourcesDir.DIRECTORY_SEPARATOR.$cmdHandle)){
		    @rmdir($sourcesDir.DIRECTORY_SEPARATOR.$cmdHandle);
		}
	    }
	}
    }
}





$scriptsConfig = array(
    /*
    [
	'title' => 'Generate Footnote ID\'s',
	'handle' => '1.indexnotes',
	'status' => 0,
	'description' => 'Some of the footnotes are repeated a lot. It is best if we can consolidate every reference to a footnote into a single footnote, per page. '
	. 'To do this, we need to assign a unique id for each footnote pattern, and every repeated occurence of that footnote pattern shall use the id as the handle.'
    ],
    [
	'title' => 'Standardise XML',
	'handle' => '1.standardise',
	'status' => 0,
	'description' => 'Loop through all books in the XML file and convert the data into a standardised format. '
	. 'Each book with chapters and verses is converted into a PHP class and is stored in a separate file. '
	. 'This first step will enable all subsequent operations to be reused for other translations of the bible in future. '
	. 'It will also reduce the server load for subsequent processes - otherwise the server would crash when performing complex operations!'
    ],
    */
    [
	'title' => 'Render LaTeX files from SQLite3 DB',
	'handle' => '2.renderlatex',
	'status' => 0,
	'description' => 'Read the SQLLite Database and generate .tex files. '
	. 'Each book with chapters and verses is converted into a PHP class and is stored in a separate file. '
    ],
    /*
    [
	'title' => '',
	'handle' => '',
	'status' => 0,
	'description' => ''
    ],
    */
);


//// find out if the scripts have been run yet
//while($file = readdir($sourcesDir)){
//    array_walk($scriptsConfig, function(&$config, $configIndex){
//	if($config['handle']===$file){
//	    // this script has already been processed
//	    $config['status'] = 1;
//	}
//    });
//}


// let's perform an action
if(array_key_exists('cmd', $_GET)){
    
    // not allowed to traverse directories
    $cmd = $_GET['cmd'];
    while(strpos($cmd, '..')>-1){
	$cmd = str_replace('..', '.', $cmd);
    }
    while(strpos($cmd, DIRECTORY_SEPARATOR)>-1){
	$cmd = str_replace(DIRECTORY_SEPARATOR, '', $cmd);
    }
    while(strpos($cmd, '/')>-1){
	$cmd = str_replace('/', '', $cmd);
    }
    
    $hasFound = false;
    foreach($scriptsConfig as $configIndex=>$config){
	if($config['handle']===$cmd){
	    $hasFound = true;
	}
	if($hasFound){
	    // destroy all changes that happened after this process
	    reset_script($cmd);
	}
    }
    
    if($hasFound){
	// now let's excute this script
	if(!is_dir($sourcesDir.DIRECTORY_SEPARATOR.$cmd)){
	    mkdir($sourcesDir.DIRECTORY_SEPARATOR.$cmd);
	}
	
	require($scriptsDir.DIRECTORY_SEPARATOR.$cmd.DIRECTORY_SEPARATOR.'index.php');
	
	require('script-success.html');
	exit;
	
    }
}



?>
<!DOCTYPE html>
<html>
  <head>
    <title>Import LEB from XML to LaTeX</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript">
    function runscript(scriptname){
	if(confirm('Are you sure? (This will cause irreversible changes!)')){
	    window.location='?cmd='+scriptname;
	    return;
	}
    }
    </script>
  </head>
  <body>
    <h1>Importing LEB Bible Sources</h1>
    
    <p>The LEB is given free for use as an XML file. In order to publish the XML file in the form of a paper book,
    it needs to be converted into LaTeX markup and then rendered by a LaTeX engine into a pdf document.</p>
    
    <p>These scripts will perform the conversions in a number of series and steps, each step performing a dedicated
    process upon the data until eventually it arrives in a format readable by a LaTeX engine, and final layout
    tweaks can then be done by hand.</p>
    
    <p><strong>Be Careful! .. when a script is run, it will destroy all scripts that must be run after it!</strong></p>
    <p>(In other words, if you run script 3, then all scripts of 4 and above will need to be run again - there is no way
      to undo the changes that you do here)</p>
    
    <ul>
	<?php 
	    foreach($scriptsConfig as $config){
		switch($config['status']){
		    case '0':
			$cssClass="ready";
			break;
		    case '1':
			$cssClass="processed";
			break;
		}
	?>
		
	    <li class="<?php echo $cssClass ?>">
	      <h3><?php echo $config['title'] ?></h3>
	      <p><?php echo htmlentities($config['description']) ?></p>
	      <p><a href="javascript:runscript('<?php echo $config['handle'] ?>')">run</a></p>
	    </li>
	
      <?php } ?>
      
    </ul>
    
  </body>
</html>

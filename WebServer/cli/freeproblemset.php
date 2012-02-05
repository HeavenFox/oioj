<?php
if (!isset($_SERVER['argc']))
{
	die('This script can only be run in command line mode');
}

if ($_SERVER['argc'] != 2)
{
	fprintf(STDOUT,'Usage: php freeproblemset.php FILENAME');
	die();
}
set_time_limit(0);

require dirname(__FILE__).'/../init.php';

import('Settings');
import('OIOJ');
OIOJ::InitTemplate();

fprintf(STDOUT,"Free Problem Set Import Utility\n");
fprintf(STDOUT,"Connecting to database...");
OIOJ::InitDatabase();
fprintf(STDOUT,"done\n");

import('FreeProblemSet');

set_error_handler(function($no,$str){
	fprintf(STDOUT,"There's an error parsing your file. Script will now abort.\n");
	die();
},E_ALL & ~E_NOTICE);

$fp = new FreeProblemSet($_SERVER['argv'][1]);
$fp->parse(function($problem)
{
	fprintf(STDOUT,"Finished parsing problem {$problem->title}\n");
});
$fp->queueForDispatch(function($problem)
{
	fprintf(STDOUT,"Finished queuing problem {$problem->title} (#{$problem->id}) for dispatch\n");
});
fprintf(STDOUT,"Adding distribution cronjob...\n");
import('Cronjob');
Cronjob::AddJob('ProblemDistribution','dispatch',array(), 0, 3);
fprintf(STDOUT,"Done\n");
?>
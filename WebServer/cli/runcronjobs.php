<?php
if (!isset($_SERVER['argc']))
{
	die('This script can only be run in command line mode');
}

if ($_SERVER['argc'] != 2)
{
	fprintf(STDOUT,'Usage: php runcronjob.php QOS_LEVEL');
	die();
}

set_time_limit(0);

require dirname(__FILE__).'/../init.php';
import('OIOJ');

OIOJ::InitDatabase();

import('Cronjob');

Cronjob::RunScheduled(intval($_SERVER['argv'][1]));


?>
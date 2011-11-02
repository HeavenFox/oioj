<?php
/*
if (!isset($_SERVER['argc']))
{
	die('This script can only be run in command line mode');
}
*/
set_time_limit(0);

require '../init.php';
import('OIOJ');
OIOJ::InitTemplate();
OIOJ::InitDatabase();

import('FreeProblemSet');

/*
$a = new XMLReader();
$a->open('vjs.xml');
while ($a->read())
{
	echo $a->nodeType . '<br />';
	echo $a->name . '<br />';
	echo $a->value. '<br />';
}
*/
$fp = new FreeProblemSet('vjs.xml');
$fp->parse();
$fp->dispatch();


?>
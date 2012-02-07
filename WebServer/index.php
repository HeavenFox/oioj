<?php
require_once 'init.php';

import('OIOJ');
import('IO');
import('Settings');

// These modules are legimate
$availableModules = loadData('AvailableModules');

// These modules should be autoloaded
$autoloadModules = loadData('AutoloadModules');

OIOJ::InitTemplate();

try
{
	OIOJ::InitDatabase();
}
catch (Exception $e)
{
	// DB error shall not be 
	OIOJ::$template->assign('message', 'Database Server Downtime. Please check back later.');
	OIOJ::$template->display('error.tpl');
	die();
}

foreach ($autoloadModules as $module)
{
	require_once MODULE_DIR . $module . '.php';
	$mod = new $module;
	$mod->autoload();
}

$mod = IO::GET('mod',null);
if (!isset($availableModules[$mod]))
{
	$mod = 'HomeModule';
}else
{
	$mod = $availableModules[$mod];
}

require_once MODULE_DIR . $mod . '.php';
$module = new $mod;
try
{
	$module->run();
}/*
catch (PDOException $e)
{
	// DB error shall not be displayed
	error_log($e->getMessage().var_export(debug_backtrace(),true));
	OIOJ::$template->assign('message', 'Database Error');
	OIOJ::$template->display('error.tpl');
}*/
catch (Exception $e)
{
	// Catch-all
	if (IO::GET('ajax'))
	{
		die(json_encode(array('error' => $e->getMessage())));
	}else
	{
		OIOJ::$template->assign('message', $e->getMessage());
		OIOJ::$template->display('error.tpl');
	}
}

?>

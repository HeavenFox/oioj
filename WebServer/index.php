<?php
require_once 'init.php';

import('OIOJ');
import('IO');

// These modules are legimate
$availableModules = array(
	'user' => 'UserModule',
	'records' => 'RecordsModule',
	'submit' => 'SubmitModule',
	
	'problemlist' => 'ProblemListModule',
	'problem' => 'ProblemModule',
	'admin_problem' => 'AdminManageProblemModule',
	
	'contestlist' => 'ContestListModule'
);

// These modules should be autoloaded
$autoloadModules = array('UserModule');

OIOJ::InitTemplate();
OIOJ::InitDatabase();

foreach ($autoloadModules as $module)
{
	require_once MODULE_DIR . $module . '.php';
	$mod = new $module;
	$mod->autoload();
}

if (isset($_GET['mod']) && isset($availableModules[$_GET['mod']]))
{
	require_once MODULE_DIR . $availableModules[$_GET['mod']] . '.php';
	$module = new $availableModules[$_GET['mod']];
	try
	{
		$module->run();
	}catch (Exception $e)
	{
		// Catch-all
		OIOJ::$template->assign('message', $e->getMessage());
		OIOJ::$template->display('error.tpl');
	}
}
else
{
	OIOJ::$template->display('index.tpl');
}

?>

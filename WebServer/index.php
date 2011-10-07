<?php
require_once 'init.php';

import('OIOJ');
import('IO');

// These modules are legimate
$availableModules = array('user' => 'UserModule', 'records' => 'RecordsModule', 'submit' => 'SubmitModule', 'judge' => 'JudgeModule', 'problemlist' => 'ProblemListModule', 'problem' => 'ProblemModule');

// These modules should be autoloaded
$autoloadModules = array('UserModule');

OIOJ::InitTemplate();

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
	$module->run();
}
else
{
	OIOJ::$template->display('index.tpl');
}

?>

<?php
require_once 'init.php';

import('OIOJ');
import('IO');
import('Settings');

// These modules are legimate
$availableModules = array(
	'user' => 'UserModule',
	'records' => 'RecordsModule',
	'submit' => 'SubmitModule',
	
	'problemlist' => 'ProblemListModule',
	'problem' => 'ProblemModule',
	'admin_problem' => 'AdminManageProblemModule',
	
	'contestlist' => 'ContestListModule',
	'contest' => 'ContestModule',
	'contestproblem' => 'ContestProblemModule',
	'contestcp' => 'ContestCPModule',
	'admin_contest' => 'AdminManageContestModule',
		
	'admin_judgeserver' => 'AdminManageJudgeServerModule',
	'admin_user' => 'AdminManageUserModule',
		
	'admin_home' => 'AdminHomeModule'
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
}catch (Exception $e)
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

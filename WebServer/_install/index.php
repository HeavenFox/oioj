<?php
define('INSTALL_ROOT',dirname(__FILE__) . '/');

file_exists(INSTALL_ROOT.'installer.lock') and die();

require INSTALL_ROOT . '../init.php';

import('IO');

require_once INSTALL_ROOT . 'steps/Step.php';

$steps = array('Welcome','SystemCheck','DBSettings','LoadData','CreateAdmin','Done');
$stepTitles = array('Welcome','System Check','Database','Load Data','Create Admin','Done');

$curStep = IO::GET('step',0,'intval');
$prevStep = $curStep > 0 ? $curStep-1 : null;

$next = null;

$stepObject = null;

if ($prevStep !== null)
{
	$cls = 'Step'.$steps[$prevStep];
	require_once INSTALL_ROOT . 'steps/'.$cls.'.php';
	$stepObject = new $cls;
	if (!$stepObject->processData())
	{
		// remain in previous class
		$next = $curStep;
		$curStep = $prevStep;
	}
}

// Process successful
if ($curStep !== $prevStep)
{
	$cls = 'Step'.$steps[$curStep];
	require_once INSTALL_ROOT . 'steps/'.$cls.'.php';
	$stepObject = new $cls;
	if ($stepObject->prepareStep())
	{
		$next = ($curStep < count($steps)-1) ? $curStep + 1 : null;
	}
}

function renderSidebar()
{
	global $stepTitles, $curStep;
	echo '<ul>';
	for ($i = 0; $i < $curStep; $i++)
	{
		echo '<li class="prev">'.$stepTitles[$i].'</li>';
	}
	echo '<li class="cur">'.$stepTitles[$curStep].'</li>';
	for ($i = $curStep + 1; $i < count($stepTitles); $i++)
	{
		echo '<li class="next">'.$stepTitles[$i].'</li>';
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>OIOJ - Installer</title>
<link rel='stylesheet' href='style.css' />
<?php $stepObject->renderHeader(); ?>
</head>

<body>
<div id='bg'>
	<div id='sidebar'>
	<?php renderSidebar(); ?>
	</div>
	<form method="post" action="index.php?step=<?php echo $next; ?>">
	<div id='main'>
		<?php $stepObject->renderStep(); ?>
		<?php if ($next) { ?>
		<div id='nextbutton'><input type="submit" value="Next" /></div>
		<?php } ?>
	</div>
	</form>
</div>
</body>
</html>

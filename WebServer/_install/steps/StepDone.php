<?php
class StepDone extends Step
{
	public function renderStep()
	{
		echo '<h1>Thank You!</h1>';
		echo '<p>Your OIOJ has been installed</p>';
		if ($fp = @fopen(INSTALL_ROOT.'installer.lock','w'))
		{
			fwrite($fp,'NEVER DELETE THIS FILE! DOING SO WILL ENABLE INSTALLER THUS PUTING SITE IN GRAVE DANGER!');
			fclose($fp);
			echo '<p>Installer has been locked. However, as a precaution, we strongly recommend that you remove the entire _install directory</p>';
		}else
		{
			echo '<p style="color: red; font-weight: bold;">FAILED TO LOCK INSTALLER!!<br />YOU MUST REMOVE _install FOLDER COMPLETELY FROM SERVER, OR YOUR SITE IS IN GRAVE DANGER!</p>';
		}
	}
}
?>
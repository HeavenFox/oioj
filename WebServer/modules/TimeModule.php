<?php
IN_OIOJ || die('Forbidden');

class TimeModule
{
	public function autoload()
	{
		// Set default timezone
		date_default_timezone_set(Settings::Get('default_timezone'));
	}
	
	
	public function run()
	{
		switch (IO::GET('act'))
		{
		case 'tzcomplete':
			$this->completeTimeZone();
			break;
		}
	}
	
	
	public function completeTimeZone()
	{
		echo json_encode(array_filter(timezone_identifiers_list(),function($val){
			return strpos($val, IO::REQUEST('term')) !== false;
		}));
	}
}
?>
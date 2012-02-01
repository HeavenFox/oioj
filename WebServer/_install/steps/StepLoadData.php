<?php
class StepLoadData extends Step
{
	public function prepareStep()
	{
		import('database.Database');
		import('OIOJ');
		OIOJ::InitDatabase();
		
		$queries = $this->splitQuery(INSTALL_ROOT.'oioj.sql');
		
		foreach($queries as $v)
		{
			Database::Get()->exec($v);
		}
		
		return true;
	}
	
	public function processData()
	{
		return true;
	}
	
	public function renderStep()
	{
		echo 'Data successfully imported.';
	}
	
	private function splitQuery($file)
	{
		$queries = array();
		$templine = '';
		$DELIMITER = ';';
		$D_LEN = 1;
		
		$lines = file($file);
		
		foreach ($lines as $line_num => $line)
		{
			$line = trim($line);
			
			if(substr($line, 0, 9) == 'DELIMITER')
			{
				$DELIMITER = str_replace('DELIMITER ', '', $line);
				$D_LEN = strlen($DELIMITER);
				continue;
			}
			
			if(substr($line, 0, 2) != '--' && $line != '')
			{
				$templine .= $line;
				
				if(substr($line, -$D_LEN, $D_LEN) == $DELIMITER)
				{ 
					$queries[] = rtrim($templine, $DELIMITER);
					$templine = '';
				} else {
					$templine .= "\n";
				}
			}
		}
		
		return $queries;
	}
	
	public function renderHeader()
	{
	}
}
?>
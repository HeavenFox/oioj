<?php
class StepLoadData extends Step
{
	const COOKIE_SECRET_LEN = 10;
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
		
		// Cookie Secret
		$alphaNum = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		
		$cookieSecret = '';
		
		
		for ($i=0;$i<self::COOKIE_SECRET_LEN;$i++)
		{
			$cookieSecret .= substr($alphaNum,mt_rand(0,61),1);
		}
		
		$cookieSecretFile = <<<EOF
<?php
// This is token used to check authenticity of cookie and other user-provided data. Keep it secret.
\$CookieSecret = '{$cookieSecret}';
?>

EOF;
		
		file_put_contents(VAR_DIR.'CookieSecret.php',$cookieSecretFile);
		
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
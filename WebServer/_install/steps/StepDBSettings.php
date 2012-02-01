<?php
import('SmartyForm');
class StepDBSettings
{
	private $form;
	private $error = '';
	
	public function processData()
	{
		$this->form = $this->getForm();
		$this->form->gatherFromPOST();
		$fileTemplate = <<<'EOF'
<?php
IN_OIOJ || die('Forbidden');
class Config
{
	static $MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => '{host}',
		'port' => '{port}',
		'username' => '{username}',
		'password' => '{password}',
		'database' => '{db}'
	);
}
?>
EOF;
		
		foreach (array('host','db','username','password','port') as $v)
		{
			$fileTemplate = str_replace('{'.$v.'}',$this->form->get($v)->data,$fileTemplate);
		}
		
		file_put_contents(ROOT.'config.php',$fileTemplate);
		
		import('database.Database');
		import('OIOJ');
		try
		{
			OIOJ::InitDatabase();
		}catch (Exception $e)
		{
			$this->error = 'Unable to connect. Please double check the information provided.';
			return false;
		}
		return true;
	}
	
	public function prepareStep()
	{
		$this->form = $this->getForm();
		return true;
	}
	
	public function renderStep()
	{
		if ($this->error)
		{
			echo "<div class='error'>{$this->error}</div>";
		}
		echo '<table>';
		echo $this->form->getFormHTML('table');
		echo '</table>';
	}
	
	public function getForm()
	{
		$form = new SmartyForm;
		$form->add(new SF_TextField('id','host','label','DB Host','data','localhost'));
		$form->add(new SF_Number('id','port','label','Port','data',3306));
		$form->add(new SF_TextField('id','username','label','Username','data','root'));
		$form->add(new SF_TextField('id','password','label','Password'));
		$form->add(new SF_TextField('id','db','label','DB name','data','oioj'));
		
		return $form;
	}
	
	public function renderHeader()
	{
	}
}
?>
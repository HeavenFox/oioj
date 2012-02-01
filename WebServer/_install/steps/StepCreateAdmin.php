<?php
import('SmartyForm');
class StepCreateAdmin extends Step
{
	public function prepareStep()
	{
		return true;
	}
	
	private function getForm()
	{
		$form = new SmartyForm();
		$form->add(new SF_TextField('id','username','label','Username'));
		$form->add(new SF_Password('id','password','label','Password'));
		$form->add(new SF_EMail('id','email','label','Email'));
		return $form;
	}
	
	public function processData()
	{
		import('database.Database');
		import('OIOJ');
		OIOJ::InitDatabase();
		
		import('User');
		
		$form = $this->getForm();
		$form->gatherFromPOST();
		
		$user = new User();
		$user->username = $form->get('username')->data;
		$user->password = $form->get('password')->data;
		$user->email = $form->get('email')->data;
		
		$user->add();
		
		Database::Get()->exec('INSERT INTO `oj_user_acl` (`uid`,`key`,`permission`) VALUES ('.$user->id.',\'omnipotent\',10)');
		
		return true;
	}
	
	public function renderStep()
	{
		echo '<table>';
		echo $this->getForm()->getFormHTML('table');
		echo '</table><small>This user shall be omnipotent</small>';
	}
	
	public function renderHeader()
	{
	}
}
?>
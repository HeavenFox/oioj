<?php
import('SmartyForm');

class SettingsForm extends SmartyForm
{
	public function gatherFromSettings()
	{
		$settings = Settings::GetAll();
		
		foreach ($settings as $k => $v)
		{
			if ($this->exists($k))
			{
				$this->get($k)->data = $v;
			}
		}
	}
	
	public function saveToSettings()
	{
		foreach ($this->elements as $k=>$v)
		{
			if ($v->data !== null)
			{
				Settings::Set($k,$v->data);
			}
		}
	}
}

class SF_ServerBrowser extends SF_TextField
{
	/**
	 * Which kind of object to look for
	 * directory, file, both
	 *
	 * @var string
	 */
	public $object = 'both';
	
	public function html()
	{
		$this->setDefaultAttributes();
		return "<input " . $this->generateAtrributes() . " /><a href='javascript:;' onclick='window.open(\"index.php?mod=admin_commons&act=browseserver&object=".$this->object."&callback=".urlencode("function(data){this.setVar('".$this->getHTMLID()."',data)}")."\")'>[Browse]</a>" . $this->appendedErrorMessage();
	}
}
?>
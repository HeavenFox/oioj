<?php
require_once 'Smarty.class.php';

class Template extends Smarty
{
	public function __construct()
	{
		parent::__construct();
		$this->template_dir = ROOT . 'templates' . DIRECTORY_SEPARATOR . 'sources' . DIRECTORY_SEPARATOR;
		$this->compile_dir = ROOT. 'templates' . DIRECTORY_SEPARATOR . 'compile'. DIRECTORY_SEPARATOR;
		$this->cache_dir = ROOT. 'templates' . DIRECTORY_SEPARATOR.'cache'.DIRECTORY_SEPARATOR;
		$this->config_dir = ROOT . 'templates' . DIRECTORY_SEPARATOR.'configs'.DIRECTORY_SEPARATOR;
		//$this->caching = Smarty::CACHING_LIFETIME_CURRENT;
		
		$this->addPluginsDir(SMARTY_DIR.DIRECTORY_SEPARATOR.'oiojplugins');
	}
}

?>
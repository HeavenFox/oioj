<?php
IN_OIOJ || die('Forbidden');
class Config
{
	static $MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'port' => '3306',
		'username' => 'root',
		'password' => 'root',
		'database' => 'oioj_new'
	);
}
?>
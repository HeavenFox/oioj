<?php
IN_OIOJ || die('Forbidden');
class Config
{
	static $MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => '127.0.0.1',
		'username' => 'root',
		'password' => 'root',
		'database' => 'oioj'
	);
}
?>
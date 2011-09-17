<?php
IN_OIOJ || die('Forbidden');
class Config
{
	static $MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'root',
		'database' => 'oioj'
	);
	
	// This is the passphrase that authenticate communication between judge server & web server
	static $Passphrase = 'nVM)[6Zm@5wBU@My>uQ(tU76Z=6:.d}Mx>8cZ44K!Wyd<Hu*aSn{3~vg,~pM>tmf';
}
?>
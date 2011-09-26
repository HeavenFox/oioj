<?php
IN_OIOJ || die('Forbidden');
class Config
{
	static $MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'oioj'
	);
	
	// This is the passphrase that authenticates communication between judge server & web server
	// This should be kept secret otherwise security will be seriously compromised
	static $Token = 'nVM)[6Zm@5wBU@My>uQ(tU76Z=6:.d}Mx>8cZ44K!Wyd<Hu*aSn{3~vg,~pM>tmf';
}
?>
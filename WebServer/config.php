<?php
IN_OIOJ || die('Forbidden');
class Config
{
	const MySQL = array(
		'driver' => 'pdo_mysql',
		'host' => 'localhost',
		'username' => 'root',
		'password' => 'zhujingsi',
		'database' => 'oioj'
	);
	
	// This is the passphrase that authenticate communication between judge server & web server
	const Passphrase = 'nVM)[6Zm@5wBU@My>uQ(tU76Z=6:.d}Mx>8cZ44K!Wyd<Hu*aSn{3~vg,~pM>tmf';
}
?>
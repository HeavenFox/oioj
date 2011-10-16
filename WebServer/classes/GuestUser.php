<?php
import('User');

class GuestUser extends User
{
	public $id = 0;
	public $username = 'Guest';
	
	public function add()
	{
		throw new Exception('Trying to add guest');
	}
}
?>
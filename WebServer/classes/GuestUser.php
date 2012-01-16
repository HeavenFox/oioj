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
	
	public function ableTo($key)
	{
		// Cache Guest ACL, since they're expected to constitute a good portion
		if (!($acl = Cache::MemGet('guest-acl')))
		{
			Cache::MemSet('guest-acl',$acl = $this->getACL());
		}
		return isset($acl[$key]) && $acl[$key] > 0;
	}
	
	public function unableTo($key)
	{
		// Cache Guest ACL, since they're expected to constitute a good portion
		if (!($acl = Cache::MemGet('guest-acl')))
		{
			Cache::MemSet('guest-acl',$acl = $this->getACL());
		}
		return isset($acl[$key]) && $acl[$key] < 0;
	}
}
?>
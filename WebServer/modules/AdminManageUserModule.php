<?php
defined('IN_OIOJ') || die('Forbidden');

import('User');

class PermTreeNode
{
	public $key;
	public $level;
	public $children = array();
	
	public function __construct($key,$level)
	{
		$this->key = $key;
		$this->level = $level;
	}
}

class AdminManageUserModule
{
	public function run()
	{
		$user = User::GetCurrent();
		if (!($user->ableTo('manage_user') || ($user->ableTo('admin_cp') && !$user->unableTo('manage_user'))))
		{
			throw new PermissionException();
		}
		switch (IO::GET('act'))
		{
		case 'add':
			$this->add();
			break;
		case 'edit':
			$this->edit();
			break;
		case 'save':
			$this->save();
			break;
		case 'loginas':
			$this->loginAs();
			break;
		case 'invitation':
			$this->invitation();
			break;
		case 'permissions':
			$this->permissions();
			break;
		case 'tagpermissions':
			$this->tagPermissions();
			break;
		case 'doeditperm':
			$this->ajaxEditPermissionValue();
			break;
		case 'tagproperties':
			$this->ajaxTagProperties();
			break;
		default:
			$this->listUsers();
		}
		
	}
	
	public function listUsers()
	{
		import('RecordSelector');
		$sel = new RecordSelector('User');
		$page = IO::REQUEST('page',1,'intval');
		
		$maxPage = 1;
		$users = $sel->findAtPage($page, 10, $maxPage, array('id','username'));
		
		OIOJ::$template->assign('page_cur', $page);
		OIOJ::$template->assign('page_max', $maxPage);
		OIOJ::$template->assign('users', $users);
		OIOJ::$template->display('admin_user_list.tpl');
	}
	
	public function ajaxTagProperties()
	{
		if (IO::POST('state',0,'intval'))
		{
			$stmt = Database::Get()->prepare('INSERT INTO `oj_usertag_properties` (`tid`,`key`) VALUES (?,?)');
		}
		else
		{
			$stmt = Database::Get()->prepare('DELETE FROM `oj_usertag_properties` WHERE `tid` = ? AND `key` = ?');
		}
		$stmt->execute(array(IO::POST('tid',null,'intval'),IO::POST('key')));
	}
	
	public function ajaxEditPermissionValue()
	{
		function process($id,$key,$table,$column)
		{
			$sql = null;
			if (IO::POST('old',0,'intval'))
			{
				if ($newperm = IO::POST('new',0,'intval'))
				{
					$sql = "UPDATE `{$table}` SET `permission` = {$newperm} WHERE `{$column}` = {$id} AND `key` = ?";
				}else
				{
					$sql = "DELETE FROM `{$table}` SET WHERE `{$column}` = {$id} AND `key` = ?";
				}
			}else
			{
				if ($newperm = IO::POST('new',0,'intval'))
				{
					$sql = "INSERT INTO `{$table}` (`{$column}`,`key`,`permission`) VALUES ({$id},?,{$newperm})";
				}
			}
			if ($sql)
			{
				$stmt = Database::Get()->prepare($sql);
				$stmt->execute(array($key));
			}
		}
		if ($id = IO::POST('uid',null,'intval'))
		{
			process($id,IO::POST('key'),'oj_user_acl','uid');
		}
		else if ($id = IO::POST('tid',null,'intval'))
		{
			process($id,IO::POST('key'),'oj_tag_acl','tid');
		}
		echo '{}';
	}
	
	public function permissions()
	{
		// We have a forest in $roots
		$roots = $this->generatePermissionTree();
		
		$table = $this->travelTree($roots);
		// Generate tags
		$uid = IO::GET('uid',0,'intval');
		
		$user = new User($uid);
		
		$user->fetch(array('username'));
		$tags = $user->getTags();
		
		$this->generatePermissionTable($user,$tags,$table);
		
		OIOJ::$template->assign('table',$table);
		OIOJ::$template->assign('tags',$tags);
		OIOJ::$template->assign('user',$user);
		
		OIOJ::$template->display('admin_user_permissions.tpl');
	}
	
	public function loginAs()
	{
		// This is a very advanced feature and should only be granted to OMNIPOTENT user
		User::GetCurrent()->assertAble('omnipotent');
		
		$user = User::first(array('id','username'),'`id`='.IO::GET('uid',0,'intval'));
		if ($user)
		{
			$user->createSession();
		}
		else
		{
			throw new InputException('Invalid User ID');
		}
	}
	
	private function generatePermissionTable($user, $tags, &$table)
	{
		
		if ($tags)
		{
			$tagAcl = array();
			$tagExist = array();
			foreach ($tags as $t)
			{
				$tagExist[$t->id] = true;
			}
			
			foreach(Database::Get()->query('SELECT `tid`,`key`,`permission` FROM `oj_tag_acl`') as $row)
			{
				if (isset($tagExist[intval($row['tid'])]))
				{
					$tagAcl[intval($row['tid'])][$row['key']] = intval($row['permission']);
				}
			}
		}
		
		if ($user)
		{
			$userAcl = array();
			foreach(Database::Get()->query('SELECT `uid`,`key`,`permission` FROM `oj_user_acl` WHERE `uid`='.$user->id) as $row)
			{
				$userAcl[$row['key']] = intval($row['permission']);
			}
		}
		
		
		$names = loadData('PermissionKeyNames');
		
		foreach ($table as $k => $v)
		{
			// Format Name
			$table[$k]['name'] = '';
			if ($v['level'] > 0)
			{
				for ($i=0;$i<$v['level'];$i++)
				{
					$table[$k]['name'] .= '&nbsp;&nbsp;';
				}
				$table[$k]['name'] .= '|-';
			}
			$table[$k]['name'] .= htmlspecialchars($names[$v['key']]);
			// Format Permission
			if ($user)
			{
				$table[$k]['user_perms'] = IO::GetArrayElement($userAcl,$v['key'],0);
			}
			if ($tags)
			{
				$table[$k]['tag_perms'] = array();
				foreach ($tags as $tag)
				{
					$table[$k]['tag_perms'][] = IO::GetArrayElement($tagAcl[$tag->id],$v['key'],0);
				}
			}
		}
	}
	
	public function tagPermissions()
	{
		$roots = $this->generatePermissionTree();
		
		$table = $this->travelTree($roots);
		
		if (IO::POST('tag'))
		{
			$tags = array(Tag::AddIfNotExist(IO::POST('tag')));
		}
		else
		{
			$tags = User::GetPopularTags();
		}
		
		
		$this->generatePermissionTable(null,$tags,$table);
		
		// Tag Properties
		$stmt = Database::Get()->query('SELECT `tid`,`key` FROM `oj_usertag_properties`');
		$prop = array_fill(0,count($tags),array());
		foreach ($stmt as $row)
		{
			for ($i=0;$i<count($tags);$i++)
			{
				if (intval($row['tid']) == $tags[$i]->id)
				{
					$prop[$i][$row['key']] = true;
				}
			}
		}
		
		OIOJ::$template->assign('table',$table);
		OIOJ::$template->assign('tags',$tags);
		OIOJ::$template->assign('properties',$prop);
		
		OIOJ::$template->display('admin_tag_permissions.tpl');
	}
	
	private function generatePermissionTree()
	{
		// Arrange into tree
		$names = loadData('PermissionKeyNames');
		
		$hier = loadData('PermissionHierarchy');
		
		$roots = array();
		
		foreach ($names as $k=>$v)
		{
			if (!isset($hier[$k]))
			{
				$roots[$k] = new PermTreeNode($k,0);
			}
		}
		
		$cur = $roots;
		$newcur = array();
		$level = 1;
		while (count($hier) > 0)
		{
			foreach ($hier as $child => $father)
			{
				if (isset($cur[$father]))
				{
					$obj = new PermTreeNode($child,$level);
					$cur[$father]->children[$child] = $obj;
					$newcur[$child] = $obj;
					unset($hier[$child]);
				}
			}
			$cur = $newcur;
			$newcur = array();
			$level++;
		}
		return $roots;
	}
	
	private function travelTree($roots)
	{
		$permissions = array();
		foreach ($roots as $k => $v)
		{
			$permissions[] = array('key' => $k, 'level' => $v->level);
			
			if (count($v->children))
			{
				$permissions = array_merge($permissions,$this->travelTree($v->children));
			}
		}
		return $permissions;
	}
	
	public function invitation()
	{
		import('Invitation');
		if (IO::GET('do') == 'generate')
		{
			$num = IO::POST('count',5,'intval');
			
			for ($i=0;$i<$num;$i++)
			{
				$n = new Invitation();
				$n->code = md5(rand()*time());
				$n->submit();
			}
		}
		$invitations = Invitation::find(array('code','user'=>array('username')),'WHERE `sender` IS NULL');
		OIOJ::$template->assign('invitations',$invitations);
		OIOJ::$template->display('admin_user_invitation.tpl');
	}
	
	
}
?>
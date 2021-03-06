<?php
defined('IN_OIOJ') || die('Forbidden');
class AdminCommonsModule
{
	public function run()
	{
		User::GetCurrent()->assertAble('admin_cp');
		switch (IO::GET('act'))
		{
		case 'browseserver':
			$this->browseServer();
			break;
		}
	}
	
	public function browseServer()
	{
		$obj = IO::GET('object');
		if (!in_array($obj,array('directory','file','both')))
		{
			throw new InputException('Invalid Object');
		}
		
		$pwd = IO::GET('pwd',ROOT);
		function makeCallback($file)
		{
			$func = "(".IO::GET('callback').").call(window.opener,'".str_replace("\\","\\\\",$file)."');window.close();";
			return 'onclick="'.htmlspecialchars($func).'"';
		}
		
		echo '<!DOCTYPE HTML><html><head><meta charset="utf-8" /><title>'.htmlspecialchars($pwd).'</title><link rel="stylesheet" href="templates/admin_serverbrowser.css" /></head>';
		echo '<body>';
		
		if (!defined('DS'))
			define('DS',DIRECTORY_SEPARATOR);
		echo '<p>';
		echo $pwd;
		if ($obj != 'file')
		{
			echo " <a href='javascript:;' ".makeCallback($pwd).">[Select]</a>";
		}
		echo '</p>';
		echo '<table>';
		echo '<thead><tr><td>Name</td><td>Size</td><td>Stat.</td><td>Perm.</td></tr></thead>';
		echo '<tbody>';
		
		foreach (scandir($pwd) as $f)
		{
			$loc = $pwd . $f;
			$isFile = is_file($loc);
			if ($obj == 'directory' && $isFile)
			{
				continue;
			}
			
			echo '<tr>';
			echo '<td class="filename">';
			if ($isFile)
			{
				echo "<a href='javascript:;' ".makeCallback($loc).">";
			}
			else
			{
				echo '<a href="index.php?mod=admin_commons&act=browseserver&amp;callback='.urlencode(IO::GET('callback')).'&amp;object='.$obj.'&amp;pwd='.urlencode(realpath($loc).DS).'">';
			}
			echo htmlspecialchars($f);
			echo '</a>';
			echo '</td>';
			echo '<td class="size">';
			
			if ($isFile)
			{
				$size = filesize($loc);
				$unit = array('B','KB','MB','GB');
				$cur = 0;
				while ($size > 1024 && $cur < count($unit)-1)
				{
					$size /= 1024;
					$cur++;
				}
				echo sprintf("%.2f",$size) . $unit[$cur];
			}else
			{
				echo '-';
			}
			echo '</td>';
			echo '<td>';
			if (is_readable($loc))
			{
				echo '<span style="color: green">r</span>';
			}
			else
			{
				echo '<span style="color: red">r</span>';
			}
			
			if (is_writable($loc))
			{
				echo '<span style="color: green">w</span>';
			}
			else
			{
				echo '<span style="color: red">w</span>';
			}
			
			if (is_executable($loc))
			{
				echo '<span style="color: green">x</span>';
			}
			else
			{
				echo '<span style="color: red">x</span>';
			}
			
			echo '</td>';
			echo '<td>';
			echo substr(sprintf('%o', fileperms($loc)), -3);
			echo '</td>';
			echo '</tr>';
		}
		echo '</tbody>';
		echo '</table>';
		
		echo '</body></html>';
	}

}
?>
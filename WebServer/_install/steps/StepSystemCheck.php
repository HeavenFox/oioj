<?php
class StepSystemCheck
{
	private $html;
	public function processData()
	{
		return true;
	}
	
	public function prepareStep()
	{
		$pass = true;
		$this->html = '<table>';
		$this->html .= '<tr><td>PHP Version</td>';
		if (version_compare(PHP_VERSION,'5.3.0') >= 0)
		{
			$this->html .= '<td class="pass">'.PHP_VERSION.' &gt; 5.3.0</td>';
		}else
		{
			$this->html .= '<td class="fail">'.PHP_VERSION.' &lt; 5.3.0</td>';
			$pass = false;
		}
		$this->html .= '</tr>';
		
		$this->html .= '<tr><td>PDO</td>';
		if (class_exists('PDO') && in_array('mysql',PDO::getAvailableDrivers()))
		{
			$this->html .= '<td class="pass">PDO Installed and Supports MySQL</td>';
		}else
		{
			$this->html .= '<td class="fail">PDO MySQL Driver not found</td>';
			$pass = false;
		}
		$this->html .= '</tr>';
		
		$this->html .= '<tr><td>config.php</td>';
		if (is_writable(ROOT.'config.php'))
		{
			$this->html .= '<td class="pass">Writable</td>';
		}else
		{
			$this->html .= '<td class="fail">Not Writable. Please chmod to 777</td>';
			$pass = false;
		}
		$this->html .= '</tr>';
		
		
		$this->html .= '</table>';
		
		return $pass;
	}
	
	public function renderStep()
	{
		echo $this->html;
	}
	
	public function renderHeader()
	{
		echo '
<style type="text/css">
.pass
{
	color: green;
}

.fail
{
	color: red;
}
</style>
';
	}
}
?>
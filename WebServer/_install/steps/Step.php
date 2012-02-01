<?php
abstract class Step
{
	public function processData()
	{
		return true;
	}
	
	public function prepareStep()
	{
		return true;
	}
	
	abstract public function renderStep();
	
	public function renderHeader()
	{
	}
}
?>
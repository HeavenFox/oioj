<?php
import('ActiveRecord');
class Dependency extends ActiveRecord
{
	const COMPLILE_TYPE = 1;
	const RUNTIME_TYPE = 1<<1;
	
}
?>
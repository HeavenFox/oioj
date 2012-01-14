<?php
import('User');
class ContestParticipant extends User
{
	public $rankingParams = array();
	
	public $rankingCriteria = array();
	
	public $rank;
}
?>
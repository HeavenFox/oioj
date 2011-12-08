function updateTimer()
{
	var now = new Date();
	var diff = endTime - Math.round(now.getTime()/1000);
	var sec = diff % 60;
	if (sec < 10)
	{
		sec = "0" + sec;
	}
	diff = Math.floor(diff/60);
	var min = diff % 60;
	if (min < 10)
	{
		min = "0" + min;
	}
	diff = Math.floor(diff/60);
	var hour = diff;
	if (diff < 0)
	{
		$('#timer-display').html('<span style="font-weight: bold;color: red;">Deadline has passed</span>');
	}
	else
	{
		$('#timer-display').text(""+hour+":"+min+":"+sec);
	}
	setTimeout('updateTimer();',1000);
}
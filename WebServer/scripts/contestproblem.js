function updateTimer()
{
	var now = new Date();
	var diff = endTime - Math.round(now.getTime()/1000);
	var sec = diff % 60;
	diff = Math.floor(diff/60);
	var min = diff % 60;
	diff = Math.floor(diff/60);
	var hour = diff;
	$('#timer-display').text(""+hour+":"+min+":"+sec);
	setTimeout('updateTimer();',1000);
}
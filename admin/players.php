<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';

	$player_query = "SELECT * from players;";
	$pq_ret = $db->query($player_query);
	$row_count = 0;
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		if ($row_count == 0){
			// print table header
			print "<h3>Existing Player list</h3>";
		}
		$row_count++;
		$playerid = $row['playerid'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
		$pool = $row['pool'];
		print "$firstname $lastname ID#:$playerid Pool:$pool<br>";
	}
	if ($row_count != 0) {
		// close table
		print "<hr>";
	}
	print "<h3>Add a new player</h3>";
	print "<form action='add_player.php' method='post'>";
	print "First Name: <input type='text' name='firstname'><br>";
	print "Last Name: <input type='text' name='lastname'><br>";
	print "Pool: <select name = 'pool'><option value='A'>A</option><option value='B'>B</option><option value='C'>C</option><option value='W'>W</option></select><br>";
	print ('<input type="submit" value="Submit">');
	print ('</form>');
?>

<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
	$lastname = $_POST['lastname'];
  $course = $_POST['course'];

	$player_query = "SELECT * from players WHERE lastname LIKE :lastname;";
	$pq_stmt = $db->prepare($player_query);
	$pq_stmt->bindParam(":lastname",$lastname);
	$pq_ret = $pq_stmt->execute();
	$row_count = 0;
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		if ($row_count == 0){
			// print table header
			print "<h3>Matching Players</h3>";
		}
		$row_count++;
		$playerid = $row['playerid'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
		$pool = $row['pool'];
    print "<form action='check_in_player.php' method='post'>";
		print "<input type='radio' name='playerid' value=$playerid>$firstname $lastname ID#:$playerid Pool:$pool</input><br>";
	}
	if ($row_count != 0) {
		// close table
    print "Incoming Tag #: <input type='text' name='incoming_tag'><br>";
    print "<input type='hidden' name='course' value=$course>";
    print "<input type='submit' value='Check In'>";
    print "</form>";
		print "<hr>";
	} else {
		print "No matching players found<br>";
	}
	print "<h3>Add a new player?</h3>";
	print "<form action='admin/add_player.php' method='post'>";
	print "First Name: <input type='text' name='firstname'><br>";
	print "Last Name: <input type='text' name='lastname' value=$lastname><br>";
	print "Pool: <select name = 'pool'><option value='A'>A</option><option value='B'>B</option><option value='C'>C</option><option value='W'>W</option></select><br>";
	print ('<input type="submit" value="Submit">');
	print ('</form>');
?>

<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  print ("<h3>Checked in Players for week $week</h3>");
  $player_count = 0;
  $checked_in_players_query = "SELECT * from scores WHERE week IS :week";
  $cipq_stmt = $db->prepare($checked_in_players_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_ret = $cipq_stmt->execute();
  while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
    if ($player_count == 0){
      print ("<table style='width:100%' border='1'>");
      print ("<tr><td>Player</td><td>Pool</td><td>Course</td><td>Starting Hole</td></tr>");
    }
    $player_count++;
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $pool = $row['pool'];
    $course = $row['course'];
    $start_hole = $row['start_hole'];
    print ("<tr><td>$firstname $lastname</td><td>$pool</td><td>$course</td><td>$start_hole</td></tr>");
  }
  if ($player_count == 0){
    print ("No players checked in for week $week<br>");
  } else {
    print ("</table>");
  }

	print ("<h3>Current Configuration</h3>");

	print ('<form action="update_config.php" method="post">');
	print ("Current Week Number: <input type='text' name='week' value=$week><br>");
	print ("System State: <select type='text' name='system_state'>");
  print ("<option value='check_in_open'");
  if ($system_state == 'check_in_open') {
    print (" selected");
  }
  print (">Check In Open</option>");
  print ("<option value='score_entry'");
  if ($system_state == 'score_entry') {
    print (" selected");
  }
  print (">Score Entry</option>");
  print ("<option value='closed'");
  if ($system_state == 'closed') {
    print (" selected");
  }
  print (">Closed</option>");
  print ("</select><br>");
  print ("Hill start order configuration: <input type='text' name='hill_start_order' value=$hill_start_order><br>");
  print ("General start order configuration: <input type='text' name='general_start_order' value=$general_start_order><br>");
	print ("Money to payout per player: <input type='text' name='amount_to_payout' value=$amount_to_payout><br>");
	print ("Money to ace pot per player: <input type='text' name='amount_to_ace_pot' value=$amount_to_ace_pot><br>");
	print ("Money to course per player: <input type='text' name='amount_to_course' value=$amount_to_course><br>");
	print ("Money to bonanza per player: <input type='text' name='amount_to_bonanza' value=$amount_to_bonanza><br>");
	print ("Number of players to payout in A pool: <input type='text' name='a_pool_payout_count' value=$a_pool_payout_count><br>");
	print ("Number of players to payout in B pool: <input type='text' name='b_pool_payout_count' value=$b_pool_payout_count><br>");
	print ("Number of players to payout in C pool: <input type='text' name='c_pool_payout_count' value=$c_pool_payout_count><br>");
	print ("Number of players to payout in W pool: <input type='text' name='w_pool_payout_count' value=$w_pool_payout_count><br>");
	print ("A pool General Handicap: <input type='text' name='a_pool_handicap' value=$a_pool_handicap><br>");
	print ("B pool General Handicap: <input type='text' name='b_pool_handicap' value=$b_pool_handicap><br>");
	print ("C pool General Handicap: <input type='text' name='c_pool_handicap' value=$c_pool_handicap><br>");
	print ("W pool General Handicap: <input type='text' name='w_pool_handicap' value=$w_pool_handicap><br>");
	print ('<input type="submit" value="Update Config">');
	print ('</form>');
?>

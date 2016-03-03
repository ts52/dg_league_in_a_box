<html>
<head>
</head>
<style>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  th, td {
    padding: 7px;
  }
</style>
<body>
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
      print ("<table border='1'>");
      print ("<tr><td>Player</td><td>Pool</td><td>Course</td><td>Starting Hole</td><td>Tag#</td><td>Paid</td><td>Score</td><td>Handicap Score</td><td>Ace Hole</td><td>Points</td><td>Payout</td><td>Place (in pool)</td><td></td></tr>");
    }
    $player_count++;
    $playerid = $row['playerid'];
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $pool = $row['pool'];
    $course = $row['course'];
    $incoming_tag = $row['incoming_tag'];
    $start_hole = $row['start_hole'];
    $paid = $row['paid'];
    $score = $row['score'];
    $handicap_score = $row['handicap_score'];
    $ace = $row['ace'];
    $points = $row['points'];
    $payout = $row['payout'];
    $place_in_pool = $row['place_in_pool'];
    print ("<form action='update_checked_in_player.php' method='post'>");
    print ("<input type='text' name='playerid' value=$playerid hidden>");
    print ("<tr><td>$firstname $lastname</td>");
    print ("<td>");
    print ("<select type='text' name='pool'>");
    print ("<option value='A'");
    if ( $pool == "A" ) {
      print (" selected");
    }
    print (">A</option>");
    print ("<option value='B'");
    if ( $pool == "B" ) {
      print (" selected");
    }
    print (">B</option>");
    print ("<option value='C'");
    if ( $pool == "C" ) {
      print (" selected");
    }
    print (">C</option>");
    print ("<option value='W'");
    if ( $pool == "W" ) {
      print (" selected");
    }
    print (">W</option>");
    print ("</td>");

    print ("<td>");
    print ("<select type='text' name='course'>");
    print ("<option value='hill'");
    if ( $course == "hill" ) {
      print (" selected");
    }
    print (">hill</option>");
    print ("<option value='general'");
    if ( $course == "general" ) {
      print (" selected");
    }
    print (">general</option>");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='start_hole' value=\"$start_hole\" size='3'>");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='incoming_tag' value=\"$incoming_tag\" size='4'>");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='paid' value=\"$paid\" size='2' >");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='score' value=\"$score\" size='4' >");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='handicap_score' value=\"$handicap_score\" size='4'>");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='ace' value=\"$ace\" size='3'>");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='points' value=\"$points\" size='2' >");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='payout' value=\"$payout\" size='8' >");
    print ("</td>");

    print ("<td>");
    print ("<input type='text' name='place_in_pool' value=\"$place_in_pool\" size='4' >");
    print ("</td>");

    print ("<td><input type='submit' value='Update'</td>");
    print ("</tr>");
    print ("</form>");
  }
  if ($player_count == 0){
    print ("No players checked in for week $week<br>");
  } else {
    print ("</table>");
  }
?>

<h3><a href=results.php>Generate Results</a></h3>

<h3>Current Configuration</h3>

<?php
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
  //print ("Current ace pot: <input type='text' name='current_ace_pot' value=\"$current_ace_pot\"><br>\n");
  //print ("Max ace pot: <input type='text' name='max_ace_pot' value=\"$max_ace_pot\"><br>\n");
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
<h3><a href="shutdown.php">Shutdown the system</a></h3>
</body>
</html>

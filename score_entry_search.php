<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';
	$lastname = $_POST['lastname'];
  $course = $_POST['course'];
  $playerid = $_POST['playerid'];

  print "<style>\n";
  print "table, th, td {\n";
  print "border: 1px solid black;\n";
  print "border-collapse: collapse;\n"; 
  print "}\n";
  print "th, td {\n";
  print "padding: 7px;\n";
  print "}\n";
  print "</style>\n";

  print "DEBUG: in score_entry_search.php<br>\n";
	$player_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND";
  if (!empty($playerid)) {
    $player_query = "$player_query playerid IS :playerid;";
  } else {
    $player_query = "$player_query lastname LIKE :lastname;";
  }
  print "DEBUG: player_query:($player_query)<br>\n";
	$pq_stmt = $db->prepare($player_query);
  print "DEBUG: binding :lastname to $lastname<br>\n";
	$pq_stmt->bindParam(":lastname",$lastname);
  if (!empty($playerid)){
    print "DEBUG: binding :playerid to $playerid <br>\n";
    $pq_stmt->bindParam(":playerid",$playerid);
  }
  print "DEBUG: binding :week to $week<br>\n";
	$pq_stmt->bindParam(":week",$week);
  print "DEBUG: binding :course to $course<br>\n";
	$pq_stmt->bindParam(":course",$course);
	$pq_ret = $pq_stmt->execute();
	$row_count = 0;
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
    $row_count++;
    $start_hole = $row['start_hole'];
	}
	if ($row_count > 1) {
    print "DEBUG: more than 1 matching player<br>\n";
    print "DEBUG: row_count = $row_count<br>\n";
    // more than one matching last name on the course
    print "<form action'score_entry_search.php' method='post'>\n";
    $pq_ret->reset();
    $row_count = 0;
    while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
      if ($row_count == 0){
        // print table header
        print "<h3>Matching Players</h3>\n";
        print "<table border='1'>\n";
        print "<tr><td></td><td>Player Name</td><td>Pool</td><td>Start Hole</td></tr>\n";
      }
      $row_count++;
      $playerid = $row['playerid'];
      $lastname = $row['lastname'];
      $firstname = $row['firstname'];
      $pool = $row['pool'];
      $start_hole = $row['start_hole'];
      print "<tr>\n";
      print "<td><input type='radio' name='playerid' value=$playerid></td>\n";
      print "<td>$firstname $lastname</td>\n";
      print "<td>$pool</td>\n";
      print "<td>$start_hole</td>\n";
      print "</tr>\n";
    }
    print "</table>\n";
    print "<input type='hidden' name='course' value=$course>\n";
    print "<input type='hidden' name='lastname' value=$lastname>\n";
    print "<input type='submit' value='Select Player'>\n";
    print "</form>\n";
  } elseif ($row_count == 1) {
    print "DEBUG: exactly 1 matching player<br>\n";
    print "DEBUG: start hole is $start_hole<br>\n";
    // exactly one match, use start_hole to find the card and get scores
    $card_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS :start_hole ORDER BY incoming_tag;";
    $cq_stmt = $db->prepare($card_query);
    $cq_stmt->bindParam(":week", $week);
    $cq_stmt->bindParam(":course", $course);
    $cq_stmt->bindParam(":start_hole", $start_hole);
    print "DEBUG: week:$week course:$course start_hole:$start_hole<br>\n";
    print "DEBUG: Searching for players on the same card with query:<br>$card_query<br>\n";
    $cq_ret = $cq_stmt->execute();
    $row_count = 0;
    while ( $row = $cq_ret->fetchArray(SQLITE3_ASSOC) ) {
      if ($row_count == 0){
        print "<form action='update_scores.php' method='post'>\n";
        print "<table border='1'>\n";
        print "<tr><td>Player</td><td>Pool</td><td>Score</td></tr>\n";
      }
      $playerid = $row['playerid'];
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $pool = $row['pool'];
      $course = $row['course'];
      $playerid_name = "playerid$row_count";
      print "<input type='hidden' name=$playerid_name value=$playerid >\n";
      $score_name = "score$row_count";
      print "<tr><td>$firstname $lastname</td><td>$pool</td>\n";
      print "<td><input type='text' name=$score_name></td></tr>\n";
      $row_count++;
    }
    if ($row_count > 0) {
      print "</table><input type='submit' value='Submit Scores'></form>\n";
    } else {
      print "ERROR: no players found for hole $start_hole on the $course in week $week<br>\n";
    }
	} else {
		print "No matching players found with lastname: $lastname on the $course<br>\n";
    print "<a href='index.php'>Try again.</a>\n";
	}
?>

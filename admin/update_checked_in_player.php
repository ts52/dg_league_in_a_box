<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  $playerid = $_POST['playerid'];
  $course = $_POST['course'];
  $pool = $_POST['pool'];
  $start_hole = $_POST['start_hole'];
  $incoming_tag = $_POST['incoming_tag'];

  // Check to make sure hole isn't full, then update scores table

  $player_query = "SELECT * from players WHERE playerid IS :playerid";
  $pq_stmt = $db->prepare($player_query);
  $pq_stmt->bindParam(":playerid",$playerid);
  $pq_ret = $pq_stmt->execute();
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		$playerid = $row['playerid'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
  }

  print "Updating ID:$playerid $firstname $lastname to the $course, starting hole $start_hole, $pool pool, incoming tag $incoming_tag, for week $week<br>";

  $checked_in_player_query = "SELECT * from scores WHERE week IS :week AND start_hole IS :start_hole";
  $cipq_stmt = $db->prepare($checked_in_player_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_stmt->bindParam(":start_hole",$start_hole);
  $cipq_ret = $cipq_stmt->execute();
  $player_count = 0;
  while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
    $player_count++;
  }

  print "There are already $player_count players checked in to start on $start_hole on the $course<br>";

  if ( $player_count == 4 ) {
    print ("<br>$course $start_hole is alread full, please try a different hole<br>");
  } else {
    $update_sql = <<<EOF
      UPDATE scores 
          SET pool=:pool,course=:course,incoming_tag=:incoming_tag,start_hole=:start_hole
          WHERE week IS :week AND playerid IS :playerid;
EOF;
    $update_player_stmt = $db->prepare($update_sql);

    $update_player_stmt->bindParam(":playerid", $playerid);
    $update_player_stmt->bindParam(":pool", $pool);
    $update_player_stmt->bindParam(":week", $week);
    $update_player_stmt->bindParam(":course", $course);
    $update_player_stmt->bindParam(":incoming_tag", $incoming_tag);
    $update_player_stmt->bindParam(":start_hole", $start_hole);
    $update_player_stmt->execute();
    print ("Player $firstname $lastname checked in to the $course<br>");
  }
?>

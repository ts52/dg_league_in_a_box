<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  $playerid = $_POST['playerid'];
  $course = $_POST['course'];
  $pool = $_POST['pool'];
  $start_hole = $_POST['start_hole'];
  $incoming_tag = $_POST['incoming_tag'];
  $paid = $_POST['paid'];
  $score = $_POST['score'];
  $handicap_score = $_POST['handicap_score'];
  $ace = $_POST['ace'];
  $points = $_POST['points'];
  $payout = $_POST['payout'];
  $place_in_pool = $_POST['place_in_pool'];

  if ( ! empty ( $course ) and ! empty( $score ) and empty( $handicap_score ) and ! empty( $pool ) ) {
    $handicap_score = $score;
    if ( $course == 'general' ) $handicap_score = $score - $handicap[$pool];
  }

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

  print "Updating ID:$playerid $firstname $lastname to the $course, starting hole $start_hole, $pool pool, incoming tag $incoming_tag, paid $paid, for week $week<br>";

  $update_sql = <<<EOF
    UPDATE scores 
        SET pool=:pool,course=:course,incoming_tag=:incoming_tag,start_hole=:start_hole,paid=:paid,
            score=:score,handicap_score=:handicap_score,ace=:ace,points=:points,payout=:payout,place_in_pool=:place_in_pool
        WHERE week IS :week AND playerid IS :playerid;
EOF;
  $update_player_stmt = $db->prepare($update_sql);

  $update_player_stmt->bindParam(":playerid", $playerid);
  $update_player_stmt->bindParam(":pool", $pool);
  $update_player_stmt->bindParam(":week", $week);
  $update_player_stmt->bindParam(":course", $course);
  $update_player_stmt->bindParam(":incoming_tag", $incoming_tag);
  $update_player_stmt->bindParam(":start_hole", $start_hole);
  $update_player_stmt->bindParam(":paid", $paid);
  $update_player_stmt->bindParam(":score", $score);
  $update_player_stmt->bindParam(":handicap_score", $handicap_score);
  $update_player_stmt->bindParam(":ace", $ace);
  $update_player_stmt->bindParam(":points", $points);
  $update_player_stmt->bindParam(":payout", $payout);
  $update_player_stmt->bindParam(":place_in_pool", $place_in_pool);
  $update_player_stmt->execute();
  print ("Player $firstname $lastname checked in to the $course<br>");
?>
<h3><a href="index.php">Back</a></h3>

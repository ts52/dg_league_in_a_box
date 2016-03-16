<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';

  $playerid = $_POST['playerid'];
  $course = $_POST['course'];
  $incoming_tag = $_POST['incoming_tag'];

  $pay_amount = '$5';
  if (empty($incoming_tag)){
    $pay_amount = '$7';
    $incoming_tag = 9999;
  }

  $player_query = "SELECT * from players WHERE playerid IS :playerid";
  $pq_stmt = $db->prepare($player_query);
  $pq_stmt->bindParam(":playerid",$playerid);
  $pq_ret = $pq_stmt->execute();
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
    $pool = $row['pool'];
  }

  $player_query = "SELECT * from scores WHERE week IS :week AND playerid IS :playerid";
  $pqq_stmt = $db->prepare($player_query);
  $pqq_stmt->bindParam(":week",$week);
  $pqq_stmt->bindParam(":playerid",$playerid);
  $pqq_ret = $pqq_stmt->execute();
  $player_count = 0;
  while ($row = $pqq_ret->fetchArray(SQLITE3_ASSOC) ){
    $player_count++;
    $course = $row['course'];
    $start_hole = $row['start_hole'];
  }

  print ("<!--DEBUG: found $player_count players checked in with ID:$playerid-->\n");

  if ($player_count != 0) {
    print "$firstname $lastname is already checked in to the $course on hole $start_hole<br>";
  } else {
    print "<!--Checking $firstname $lastname in to the $course for week $week-->\n";

    $checked_in_player_query = "SELECT * from scores WHERE week IS :week AND course IS :course";
    $cipq_stmt = $db->prepare($checked_in_player_query);
    $cipq_stmt->bindParam(":week",$week);
    $cipq_stmt->bindParam(":course",$course);
    $cipq_ret = $cipq_stmt->execute();
    $player_count = 0;
    while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
      $player_count++;
    }

    print "<!--There are already $player_count players checked in to the $course-->\n";

    if ($course == 'hill'){
      $start_hole = $hill_start_array[$player_count];
    }elseif ($course == 'general'){
      $start_hole = "W";
    }

    $insert_sql = <<<EOF
      INSERT INTO scores
          (playerid,lastname,firstname,pool,week,course,incoming_tag,start_hole)
          VALUES
          (:playerid,:lastname,:firstname,:pool,:week,:course,:incoming_tag,:start_hole);
EOF;
    $add_player_stmt = $db->prepare($insert_sql);

    $add_player_stmt->bindParam(":playerid", $playerid);
    $add_player_stmt->bindParam(":lastname", $lastname);
    $add_player_stmt->bindParam(":firstname", $firstname);
    $add_player_stmt->bindParam(":pool", $pool);
    $add_player_stmt->bindParam(":week", $week);
    $add_player_stmt->bindParam(":course", $course);
    $add_player_stmt->bindParam(":incoming_tag", $incoming_tag);
    $add_player_stmt->bindParam(":start_hole", $start_hole);
    $add_player_stmt->execute();

		print "<!-- Checking how many players already on hole $start_hole on the $course -->\n";
		$hole_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS :start_hole";
		$hq_stmt = $db->prepare($hole_query);
		$hq_stmt->bindParam(":week", $week);
		$hq_stmt->bindParam(":course", $course);
		$hq_stmt->bindParam(":start_hole", $start_hole);
		$hq_ret = $hq_stmt->execute();
		$player_count = 0;
		while($row = $hq_ret->fetchArray(SQLITE3_ASSOC) ){
			$player_count++;
		}
		print "<!-- $player_count players are on this hole. -->\n";

    print ("Player $firstname $lastname checked in to the $course<br>\n");
    if ($course == "hill") print ("<h2>Your start hole is $start_hole.</h2>\n");
		if ($player_count == 1 and $course == "hill") {
			print ("<h3>Please put your tag on the board, pay $pay_amount and get a scorecard.</h3>\n");
		} else {
			print ("Please put your tag on the board and pay $pay_amount.<br>\n");
		}
    if ($course == "general"){
      print "<br><a href='form_general_card.php'>Find other players waiting for a card on the General</a>\n";
    }
  }
?>
<h3><a href="index.php">Back to check in</a></h3>

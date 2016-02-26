<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include ',/get_config.php';

  $playerid = $_POST['playerid'];
  $course = $_POST['course'];

  $player_query = "SELECT * from players WHERE playerid IS :playerid";
  $pq_stmt = $db->prepare($player_query);
  $pq_stmt->bindParam(":playerid",$playerid);
  $pq_ret = $pq_stmt->execute();
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		$playerid = $row['playerid'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
    $pool = $row['pool'];
  }

  $checked_in_player_query = "SELECT * from scores WHERE week IS :week AND course IS :course"
  $cipq_stmt = $db->prepare($checked_in_player_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_stmt->bindParam(":course",$course);
  $cipq_ret = $cipq_stmt->execute();
  $row_count = 0;
  while ($row = $cipq_ret->ftechArray(SQLITE3_ASSOC) ){
    $row_count++;
  }

  if ($course == 'hill'){
    $start_hole = $hill_start_array[$row_count];
  }elseif ($course == 'general'){
    $start_hole = $general_start_array[$row_count];
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
	print ("Player $firstname $lastname checked in to the $course<br>");
  print ("Your start hole is $start_hole.<br>");
?>

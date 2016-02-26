<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include ',/get_config.php';

  $playerid = $_POST['playerid'];
  $course = $_POST['course'];

  $player_query = "SELECT * from players WHERE playerid is :playerid";
  $pq_stmt = $db->prepare($player_query);
  $pq_stmt->bindParam(":playerid",$playerid);
  $pq_ret = $pq_stmt->execute();
	while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
		$playerid = $row['playerid'];
		$lastname = $row['lastname'];
		$firstname = $row['firstname'];
    $pool = $row['pool'];
  }

	$insert_sql = <<<EOF
		INSERT INTO scores 
		    (playerid,lastname,firstname,pool)
		    VALUES
		    (:playerid,:lastname,:firstname,:pool);
EOF;
	$add_player_stmt = $db->prepare($insert_sql);

	$add_player_stmt->bindParam(":lastname", $lastname);
	$add_player_stmt->bindParam(":firstname", $firstname);
	$add_player_stmt->bindParam(":pool", $pool);
	$add_player_stmt->execute();
	print ("Player $firstname $lastname added to $pool pool<br>");
?>

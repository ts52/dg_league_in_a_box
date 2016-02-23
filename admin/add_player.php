<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$pool = $_POST['pool'];

	$insert_sql = <<<EOF
		INSERT INTO players 
		    (lastname,firstname,pool)
		    VALUES
		    (:lastname,:firstname,:pool);
EOF;
	$add_player_stmt = $db->prepare($insert_sql);

	$add_player_stmt->bindParam(":lastname", $lastname);
	$add_player_stmt->bindParam(":firstname", $firstname);
	$add_player_stmt->bindParam(":pool", $pool);
	$add_player_stmt->execute();
	print ("Player $firstname $lastname added to $pool pool<br>");
?>

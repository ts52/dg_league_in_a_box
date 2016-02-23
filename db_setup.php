<?php
  // Before including tihs php file, set $db_file
	// Open DB and set up tables if they don't already exist
	$db = new SQLite3($db_file) or die('Unable to open database');

	$user_table_create = <<<EOD
	   CREATE TABLE IF NOT EXISTS players (
		playerid INTEGER PRIMARY KEY ASC AUTOINCREMENT,
		lastname STRING,
		firstname STRING,
		pool STRING )
EOD;
	$db->exec($user_table_create) or die('Create user db failed');

	$config_table_create = <<<EOD
	    CREATE TABLE IF NOT EXISTS config (
		week INTEGER,
		system_state STRING,
		hill_start_order STRING,
		general_start_order STRING,
		amount_to_payout DOUBLE,
		amount_to_ace_pot DOUBLE,
		amount_to_course DOUBLE,
		amount_to_bonanza DOUBLE,
		a_pool_payout_count INTEGER,
		b_pool_payout_count INTEGER,
		c_pool_payout_count INTEGER,
		w_pool_payout_count INTEGER,
		a_pool_handicap DOUBLE,
		b_pool_handicap DOUBLE,
		c_pool_handicap DOUBLE,
		w_pool_handicap DOUBLE )
EOD;
	$db->exec($config_table_create) or die('Create config db failed');
	
	$scores_table_create = <<<EOD
	    CREATE TABLE IF NOT EXISTS scores (
		playerid INTEGER,
		lastname STRING,
		firstname STRING,
		pool STRING,
		week INTEGER,
		incoming_tag INTEGER,
		course STRING,
		start_hole INTEGER,
		paid INTEGER,
		score INTEGER,
		handicap_score INTEGER,
		points INTEGER,
		place_in_pool INTEGER,
		ace INTEGER,
		payout DOUBLE )
EOD;
	$db->exec($scores_table_create) or die('Create scores db failed');
?>

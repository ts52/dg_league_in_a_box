<?php
  // Before including tihs php file, set $db_file
	// Open DB and set up tables if they don't already exist
	$db = new SQLite3($db_file) or die('Unable to open database');

	$user_table_create = <<<EOD
	   CREATE TABLE IF NOT EXISTS players (
		playerid INTEGER PRIMARY KEY ASC AUTOINCREMENT,
		lastname STRING,
		firstname STRING,
		pool STRING );
EOD;
	$ret = $db->exec($user_table_create);
	if ( ! $ret ) {
		print "ERROR: <br>\n{$db->lastErrorMsg()}<br>\n";
		die('Create user db failed');
	}

  $current_state_table_create = <<<EOD
CREATE TABLE IF NOT EXISTS current_state (
  week INTEGER,
  system_state STRING,
  general_open INTEGER,
  current_ace_pot DOUBLE,
  bonanza_fund DOUBLE,
  course_fund DOUBLE );
EOD;
  $db->exec($current_state_table_create) or die('Create current_state db failed');

	$config_table_create = <<<EOD
	    CREATE TABLE IF NOT EXISTS config (
		week INTEGER,
		hill_start_order STRING,
    hill_max_players INTEGER,
		general_start_order STRING,
    general_max_players INTEGER,
		amount_to_payout DOUBLE,
		amount_to_ace_pot DOUBLE,
		amount_to_course DOUBLE,
		amount_to_bonanza DOUBLE,
    max_ace_pot DOUBLE,
		a_pool_payout_count INTEGER,
		b_pool_payout_count INTEGER,
		c_pool_payout_count INTEGER,
		w_pool_payout_count INTEGER,
		a_pool_payout_schedule STRING,
		b_pool_payout_schedule STRING,
		c_pool_payout_schedule STRING,
		w_pool_payout_schedule STRING,
		a_pool_handicap DOUBLE,
		b_pool_handicap DOUBLE,
		c_pool_handicap DOUBLE,
		w_pool_handicap DOUBLE );
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

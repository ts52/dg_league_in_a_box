<?php
  // Before including this php file
  // set $db_file, and include db_setup.php
	$week = -1;
	$system_state = "";
  $hill_start_order = "1,2,3,5,6,11,13,14,18,17,1,2,3,5,6,11,13,14,18,17,1,2,3,5,6,11,13,14,18,17,1,2,3,5,6,11,13,14,18,17,10,10,10,10,12,12,12,12,15,15,15,15,16,16,16,16,4,4,4,4,9,9,9,9,8,8,8,8,7,7,7,7";
  $hill_max_players = 72;
  $general_start_order = "1,1,1,1,14,14,14,14,12,12,12,12,9,9,9,9,1,1,1,1,12,12,12,12,9,9,9,9,1,1,1,1,9,9,9,9";
  $general_max_players = 36;
	$amount_to_payout = 2.25;
	$amount_to_ace_pot = 0.75;
	$amount_to_course = 1;
	$amount_to_bonanza = 1;
  $current_ace_pot = 0;
  $max_ace_pot = 250;
	$a_pool_payout_count = 5;
	$b_pool_payout_count = 5;
	$c_pool_payout_count = 5;
	$w_pool_payout_count = 3;
	$a_pool_handicap = 6;
	$b_pool_handicap = 6;
	$c_pool_handicap = 6;
	$w_pool_handicap = 6;
	$config_query = "SELECT * from config;";
	$cq_ret = $db->query($config_query);
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
		$week = $row['week'];
		$system_state = $row['system_state'];
    $hill_start_order = $row['hill_start_order'];
    $general_start_order = $row['general_start_order'];
		$amount_to_payout = $row['amount_to_payout'];
		$amount_to_ace_pot = $row['amount_to_ace_pot'];
		$amount_to_course = $row['amount_to_course'];
		$amount_to_bonanza = $row['amount_to_bonanza'];
    //$current_ace_pot = $row['current_ace_pot'];
    //$max_ace_pot = $row['max_ace_pot'];
		$a_pool_payout_count = $row['a_pool_payout_count'];
		$b_pool_payout_count = $row['b_pool_payout_count'];
		$c_pool_payout_count = $row['c_pool_payout_count'];
		$w_pool_payout_count = $row['w_pool_payout_count'];
		$a_pool_handicap = $row['a_pool_handicap'];
		$b_pool_handicap = $row['b_pool_handicap'];
		$c_pool_handicap = $row['c_pool_handicap'];
		$w_pool_handicap = $row['w_pool_handicap'];
	}
  $hill_start_array = explode ( ',', $hill_start_order );
  $general_start_array = explode ( ',', $general_start_order );
  $payout_count = array();
  $payout_count['A'] = $a_pool_payout_count;
  $payout_count['B'] = $b_pool_payout_count;
  $payout_count['C'] = $c_pool_payout_count;
  $payout_count['W'] = $w_pool_payout_count;
  $handicap = array();
  $handicap['A'] = $a_pool_handicap;
  $handicap['B'] = $b_pool_handicap;
  $handicap['C'] = $c_pool_handicap;
  $handicap['W'] = $w_pool_handicap;
?>

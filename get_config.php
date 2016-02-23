<?php
  // Before including this php file
  // set $db_file, and include db_setup.php
	$week = "";
	$system_state = "";
  $hill_start_order = "";
  $general_start_order = "";
	$amount_to_payout = "";
	$amount_to_ace_pot = "";
	$amount_to_course = "";
	$amount_to_bonanza = "";
	$a_pool_payout_count = "";
	$b_pool_payout_count = "";
	$c_pool_payout_count = "";
	$w_pool_payout_count = "";
	$a_pool_handicap = "";
	$b_pool_handicap = "";
	$c_pool_handicap = "";
	$w_pool_handicap = "";
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
		$a_pool_payout_count = $row['a_pool_payout_count'];
		$b_pool_payout_count = $row['b_pool_payout_count'];
		$c_pool_payout_count = $row['c_pool_payout_count'];
		$w_pool_payout_count = $row['w_pool_payout_count'];
		$a_pool_handicap = $row['a_pool_handicap'];
		$b_pool_handicap = $row['b_pool_handicap'];
		$c_pool_handicap = $row['c_pool_handicap'];
		$w_pool_handicap = $row['w_pool_handicap'];
	}
?>

<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	$week = "";
	$check_in_open = "";
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
		$check_in_open = $row['check_in_open'];
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

	print ('Current Configuration<br>');

	print ('<form action="update_config.php" method="post">');
	print ("Current Week Number: <input type='text' name='week' value=$week><br>");
	print ("Check in open? (TRUE|FALSE): <input type='text' name='check_in_open' value=$check_in_open><br>");
  print ("Hill start order configuration: <input type='text' name='hill_start_order' value=$hill_start_order><br>");
  print ("General start order configuration: <input type='text' name='general_start_order' value=$general_start_order><br>");
	print ("Money to payout per player: <input type='text' name='amount_to_payout' value=$amount_to_payout><br>");
	print ("Money to ace pot per player: <input type='text' name='amount_to_ace_pot' value=$amount_to_ace_pot><br>");
	print ("Money to course per player: <input type='text' name='amount_to_course' value=$amount_to_course><br>");
	print ("Money to bonanza per player: <input type='text' name='amount_to_bonanza' value=$amount_to_bonanza><br>");
	print ("Number of players to payout in A pool: <input type='text' name='a_pool_payout_count' value=$a_pool_payout_count><br>");
	print ("Number of players to payout in B pool: <input type='text' name='b_pool_payout_count' value=$b_pool_payout_count><br>");
	print ("Number of players to payout in C pool: <input type='text' name='c_pool_payout_count' value=$c_pool_payout_count><br>");
	print ("Number of players to payout in W pool: <input type='text' name='w_pool_payout_count' value=$w_pool_payout_count><br>");
	print ("A pool General Handicap: <input type='text' name='a_pool_handicap' value=$a_pool_handicap><br>");
	print ("B pool General Handicap: <input type='text' name='b_pool_handicap' value=$b_pool_handicap><br>");
	print ("C pool General Handicap: <input type='text' name='c_pool_handicap' value=$c_pool_handicap><br>");
	print ("W pool General Handicap: <input type='text' name='w_pool_handicap' value=$w_pool_handicap><br>");
	print ('<input type="submit" value="Submit">');
	print ('</form>');
?>

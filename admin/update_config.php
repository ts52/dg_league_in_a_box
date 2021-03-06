<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  $hill_start_order = $_POST['hill_start_order'];
  $general_start_order = $_POST['general_start_order'];
	$amount_to_payout = $_POST['amount_to_payout'];
	$amount_to_ace_pot = $_POST['amount_to_ace_pot'];
	$amount_to_course = $_POST['amount_to_course'];
	$amount_to_bonanza = $_POST['amount_to_bonanza'];
  $current_ace_pot = $_POST['current_ace_pot'];
  $max_ace_pot = $_POST['max_ace_pot'];
	$a_pool_payout_count = $_POST['a_pool_payout_count'];
	$a_pool_payout_schedule = $_POST['a_pool_payout_schedule'];
	$b_pool_payout_count = $_POST['b_pool_payout_count'];
	$b_pool_payout_schedule = $_POST['b_pool_payout_schedule'];
	$c_pool_payout_count = $_POST['c_pool_payout_count'];
	$c_pool_payout_schedule = $_POST['c_pool_payout_schedule'];
	$w_pool_payout_count = $_POST['w_pool_payout_count'];
	$w_pool_payout_schedule = $_POST['w_pool_payout_schedule'];
	$a_pool_handicap = $_POST['a_pool_handicap'];
	$b_pool_handicap = $_POST['b_pool_handicap'];
	$c_pool_handicap = $_POST['c_pool_handicap'];
	$w_pool_handicap = $_POST['w_pool_handicap'];

	$config_query = "SELECT * from config WHERE week IS :week;";
  $cq_stmt = $db->prepare($config_query);
  $cq_stmt->bindParam(":week",$week);
	$cq_ret = $cq_stmt->execute();
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
	}
	if ($row_count == 0) {
		// no rows, use insert
		$insert_sql = <<<EOF
			INSERT INTO config 
			    (week,hill_start_order,general_start_order,
           amount_to_payout,amount_to_ace_pot,amount_to_course,amount_to_bonanza,
           current_ace_pot,max_ace_pot,
			     a_pool_payout_count,b_pool_payout_count,c_pool_payout_count,w_pool_payout_count,
			     a_pool_payout_schedule,b_pool_payout_schedule,c_pool_payout_schedule,w_pool_payout_schedule,
			     a_pool_handicap,b_pool_handicap,c_pool_handicap,w_pool_handicap)
			    VALUES
			    (:week,:hill_start_order,:general_start_order,
           :amount_to_payout,:amount_to_ace_pot,:amount_to_course,:amount_to_bonanza,
           :current_ace_pot,:max_ace_pot,
			     :a_pool_payout_count,:b_pool_payout_count,:c_pool_payout_count,:w_pool_payout_count,
			     :a_pool_payout_schedule,:b_pool_payout_schedule,:c_pool_payout_schedule,:w_pool_payout_schedule,
			     :a_pool_handicap,:b_pool_handicap,:c_pool_handicap,:w_pool_handicap );
EOF;
		$cfg_update_stmt = $db->prepare($insert_sql);
	} else {
		// rows exist, use update
		$update_sql = <<<EOF
			UPDATE config 
			    SET hill_start_order=:hill_start_order, 
           general_start_order=:general_start_order, amount_to_payout=:amount_to_payout, 
           amount_to_ace_pot=:amount_to_ace_pot, amount_to_course=:amount_to_course,
           amount_to_bonanza=:amount_to_bonanza, current_ace_pot=:current_ace_pot, max_ace_pot=:max_ace_pot,
			     a_pool_payout_count=:a_pool_payout_count, b_pool_payout_count=:b_pool_payout_count,
			     c_pool_payout_count=:c_pool_payout_count, w_pool_payout_count=:w_pool_payout_count,
			     a_pool_payout_schedule=:a_pool_payout_schedule, b_pool_payout_schedule=:b_pool_payout_schedule,
			     c_pool_payout_schedule=:c_pool_payout_schedule, w_pool_payout_schedule=:w_pool_payout_schedule,
			     a_pool_handicap=:a_pool_handicap, b_pool_handicap=:b_pool_handicap,
			     c_pool_handicap=:c_pool_handicap, w_pool_handicap=:w_pool_handicap
      WHERE week IS :week ;
EOF;
		$cfg_update_stmt = $db->prepare($update_sql);
	}
	$cfg_update_stmt->bindParam(":week", $week);
	$cfg_update_stmt->bindParam(":hill_start_order", $hill_start_order);
	$cfg_update_stmt->bindParam(":general_start_order", $general_start_order);
	$cfg_update_stmt->bindParam(":amount_to_payout", $amount_to_payout);
	$cfg_update_stmt->bindParam(":amount_to_ace_pot", $amount_to_ace_pot);
	$cfg_update_stmt->bindParam(":amount_to_course", $amount_to_course);
	$cfg_update_stmt->bindParam(":amount_to_bonanza", $amount_to_bonanza);
	$cfg_update_stmt->bindParam(":current_ace_pot", $current_ace_pot);
	$cfg_update_stmt->bindParam(":max_ace_pot", $max_ace_pot);
	$cfg_update_stmt->bindParam(":a_pool_payout_count", $a_pool_payout_count);
	$cfg_update_stmt->bindParam(":b_pool_payout_count", $b_pool_payout_count);
	$cfg_update_stmt->bindParam(":c_pool_payout_count", $c_pool_payout_count);
	$cfg_update_stmt->bindParam(":w_pool_payout_count", $w_pool_payout_count);
	$cfg_update_stmt->bindParam(":a_pool_payout_schedule", $a_pool_payout_schedule);
	$cfg_update_stmt->bindParam(":b_pool_payout_schedule", $b_pool_payout_schedule);
	$cfg_update_stmt->bindParam(":c_pool_payout_schedule", $c_pool_payout_schedule);
	$cfg_update_stmt->bindParam(":w_pool_payout_schedule", $w_pool_payout_schedule);
	$cfg_update_stmt->bindParam(":a_pool_handicap", $a_pool_handicap);
	$cfg_update_stmt->bindParam(":b_pool_handicap", $b_pool_handicap);
	$cfg_update_stmt->bindParam(":c_pool_handicap", $c_pool_handicap);
	$cfg_update_stmt->bindParam(":w_pool_handicap", $w_pool_handicap);
	$cfg_update_stmt->execute();
	print ("Configuration updated<br>\n");
?>
<h3><a href="index.php">Back to Admin</a></h3>

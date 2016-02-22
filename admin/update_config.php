<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	$week = $_POST['week'];
	$check_in_open = $_POST['check_in_open'];
	$amount_to_payout = $_POST['amount_to_payout'];
	$amount_to_ace_pot = $_POST['amount_to_ace_pot'];
	$amount_to_course = $_POST['amount_to_course'];
	$a_pool_payout_count = $_POST['a_pool_payout_count'];
	$b_pool_payout_count = $_POST['b_pool_payout_count'];
	$c_pool_payout_count = $_POST['c_pool_payout_count'];
	$w_pool_payout_count = $_POST['w_pool_payout_count'];
	$a_pool_handicap = $_POST['a_pool_handicap'];
	$b_pool_handicap = $_POST['b_pool_handicap'];
	$c_pool_handicap = $_POST['c_pool_handicap'];
	$w_pool_handicap = $_POST['w_pool_handicap'];

	$config_query = "SELECT * from config;";
	$cq_ret = $db->query($config_query);
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
	}
	if ($row_count == 0) {
		// no rows, use insert
		$insert_sql = <<<EOF
			INSERT INTO config 
			    (week,check_in_open,amount_to_payout,amount_to_ace_pot,amount_to_course,
			     a_pool_payout_count,b_pool_payout_count,c_pool_payout_count,w_pool_payout_count,
			     a_pool_handicap,b_pool_handicap,c_pool_handicap,w_pool_handicap)
			    VALUES
			    (:week,:check_in_open,:amount_to_payout,:amount_to_ace_pot,:amount_to_course,
			     :a_pool_payout_count,:b_pool_payout_count,:c_pool_payout_count,:w_pool_payout_count,
			     :a_pool_handicap,:b_pool_handicap,:c_pool_handicap,:w_pool_handicap );
EOF;
		$cfg_update_stmt = $db->prepare($insert_sql);
	} else {
		// rows exist, use update
		$update_sql = <<<EOF
			UPDATE config 
			    SET week=:week, check_in_open=:check_in_open, amount_to_payout=:amount_to_payout, amount_to_ace_pot=:amount_to_ace_pot, amount_to_course=:amount_to_course,
			     a_pool_payout_count=:a_pool_payout_count, b_pool_payout_count=:b_pool_payout_count,
			     c_pool_payout_count=:c_pool_payout_count, w_pool_payout_count=:w_pool_payout_count,
			     a_pool_handicap=:a_pool_handicap, b_pool_handicap=:b_pool_handicap,
			     c_pool_handicap=:c_pool_handicap, w_pool_handicap=:w_pool_handicap;
EOF;
		$cfg_update_stmt = $db->prepare($update_sql);
	}
	$cfg_update_stmt->bindParam(":week", $week);
	$cfg_update_stmt->bindParam(":check_in_open", $check_in_open);
	$cfg_update_stmt->bindParam(":amount_to_payout", $amount_to_payout);
	$cfg_update_stmt->bindParam(":amount_to_ace_pot", $amount_to_ace_pot);
	$cfg_update_stmt->bindParam(":amount_to_course", $amount_to_course);
	$cfg_update_stmt->bindParam(":a_pool_payout_count", $a_pool_payout_count);
	$cfg_update_stmt->bindParam(":b_pool_payout_count", $b_pool_payout_count);
	$cfg_update_stmt->bindParam(":c_pool_payout_count", $c_pool_payout_count);
	$cfg_update_stmt->bindParam(":w_pool_payout_count", $w_pool_payout_count);
	$cfg_update_stmt->bindParam(":a_pool_handicap", $a_pool_handicap);
	$cfg_update_stmt->bindParam(":b_pool_handicap", $b_pool_handicap);
	$cfg_update_stmt->bindParam(":c_pool_handicap", $c_pool_handicap);
	$cfg_update_stmt->bindParam(":w_pool_handicap", $w_pool_handicap);
	$cfg_update_stmt->execute();
	print ("Configuration updated<br>");
?>

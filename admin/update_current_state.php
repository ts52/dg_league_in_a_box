<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	$week = $_POST['week'];
	$system_state = $_POST['system_state'];

	$config_query = "SELECT * from current_state ;";
	$cq_ret = $db->query($config_query);
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
	}
	if ($row_count == 0) {
		// no rows, use insert
		$insert_sql = <<<EOF
			INSERT INTO current_state 
        (week, system_state)
        VALUES
        (:week, :system_state);
EOF;
		$cfg_update_stmt = $db->prepare($insert_sql);
	} else {
		// rows exist, use update
		$update_sql = <<<EOF
			UPDATE current_state 
			    SET week=:week, system_state=:system_state ;
EOF;
		$cfg_update_stmt = $db->prepare($update_sql);
	}
	$cfg_update_stmt->bindParam(":week", $week);
	$cfg_update_stmt->bindParam(":system_state", $system_state);
	$cfg_update_stmt->execute();
	print "Current State updated<br>\n";
?>
<h3><a href="index.php">Back to Admin</a></h3>

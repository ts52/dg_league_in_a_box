<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	$week = $_POST['week'];
	$system_state = $_POST['system_state'];

  print "<!--DEBUG: updating week to $week and state to $system_state-->\n";

	$config_query = "SELECT * from current_state ;";
	$cq_ret = $db->query($config_query);
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
	}
  print "<!--DEBUG: current_state row count is $row_count-->\n";
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
	$update_ret = $cfg_update_stmt->execute();
  if ($update_ret){
    print "Current State updated<br>\n";
  } else {
    print "ERROR: Update failed.<br>\nSQLITE Error:<br>\n";
    print "{$db->lastErrorMsg()}<br>\n";
  }
?>
<h3><a href="index.php">Back to Admin</a></h3>

<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';

  $update_keys = array('week','system_state','current_ace_pot','bonanza_fund','course_fund','general_open');
  $update_values = array();
  $update_count = 0;
  foreach ($update_keys as $key) {
    if (isset($_POST[$key])) {
      $update_values[$key] = $_POST[$key];
      $update_count++;
    }
  }

  print "<!-- DEBUG: update count = {$update_count} -->\n";

  if ($update_count == 0) {
    print "ERROR: there isn't anythnig to update!?<br>\n";
    exit();
  }

  foreach ($update_values as $key => $value) {
    print "<!--DEBUG: updating {$key} to {$value} -->\n";
  }

	$config_query = "SELECT * from current_state ;";
	$cq_ret = $db->query($config_query);
	$row_count = 0;
	while ($row = $cq_ret->fetchArray(SQLITE3_ASSOC) ){
		$row_count++;
	}
  print "<!--DEBUG: current_state row count is $row_count-->\n";
	if ($row_count == 0) {
		// no rows, use insert
    $insert_count = 0;
    $column_string = "";
    $value_string = "";
    foreach ($update_values as $key => $value) {
      if ($insert_count > 0) {
        $column_string = "{$column_string}, ";
        $value_string = "{$value_string}, ";
      }
      $column_string = "{$column_string}{$key}";
      $value_string = "{$value_string}:{$key}";
      $insert_count++;
    }
		$insert_sql = "INSERT INTO current_state ( $column_string ) VALUES ( $value_string );";
    print "<!-- DEBUG: SQL INSERT Stament is: $insert_sql -->\n";
		$cfg_update_stmt = $db->prepare($insert_sql);
	} else {
		// rows exist, use update
    $update_count = 0;
    $update_string = "";
    foreach ($update_values as $key => $value) {
      print "<!-- DEBUG: Adding {$key} to SQL update string -->\n";
      if ($update_count > 0) {
        $update_string = "{$update_string}, ";
      }
      $update_string = "{$update_string}{$key}=:{$key}";
      $update_count++;
    }
		$update_sql = " UPDATE current_state SET {$update_string} ; ";
    print "<!-- DEBUG: SQL UPDATE Stament is: $update_sql -->\n";
		$cfg_update_stmt = $db->prepare($update_sql);
	}
  foreach ($update_values as $key => $value) {
    $cfg_update_stmt->bindValue(":{$key}", $value);
  }
	$update_ret = $cfg_update_stmt->execute();
  if ($update_ret){
    print "Current State updated<br>\n";
  } else {
    print "ERROR: Update failed.<br>\nSQLITE Error:<br>\n";
    print "{$db->lastErrorMsg()}<br>\n";
  }
?>
<h3><a href="index.php">Back to Admin</a></h3>

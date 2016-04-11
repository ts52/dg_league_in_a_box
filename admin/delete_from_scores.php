<html>
<head>
<style>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  th, td {
    padding: 7px;
  }
</style>
</head>
<body>
<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
	include '../get_config.php';
	$playerid = $_POST['playerid'];

	print "DELETING: playerid:{$playerid} week:{$week}\n";

	$delete_sql = "DELETE from scores WHERE week IS :week AND playerid IS :playerid ;";
	$del_stmt = $db->prepare($delete_sql);
	$del_stmt->bindParam(":week", $week);
	$del_stmt->bindParam(":playerid", $playerid);
	$del_ret = $del_stmt->execute();
	if (! $del_ret) {
		print "ERROR: Something went wrong<br>\n";
		print "SQL_ERROR: {$db->lastErrorMsg()}<br>\n";
	}else{
		print "Deleted!\n";
	}
?>
<h3><a href="/admin/index.php">Back to Admin</a></h3>
</body>
</html>

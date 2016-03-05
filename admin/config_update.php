<html>
<head>
</head>
<style>
  table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }
  th, td {
    padding: 7px;
  }
</style>
<body>
<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  print "<h3>Configuration for Week $week</h3>\n";

	print ("<form action='update_config.php' method='post'>\n");
  print ("Hill start order configuration: <input type='text' name='hill_start_order' size='140' value=$hill_start_order><br>\n");
  print ("General start order configuration: <input type='text' name='general_start_order' size='140' value=$general_start_order><br>\n");
	print ("Money to payout per player: <input type='text' name='amount_to_payout' value=$amount_to_payout><br>\n");
	print ("Money to ace pot per player: <input type='text' name='amount_to_ace_pot' value=$amount_to_ace_pot><br>\n");
	print ("Money to course per player: <input type='text' name='amount_to_course' value=$amount_to_course><br>\n");
	print ("Money to bonanza per player: <input type='text' name='amount_to_bonanza' value=$amount_to_bonanza><br>\n");
  print ("Current ace pot: <input type='text' name='current_ace_pot' value=\"$current_ace_pot\"><br>\n");
  print ("Max ace pot: <input type='text' name='max_ace_pot' value=\"$max_ace_pot\"><br>\n");
	print ("Number of players to payout in A pool: <input type='text' name='a_pool_payout_count' value=$a_pool_payout_count><br>\n");
  print ("A pool payout fraction of total: <input type='text' name='a_pool_payout_schedule' value=$a_pool_payout_schedule><br>\n");
	print ("Number of players to payout in B pool: <input type='text' name='b_pool_payout_count' value=$b_pool_payout_count><br>\n");
  print ("B pool payout fraction of total: <input type='text' name='b_pool_payout_schedule' value=$b_pool_payout_schedule><br>\n");
	print ("Number of players to payout in C pool: <input type='text' name='c_pool_payout_count' value=$c_pool_payout_count><br>\n");
  print ("C pool payout fraction of total: <input type='text' name='c_pool_payout_schedule' value=$c_pool_payout_schedule><br>\n");
	print ("Number of players to payout in W pool: <input type='text' name='w_pool_payout_count' value=$w_pool_payout_count><br>\n");
  print ("W pool payout fraction of total: <input type='text' name='w_pool_payout_schedule' value=$w_pool_payout_schedule><br>\n");
	print ("A pool General Handicap: <input type='text' name='a_pool_handicap' value=$a_pool_handicap><br>\n");
	print ("B pool General Handicap: <input type='text' name='b_pool_handicap' value=$b_pool_handicap><br>\n");
	print ("C pool General Handicap: <input type='text' name='c_pool_handicap' value=$c_pool_handicap><br>\n");
	print ("W pool General Handicap: <input type='text' name='w_pool_handicap' value=$w_pool_handicap><br>\n");
	print ("<input type='submit' value='Update Config'>\n");
	print ("</form>\n");
?>
<h3><a href="index.php">Back to Admin</a></h3>
</body>
</html>

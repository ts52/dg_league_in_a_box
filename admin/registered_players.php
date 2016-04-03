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
?>

<h3>All Players</h3>
<?php
  $row_count = 0;
  $registered_players_query = "SELECT * from players ORDER BY lastname ; ";
  $rpq_ret = $db->query($registered_players_query);
  while ($row = $rpq_ret->fetchArray(SQLITE3_ASSOC) ){
    if ($row_count == 0){
      print "<table border='1'>\n";
      print "<tr>\n";
      print "<td>ID</td><td>Player</td><td>Pool</td>\n";
      print "</tr>\n";
    }
      $playerid = $row['playerid'];
      $pool = $row['pool'];
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $player = "$firstname $lastname";
      print "<tr>";
      print "<td>$playerid</td>";
      print "<td>$player</td>";
      print "<td>$pool</td>";
      print "</tr>\n";
      $row_count++;
  }
  if ($row_count > 0){
    print "</table>\n";
    print "<p>There are $row_count registered players.</p>\n";
  }
?>

</body>
</html>

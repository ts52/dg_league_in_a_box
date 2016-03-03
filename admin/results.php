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

  $player_count = 0;
  $players_per_pool = array ('A' => 0, 'B' => 0, 'C' => 0, 'W' => 0);
  $ace_count = 0;
  $checked_in_players_query = "SELECT * from scores WHERE week IS :week ORDER BY handicap_score";
  $cipq_stmt = $db->prepare($checked_in_players_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_ret = $cipq_stmt->execute();
  while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
    $paid = $row['paid'];
    $ace = $row['ace'];
    $player_pool = $row['pool'];
    if (!empty($paid)){
      $player_count++;
      foreach (array('A','B','C','W') as $pool){
        if ($player_pool == $pool){
          $players_per_pool[$pool]++;
        }
      }
      if (!empty($ace)) {
        $ace_count++;
      }
    } else {
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      print "Warning: $firstname $lastname is not recorded as paid! Not counting them for player totals and payouts.<br>\n";
    }
  }
?>
<h3>Player Counts</h3>
<table border="1">
<tr><td>Total</td><td>A Pool</td><td>B Pool</td><td>C Pool</td><td>W Pool</td></tr>
<?php
  print "<tr>\n";
  print "<td>$player_count</td>\n";
  print "<td>{$players_per_pool['A']}</td>\n";
  print "<td>{$players_per_pool['B']}</td>\n";
  print "<td>{$players_per_pool['C']}</td>\n";
  print "<td>{$players_per_pool['W']}</td>\n";
  print "</tr>\n";
?>
</table>
<h3>Money</h3>
<table border="1">
<tr><td>Total Collected</td><td>Total A Pool</td><td>Total B Pool</td><td>Total C Pool</td><td>Total W Pool</td><td>Ace Pot</td><td>Course</td><td>Bonanza</td></tr>
<?php
  $collected = $player_count * ($amount_to_payout + $amount_to_ace_pot + $amount_to_course + $amount_to_bonanza);
  $pool_payout = array();
  foreach (array('A','B','C','W') as $pool){
    $pool_payout[$pool] = $players_per_pool[$pool] * $amount_to_payout;
  }
  $total_ace_pot = $current_ace_pot + ($player_count * $amount_to_ace_pot);
  if ($total_ace_pot > 250) {
    $current_ace_pot = 250;
  } else {
    $current_ace_pot = $total_ace_pot;
  }
  if ($ace_count > 0) {
    $total_ace_pot = $total_ace_pot = $current_ace_pot;
  }
  $course_money = $player_count * $amount_to_course;
  $bonanza_money = $player_count * $amount_to_bonanza;
  print "<tr>\n";
  print "<td>$collected</td>\n";
  print "<td>{$pool_payout['A']}</td>\n";
  print "<td>{$pool_payout['B']}</td>\n";
  print "<td>{$pool_payout['C']}</td>\n";
  print "<td>{$pool_payout['W']}</td>\n";
  print "<td>$current_ace_pot</td>\n";
  print "<td>$course_money</td>\n";
  print "<td>$bonanza_money</td>\n";
  print "</tr>\n";
?>
</table>
<h3>Top N per Pool</h3>
<?php
  foreach (array('A','B','C','W') as $pool){
    print "<h4>$pool Pool</h4>\n";
    $pool_results_query = "SELECT * from scores WHERE week IS :week AND pool IS :pool ORDER BY handicap_score";
    $pool_stmt = $db->prepare($pool_results_query);
    $pool_stmt->bindParam(":week",$week);
    $pool_stmt->bindParam(":pool",$pool);
    $pool_ret = $pool_stmt->execute();
    $row_count = 0;
    while (($row = $pool_ret->fetchArray(SQLITE3_ASSOC)) and ($row_count < $payout_count[$pool])){
      if ($row_count == 0){
        print "<table border='1'>\n";
        print "<tr>\n";
        print "<td>Place</td><td>Player</td><td>Course</td><td>Score</td><td>Handicap Score</td><td>Points</td><td>Payout</td>\n";
        print "</tr>\n";
      }
      $row_count++;
      print "<tr>\n";
      print "<td>$row_count</td>\n";
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $playerid = $row['playerid'];
      print "<td>$firstname $lastname</td>\n";
      print "<td>{$row['course']}</td>\n";
      print "<td>{$row['score']}</td>\n";
      print "<td>{$row['handicap_score']}</td>";
      // FIXME Add points calculation here
      $points = 5;
      print "<td>$points</td>\n";
      // FIXME Add payout calucation here
      $payout = 0;
      print "<td>$payout</td>\n";
      print "</tr>\n";
      // FIXME update table row with place, points and payout
    }
    if ($row_count > 0){
      print "</table>\n";
    }
  }
?>

</body>
</html>

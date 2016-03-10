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
  $collected = 0;
  $checked_in_players_query = "SELECT * from scores WHERE week IS :week ORDER BY handicap_score,incoming_tag";
  $cipq_stmt = $db->prepare($checked_in_players_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_ret = $cipq_stmt->execute();
  while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
    $paid = $row['paid'];
    $collected += $paid;
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
  // FIXME Need to add a Money section to admin/index.php
  // FIXME should probably add a check that the number of players in the pool is > the number of spots to payout
  $pool_payout = array();
  $pool_place_payout = array();
  foreach (array('A','B','C','W') as $pool){
    print "<!--DEBUG: calculating pool payouts for each place-->\n";
    $pool_payout[$pool] = $players_per_pool[$pool] * $amount_to_payout;
    print "<!--DEBUG: $pool pool, total payout {$pool_payout[$pool]}-->\n";
    $pool_place_payout[$pool] = array();
    $count = 0;
    while ($count < $payout_count[$pool]) {
      $payout_factor = $pool_payout_schedule[$pool][$count];
      $place_payout_tmp = $pool_place_payout[$pool][$count] = round (($pool_payout[$pool] / $payout_factor),0);
      $count++;
      print "<!-- Calculate payout for $count place -->\n";
      print "<!-- factor = $payout_factor : payout = $place_payout_tmp -->\n";
    }
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
  foreach (array('A','B','C','W') as $pool){
    print "<td>{$pool_payout[$pool]}:";
    $count = 0;
    while ($count < $payout_count[$pool]){
      print "{$pool_place_payout[$pool][$count]}";
      $count++;
      if ($count < $payout_count[$pool]) {
        print ":";
      }
    }
    print "</td>\n";
  }
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
    $pool_results_query = "SELECT * from scores WHERE week IS :week AND pool IS :pool ORDER BY handicap_score,incoming_tag";
    $pool_stmt = $db->prepare($pool_results_query);
    $pool_stmt->bindParam(":week",$week);
    $pool_stmt->bindParam(":pool",$pool);
    $pool_ret = $pool_stmt->execute();
    $row_count = 0;
    while ($row = $pool_ret->fetchArray(SQLITE3_ASSOC)){
      // FIXME - this loop needs to iterate over the entir pool and update the scores database for each player
      // FIXME - but should stop displaying after players that get pait
      if ($row_count == 0){
        print "<table border='1'>\n";
        print "<tr>\n";
        print "<td>Place</td><td>Player</td><td>Course</td><td>Score</td><td>Adjusted Score</td><td>Points</td><td>Payout</td>\n";
        print "</tr>\n";
      }
      $points = $payout_count[$pool] - $row_count;
      $place = $row_count + 1;
      $score = $row['handicap_score'];
      $tie_query = "SELECT * from scores WHERE week IS :week AND pool IS :pool AND handicap_score IS :handicap_score";
      $tie_stmt = $db->prepare($tie_query);
      $tie_stmt->bindParam(":week",$week);
      $tie_stmt->bindParam(":pool",$pool);
      $tie_stmt->bindParam(":handicap_score",$score);
      $tie_ret = $tie_stmt->execute();
      $tie_count = 0;
      $payout_sum = 0;
      while ($tie_row = $tie_ret->fetchArray(SQLITE3_ASSOC)){
        $index = $row_count + $tie_count;
        if ($index < $payout_count[$pool]) {
          $payout_sum += $pool_place_payout[$pool][$index];
        }
        $tie_count++;
      }
      $payout = round (($payout_sum / $tie_count),0);
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $playerid = $row['playerid'];
      if ($row_count < $payout_count[$pool]){
        print "<tr>\n";
        print "<td>$place</td>\n";
        print "<td>$firstname $lastname</td>\n";
        print "<td>{$row['course']}</td>\n";
        print "<td>{$row['score']}</td>\n";
        print "<td>{$row['handicap_score']}</td>";
        print "<td>$points</td>\n";
        print "<td>$payout</td>\n";
        print "</tr>\n";
      } else {
        $points = $payout = 0;
      }
      $update_payouts_query = <<<EOF
UPDATE scores
  SET payout=:payout, points=:points, place_in_pool=:place_in_pool
  WHERE week IS :week AND playerid IS :playerid;
EOF;
      $update_payouts_stmt = $db->prepare($update_payouts_query);
      $update_payouts_stmt->bindParam(":week",$week);
      $update_payouts_stmt->bindParam(":playerid",$playerid);
      $update_payouts_stmt->bindParam(":payout",$payout);
      $update_payouts_stmt->bindParam(":points",$points);
      $update_payouts_stmt->bindParam(":place_in_pool",$place_in_pool);
      $update_payouts_stmt->execute();
      $new_row_count = $row_count + $tie_count;
      while ($tie_count > 1){
        $row = $pool_ret->fetchArray(SQLITE3_ASSOC);
        $tie_count--;
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $playerid = $row['playerid'];
        if ($row_count < $payout_count[$pool]){
          print "<tr>\n";
          print "<td>$place</td>\n";
          print "<td>$firstname $lastname</td>\n";
          print "<td>{$row['course']}</td>\n";
          print "<td>{$row['score']}</td>\n";
          print "<td>{$row['handicap_score']}</td>";
          print "<td>$points</td>\n";
          print "<td>$payout</td>\n";
          print "</tr>\n";
        } else {
          $points = $payout = 0;
        }
        $tie_update_payouts_stmt = $db->prepare($update_payouts_query);
        $tie_update_payouts_stmt->bindParam(":week",$week);
        $tie_update_payouts_stmt->bindParam(":playerid",$playerid);
        $tie_update_payouts_stmt->bindParam(":payout",$payout);
        $tie_update_payouts_stmt->bindParam(":points",$points);
        $tie_update_payouts_stmt->bindParam(":place_in_pool",$place_in_pool);
        $tie_update_payouts_stmt->execute();
      }
      $row_count = $new_row_count;
    }
    if ($row_count > 0){
      print "</table>\n";
    }
  }
?>

<h3>All Players</h3>
<?php
  $row_count = 0;
  $checked_in_players_query = "SELECT * from scores WHERE week IS :week ORDER BY handicap_score,incoming_tag";
  $cipq_stmt = $db->prepare($checked_in_players_query);
  $cipq_stmt->bindParam(":week",$week);
  $cipq_ret = $cipq_stmt->execute();
  while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
    if ($row_count == 0){
      print "<table border='1'>\n";
      print "<tr>\n";
      print "<td>Place</td><td>Player</td><td>Pool</td><td>Incoming Tag</td><td>Course</td><td>Score</td><td>Adjusted Score</td>\n";
      print "</tr>\n";
    }
      $place = $row_count + 1;
      $player = "{$row['firstname']} {$row['lastname']}";
      $pool = $row['pool'];
      $incoming_tag = $row['incoming_tag'];
      $row_color = "";
      if ($incoming_tag == 9999) {
        $incoming_tag = "NO TAG";
        $row_color = "bgcolor=\"#FF0000\"";
      }
      $course = $row['course'];
      $score = $row['score'];
      $handicap_score = $row['handicap_score'];
      print "<tr $row_color>";
      print "<td>$place</td>";
      print "<td>$player</td>";
      print "<td>$pool</td>";
      print "<td>$incoming_tag</td>";
      print "<td>$course</td>";
      print "<td>$score</td>";
      print "<td>$handicap_score</td>";
      print "</tr>\n";
      $row_count++;
  }
  if ($row_count > 0){
    print "</table>\n";
  }
?>

</body>
</html>

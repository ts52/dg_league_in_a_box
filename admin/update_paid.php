<?php
	$db_file = '../dg_league.db';
	include '../db_setup.php';
  include '../get_config.php';

  $total_entries = $_POST['player_count'];
  print "<!--DEBUG: total entry count is $total_entries-->\n";
  $player_count = 0;
  while ($player_count < $total_entries) {
    $playerid_name = "playerid$player_count";
    $paid_name =  "paid$player_count";
    $playerid = $_POST[$playerid_name];
    $paid = $_POST[$paid_name];

    if (!empty($paid)){
      print "<!--DEBUG: Updating paid to $paid for playerid $playerid-->\n";

      $update_sql = <<<EOF
        UPDATE scores 
            SET paid=:paid
            WHERE week IS :week AND playerid IS :playerid;
EOF;
      $update_player_stmt = $db->prepare($update_sql);
      $update_player_stmt->bindParam(":playerid", $playerid);
      $update_player_stmt->bindParam(":week", $week);
      $update_player_stmt->bindParam(":paid", $paid);
      $update_player_stmt->execute();
    } else {
      print "<!--DEBUG: no paid value for playerid $playerid-->\n";
    }
    $player_count++;
  }
  print "Updated paid info<br>\n";

?>
<h3><a href="index.php">Back</a></h3>

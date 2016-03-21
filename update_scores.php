<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';

  $player_count = 0;
  $playerid_name = "playerid$player_count";
  while (!empty($_POST[$playerid_name])) {
    $score_name = "score$player_count";
    $playerid = $_POST[$playerid_name];
    $score = $_POST[$score_name];
    $ace_name = "ace$player_count";
    $ace = $_POST[$ace_name];

    $player_query = "SELECT * from scores WHERE playerid IS :playerid AND week IS :week;";
    $pq_stmt = $db->prepare($player_query);
    $pq_stmt->bindParam(":playerid",$playerid);
    $pq_stmt->bindParam(":week",$week);
    $pq_ret = $pq_stmt->execute();
    $row_count = 0;
    while ( $row = $pq_ret->fetchArray(SQLITE3_ASSOC) ) {
      $firstname = $row['firstname'];
      $lastname = $row['lastname'];
      $pool = $row['pool'];
      $course = $row['course'];
      $row_count++;
    }
    
    if ($row_count == 0){
      print "ERROR: no player info found for player id $playerid<br>\n";
    }

    if (empty($score)) {
      print "ERROR: No score entered for $firstname $lastname<br>\n";
    }else{
      if ($course == 'general') {
        $handicap_score = $score - $handicap[$pool];
      } else {
        $handicap_score = $score;
      }

      print "Updating score for $firstname $lastname, week $week, to $score : $handicap_score<br>\n";

      $update_sql = <<<EOF
        UPDATE scores 
            SET score=:score,ace=:ace,handicap_score=:handicap_score
            WHERE week IS :week AND playerid IS :playerid;
EOF;
      $update_player_stmt = $db->prepare($update_sql);
      $update_player_stmt->bindParam(":playerid", $playerid);
      $update_player_stmt->bindParam(":week", $week);
      $update_player_stmt->bindParam(":score", $score);
      $update_player_stmt->bindParam(":ace", $ace);
      $update_player_stmt->bindParam(":handicap_score", $handicap_score);
      $update_player_stmt->execute();
    }

    $player_count++;
    $playerid_name = "playerid$player_count";
  }

?>
<h3><a href="index.php">Back</a></h3>

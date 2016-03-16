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
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';
?>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    print "<!-- DEBUG processing POST -->\n";
    $playerids = $_POST['playerid'];
    $general_card_query =  "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS NOT :start_hole ;";
    $gcq_stmt = $db->prepare($general_card_query);
    $gcq_stmt->bindValue(":course","general");
    $gcq_stmt->bindValue(":start_hole","W");
    $gcq_stmt->bindParam(":week",$week);
    $gcq_ret = $gcq_stmt->execute();
    if ( ! $gcq_ret ) print "<h3> ERROR: sqlite error: {$db->lastErrorMsg()} </h3>\n";
    $card_count = 0;
    $taken_holes = array();
    while ( $row = $gcq_ret->fetchArray(SQLITE3_ASSOC) ) {
      if (empty($taken_holes[$row['start_hole']])) {
        $card_count++;
        $taken_holes[$row['start_hole']] = TRUE;
      }
    }
    print "<!-- DEBUG card count is $card_count -->\n";
    $start_hole = $general_start_array[$card_count];
    print "<!-- DEBUG start hole is $start_hole-->\n";
    $player_update = "UPDATE scores SET start_hole=:start_hole WHERE week IS :week AND playerid IS :playerid ;";
    $pu_stmt = $db->prepare($player_update);
    $pu_stmt->bindParam(":week",$week);
    $pu_stmt->bindParam(":start_hole",$start_hole);
    foreach ( $playerids as $playerid ) {
      $pu_stmt->reset();
      $pu_stmt->bindParam(":playerid",$playerid);
      $ret = $pu_stmt->execute();
      if ( ! $ret ) {
        print "ERROR: Failed to update playerid $playerid<br>\n";
      }
    }
    print "<h3>Assigned to hole $start_hole</h3>\n";
  } else {
    print "<!-- DEBUG no POST -->\n";
    $waiting_player_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS :start_hole; ";
    $wpq_stmt = $db->prepare($waiting_player_query);
    $wpq_stmt->bindValue(":course","general");
    $wpq_stmt->bindValue(":start_hole","W");
    $wpq_stmt->bindParam(":week", $week);
    $wpq_ret = $wpq_stmt->execute();
    $player_count = 0;
    while ( $row = $wpq_ret->fetchArray(SQLITE3_ASSOC) ) {
      if ($player_count == 0) {
        print "<form action='form_general_card.php' method='post'>\n";
        print "<table border='1'>\n";
        print "<tr><td></td><td>Player</td><td>Pool</td></tr>\n";
      }
      $playerid = $row['playerid'];
      print "<tr><td>\n";
      print "<input type='checkbox' name='playerid[]' value=\"{$playerid}\">\n";
      print "</td>\n";
      print "<td>{$row['firstname']} {$row['lastname']}</td>\n";
      print "<td>{$row['pool']}</td>\n";
      print "</tr>\n";
      $player_count++;
    }
    if ($player_count > 0){
      print "</table>\n";
      print "<input type='submit' value='Form Card'>\n";
      print "</form>\n";
    }
  }
?>

<h3><a href="index.php">Back to Check In</a></h3>

</body>
</html>

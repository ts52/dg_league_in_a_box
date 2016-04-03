<!-- The idea here is to show what is needed depending on server state -->
<!-- Initial state shows an admin login, which leads to ./admin after login to configure and control things -->
<!-- When the admin has 'opened up sign in', this page will show the sign in -->
<!-- After sign in is closed, this page will switch to score entry -->
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>
<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';
  if ($system_state == 'check_in_open') {
print "<!-- DEBUG check in open -->\n";
    $player_query = "SELECT * from scores WHERE week IS :week ;";
    $hill_player_count = $general_player_count = 0;
    $taken_holes = array();
    $pq_stmt = $db->prepare($player_query);
    $pq_stmt->bindParam(":week",$week);
print "<!-- DEBUG set up for player counting -->\n";
    $pq_ret = $pq_stmt->execute();
print "<!-- DEBUG after SQL query -->\n";
    while ( $row = $pq_ret->fetchArray(SQLITE3_ASSOC) ) {
      if ($row['course'] == 'hill'){
print "<!-- DEBUG counting player for the hill -->\n";
        $hill_player_count++;
      } elseif ($row['course'] == 'general') {
print "<!-- DEBUG counting player for the general -->\n";
        if ($row['start_hole'] == "W") {
print "<!-- DEBUG counting player for the general waiting area -->\n";
          $general_player_count++;
        } else {
print "<!-- DEBUG counting player for the general hole {$row['start_hole']} -->\n";
          $taken_holes[$row['start_hole']] = TRUE;
        }
      }
    }
print "<!-- DEBUG after count loop -->\n";
    foreach ( $taken_holes as $hole => $value ) {
      print "<!-- DEBUG: general_hole $hole is already taken -->\n";
      $general_player_count += 4;
    }
    print "<h3>Check in</h3>";
    print "Current week is $week<br>";
    print "The Hill is $hill_player_count/$hill_max_players full.<br>\n";
    if ($general_open) {
      print "The General is $general_player_count/$general_max_players full.<br>\n";
    } else {
      print "The General is closed.<br>\n";
    }
    print "<br>\n";
    print "<form action='check_in_search.php' method='post'>";
    if ($hill_player_count < $hill_max_players) print "<input type='radio' name='course' value='hill' required>The Hill</input><br>";
    if ($general_open and ($general_player_count < $general_max_players) ) print "<input type='radio' name='course' value='general' required>The General</input><br>";
    print "Last Name: <input type='text' name='lastname' required><br>";
    print ('<input type="submit" value="Submit">');
    print ('</form>');
  } elseif ($system_state == 'score_entry') {
    print ("<h3>Score Entry</h3>");
    print "Current week is $week<br>";
    print "<form action='score_entry_search.php' method='post'>";
    print "Last Name: <input type='text' name='lastname'><br>";
    //print "Starting Hole: <input type='text' name='start_hole'><br>";
    print ('<input type="submit" value="Submit">');
    print ('</form>');
  } else {
    print ("Check in not open<br>");
  }
?>
</body>
</html>

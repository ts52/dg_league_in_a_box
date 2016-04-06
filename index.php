<?php session_start(); ?>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1" />
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
	if ( isset($_SESSION["playerid"]) ) {
		if ($system_state == 'check_in_open') {
		} elseif ($system_state == 'score_entry') {
		} else {
		}
	} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
		if ($_POST['function'] == 'check_in_search'){
			$lastname = $_POST['lastname'];
			$course = $_POST['course'];

			$player_query = "SELECT * from players WHERE lastname LIKE :lastname;";
			$pq_stmt = $db->prepare($player_query);
			$pq_stmt->bindParam(":lastname",$lastname);
			$pq_ret = $pq_stmt->execute();
			$matching_player_count = 0;
			while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
				$matching_player_count++;
			}
			if ($matching_player_count == 0){
				print "No matching players found<br>";
			} else {
				$row_count = 0;
				$pq_ret->reset();
				while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
					if ($row_count == 0){
						// print table header
						print "<h3>Matching Players</h3>";
					}
					$row_count++;
					$playerid = $row['playerid'];
					$lastname = $row['lastname'];
					$firstname = $row['firstname'];
					$pool = $row['pool'];
					print "<form action='index.php' method='post'>";
					print "<input type='hidden' name='function' value='check_in_player'/>\n";
					$selected = "";
					if ($matching_player_count == 1) {
						$selected = "checked";
					}
					print "<input type='radio' name='playerid' $selected value=$playerid>$firstname $lastname ID#:$playerid Pool:$pool</input><br>";
				}
				if ($row_count != 0) {
					// close table
					print "Incoming Tag #: <input type='text' name='incoming_tag'> Leave blank for no tag<br>";
					print "<input type='hidden' name='course' value=$course>";
					print "<input type='submit' value='Check In'>";
					print "</form>";
					print "<hr>";
				}
			}
		} elsif ($_POST['function'] == 'check_in_player') {
			$playerid = $_POST['playerid'];
			$course = $_POST['course'];
			$incoming_tag = $_POST['incoming_tag'];

			if (empty($playerid)){
				print "ERROR: Trying to check in an empty playerid<br>\n";
			}else{
				$pay_amount = '$5';
				if (empty($incoming_tag)){
					$pay_amount = '$7';
					$incoming_tag = 9999;
				}

				$player_count_query = "SELECT * from scores WHERE week IS :week ;";
				$hill_player_count = $general_player_count = 0;
				$taken_holes = array();
				$pcq_stmt = $db->prepare($player_count_query);
				$pcq_stmt->bindParam(":week",$week);
				$pcq_ret = $pcq_stmt->execute();
				while ( $row = $pcq_ret->fetchArray(SQLITE3_ASSOC) ) {
					if ($row['course'] == 'hill'){
						$hill_player_count++;
					} elseif ($row['course'] == 'general') {
						if ($row['start_hole'] == "W") {
							$general_player_count++;
						} else {
							$taken_holes[$row['start_hole']] = TRUE;
						}
					}
				}
				foreach ( $taken_holes as $hole => $value ) {
					$general_player_count += 4;
				}

				$course_closed = 0;
				if ($course == 'hill') {
					print "<!-- DEBUG checking if the hill is full -->\n";
					if ( $hill_player_count >= $hill_max_players ) {
						print "<!-- DEBUG the hill is full -->\n";
						$course_closed = 1;
					}
				} else {
					print "<!-- DEBUG checking if the general is closed or full -->\n";
					if ( $general_open == 0 ) {
						print "<!-- DEBUG the general is closed -->\n";
						$course_closed = 1;
					} elseif ( $general_player_count >= $general_max_players ) {
						print "<!-- DEBUG the general is full -->\n";
						$course_closed = 1;
					}
				}

				if ( $course_closed ) { 
					print "<h1>The {$course} is closed.</h1> <h1><a href='index.php'>Please try the other course.</a></h1>\n";
				} else {

					$player_query = "SELECT * from players WHERE playerid IS :playerid";
					$pq_stmt = $db->prepare($player_query);
					$pq_stmt->bindParam(":playerid",$playerid);
					$pq_ret = $pq_stmt->execute();
					while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
						$lastname = $row['lastname'];
						$firstname = $row['firstname'];
						$pool = $row['pool'];
					}

					$player_query = "SELECT * from scores WHERE week IS :week AND playerid IS :playerid";
					$pqq_stmt = $db->prepare($player_query);
					$pqq_stmt->bindParam(":week",$week);
					$pqq_stmt->bindParam(":playerid",$playerid);
					$pqq_ret = $pqq_stmt->execute();
					$player_count = 0;
					while ($row = $pqq_ret->fetchArray(SQLITE3_ASSOC) ){
						$player_count++;
						$course = $row['course'];
						$start_hole = $row['start_hole'];
					}

					print ("<!--DEBUG: found $player_count players checked in with ID:$playerid-->\n");

					if ($player_count != 0) {
						print "$firstname $lastname is already checked in to the $course on hole $start_hole<br>";
					} else {
						print "<!--Checking $firstname $lastname in to the $course for week $week-->\n";

						$checked_in_player_query = "SELECT * from scores WHERE week IS :week AND course IS :course";
						$cipq_stmt = $db->prepare($checked_in_player_query);
						$cipq_stmt->bindParam(":week",$week);
						$cipq_stmt->bindParam(":course",$course);
						$cipq_ret = $cipq_stmt->execute();
						$player_count = 0;
						while ($row = $cipq_ret->fetchArray(SQLITE3_ASSOC) ){
							$player_count++;
						}

						print "<!--There are already $player_count players checked in to the $course-->\n";

						if ($course == 'hill'){
							$start_hole = $hill_start_array[$player_count];
						}elseif ($course == 'general'){
							$start_hole = "W";
						}

						$insert_sql = <<<EOF
							INSERT INTO scores
									(playerid,lastname,firstname,pool,week,course,incoming_tag,start_hole)
									VALUES
									(:playerid,:lastname,:firstname,:pool,:week,:course,:incoming_tag,:start_hole);
EOF;
						$add_player_stmt = $db->prepare($insert_sql);

						$add_player_stmt->bindParam(":playerid", $playerid);
						$add_player_stmt->bindParam(":lastname", $lastname);
						$add_player_stmt->bindParam(":firstname", $firstname);
						$add_player_stmt->bindParam(":pool", $pool);
						$add_player_stmt->bindParam(":week", $week);
						$add_player_stmt->bindParam(":course", $course);
						$add_player_stmt->bindParam(":incoming_tag", $incoming_tag);
						$add_player_stmt->bindParam(":start_hole", $start_hole);
						$add_player_stmt->execute();

						print "<!-- Checking how many players already on hole $start_hole on the $course -->\n";
						$hole_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS :start_hole";
						$hq_stmt = $db->prepare($hole_query);
						$hq_stmt->bindParam(":week", $week);
						$hq_stmt->bindParam(":course", $course);
						$hq_stmt->bindParam(":start_hole", $start_hole);
						$hq_ret = $hq_stmt->execute();
						$player_count = 0;
						while($row = $hq_ret->fetchArray(SQLITE3_ASSOC) ){
							$player_count++;
						}
						print "<!-- $player_count players are on this hole. -->\n";

						print ("Player $firstname $lastname checked in to the $course<br>\n");
						if ($course == "hill") print ("<h2>Your start hole is $start_hole.</h2>\n");
						if ($player_count == 1 and $course == "hill") {
							print ("<h3>Please put your tag on the board, pay $pay_amount and get a scorecard.</h3>\n");
						} else {
							print ("Please put your tag on the board and pay $pay_amount.<br>\n");
						}
						if ($course == "general"){
							print "<br><a href='form_general_card.php'>Find other players waiting for a card on the General</a>\n";
						}
					}
				}
			}
		} elsif ($_POST['function'] == 'score_entry_search') {
			$lastname = $_POST['lastname'];
			$playerid = $_POST['playerid'];

			print "<!--DEBUG: in score_entry_search.php-->\n";
			$player_query = "SELECT * from scores WHERE week IS :week AND";
			if (!empty($playerid)) {
				$player_query = "$player_query playerid IS :playerid;";
			} else {
				$player_query = "$player_query lastname LIKE :lastname;";
			}
			print "<!--DEBUG: player_query:($player_query)-->\n";
			$pq_stmt = $db->prepare($player_query);
			print "<!--DEBUG: binding :lastname to $lastname-->\n";
			$pq_stmt->bindParam(":lastname",$lastname);
			if (!empty($playerid)){
				print "<!--DEBUG: binding :playerid to $playerid -->\n";
				$pq_stmt->bindParam(":playerid",$playerid);
			}
			print "<!--DEBUG: binding :week to $week-->\n";
			$pq_stmt->bindParam(":week",$week);
			$pq_ret = $pq_stmt->execute();
			$row_count = 0;
			while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
				if (empty($row['score'])){
					$row_count++;
					$course = $row['course'];
					$start_hole = $row['start_hole'];
				}
			}
			if ($row_count > 1) {
				print "<!--DEBUG: more than 1 matching player-->\n";
				print "<!--DEBUG: row_count = $row_count-->\n";
				// more than one matching last name on the course
				print "<form action'score_entry_search.php' method='post'>\n";
				$pq_ret->reset();
				$row_count = 0;
				while ($row = $pq_ret->fetchArray(SQLITE3_ASSOC) ){
					if ($row_count == 0){
						// print table header
						print "<h3>Matching Players</h3>\n";
						print "<table border='1'>\n";
						print "<tr><td></td><td>Player Name</td><td>Pool</td><td>Course</td><td>Start Hole</td></tr>\n";
					}
					if (empty($row['score'])){
						$row_count++;
						$playerid = $row['playerid'];
						$lastname = $row['lastname'];
						$firstname = $row['firstname'];
						$pool = $row['pool'];
						$course = $row['course'];
						$start_hole = $row['start_hole'];
						print "<tr>\n";
						print "<td><input type='radio' name='playerid' value=$playerid></td>\n";
						print "<td>$firstname $lastname</td>\n";
						print "<td>$pool</td>\n";
						print "<td>$course</td>\n";
						print "<td>$start_hole</td>\n";
						print "</tr>\n";
					}
				}
				print "</table>\n";
				print "<input type='hidden' name='lastname' value=$lastname>\n";
				print "<input type='submit' value='Select Player'>\n";
				print "</form>\n";
			} elseif ($row_count == 1) {
				print "<!--DEBUG: exactly 1 matching player-->\n";
				print "<!--DEBUG: start hole is $start_hole-->\n";
				// exactly one match, use start_hole to find the card and get scores
				$card_query = "SELECT * from scores WHERE week IS :week AND course IS :course AND start_hole IS :start_hole ORDER BY incoming_tag;";
				$cq_stmt = $db->prepare($card_query);
				$cq_stmt->bindParam(":week", $week);
				$cq_stmt->bindParam(":course", $course);
				$cq_stmt->bindParam(":start_hole", $start_hole);
				print "<h3>Enter Scores for the $course, starting hole $start_hole</h3>\n";
				print "<!--DEBUG: week:$week course:$course start_hole:$start_hole-->\n";
				print "<!--DEBUG: Searching for players on the same card with query:\n$card_query-->\n";
				$cq_ret = $cq_stmt->execute();
				$row_count = 0;
				while ( $row = $cq_ret->fetchArray(SQLITE3_ASSOC) ) {
					if ($row_count == 0){
						print "<form action='update_scores.php' method='post'>\n";
						print "<table border='1'>\n";
						print "<tr><td>Player</td><td>Pool</td><td>Score</td><td>Ace Hole</td></tr>\n";
					}
					$playerid = $row['playerid'];
					$firstname = $row['firstname'];
					$lastname = $row['lastname'];
					$pool = $row['pool'];
					$course = $row['course'];
					$playerid_name = "playerid$row_count";
					print "<input type='hidden' name=$playerid_name value=$playerid >\n";
					$score_name = "score$row_count";
					print "<tr><td>$firstname $lastname</td><td>$pool</td>\n";
					print "<td><input type='text' name=$score_name></td>\n";
					$ace_name = "ace$row_count";
					print "<td><input type='text' name=$ace_name></td></tr>\n";
					$row_count++;
				}
				if ($row_count > 0) {
					print "</table><input type='submit' value='Submit Scores'></form>\n";
				} else {
					print "ERROR: no players found for hole $start_hole on the $course in week $week<br>\n";
				}
			} else {
				print "No matching players found with lastname: $lastname on the $course<br>\n";
				print "<a href='index.php'>Try again.</a>\n";
			}
		} else {
		}
	} else {
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
			print "<form action='index.php' method='post'>";
			print "<input type='hidden' name='function' value='check_in_search'/>\n";
			if ($hill_player_count < $hill_max_players) print "<input type='radio' name='course' value='hill' required>The Hill</input><br>";
			if ($general_open and ($general_player_count < $general_max_players) ) print "<input type='radio' name='course' value='general' required>The General</input><br>";
			print "Last Name: <input type='text' name='lastname' required><br>";
			print ('<input type="submit" value="Submit"/>');
			print ('</form>');
		} elseif ($system_state == 'score_entry') {
			print ("<h3>Score Entry</h3>");
			print "Current week is $week<br>";
			print "<form action='index.php' method='post'>";
			print "<input type='hidden' name='function' value='score_entry_search'/>\n";
			print "Last Name: <input type='text' name='lastname'><br>";
			//print "Starting Hole: <input type='text' name='start_hole'><br>";
			print ('<input type="submit" value="Submit">');
			print ('</form>');
		} else {
			print ("Check in not open<br>");
		}
	}
?>
</body>
</html>

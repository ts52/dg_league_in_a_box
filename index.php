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
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$function = $_POST['function'];
		print "<!-- DEBUG _POST function is {$function} -->\n";
		if ($function == 'check_in_search'){
			check_in_search($db);
		} elseif ($function == 'check_in_player') {
			check_in_player($db, $week, $hill_max_players, $general_open, $general_max_players);
		} elseif ($function == 'score_entry_search') {
			score_entry_search($_POST['playerid'], $_POST['lastname'], $db, $week);
		} elseif ($function == 'update_scores') {
			update_scores($db, $week, $handicap);
		} else {
		}
	} elseif ( isset($_SESSION["playerid"]) ) {
		print "<!-- DEBUG _SESSION[playerid] is set to {$_SESSION['playerid']} -->\n";
		if ($system_state == 'check_in_open') {
			check_in_start($db, $week, $general_open, $general_max_players, $hill_max_players);
		} elseif ($system_state == 'score_entry') {
			score_entry_search($_SESSION['playerid'], "", $db, $week);
		} else {
			print ("Check in not open<br>");
		}
	} else {
		print "<!-- DEBUG No SESSION, no POST -->\n";
		if ($system_state == 'check_in_open') {
			check_in_start($db, $week, $general_open, $general_max_players, $hill_max_players);
		} elseif ($system_state == 'score_entry') {
			score_entry_start($week);
		} else {
			print ("Check in not open<br>");
		}
	}
?>
</body>
</html>

<?php
	// Function definitions
	
	function check_in_start($db, $week, $general_open, $general_max_players, $hill_max_players) {
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
		print "<h3>Check In</h3>";
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
		$selected = "";
		if ( ( ! $general_open ) or ( $general_player_count >= $general_max_players ) or ( $hill_player_count >= $hill_max_players ) ) $selected = "checked";
		if ($hill_player_count < $hill_max_players) {
			print "<input type='radio' name='course' value='hill' {$selected} required>The Hill</input><br>\n";
		}
		if ($general_open and ($general_player_count < $general_max_players) ) {
			print "<input type='radio' name='course' value='general' {$selected} required>The General</input><br>\n";
		}
		print "Last Name: <input type='text' name='lastname' required><br>";
		print "<input type='checkbox' name='remember' value='1' checked>Remember Me<br>\n";
		print ('<input type="submit" value="Submit"/>');
		print ('</form>');
		print "<br>\n";
		print "<h3><a href='form_general_card.php'>General Waiting List</a></h3>\n";
	}

	function score_entry_start($week) {
		print ("<h3>Score Entry</h3>");
		print "Current week is $week<br>";
		print "<form action='index.php' method='post'>";
		print "<input type='hidden' name='function' value='score_entry_search'/>\n";
		print "Last Name: <input type='text' name='lastname'><br>";
		print ('<input type="submit" value="Submit">');
		print ('</form>');
	}

	function check_in_search($db) {
		$lastname = $_POST['lastname'];
		$course = $_POST['course'];
		$remember = $_POST['remember'];
		if ($remember) $_SESSION["lastname"] = $lastname;

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
				if ($remember) print "<input type='hidden' name='remember' value='1'/>\n";
				$selected = "";
				if ($matching_player_count == 1) {
					$selected = "checked";
				}
				print "<input type='radio' name='playerid' $selected value=$playerid>$firstname $lastname : $pool Pool</input><br>";
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
	}

	function check_in_player($db, $week, $hill_max_players, $general_open, $general_max_players) {
		$admin_request = 0;
		if (isset($_POST['admin_request'])) $admin_request = 1;
		$playerid = $_POST['playerid'];
		$course = $_POST['course'];
		$incoming_tag = $_POST['incoming_tag'];
		$remember = $_POST['remember'];

		if (empty($playerid)){
			print "ERROR: Trying to check in an empty playerid<br>\n";
		}else{
			if ($remember) $_SESSION["playerid"] = $playerid;
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

			if ( $course_closed and !	$admin_request ) { 
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
	}

	function score_entry_search($playerid, $lastname, $db, $week) {

		print "<!--DEBUG: in score_entry_search.php-->\n";
		$player_query = "SELECT * from scores WHERE week IS :week AND";
		if (!empty($playerid)) {
			$player_query = "$player_query playerid IS :playerid;";
		} else {
			$player_query = "$player_query lastname LIKE :lastname;";
		}
		print "<!--DEBUG: player_query:($player_query)-->\n";
		$pq_stmt = $db->prepare($player_query);
		if (!empty($playerid)){
			print "<!--DEBUG: binding :playerid to $playerid -->\n";
			$pq_stmt->bindParam(":playerid",$playerid);
		} else {
			print "<!--DEBUG: binding :lastname to $lastname-->\n";
			$pq_stmt->bindParam(":lastname",$lastname);
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
			print "<form action'index.php' method='post'>\n";
			print "<input type='hidden' name='function' value='score_entry_search'/>\n";
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
					print "<form action='index.php' method='post'>\n";
					print "<input type='hidden' name='function' value='update_scores'/>\n";
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
	}

	function update_scores($db, $week, $handicap) {
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
	}
?>

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
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$pool = $_POST['pool'];

  if ( empty($firstname) or empty($lastname) or empty($pool) ) {
    if (empty($firstname)) print "ERROR: First Name must not be empty<br>\n";
    if (empty($lastname)) print "ERROR: Last Name must not be empty<br>\n";
    if (empty($pool)) print "ERROR: Pool must not be empty<br>\n";
  } else {

    $insert_sql = <<<EOF
      INSERT INTO players 
          (lastname,firstname,pool)
          VALUES
          (:lastname,:firstname,:pool);
EOF;
    $add_player_stmt = $db->prepare($insert_sql);

    $add_player_stmt->bindParam(":lastname", $lastname);
    $add_player_stmt->bindParam(":firstname", $firstname);
    $add_player_stmt->bindParam(":pool", $pool);
    $add_player_stmt->execute();
    print ("Player $firstname $lastname added to $pool pool<br>");
  }
?>
<h3><a href="/admin/index.php">Back to Admin</a></h3>
</body>
</html>

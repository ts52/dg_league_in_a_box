<!-- The idea here is to show what is needed depending on server state -->
<!-- Initial state shows an admin login, which leads to ./admin after login to configure and control things -->
<!-- When the admin has 'opened up sign in', this page will show the sign in -->
<!-- After sign in is closed, this page will switch to score entry -->
<?php
	$db_file = './dg_league.db';
	include './db_setup.php';
  include './get_config.php';
  if ($system_state == 'check_in_open') {
    print "<h3>Check in</h3>";
    print "Current week is $week<br>";
    print "<form action='check_in_search.php' method='post'>";
    print "<input type='radio' name='course' value='hill'>The Hill</input>  ";
    print "<input type='radio' name='course' value='general'>The General</input><br>";
    print "Last Name: <input type='text' name='lastname'><br>";
    print ('<input type="submit" value="Submit">');
    print ('</form>');
  } else {
    print ("Check in not open<br>");
  }
?>

<?php 

// if no user handling, 
	// check for file management enabled or not
	// check for automatic deletion for over quota enabled or not
	// check for quota limitation for upload (over or not)
// if with user handling, check for
	// download rights
	// upload rights
	// file management (rename, delete) rights
	// admin rights
	// separate home folder
	// common (and shared) folder
	// check for user quota
// all rights of which should be returned by the user class 

// session handler should work with current folder
	// working folder checks for rights 


class accessControl {
	
	function dbconnect() {
		if (!isset($dbconn)) {
			$dbconn = new mysqli($dbhost, $dbuser, $dbpass, $dbname;
			if ($dbconn->connect_error) {
				// homework!! Error handling;
				echo "<h1> Fatal error! </h1>";
			}
		}
	}
	

}

?>

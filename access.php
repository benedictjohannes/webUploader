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


/*

DATABASE TABLE STRUCTURES:
	user		-> id, name, token, login, password, homelimit (negative = no homedir, 0 = nolimit, positive = MB), admin (1=yes)
	folder 		-> id, name, limit
	--- single share = 00, MASTER, 0;
	folderpermit-> folder id, user, permission (0 = no permissions, 1 = read, 2 = read upload, 3 = read manage, 4 = read manage upload)
	--- single share = applies
	uploads		-> timestamp, url, size, user id, status (1 = success, 0 = initiate)
	--- think whether it is necessary: times
	

*/





function random_hex($length)
{
	if (version_compare(phpversion(), '7.0.0', '>=')) {
		$token = bin2hex(random_bytes($length));
	} elseif (version_compare(phpversion(), '5.3.0', '>=')) {
		$token = bin2hex(openssl_random_pseudo_bytes($length)); 
	} else {
		$pieces = [];
		for ($i = 0; $i < $length ; ++$i) {
			$pieces[] = dechex(rand(0,255));
		}
		$token = implode('', $pieces);
	}
	return $token;
}

?>

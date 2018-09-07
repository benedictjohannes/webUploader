<?php 

//session management
session_start();
if (isset($_SESSION['discard_after'])) {if (time()>$_SESSION['discard_after']) {
	// this session has expired; kill it and start a brand new one
	session_unset(); session_destroy(); session_start();
}}
    // either new or old, sessions are kept for $expireTimeout
$_SESSION['discard_after'] = time() + $expireTimeout; 
// end of session management
	// still not designed: user login session handling


function random_hex($length) {
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

<?php 
require_once ('settings.php');
require_once ('access.php');

/* 
 * session management
 */
session_start();

if (isset($_SESSION['discard_after'])) {if (time()>$_SESSION['discard_after']) {
	// this session has expired; kill it and start a brand new one
	session_unset(); session_destroy(); session_start();
}}
    // either new or old, sessions are kept for $expireTimeout
$_SESSION['discard_after'] = time() + $expireTimeout; 

?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload file from URL</title>
</head>
<body>
<?php

/*
 * The following commented part is for working commands were working for (only) remote file upload
 * Recoding to fit better OOP practices and support other feature lists.
 *
if (isset($_POST['url'])) {
function progressCallback($ch , $download_size, $downloaded_size, $upload_size, $uploaded_size ) {
    static $previousProgress = 0;
    
    if ( $download_size == 0 )
        $progress = 0;
    else
        $progress = round( $downloaded_size * 100 / $download_size );
    
    if ( $progress > $previousProgress) {
        $previousProgress = $progress;
        $fp = fopen( 'progress.txt', 'a' );
        fputs( $fp, date('ymdHis',time()) . "\t\t$progress\n"  );
        fclose( $fp );
    }
}
////////////////////////////////////////////////////////////////////////////////
    $url = $_POST['url'];
    $url_array = explode('/',$url);
    $name = end($url_array);
                    file_put_contents( 'progress.txt', '' ); //HOMEWORK!!
    $targetFile = fopen( $name, 'w' );
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
    curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
    curl_setopt( $ch, CURLOPT_FILE, $targetFile );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_exec( $ch );
		echo "I'm here already!"; var_dump($ch);
    if (isset($targetFile)) {
    fclose($targetFile);
    }
}
//curl_close($ch); //HOMEWORK!!
 */


class uploadCurl {
    private $url;
    private $name;
    
    public function setUrl($urlInput) {
        if (filter_var($urlInput, FILTER_VALIDATE_URL)) {
            $this->url = $urlInput;
        } else {
                echo "$urlInput is not a valid URL"; //HOMEWORK!! error handling
        }    
    }
    public function getUrl() {
        if (isset($this->url)) {
            return $this->url;
        } else {
            return NULL;
        }
    }    
    
    public function setName($nameInput) {
        if (!is_null($nameInput)) {
            $this->name = $nameInput;
        } 
    }
    public function mkName() {
        if (isset($this->url)) {
            $exploded_url = explode('/',$this->url);
            $this->name = end($exploded_url );
        } else {
            echo("Cannot infer name"); //HOMEWORK!! error handling
        }    
    }    
    public function getName() {
        if (isset($this->name)) {
            return $this->name;
        } else  {
            return NULL;
        }
    }
    public function progressCallback($ch , $download_size, $downloaded_size, $upload_size, $uploaded_size ) {
        static $previousProgress = 0;
    
        if ( $download_size == 0 )
            $progress = 0;
        else
            $progress = round( $downloaded_size * 100 / $download_size );
        
        if ( $progress > $previousProgress) {
            $previousProgress = $progress;
            $fp = fopen( 'progress.txt', 'a' );
            fputs( $fp, date('ymdHis',time()) . "\t\t$progress\n"  );
            fclose( $fp );
        }
        //return $progress;
    }
    public function doDownload() {
			// homework: target file should be set with working folder
        $targetFile = fopen( $this->getName(), 'w' );
        $ch = curl_init( $this->url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 
				array($this,'progressCallback')
				/*
				'progressCallback'  
				*/
				);
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_exec( $ch );
			// var_dump($ch);
        if (isset($targetFile)) {
            fclose($targetFile);
        }
    }
    function __construct($getUrlInput) {
        $this->setUrl($getUrlInput);
    }
}
?>


	<!-- HOMEWORK move to upload form class -->
	<!--  HOMEWORK upload form only occurs if upload rights are present in the current folder -->
	
<?php $BASE_URL = strtok($_SERVER['REQUEST_URI'],'?');?>


    <form name='upload' method='post' action="<?php echo $BASE_URL; ?>">
        <input type='text' id='url' name='url' size='128' /><br>
        <input type='text' id='filenameUserInput' name='filenameUserInput' size='128' /><br>
        <input type="submit" name='execute' value="Upload">
    </form>
	
<?php 

if (isset($_POST['url'])) {
    $wuUploader = new uploadCurl($_POST['url']);
    if (strlen($_POST['filenameUserInput'])>0) {
        $wuUploader->setName($_POST['filenameUserInput']);
    } else {
        $wuUploader->mkName();
    }
}
if (isset($_POST['execute'])) {
    echo "<br> execute handler / button is clicked";
	$wuUploader->doDownload(); echo "<br> do download called";
}


?>

<!-------------------------------------
below these lines: new document structure
-------------------------------------->

<?php


if ($wuPermissionEnable) {
	if (!isset($dbconn)) {
		$dbconn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($dbconn->connect_error) {
			// homework!! Error handling;
			echo "<h1> FATAL ERROR: Cannot access database </h1>"; 
		}
	}
	// now that database access is established, we should start:
		// check session for user login validity
		// check session for working folder path
		// get permission for the user
			// upload permission: check wuTotalSize and user folder size usage
				// if not over limit, enable upload form
			// download & manage permission for the folder
} else {
	echo "no permissions mode enabled";
}

// next part: go to document handling
	// welcoming header for the user
	// if upload form is enabled, render
	// if manage permission is enabled, render folder list with rename/delete buttons
	// if download permission is enabled, render folder list without rename/delete buttons
	// if user is admin, prepare admin form 
	// --- (total folder stats only)
	// --- if clicked admin console, load folder management, user management. 

?>



</body>
</html>
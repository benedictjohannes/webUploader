<?php 
require_once ('settings.php');
require_once ('access.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload file from URL</title>
</head>
<body>

<!--t-h-e--P-L-A-N-N-I-N-G---------------------
below these lines: new document structure as designed
document structure design:
	welcoming header for the user
	if upload form is enabled, render
	if manage permission is enabled, render folder list with rename/delete buttons
	if download permission is enabled, render folder list without rename/delete buttons
	if user is admin, prepare admin form 
logic design:
	if no user handling, 
		check for file management enabled or not
		check for automatic deletion for whole folder over quota, enabled or not
		check for quota limitation for upload (over or not)
		check for maximum upload file size & remaining capacity of wholefolder
	if with user handling, check for
		download rights
		upload rights
		file management (rename, delete) rights
		admin rights
		separate home folder
		common (and shared) folder
		check for user quota
	all rights of which should be returned by the user class 
	session handler should work with current folder
		working folder checks for rights 
database table structure design:
	user		-> id, name, token, login, password, homelimit (negative = no homedir, 0 = nolimit, positive = MB), admin (1=yes)
	folder 		-> id, name, limit
	--- single share = 00, MASTER, 0;
	folderpermit-> folder id, user, permission (0 = no permissions, 1 = read, 2 = read upload, 3 = read manage, 4 = read manage upload)
	--- single share = applies
	uploads		-> timestamp, url, size, user id, status (1 = success, 0 = initiate)
	--- think whether it is necessary: times
-->

<?php
								// echo "<br> execution reached here";

function wuFolderScan($dir) {
// wuFolderScan returns array of all files and subdirs ( 0=>name, 1=>size and 2=>bool_is_directory)
	$files = array();
    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
		if (is_file($each)) {
			$files[] = array($each,filesize($each),0);
		}
		else {
			$files[] = array($each,wuFolderSize($each),0);
		}
    }
	return $files;
}

function wuFolderSize($dir) {
// wuFolderSize returns size of given folder and all subdirectories
    $size = 0;
    foreach (glob(rtrim($dir, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : wuFolderSize($each);
    }
    return $size;
}

function wuHumanFilesize($bytes, $decimals = 2) {
    $size = array('B','kB','MB','GB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . $size[$factor];
}

/// MAINHOMEWORK here: start with permission logic!
if ($wuPermissionEnable) {
	if (!isset($dbconn)) {
		$dbconn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		if ($dbconn->connect_error) {
			// HOMEWORK no database access error handling;
			echo "<h1> FATAL ERROR: Cannot access database </h1>"; 
		}
	}
	// HOMEWORK now that database connection is established, we should start:
		// check session for user login validity
		// check session for working folder path
		// get permission for the user
			// upload permission: check wuTotalSize and user folder size usage
				// if not over limit, enable upload form
			// download & manage permission for the folder
} else {
// no permissions mode codes here
	if ( file_exists('./'.$wuSingleFolderName) && !is_file('./'.$wuSingleFolderName) && isset($wuTotalSizeLimitMB) ) { 
		$wuSingleFolderSize = wuFolderSize('./'.$wuSingleFolderName);
		if ( $wuTotalSizeLimitMB==0 | ( $wuSingleFolderSize <= ($wuTotalSizeLimitMB*1024*1024) ) ) {
			$wuSingleFolderSizeClear = 1;
		} else {
			$wuSingleFolderSizeClear = 0;
		}
		// HOMEWORK no permission mode: display upload form, display folder content
	} else { 
		$wuNotInitiated = 1 ;
				// HOMEWORK initiation mode: create folder
		echo "<br> Folder Not initiated";
	}
}

?>

<!-- uploadCurl has no subfolder handling capability yet, should add getter & setter for sub folder path
<?php class uploadCurl {
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
				);
        curl_setopt( $ch, CURLOPT_FILE, $targetFile );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_exec( $ch );
        if (isset($targetFile)) {
            fclose($targetFile);
        }
    }
    function __construct($getUrlInput) {
        $this->setUrl($getUrlInput);
    }
}
?>

<!-- define PHP form listeners -->
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
	$wuUploader->doDownload(); 
    echo "<br> doDownload has been called";
}

?>

<!-- Form Creation -->
<div>
	<!-- HOMEWORK move to upload form class -->
	<!--  HOMEWORK upload form only occurs if upload rights are present in the current folder -->
	<?php $BASE_URL = strtok($_SERVER['REQUEST_URI'],'?');?>
	<form name='upload' method='post' action="<?php echo $BASE_URL; ?>">
		<input type='text' id='url' name='url' size='128' /><br>
		<input type='text' id='filenameUserInput' name='filenameUserInput' size='128' /><br>
		<input type="submit" name='execute' value="Upload">
	</form>
</div>

</body>
</html>
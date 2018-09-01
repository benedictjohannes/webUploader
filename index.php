<?php 
require_once ('settings.php');

if (isset($_SESSION['discard_after'])) {if (time()>$_SESSION['discard_after']) {
        // this session has expired; kill it and start a brand new one
        session_unset(); session_destroy(); session_start();
}}
    // either new or old, sessions are kept for $expireTimeout
$_SESSION['discard_after'] = time() + $expireTimeout;
session_start;
    /* for troubleshooting */ if (isset($_SESSION)) { var_dump($_SESSION); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload file from URL</title>
</head>
<body>
<?php

/*
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
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
    curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
    curl_setopt( $ch, CURLOPT_FILE, $targetFile );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_exec( $ch );
    if (isset($targetFile)) {
    fclose($targetFile);
    }
}
//curl_close($c); //HOMEWORK!!
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
    private function progressCallback($ch , $download_size, $downloaded_size, $upload_size, $uploaded_size ) {
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
        return $progress;
    }
    public function doDownload() {
        $targetFile = fopen( getname(), 'w' );
        $ch = curl_init( $url );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $ch, CURLOPT_NOPROGRESS, false );
        curl_setopt( $ch, CURLOPT_PROGRESSFUNCTION, 'progressCallback' );
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




    <form name='upload' method='post' action="<?php echo $BASE_URL; ?>">
        <input type='text' id='url' name='url' size='128' /><br>
        <input type='text' id='filenameUserInput' name='filenameUserInput' size='128' /><br>
        <input type="submit" name='execute' value="Upload">
    </form>
	
<?php 

if (isset($_POST['url'])) {
    $myUploader = new uploadCurl($_POST['url']);
    if (strlen($_POST['filenameUserInput'])>0) {
        $myUploader->setName($_POST['filenameUserInput']);
        
    } else {
        $myUploader->mkName();
    }
}
if (isset($_POST['execute'])) {
    echo "<br> execute handler / button is clicked";
}

?>
</body>
</html>
<?php 
// time to expiration of session, where after timeout, user have to login again
$expireTimeout = 1800 ;

// $webuploader_limit
// $upload_size_limit


// size limit for the whole WebUploader folder, in MB. Set 0 for unlimited.
$wuTotalSizeLimitMB = 1024;

// Permission and access management: 
// Set 1 for enabled, where database connection and user authentication would be required
// Set 0 for disabled, for which single folder size limit would be the only limiter.
$wuPermissionEnable = 0;

// Single Folder Mode (no Access Management enabled)
$wuSingleFolderName = "main";

// overwrite oldest file upon hitting WebUploader whole folder size limit:
$wuOverwriteOldest = 1;


// maximum upload file size limit
$wuMaxUploadSize = 0;

// A folder for each user
$wuUserHome = 1;

// Multiple Shared Folder
$wuMultipleShares = 1;

// user database settings 
$dbhost = 'localhost';
$dbname = 'webuploader';
$dbuser = 'wwwLocal';
$dbpass = 'wwwLocalAdmin';




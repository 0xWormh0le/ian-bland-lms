<?php
// set max file size in MB
######## This for the remote video size  #######
$max_file_size=10;// in MB for the PC upload
######## This for the remotevideo size  #######
ini_set("memory_limit","10M"); // The max  remote video size to download 1M means 100 megabytes

$delete_files   = 24; // after how many hours the uploaded video, converted video and logs get deleted  means after  hours
$proxy          = ""; //  set your proxy details use the correct proxy format (IP:Port) Example for a working proxy: 1.179.147.2:8080
$proxyuser      = ""; // set your proxy user name if there any
$proxypass      = ""; // set your proxy password if there any
$secretkey      = 'voottacool';

$allowedExtensions = array("3gp","avi","mpg","mpeg","mpe4","mov","m4a","mj2","flv","wmv","mp4","ogg","webm","mkv"); // suported input types

##### Uncomment for your server operating system ####
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $ffmpeg="ffmpeg"; // windows
} else {
    $ffmpeg="ffmpeg"; // linux
}
### leagcy paths
//$ffmpeg="C:\ffmpeg\bin"; // location of ffmpeg on windows (ffmpeg.exe can be in same folder with this script				
$ffmpeg="ffmpeg";
$ffmpeg="/usr/local/bin/ffmpeg"; // this is default location of ffmpeg on linux - you should check yours with phpinfo()	

$max_file_size_in_bytes = $max_file_size * 1000000 ;
?>
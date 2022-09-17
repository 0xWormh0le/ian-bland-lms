<?php
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $convert_call="start /b ".$call; // windows
} else {
    $convert_call=$call." >/dev/null &"; // linux
}

?>
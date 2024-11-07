<?php
date_default_timezone_set('Asia/Kolkata');
$response = array(
    'currentDateTime' => date('Y-m-d H:i:s'),
    'currentTime' => floor(microtime(true) * 1000),
    'currentYear' => date('Y')
);
echo json_encode($response);
?>
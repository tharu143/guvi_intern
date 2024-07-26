<?php
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$token = $_GET['token'];
$id = $redis->get($token);

$response = array();
if ($id) {
    $profileData = json_decode($redis->get("user:$id:profile"), true);
    $response['success'] = true;
    $response['profile'] = $profileData;
} else {
    $response['success'] = false;
    $response['message'] = "Invalid token.";
}

echo json_encode($response);
?>

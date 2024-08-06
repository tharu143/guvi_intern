<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Ensure you have the MongoDB PHP library

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$token = $_GET['token'];
$id = $redis->get($token);

$response = array();
if ($id) {
    // Try to get the profile data from Redis_server
    $profileData = json_decode($redis->get("user:$id:profile"), true);

    if (!$profileData) {
        // If profile data is not found in Redis, get it from MongoDB
        $client = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $client->guvi->profile;

        // Fetch profile data from MongoDB
        $profileData = $collection->findOne(['user_id' => $id]);

        if ($profileData) {
            // Store the profile data in Redis server for future requests
            $redis->set("user:$id:profile", json_encode($profileData));
        } else {
            $response['success'] = false;
            $response['message'] = "Profile not found.";
            echo json_encode($response);
            exit();
        }
    }

    $response['success'] = true;
    $response['profile'] = $profileData;
} else {
    $response['success'] = false;
    $response['message'] = "Invalid token.";
}

echo json_encode($response);
?>

<?php
require_once '../vendor/autoload.php';
use MongoDB\Client;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$token = $_GET['token'];
$id = $redis->get($token);

$response = array();
if ($id) {
    // Try to get the profile data from Redis
    $profileData = $redis->get("user:$id:profile");

    if ($profileData) {
        $response['success'] = true;
        $response['profile'] = json_decode($profileData, true);
    } else {
        // If not found in Redis, get it from MongoDB
        $client = new Client('mongodb://localhost:27017/');
        $database = $client->selectDatabase('guvi');
        $profileCollection = $database->selectCollection('profile');

        $profile = $profileCollection->findOne(['user_id' => $id]);

        if ($profile) {
            $response['success'] = true;
            $response['profile'] = $profile;
            // Cache the profile data in Redis
            $redis->set("user:$id:profile", json_encode($profile));
        } else {
            $response['success'] = false;
            $response['message'] = "Profile not found.";
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid session.";
}

echo json_encode($response);
?>

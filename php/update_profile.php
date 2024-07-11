<?php
require_once '../vendor/autoload.php';
use MongoDB\Client;

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

$token = $_POST['token'];
$id = $redis->get($token);

if ($id) {
    $client = new Client('mongodb://localhost:27017/');
    $database = $client->selectDatabase('guvi');
    $profileCollection = $database->selectCollection('profile');

    $age = $_POST['age'];
    $dob = $_POST['dob'];
    $contact = $_POST['contact'];

    $filter = ['user_id' => $id];
    $update = ['$set' => ['age' => $age, 'dob' => $dob, 'contact' => $contact]];
    $options = ['upsert' => true];

    $result = $profileCollection->updateOne($filter, $update, $options);

    $response = array();
    if ($result->getMatchedCount() || $result->getUpsertedCount()) {
        // Update cache in Redis
        $profileData = [
            'user_id' => $id,
            'age' => $age,
            'dob' => $dob,
            'contact' => $contact
        ];
        $redis->set("user:$id:profile", json_encode($profileData));

        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = "Profile update failed.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid session.";
}

echo json_encode($response);
?>

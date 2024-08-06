<?php
require_once __DIR__ . '/../vendor/autoload.php'; //  MongoDB PHP library on locally

// Connect to Redis server
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);

// Get the token and fetch the user ID from Redis server
$token = $_POST['token'];
$id = $redis->get($token);

$response = array();
if ($id) {
    $dob = $_POST['dob'];
    $emergency_contact = $_POST['emergency_contact'];

    // Handle file upload to img
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        
        // Ensure the target directory exists
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo_path = $target_file;
        } else {
            $photo_path = "";
            $response['message'] = "Failed to upload photo.";
        }
    } else {
        $photo_path = "";
    }

    // Retrieve and update profile data in Redis server
    $profileData = json_decode($redis->get("user:$id:profile"), true);
    $profileData['dob'] = $dob;
    $profileData['emergency_contact'] = $emergency_contact;
    if ($photo_path) {
        $profileData['photo'] = $photo_path;
    }
    $redis->set("user:$id:profile", json_encode($profileData));

    // MongoDB connection init
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $collection = $client->guvi->profile;

    // Check if profile exists in MongoDB
    $profileDataMongo = $collection->findOne(['user_id' => $id]);
    if ($profileDataMongo) {
        // Update existing profile in MongoDB
        $collection->updateOne(
            ['user_id' => $id],
            ['$set' => ['dob' => $dob, 'emergency_contact' => $emergency_contact, 'photo' => $photo_path]]
        );
    } else {
        // Insert new profile into MongoDB
        $collection->insertOne([
            'user_id' => $id,
            'dob' => $dob,
            'emergency_contact' => $emergency_contact,
            'photo' => $photo_path
        ]);
    }

    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['message'] = "Invalid token.";
}

echo json_encode($response);
?>

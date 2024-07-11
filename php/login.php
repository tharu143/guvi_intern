<?php
$host = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "guvi";

$conn = new mysqli($host, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, fname, lname, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $fname, $lname, $hashed_password);
$stmt->fetch();
$stmt->close();

$response = array();
if (password_verify($password, $hashed_password)) {
    $token = bin2hex(random_bytes(16));
    $redis = new Redis();
    $redis->connect('127.0.0.1', 6379);
    $redis->set($token, $id, 3600);

    // Store user profile data in Redis
    $profileData = [
        'id' => $id,
        'fname' => $fname,
        'lname' => $lname,
        'email' => $email
    ];
    $redis->set("user:$id:profile", json_encode($profileData));

    $response['success'] = true;
    $response['token'] = $token;
} else {
    $response['success'] = false;
    $response['message'] = "Invalid credentials.";
}

$conn->close();
echo json_encode($response);
?>

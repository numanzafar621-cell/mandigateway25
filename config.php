<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbname = "mandigateway_db";
$username = "root";
$password = "";

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) die("Connection failed: " . mysqli_connect_error());

define('BASE_URL', 'http://localhost/mandigateway/'); // اپنی URL ڈالیں
define('UPLOAD_DIR', 'uploads/');

if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);

function getCurrentStore() {
    global $conn;
    $host = $_SERVER['HTTP_HOST'];
    $parts = explode('.', $host);
    if (count($parts) >= 3) {
        $subdomain = mysqli_real_escape_string($conn, $parts[0]);
        $query = "SELECT s.*, u.* FROM stores s 
                  JOIN users u ON s.user_id = u.id 
                  WHERE s.subdomain = '$subdomain' AND u.status = 'active'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        }
    }
    return false;
}
?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$con = new mysqli("localhost", "root", "", "akcafe");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $stmt = $con->prepare("DELETE FROM cafe_feedback WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$con->close();

header("Location: feedbackReviews.php");
exit();
?>

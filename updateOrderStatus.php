<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Establish a database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()]));
}

// Check if the necessary POST data is available
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    die(json_encode(['success' => false, 'message' => 'Invalid request']));
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Update the order status in the database
try {
    $stmt = $pdo->prepare("UPDATE cafe_order SET status = :status WHERE order_id = :order_id");
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

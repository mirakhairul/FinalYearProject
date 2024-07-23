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
    die("Connection failed: " . $e->getMessage());
}

// Initialize date range variables
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Ensure the date format is YYYY-MM-DD for MySQL compatibility
$start_date = !empty($start_date) ? date('Y-m-d', strtotime($start_date)) : '';
$end_date = !empty($end_date) ? date('Y-m-d', strtotime($end_date)) : '';

// Create the SQL query with date range filter
$sql = 'SELECT * FROM cafe_order';
if (!empty($start_date) && !empty($end_date)) {
    $sql .= ' WHERE DATE(order_date) BETWEEN :start_date AND :end_date';
}
$sql .= ' ORDER BY order_date DESC';

$stmt = $pdo->prepare($sql);

// Bind date range parameters if set
if (!empty($start_date) && !empty($end_date)) {
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
}

$stmt->execute();

// Fetch the results and return as JSON
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($orders);
?>

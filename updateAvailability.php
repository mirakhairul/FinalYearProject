<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');

    $prod_id = $_POST['prod_id'];
    $availability = $_POST['availability'];

    $stmt = $pdo->prepare('UPDATE cafe_product SET availability = ? WHERE prod_id = ?');
    $stmt->execute([$availability, $prod_id]);

    header('Location: productPage.php');
}
?>

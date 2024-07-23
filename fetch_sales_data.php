<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

try {
    $pdo = new PDO('mysql:host=localhost;dbname=akcafe', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action == 'fetch_sales' && isset($_POST['start_date']) && isset($_POST['end_date'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];

            if ($start_date && $end_date) {
                $stmt = $pdo->prepare("SELECT DATE(order_date) as date, SUM(total_price) as total_sales FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ? GROUP BY DATE(order_date)");
                $stmt->execute([$start_date, $end_date]);
                
                $salesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $labels = array_column($salesData, 'date');
                $sales = array_column($salesData, 'total_sales');

                // Fetch total sales
                $stmt = $pdo->prepare("SELECT SUM(total_price) as total_sales FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
                $stmt->execute([$start_date, $end_date]);
                $totalSales = $stmt->fetch(PDO::FETCH_ASSOC)['total_sales'];

                echo json_encode(['labels' => $labels, 'sales' => $sales, 'totalSales' => $totalSales]);
            }
        }

        if ($action == 'fetch_top_products' && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['category'])) {
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $category = $_POST['category'];

            $stmt = $pdo->prepare("SELECT order_items FROM cafe_order WHERE status = 'complete' AND order_date BETWEEN ? AND ?");
            $stmt->execute([$start_date, $end_date]);

            $productSales = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $order_items = json_decode($row['order_items'], true);
                if (is_array($order_items)) {
                    foreach ($order_items as $item) {
                        $prod_name = $item['prod_name'];

                        if ($category) {
                            $stmt_category = $pdo->prepare("SELECT category_name FROM cafe_product WHERE prod_name = ?");
                            $stmt_category->execute([$prod_name]);
                            $product_category = $stmt_category->fetch(PDO::FETCH_ASSOC)['category_name'];

                            if ($product_category != $category) {
                                continue;
                            }
                        }

                        if (isset($productSales[$prod_name])) {
                            $productSales[$prod_name]++;
                        } else {
                            $productSales[$prod_name] = 1;
                        }
                    }
                }
            }

            arsort($productSales);
            $topProducts = array_slice($productSales, 0, 10);

            $response = [];
            foreach ($topProducts as $name => $count) {
                $response[] = ['name' => $name, 'count' => $count];
            }

            echo json_encode(['topProducts' => $response]);
        }
    }
}
?>

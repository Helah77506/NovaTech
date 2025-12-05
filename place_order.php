<?php
// place_order.php
// Configuration
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'novatech';
$username = 'root';  // Change this to your database username
$password = '';      // Change this to your database password

// Create database connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Get JSON data from request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!$data || !isset($data['cart']) || !is_array($data['cart']) || empty($data['cart'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid cart data provided.'
    ]);
    exit;
}

// Get user ID (you can get this from session if user is logged in)
// For now, using a default user ID or from the request
$userId = isset($data['user_id']) ? (int)$data['user_id'] : 1;

// Calculate total from cart items
$total = 0;
$cartItems = $data['cart'];

foreach ($cartItems as $item) {
    if (!isset($item['price']) || !isset($item['quantity'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid item data in cart.'
        ]);
        exit;
    }
    $total += floatval($item['price']) * intval($item['quantity']);
}

// Start transaction
try {
    $pdo->beginTransaction();
    
    // Insert order into orders table
    $stmt = $pdo->prepare("INSERT INTO orders (User_ID, Total) VALUES (:user_id, :total)");
    $stmt->execute([
        ':user_id' => $userId,
        ':total' => $total
    ]);
    
    // Get the inserted order ID
    $orderId = $pdo->lastInsertId();
    
    // Optional: Create order_items table if you want to track individual items
    // You would need to add this table to your database first
    /*
    CREATE TABLE IF NOT EXISTS order_items (
        ID INT AUTO_INCREMENT PRIMARY KEY,
        Order_ID INT NOT NULL,
        Product_ID INT,
        Product_Name VARCHAR(255) NOT NULL,
        Price DECIMAL(10,2) NOT NULL,
        Quantity INT NOT NULL,
        Subtotal DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (Order_ID) REFERENCES orders(ID) ON DELETE CASCADE
    );
    */
    
    // Insert order items (uncomment if you create the order_items table)
    /*
    $stmtItem = $pdo->prepare("
        INSERT INTO order_items (Order_ID, Product_ID, Product_Name, Price, Quantity, Subtotal) 
        VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)
    ");
    
    foreach ($cartItems as $item) {
        $subtotal = floatval($item['price']) * intval($item['quantity']);
        $stmtItem->execute([
            ':order_id' => $orderId,
            ':product_id' => isset($item['id']) ? intval($item['id']) : null,
            ':product_name' => $item['name'],
            ':price' => floatval($item['price']),
            ':quantity' => intval($item['quantity']),
            ':subtotal' => $subtotal
        ]);
    }
    */
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_id' => $orderId,
        'total' => $total
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to place order: ' . $e->getMessage()
    ]);
}
?>

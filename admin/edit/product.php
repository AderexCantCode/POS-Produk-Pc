<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();

function getProductById($pdo, $product_id) {
    $sql = "SELECT * FROM product WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$product_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateProduct($pdo, $product_id, $price, $stock) {
    // Only update price and stock
    $sql = "UPDATE product SET price = ?, stock = ? WHERE product_id = ?";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([$price, $stock, $product_id]);
}

function setFlashMessage($type, $message) {
    $_SESSION[$type] = $message;
}

function redirect($location) {
    header("Location: $location");
    exit();
}

$product_id = $_GET['id'] ?? null;
$error = $success = '';
$product = null;

if ($product_id) {
    $product = getProductById($pdo, $product_id);
    if (!$product) {
        setFlashMessage('error', "Product not found.");
        redirect('../masterdata.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    
    $result = updateProduct($pdo, $product_id, $price, $stock);
    if ($result) {
        setFlashMessage('success', "Product updated successfully.");
        redirect('../masterdata.php');
    } else {
        $error = "Failed to update product.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex justify-center items-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Edit Product</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($success) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['product_id']) ?>">
                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-dollar-sign"></i> Price
                    </label>
                    <input type="number" name="price" value="<?= htmlspecialchars($product['price']) ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="mb-4">
                    <label for="stock" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-cubes"></i> Stock
                    </label>
                    <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
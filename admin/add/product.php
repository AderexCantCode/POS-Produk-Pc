<?php
require '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $created_at = date('Y-m-d H:i:s'); 
    $gambar = $_FILES['gambar'];

    if (empty($product_name) || empty($category_id) || empty($price) || empty($stock)) {
        $message = "All fields are required!";
    } else {
        $target_dir = '../../../../Industri/POS_ProjekAKhirV2/assets/img/';
        $target_file = $target_dir . basename($gambar["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($gambar["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $message = "File is not an image.";
            $uploadOk = 0;
        }
        if (file_exists($target_file)) {
            $message = "Sorry, file already exists.";
            $uploadOk = 0;
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }
        if ($uploadOk == 1) {
            if (move_uploaded_file($gambar["tmp_name"], $target_file)) {
                try {
                    $sql = "INSERT INTO product (product_name, category_id, price, stock, created_at, gambar) VALUES (:product_name, :category_id, :price, :stock, :created_at, :gambar)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':product_name' => $product_name,
                        ':category_id' => $category_id,
                        ':price' => $price,
                        ':stock' => $stock,
                        ':created_at' => $created_at,
                        ':gambar' => $target_file
                    ]);

                    header("Location: ../masterdata.php");
                } catch (PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        }
    }
}

// Ambil daftar kategori dari database
$categories = [];
try {
    $stmt = $pdo->query("SELECT category_id, category_name FROM category");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
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
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Create Product</h2>

            <!-- Tampilkan pesan status -->
            <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 bg-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-200 text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-700 rounded">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="product_name" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-box"></i> Product Name
                    </label>
                    <input type="text" id="product_name" name="product_name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-list"></i> Category
                    </label>
                    <select id="category_id" name="category_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo $category['category_name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="price" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-dollar-sign"></i> Price
                    </label>
                    <input type="number" id="price" name="price" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="stock" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-cubes"></i> Stock
                    </label>
                    <input type="number" id="stock" name="stock" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="gambar" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-image"></i> Product Image
                    </label>
                    <input type="file" id="gambar" name="gambar" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-plus"></i> Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

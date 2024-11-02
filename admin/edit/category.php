<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $category_name = $_POST['category_name'];

    $sql_update = "UPDATE category SET category_name = ? WHERE category_id = ?";
    $stmt = $pdo->prepare($sql_update);
    $stmt->execute([$category_name, $category_id]);

    header('Location: ../masterdata.php');
}

// Ambil data kategori yang akan diedit
$category_id = $_GET['id'];
$sql_category = "SELECT * FROM category WHERE category_id = ?";
$stmt = $pdo->prepare($sql_category);
$stmt->execute([$category_id]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
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
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Edit Category</h2>

            <form method="POST">
                <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['category_id']) ?>">
                
                <div class="mb-4">
                    <label for="category_name" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-tags"></i> Category Name
                    </label>
                    <input type="text" name="category_name" value="<?= htmlspecialchars($category['category_name']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
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

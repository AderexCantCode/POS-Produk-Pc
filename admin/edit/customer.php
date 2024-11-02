<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql_update = "UPDATE customer SET name = ?, email = ?, phone = ?, address = ? WHERE customer_id = ?";
    $stmt = $pdo->prepare($sql_update);
    $stmt->execute([$name, $email, $phone, $address, $customer_id]);

    header('Location: ../masterdata.php');
}

// Ambil data customer yang akan diedit
$customer_id = $_GET['id'];
$sql_customer = "SELECT * FROM customer WHERE customer_id = ?";
$stmt = $pdo->prepare($sql_customer);
$stmt->execute([$customer_id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Customer</title>
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
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Edit Customer</h2>

            <form method="POST">
                <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customer_id']) ?>">
                
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user"></i> Name
                    </label>
                    <input type="text" name="name" value="<?= htmlspecialchars($customer['name']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" name="email" value="<?= htmlspecialchars($customer['email']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-phone"></i> Phone
                    </label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-home"></i> Address
                    </label>
                    <textarea name="address" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required><?= htmlspecialchars($customer['address']) ?></textarea>
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

<?php
require '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();

$message = ""; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $created_at = $_POST['created_at'];

    // validasi
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $message = "All fields are required!";
    } else {
        // Menambah 
        try {
            $sql = "INSERT INTO customer (name, email, phone, address, created_at) VALUES (:name, :email, :phone, :address, :created_at)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':address' => $address,
                ':created_at' => $created_at
            ]);

            header("Location: ../masterdata.php");
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer</title>
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
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Create Customer</h2>

            <!-- Display status message -->
            <?php if (!empty($message)): ?>
                <div class="mb-4 p-4 bg-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-200 text-<?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>-700 rounded">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user"></i> Name
                    </label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="phone" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-phone"></i> Phone
                    </label>
                    <input type="text" id="phone" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-home"></i> Address
                    </label>
                    <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <div class="mb-6">
                    <label for="created_at" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-calendar"></i> Created At
                    </label>
                    <input type="datetime-local" id="created_at" name="created_at" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="bg-blue-500 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-600 transition duration-200">
                        <i class="fas fa-plus"></i> Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

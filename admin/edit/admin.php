<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';
cekLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['admin_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql_update = "UPDATE admin SET username = ?, email = ?, password = ? WHERE admin_id = ?";
    $stmt = $pdo->prepare($sql_update);
    $stmt->execute([$username, $email, $password, $admin_id]);

    header('Location: ../masterdata.php');
}

$admin_id = $_GET['id'];
$sql_admin = "SELECT * FROM admin WHERE admin_id = ?";
$stmt = $pdo->prepare($sql_admin);
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
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
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-6">Edit Admin</h2>

            <form method="POST">
                <input type="hidden" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']) ?>">

                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" id="password" name="password" value="<?= htmlspecialchars($admin['password']) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
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

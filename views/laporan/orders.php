<?php
require_once '../../system/koneksi.php';
cekLogin();

function getOrders($pdo) {
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.total_amount, o.order_date, o.status, 
               c.name AS customer_name, a.username AS admin_name
        FROM orders o
        JOIN customer c ON o.customer_id = c.customer_id
        JOIN admin a ON o.admin_id = a.admin_id
        ORDER BY o.order_date DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function updateOrderStatus($pdo, $orderId, $newStatus) {
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE order_id = :orderId");
    return $stmt->execute([
        ':status' => $newStatus,
        ':orderId' => $orderId
    ]);
}

function getOrderDetails($pdo, $orderId) {
    $stmt = $pdo->prepare("
        SELECT p.product_name, od.quantity, od.price, (od.quantity * od.price) as subtotal
        FROM orders_detail od
        JOIN product p ON od.product_id = p.product_id
        WHERE od.order_id = :orderId
    ");
    $stmt->execute([':orderId' => $orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function deleteOrder($pdo, $orderId) {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM orders_detail WHERE order_id = :orderId");
        $stmt->execute([':orderId' => $orderId]);

        $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = :orderId");
        $stmt->execute([':orderId' => $orderId]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deleteOrder') {
    $orderId = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $success = deleteOrder($pdo, $orderId);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
} elseif (isset($_POST['action']) && $_POST['action'] === 'updateStatus') {
    $orderId = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $newStatus = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    $success = updateOrderStatus($pdo, $orderId, $newStatus);
    header('Content-Type: application/json');
    echo json_encode(['success' => $success]);
    exit;
}

if (isset($_GET['action']) && $_GET['action'] === 'getOrderDetails') {
    $orderId = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $orderDetails = getOrderDetails($pdo, $orderId);
    header('Content-Type: application/json');
    echo json_encode($orderDetails);
    exit;
}

$orders = getOrders($pdo);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Order Report</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .sidebar { transition: width 0.3s ease; }
        .sidebar:hover { width: 16rem; }
        .modal { transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out; }
        .modal-active { overflow: hidden; }
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-left-color: #3B82F6;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .tab-content { display: none; }
        .tab-active { display: block; }
    </style>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Sidebar -->
    <aside class="sidebar w-16 bg-blue-600 text-white fixed h-full hover:w-64 overflow-hidden transition-all duration-300 ease-in-out">
        <?php include '../templates/sidebar.php'; ?>
    </aside>

    <div class="ml-16 p-8">
        <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-8">
                <h1 class="text-3xl font-bold mb-6 text-gray-800">Laporan Orderan Customer <i class="fas fa-file-alt ml-2 text-blue-500"></i></h1>
                
                <!-- Tabs -->
                <div class="mb-6">
                    <ul class="flex border-b border-gray-200">
                        <li class="mr-1">
                            <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold rounded-t-lg cursor-pointer transition duration-300 ease-in-out" onclick="showTab('orders')">Orders</a>
                        </li>
                        <li class="mr-1">
                            <a class="bg-white inline-block py-2 px-4 text-blue-500 hover:text-blue-800 font-semibold rounded-t-lg cursor-pointer transition duration-300 ease-in-out" onclick="showTab('edit')">Edit</a>
                        </li>
                    </ul>
                </div>

                <!-- Orders Tab Content -->
                <div id="orders" class="tab-content tab-active">
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Customer Name</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Admin Name</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Total Amount</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Order Date</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 border-b border-gray-200 text-left text-xs leading-4 font-medium text-gray-600 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($orders as $order): ?>
                                    <tr class="hover:bg-gray-50 transition duration-150 ease-in-out">
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium text-gray-900"><?= htmlspecialchars($order['order_id']) ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500"><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500"><?= htmlspecialchars($order['admin_name']) ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                                        <td class="px-6 py-4 whitespace-no-wrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $order['status'] == 'Completed' ? 'bg-green-100 text-green-800' : ($order['status'] == 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($order['status'] == 'Canceled' ? 'bg-red-100 text-red-800' : 'bg-red-100 text-red-800')) ?>">
                                            <?= htmlspecialchars(ucfirst($order['status'])) ?>
                                        </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 font-medium">
                                            <button class="text-blue-600 hover:text-blue-900 mr-3 transition duration-150 ease-in-out" onclick="openModal(<?= $order['order_id'] ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                            <button class="text-red-600 hover:text-red-900 transition duration-150 ease-in-out" onclick="deleteOrder(<?= $order['order_id'] ?>)">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Edit Tab Content -->
                <div id="edit" class="tab-content hidden">
                    <div class="overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Order ID</th>
                                    <th scope="col" class="px-6 py-3">Customer Name</th>
                                    <th scope="col" class="px-6 py-3">Admin Name</th>
                                    <th scope="col" class="px-6 py-3">Total Amount</th>
                                    <th scope="col" class="px-6 py-3">Order Date</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= htmlspecialchars($order['order_id']) ?></td>
                                        <td class="px-6 py-4"><?= htmlspecialchars($order['customer_name']) ?></td>
                                        <td class="px-6 py-4"><?= htmlspecialchars($order['admin_name']) ?></td>
                                        <td class="px-6 py-4">Rp<?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                        <td class="px-6 py-4"><?= date('d M Y H:i', strtotime($order['order_date'])) ?></td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 font-semibold text-xs leading-tight rounded-full 
                                                <?= $order['status'] === 'Completed' ? 'text-green-700 bg-green-100' : 
                                                ($order['status'] === 'Pending' ? 'text-yellow-700 bg-yellow-100' : 
                                                'text-red-700 bg-red-100') ?>">
                                                <?= htmlspecialchars($order['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <select class="block w-full px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                                    onchange="changeOrderStatus(<?= $order['order_id'] ?>, this.value)">
                                                <option value="Pending" <?= $order['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Completed" <?= $order['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="Canceled" <?= $order['status'] === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="orderModal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center">
        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>
        
        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded shadow-lg z-50 overflow-y-auto">
            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3">
                    <p class="text-2xl font-bold text-gray-900">Order Details</p>
                    <div class="modal-close cursor-pointer z-50">
                        <i class="fas fa-times text-gray-500 hover:text-gray-800 transition duration-150 ease-in-out"></i>
                    </div>
                </div>

                <div id="modal-body">
                    <div class="spinner mx-auto"></div>
                </div>

                <div class="flex justify-end pt-2">
                    <button class="modal-close px-4 bg-blue-500 p-3 rounded-lg text-white hover:bg-blue-400 transition duration-150 ease-in-out">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
                tab.classList.remove('tab-active');
            });
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('tab-active');
        }

        function openModal(orderId) {
            const modal = document.getElementById('orderModal');
            const modalBody = document.getElementById('modal-body');
            
            modal.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('modal-active');
            
            modalBody.innerHTML = '<div class="spinner mx-auto"></div>';
            
            fetch(`?action=getOrderDetails&order_id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    let html = '<table class="min-w-full divide-y divide-gray-200">';
                    html += '<thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th></tr></thead>';
                    html += '<tbody class="bg-white divide-y divide-gray-200">';
                    
                    let total = 0;
                    data.forEach(item => {
                        html += `<tr>
                            <td class="px-6 py-4 whitespace-nowrap">${item.product_name}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${item.quantity}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp${Number(item.price).toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap">Rp${Number(item.subtotal).toLocaleString()}</td>
                        </tr>`;
                        total += Number(item.subtotal);
                    });
                    
                    html += `<tr class="bg-gray-50">
                        <td colspan="3" class="px-6 py-4 whitespace-nowrap text-right font-bold">Total:</td>
                        <td class="px-6 py-4 whitespace-nowrap font-bold">Rp${total.toLocaleString()}</td>
                    </tr>`;
                    html += '</tbody></table>';
                    
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error:', error);
                    modalBody.innerHTML = '<p class="text-red-500">Error loading order details. Please try again.</p>';
                });
        }
        function changeOrderStatus(orderId, newStatus) {
        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=updateStatus&order_id=${orderId}&status=${newStatus}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order status updated successfully.');
                location.reload();
            } else {
                alert('Failed to update order status. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }

        function closeModal() {
            const modal = document.getElementById('orderModal');
            modal.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('modal-active');
        }

        function deleteOrder(orderId) {
            if (confirm('Anda Yakin Menghapus Orderan Ini?')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=deleteOrder&order_id=${orderId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order berhasil dihapus.');
                        location.reload();
                    } else {
                        alert('Gagal menghapus order. Silakan coba lagi.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            }
        }

        document.querySelectorAll('.modal-close').forEach(element => {
            element.addEventListener('click', closeModal);
        });

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && document.body.classList.contains('modal-active')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
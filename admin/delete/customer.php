<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';

$customer_id = $_GET['id'];

$sql_delete = "DELETE FROM customer WHERE customer_id = ?";
$stmt = $pdo->prepare($sql_delete);
$stmt->execute([$customer_id]);

header('Location: ../masterdata.php');
?>

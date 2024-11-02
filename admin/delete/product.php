<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';

$product_id = $_GET['id'];

$sql_delete = "DELETE FROM product WHERE product_id = ?";
$stmt = $pdo->prepare($sql_delete);
$stmt->execute([$product_id]);

header('Location: ../masterdata.php');
?>

<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';

$admin_id = $_GET['id'];

$sql_delete = "DELETE FROM admin WHERE admin_id = ?";
$stmt = $pdo->prepare($sql_delete);
$stmt->execute([$admin_id]);

header('Location: ../masterdata.php');
?>

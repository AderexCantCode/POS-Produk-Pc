<?php
include '../../../../Industri/POS_ProjekAkhirV2/system/koneksi.php';

$category_id = $_GET['id'];

$sql_delete = "DELETE FROM category WHERE category_id = ?";
$stmt = $pdo->prepare($sql_delete);
$stmt->execute([$category_id]);

header('Location: ../masterdata.php'); 
?>

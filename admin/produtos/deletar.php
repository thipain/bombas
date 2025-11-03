<?php 
include '../../config/conexao.php';
require_once '../auth.php';

$id = $_GET['id'] ?? 0;

$sql = "DELETE FROM produtos WHERE id=$id";
if($conn->query($sql) === TRUE){
    header("Location: index.php");
    exit;
} else {
    echo "Erro ao excluir: " . $conn->error;
}
?>

<?php
include 'config/conexao.php';

$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}

header('Content-Type: application/json');
echo json_encode($produtos);

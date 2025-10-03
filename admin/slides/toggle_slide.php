<?php
include '../../config/conexao.php';

// Verificar se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: slide.php?erro=id_invalido");
    exit;
}

$id = intval($_GET['id']);

// Buscar status atual do slide com prepared statement
$stmt = $conn->prepare("SELECT ativo, titulo FROM slides WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: slide.php?erro=nao_encontrado");
    exit;
}

$slide = $result->fetch_assoc();
$novoStatus = $slide['ativo'] ? 0 : 1;

// Atualizar status com prepared statement
$stmt = $conn->prepare("UPDATE slides SET ativo = ? WHERE id = ?");
$stmt->bind_param("ii", $novoStatus, $id);

if ($stmt->execute()) {
    $acao = $novoStatus ? 'ativado' : 'desativado';
    header("Location: slide.php?sucesso=toggle&status=$acao");
    exit;
} else {
    header("Location: slide.php?erro=toggle_falhou");
    exit;
}

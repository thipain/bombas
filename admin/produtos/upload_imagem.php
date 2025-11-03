<?php
include '../../config/conexao.php';
require_once '../auth.php';

// Validar requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['imagem']) || !isset($_POST['produto_id'])) {
    http_response_code(400);
    echo json_encode(['erro' => 'Requisição inválida']);
    exit;
}

$produto_id = intval($_POST['produto_id']);
$arquivo = $_FILES['imagem'];

// Validar produto existe
$sqlVerificar = "SELECT id FROM produtos WHERE id = $produto_id";
$resultVerificar = $conn->query($sqlVerificar);
if ($resultVerificar->num_rows === 0) {
    http_response_code(404);
    echo json_encode(['erro' => 'Produto não encontrado']);
    exit;
}

// Configurações de upload
$pasta_upload = '../../assets/images/products/';
$tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$tamanho_maximo = 5 * 1024 * 1024; // 5MB

// Validar arquivo
if (!in_array($arquivo['type'], $tipos_permitidos)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Tipo de arquivo não permitido. Use: JPG, PNG, GIF ou WebP']);
    exit;
}

if ($arquivo['size'] > $tamanho_maximo) {
    http_response_code(400);
    echo json_encode(['erro' => 'Arquivo muito grande. Máximo: 5MB']);
    exit;
}

if ($arquivo['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['erro' => 'Erro ao enviar arquivo']);
    exit;
}

// Criar pasta se não existir
if (!is_dir($pasta_upload)) {
    mkdir($pasta_upload, 0755, true);
}

// Gerar nome único para arquivo
$extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
$nome_arquivo = 'produto_' . $produto_id . '_' . time() . '_' . rand(1000, 9999) . '.' . $extensao;
$caminho_arquivo = $pasta_upload . $nome_arquivo;
$url_relativa = 'assets/images/products/' . $nome_arquivo;

// Fazer upload
if (!move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar arquivo no servidor']);
    exit;
}

// Obter próxima ordem
$sqlOrdem = "SELECT MAX(ordem) as max_ordem FROM produto_imagens WHERE produto_id = $produto_id";
$resultOrdem = $conn->query($sqlOrdem);
$rowOrdem = $resultOrdem->fetch_assoc();
$proxima_ordem = ($rowOrdem['max_ordem'] ?? 0) + 1;

// Inserir no banco de dados
$sqlInserir = "INSERT INTO produto_imagens (produto_id, imagem_url, ordem) VALUES ($produto_id, '$url_relativa', $proxima_ordem)";

if ($conn->query($sqlInserir) === TRUE) {
    $imagem_id = $conn->insert_id;
    http_response_code(200);
    echo json_encode([
        'sucesso' => true,
        'id' => $imagem_id,
        'url' => $url_relativa,
        'ordem' => $proxima_ordem,
        'mensagem' => 'Imagem enviada com sucesso'
    ]);
} else {
    // Deletar arquivo se falhar no banco
    unlink($caminho_arquivo);
    http_response_code(500);
    echo json_encode(['erro' => 'Erro ao salvar imagem no banco de dados']);
}
?>
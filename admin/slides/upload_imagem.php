<?php
// upload_imagem.php
require_once 'auth.php';
header('Content-Type: application/json');

// Configurações de upload
$uploadDir = '../../assets/images/slider/';
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
$maxFileSize = 5 * 1024 * 1024; // 5MB

// Criar diretório se não existir
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Validar se arquivo foi enviado
if (!isset($_FILES['imagem']) || $_FILES['imagem']['error'] === UPLOAD_ERR_NO_FILE) {
    echo json_encode(['success' => false, 'message' => 'Nenhum arquivo foi enviado']);
    exit;
}

$file = $_FILES['imagem'];

// Verificar erros de upload
if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Erro ao fazer upload do arquivo']);
    exit;
}

// Validar tipo de arquivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG ou WEBP']);
    exit;
}

// Validar tamanho
if ($file['size'] > $maxFileSize) {
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo: 5MB']);
    exit;
}

// Gerar nome único para o arquivo
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$newFileName = 'slide_' . uniqid() . '_' . time() . '.' . $extension;
$uploadPath = $uploadDir . $newFileName;

// Mover arquivo para o diretório
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Redimensionar imagem para otimizar (opcional mas recomendado)
    $relativePath = 'assets/images/slider/' . $newFileName;
    
    echo json_encode([
        'success' => true,
        'message' => 'Upload realizado com sucesso',
        'path' => $relativePath,
        'filename' => $newFileName
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar o arquivo']);
}
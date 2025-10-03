<?php
include '../../config/conexao.php';

// Verificar se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: slide.php?erro=id_invalido");
    exit;
}

$id = intval($_GET['id']);

// Buscar dados do slide com prepared statement
$stmt = $conn->prepare("SELECT * FROM slides WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: slide.php?erro=nao_encontrado");
    exit;
}

$slide = $result->fetch_assoc();

// Processar formulário de edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $subtitulo = trim($_POST['subtitulo']);
    $texto_botao = trim($_POST['texto_botao']);
    $link_botao = trim($_POST['link_botao']);
    $ordem = intval($_POST['ordem']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $imagem_url = $slide['imagem_url']; // Manter imagem atual por padrão

    // Processar upload de nova imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/slider/';

        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024;

        $file = $_FILES['imagem'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            $erro = "Tipo de arquivo não permitido. Use JPG, PNG ou WEBP";
        } elseif ($file['size'] > $maxFileSize) {
            $erro = "Arquivo muito grande. Máximo: 5MB";
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'slide_' . uniqid() . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                // Deletar imagem antiga se existir
                $oldImagePath = '../../' . $slide['imagem_url'];
                if (file_exists($oldImagePath) && strpos($slide['imagem_url'], 'assets/images/slider/') !== false) {
                    unlink($oldImagePath);
                }

                $imagem_url = 'assets/images/slider/' . $newFileName;
            } else {
                $erro = "Erro ao salvar o arquivo";
            }
        }
    } elseif (!empty($_POST['imagem_url_manual']) && $_POST['imagem_url_manual'] !== $slide['imagem_url']) {
        $imagem_url = trim($_POST['imagem_url_manual']);
    }

    // Validação e atualização
    if (!isset($erro)) {
        if (empty($titulo) || empty($texto_botao) || empty($link_botao) || empty($imagem_url)) {
            $erro = "Preencha todos os campos obrigatórios!";
        } else {
            $stmt = $conn->prepare("UPDATE slides SET 
                    titulo = ?,
                    subtitulo = ?,
                    texto_botao = ?,
                    link_botao = ?,
                    imagem_url = ?,
                    ordem = ?,
                    ativo = ?
                    WHERE id = ?");

            $stmt->bind_param("sssssiii", $titulo, $subtitulo, $texto_botao, $link_botao, $imagem_url, $ordem, $ativo, $id);

            if ($stmt->execute()) {
                header("Location: slide.php?sucesso=editado");
                exit;
            } else {
                $erro = "Erro ao atualizar o slide. Tente novamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Slide - Painel Administrativo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #FF6B35;
            --secondary: #004E89;
            --accent: #FFA630;
            --dark: #1A1A2E;
            --light: #F7F7F7;
            --white: #FFFFFF;
            --success: #25D366;
            --error: #e74c3c;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, #e8e8e8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
        }

        .form-container {
            background: var(--white);
            padding: 45px 50px;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            width: 100%;
            animation: slideIn 0.4s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1 {
            text-align: center;
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 2.5rem;
            font-weight: 700;
            position: relative;
            padding-bottom: 15px;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            border-radius: 2px;
        }

        .form-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 35px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
            font-size: 0.95rem;
        }

        .required {
            color: var(--error);
            margin-left: 3px;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #ddd;
            border-radius: 12px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s ease;
            background: var(--light);
            color: var(--dark);
        }

        input[type="file"] {
            padding: 12px 18px;
            cursor: pointer;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            border-color: var(--secondary);
            outline: none;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(0, 78, 137, 0.1);
            transform: translateY(-2px);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        .helper-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 6px;
            display: block;
        }

        .current-image {
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .current-image img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .current-image-label {
            background: #e3f2fd;
            padding: 10px 15px;
            color: #1565c0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .upload-preview {
            margin-top: 15px;
            border-radius: 12px;
            overflow: hidden;
            display: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .upload-preview img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .upload-info {
            background: #d4edda;
            padding: 12px 18px;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
            align-items: center;
            gap: 10px;
            color: #155724;
            font-size: 0.9rem;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--secondary);
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: var(--white);
            font-weight: 700;
            padding: 16px 35px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
            margin-top: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 78, 137, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(0, 78, 137, 0.4);
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--secondary);
            font-weight: 600;
            padding: 14px 30px;
            border: 2px solid var(--secondary);
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            width: 100%;
            margin-top: 10px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-secondary:hover {
            background: var(--secondary);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 78, 137, 0.3);
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
            margin: 30px 0;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
        }

        .alert-info {
            background: #e3f2fd;
            color: #1565c0;
            border-left: 4px solid #1565c0;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid var(--error);
        }

        .upload-options {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .upload-option {
            flex: 1;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-option:hover {
            border-color: var(--secondary);
            background: rgba(0, 78, 137, 0.05);
        }

        .upload-option.active {
            border-color: var(--secondary);
            background: rgba(0, 78, 137, 0.1);
        }

        .upload-option i {
            font-size: 2rem;
            color: var(--secondary);
            margin-bottom: 10px;
        }

        .option-content {
            display: none;
        }

        .option-content.active {
            display: block;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 35px 25px;
            }

            h1 {
                font-size: 2rem;
            }

            .upload-options {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1><i class="fas fa-edit"></i> Editar Slide</h1>
        <p class="form-subtitle">Atualize as informações do slide #<?= $slide['id'] ?></p>

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <span>Editando slide na posição <strong>#<?= $slide['ordem'] ?></strong></span>
        </div>

        <?php if (isset($erro)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <strong><?= htmlspecialchars($erro) ?></strong>
            </div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">
                    <i class="fas fa-heading"></i> Título Principal
                    <span class="required">*</span>
                </label>
                <input type="text" id="titulo" name="titulo" required
                    value="<?= htmlspecialchars($slide['titulo']) ?>"
                    placeholder="Ex: Ferramentas Profissionais">
            </div>

            <div class="form-group">
                <label for="subtitulo">
                    <i class="fas fa-align-left"></i> Subtítulo
                </label>
                <textarea id="subtitulo" name="subtitulo" rows="3"
                    placeholder="Ex: As melhores marcas com até 40% de desconto"><?= htmlspecialchars($slide['subtitulo']) ?></textarea>
                <span class="helper-text">Texto secundário que aparecerá abaixo do título</span>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label>
                    <i class="fas fa-image"></i> Imagem de Fundo
                </label>

                <!-- Imagem Atual -->
                <div class="current-image">
                    <div class="current-image-label">
                        <i class="fas fa-image"></i>
                        <strong>Imagem Atual:</strong>
                    </div>
                    <img src="../../<?= htmlspecialchars($slide['imagem_url']) ?>" alt="Imagem atual">
                </div>

                <div class="upload-options">
                    <div class="upload-option" onclick="toggleUploadMode('keep')">
                        <i class="fas fa-check-circle"></i>
                        <div><strong>Manter</strong></div>
                        <small>Não alterar</small>
                    </div>
                    <div class="upload-option" onclick="toggleUploadMode('upload')">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div><strong>Substituir</strong></div>
                        <small>Novo upload</small>
                    </div>
                    <div class="upload-option" onclick="toggleUploadMode('url')">
                        <i class="fas fa-link"></i>
                        <div><strong>URL</strong></div>
                        <small>Novo caminho</small>
                    </div>
                </div>

                <div id="upload-content" class="option-content">
                    <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/jpg,image/png,image/webp" onchange="previewImage(this)">
                    <span class="helper-text">Formatos aceitos: JPG, PNG, WEBP | Máximo: 5MB | Recomendado: 1920x600px</span>

                    <div class="upload-preview" id="imagePreview">
                        <img id="previewImg" src="" alt="Preview">
                    </div>

                    <div class="upload-info" id="uploadInfo">
                        <i class="fas fa-check-circle"></i>
                        <span id="fileName"></span>
                    </div>
                </div>

                <div id="url-content" class="option-content">
                    <input type="text" id="imagem_url_manual" name="imagem_url_manual"
                        placeholder="assets/images/slider/imagem.jpg"
                        value="<?= htmlspecialchars($slide['imagem_url']) ?>">
                    <span class="helper-text">Caminho relativo da imagem no servidor</span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label for="texto_botao">
                    <i class="fas fa-mouse-pointer"></i> Texto do Botão
                    <span class="required">*</span>
                </label>
                <input type="text" id="texto_botao" name="texto_botao" required
                    value="<?= htmlspecialchars($slide['texto_botao']) ?>"
                    placeholder="Ex: Ver Ofertas">
            </div>

            <div class="form-group">
                <label for="link_botao">
                    <i class="fas fa-link"></i> Link do Botão
                    <span class="required">*</span>
                </label>
                <input type="text" id="link_botao" name="link_botao" required
                    value="<?= htmlspecialchars($slide['link_botao']) ?>"
                    placeholder="#produtos">
                <span class="helper-text">Para onde o botão irá redirecionar (ex: #produtos, produto.php?id=1)</span>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label for="ordem">
                    <i class="fas fa-sort-numeric-down"></i> Ordem de Exibição
                    <span class="required">*</span>
                </label>
                <input type="number" id="ordem" name="ordem" required
                    value="<?= $slide['ordem'] ?>" min="1">
                <span class="helper-text">Define a sequência de exibição do slide no carrossel</span>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <div class="checkbox-group">
                    <input type="checkbox" id="ativo" name="ativo"
                        <?= $slide['ativo'] ? 'checked' : '' ?>>
                    <label for="ativo">Slide ativo (visível no site)</label>
                </div>
                <span class="helper-text">Desmarque para ocultar o slide sem excluí-lo</span>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>

            <a href="slide.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar para Lista
            </a>
        </form>
    </div>

    <script>
        function toggleUploadMode(mode) {
            // Toggle botões
            document.querySelectorAll('.upload-option').forEach(opt => opt.classList.remove('active'));
            event.currentTarget.classList.add('active');

            // Toggle conteúdos
            document.querySelectorAll('.option-content').forEach(content => content.classList.remove('active'));

            if (mode === 'upload') {
                document.getElementById('upload-content').classList.add('active');
                document.getElementById('imagem_url_manual').value = '';
            } else if (mode === 'url') {
                document.getElementById('url-content').classList.add('active');
                document.getElementById('imagem').value = '';
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('uploadInfo').style.display = 'none';
            } else {
                // Modo keep - limpar campos
                document.getElementById('imagem').value = '';
                document.getElementById('imagem_url_manual').value = '';
                document.getElementById('imagePreview').style.display = 'none';
                document.getElementById('uploadInfo').style.display = 'none';
            }
        }

        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const uploadInfo = document.getElementById('uploadInfo');
            const fileName = document.getElementById('fileName');

            if (input.files && input.files[0]) {
                const file = input.files[0];

                // Validar tamanho
                if (file.size > 5 * 1024 * 1024) {
                    alert('Arquivo muito grande! Máximo: 5MB');
                    input.value = '';
                    return;
                }

                // Validar tipo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Tipo de arquivo não permitido! Use JPG, PNG ou WEBP');
                    input.value = '';
                    return;
                }

                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    uploadInfo.style.display = 'flex';
                    fileName.textContent = file.name + ' (' + (file.size / 1024 / 1024).toFixed(2) + ' MB)';
                }

                reader.readAsDataURL(file);
            }
        }
    </script>
</body>

</html>
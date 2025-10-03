<?php
include '../../config/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = trim($_POST['titulo']);
    $subtitulo = trim($_POST['subtitulo']);
    $texto_botao = trim($_POST['texto_botao']);
    $link_botao = trim($_POST['link_botao']);
    $ordem = intval($_POST['ordem']);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $imagem_url = '';

    // Processar upload de imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../assets/images/slider/';

        // Criar diretório se não existir
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB

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
                $imagem_url = 'assets/images/slider/' . $newFileName;
            } else {
                $erro = "Erro ao salvar o arquivo";
            }
        }
    } elseif (!empty($_POST['imagem_url_manual'])) {
        // Se não fez upload, usar URL manual
        $imagem_url = trim($_POST['imagem_url_manual']);
    } else {
        $erro = "É necessário fazer upload de uma imagem ou informar uma URL";
    }

    // Validação e inserção
    if (!isset($erro)) {
        if (empty($titulo) || empty($texto_botao) || empty($link_botao) || empty($imagem_url)) {
            $erro = "Preencha todos os campos obrigatórios!";
        } else {
            $stmt = $conn->prepare("INSERT INTO slides (titulo, subtitulo, texto_botao, link_botao, imagem_url, ordem, ativo) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param("sssssii", $titulo, $subtitulo, $texto_botao, $link_botao, $imagem_url, $ordem, $ativo);

            if ($stmt->execute()) {
                header("Location: slide.php?sucesso=criado");
                exit;
            } else {
                $erro = "Erro ao criar o slide. Tente novamente.";
            }
        }
    }
}

// Buscar próxima ordem disponível
$nextOrder = $conn->query("SELECT MAX(ordem) as max_ordem FROM slides")->fetch_assoc()['max_ordem'] + 1;
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Slide - Painel Administrativo</title>
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
            background: linear-gradient(90deg, var(--primary), var(--accent));
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
            border-color: var(--primary);
            outline: none;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
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
            background: #e3f2fd;
            padding: 12px 18px;
            border-radius: 10px;
            margin-top: 10px;
            display: none;
            align-items: center;
            gap: 10px;
            color: #1565c0;
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
            accent-color: var(--primary);
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-submit {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
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
            box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(255, 107, 53, 0.4);
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

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid var(--error);
            display: flex;
            align-items: center;
            gap: 12px;
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
            border-color: var(--primary);
            background: rgba(255, 107, 53, 0.05);
        }

        .upload-option.active {
            border-color: var(--primary);
            background: rgba(255, 107, 53, 0.1);
        }

        .upload-option i {
            font-size: 2rem;
            color: var(--primary);
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
        <h1><i class="fas fa-plus-circle"></i> Novo Slide</h1>
        <p class="form-subtitle">Preencha os dados para criar um novo slide</p>

        <?php if (isset($erro)): ?>
            <div class="alert-danger">
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
                <input type="text" id="titulo" name="titulo" required placeholder="Ex: Ferramentas Profissionais">
            </div>

            <div class="form-group">
                <label for="subtitulo">
                    <i class="fas fa-align-left"></i> Subtítulo
                </label>
                <textarea id="subtitulo" name="subtitulo" rows="3" placeholder="Ex: As melhores marcas com até 40% de desconto"></textarea>
                <span class="helper-text">Texto secundário que aparecerá abaixo do título</span>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label>
                    <i class="fas fa-image"></i> Imagem de Fundo
                    <span class="required">*</span>
                </label>

                <div class="upload-options">
                    <div class="upload-option active" onclick="toggleUploadMode('upload')">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <div><strong>Upload</strong></div>
                        <small>Enviar arquivo</small>
                    </div>
                    <div class="upload-option" onclick="toggleUploadMode('url')">
                        <i class="fas fa-link"></i>
                        <div><strong>URL</strong></div>
                        <small>Informar caminho</small>
                    </div>
                </div>

                <div id="upload-content" class="option-content active">
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
                    <input type="text" id="imagem_url_manual" name="imagem_url_manual" placeholder="assets/images/slider/imagem.jpg">
                    <span class="helper-text">Caminho relativo da imagem existente no servidor</span>
                </div>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label for="texto_botao">
                    <i class="fas fa-mouse-pointer"></i> Texto do Botão
                    <span class="required">*</span>
                </label>
                <input type="text" id="texto_botao" name="texto_botao" required value="Ver Ofertas" placeholder="Ex: Ver Ofertas">
            </div>

            <div class="form-group">
                <label for="link_botao">
                    <i class="fas fa-link"></i> Link do Botão
                    <span class="required">*</span>
                </label>
                <input type="text" id="link_botao" name="link_botao" required value="#produtos" placeholder="#produtos">
                <span class="helper-text">Para onde o botão irá redirecionar (ex: #produtos, produto.php?id=1)</span>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label for="ordem">
                    <i class="fas fa-sort-numeric-down"></i> Ordem de Exibição
                    <span class="required">*</span>
                </label>
                <input type="number" id="ordem" name="ordem" required value="<?= $nextOrder ?>" min="1">
                <span class="helper-text">Define a sequência de exibição do slide no carrossel</span>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-toggle-on"></i> Status
                </label>
                <div class="checkbox-group">
                    <input type="checkbox" id="ativo" name="ativo" checked>
                    <label for="ativo">Slide ativo (visível no site)</label>
                </div>
                <span class="helper-text">Desmarque para ocultar o slide sem excluí-lo</span>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Salvar Slide
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
            } else {
                document.getElementById('url-content').classList.add('active');
                document.getElementById('imagem').value = '';
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
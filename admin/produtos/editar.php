<?php
include '../../config/conexao.php';

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM produtos WHERE id=$id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Produto não encontrado!");
}
$produto = $result->fetch_assoc();

// Buscar imagens existentes
$sqlImagens = "SELECT id, imagem_url FROM produto_imagens WHERE produto_id=$id ORDER BY ordem";
$resultImagens = $conn->query($sqlImagens);
$imagensExistentes = [];
while ($img = $resultImagens->fetch_assoc()) {
    $imagensExistentes[] = $img;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $conn->real_escape_string($_POST['nome']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $preco = $conn->real_escape_string($_POST['preco']);
    $categoria = $conn->real_escape_string($_POST['categoria']);
    $badge = $conn->real_escape_string($_POST['badge']);
    $icon = $conn->real_escape_string($_POST['icon']);
    $marca = $conn->real_escape_string($_POST['marca']);
    $modelo = $conn->real_escape_string($_POST['modelo']);
    $estoque = $conn->real_escape_string($_POST['estoque']);
    $peso = $conn->real_escape_string($_POST['peso']);
    $dimensoes = $conn->real_escape_string($_POST['dimensoes']);
    $especificacoes = $conn->real_escape_string($_POST['especificacoes']);

    $sql = "UPDATE produtos 
            SET nome='$nome', descricao='$descricao', preco='$preco', categoria='$categoria', 
                badge='$badge', icon='$icon', marca='$marca', modelo='$modelo', 
                estoque='$estoque', peso='$peso', dimensoes='$dimensoes', especificacoes='$especificacoes'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        // Processar upload de novas imagens
        if (isset($_FILES['imagens']) && !empty($_FILES['imagens']['name'][0])) {
            $pasta_upload = '../../assets/images/products/';
            
            if (!is_dir($pasta_upload)) {
                mkdir($pasta_upload, 0755, true);
            }

            $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $tamanho_maximo = 5 * 1024 * 1024;
            
            // Obter próxima ordem
            $sqlOrdem = "SELECT MAX(ordem) as max_ordem FROM produto_imagens WHERE produto_id = $id";
            $resultOrdem = $conn->query($sqlOrdem);
            $rowOrdem = $resultOrdem->fetch_assoc();
            $ordem = ($rowOrdem['max_ordem'] ?? 0) + 1;

            for ($i = 0; $i < count($_FILES['imagens']['name']); $i++) {
                $arquivo = [
                    'name' => $_FILES['imagens']['name'][$i],
                    'type' => $_FILES['imagens']['type'][$i],
                    'size' => $_FILES['imagens']['size'][$i],
                    'tmp_name' => $_FILES['imagens']['tmp_name'][$i],
                    'error' => $_FILES['imagens']['error'][$i]
                ];

                if ($arquivo['error'] !== UPLOAD_ERR_OK) {
                    continue;
                }

                if (!in_array($arquivo['type'], $tipos_permitidos)) {
                    continue;
                }

                if ($arquivo['size'] > $tamanho_maximo) {
                    continue;
                }

                $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
                $nome_arquivo = 'produto_' . $id . '_' . time() . '_' . rand(1000, 9999) . '.' . $extensao;
                $caminho_arquivo = $pasta_upload . $nome_arquivo;
                $url_relativa = 'assets/images/products/' . $nome_arquivo;

                if (move_uploaded_file($arquivo['tmp_name'], $caminho_arquivo)) {
                    $url_relativa_escapada = $conn->real_escape_string($url_relativa);
                    $sqlImg = "INSERT INTO produto_imagens (produto_id, imagem_url, ordem) VALUES ($id, '$url_relativa_escapada', $ordem)";
                    $conn->query($sqlImg);
                    $ordem++;
                }
            }
        }

        header("Location: index.php");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - Painel Administrativo</title>
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
            padding: 40px 20px;
        }

        .form-container {
            background: var(--white);
            padding: 45px 50px;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
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

        .form-section {
            background: var(--light);
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
        }

        .form-section h2 {
            color: var(--dark);
            font-size: 1.3rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-section h2 i {
            color: var(--primary);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
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
            background: var(--white);
            color: var(--dark);
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
            transform: translateY(-2px);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        textarea.large {
            min-height: 150px;
        }

        .helper-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 6px;
            display: block;
        }

        .upload-area {
            border: 2px dashed var(--primary);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 107, 53, 0.05);
        }

        .upload-area:hover {
            background: rgba(255, 107, 53, 0.1);
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .upload-area.drag-over {
            background: rgba(255, 107, 53, 0.15);
            border-color: var(--accent);
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .imagens-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }

        .imagem-item {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .imagem-item:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
        }

        .imagem-item img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            display: block;
        }

        .imagem-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .imagem-item:hover .imagem-overlay {
            opacity: 1;
        }

        .btn-deletar-img {
            background: var(--error);
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .btn-deletar-img:hover {
            background: #c0392b;
            transform: scale(1.05);
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

        @media (max-width: 768px) {
            .form-container {
                padding: 35px 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h1><i class="fas fa-edit"></i> Editar Produto</h1>
        <p class="form-subtitle">Atualize as informações do produto</p>

        <form method="POST" action="" enctype="multipart/form-data">
            <!-- Informações Básicas -->
            <div class="form-section">
                <h2><i class="fas fa-info-circle"></i> Informações Básicas</h2>

                <div class="form-group">
                    <label for="nome">
                        <i class="fas fa-tag"></i> Nome do Produto
                        <span class="required">*</span>
                    </label>
                    <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="descricao">
                        <i class="fas fa-align-left"></i> Descrição
                    </label>
                    <textarea id="descricao" name="descricao" class="large"><?= htmlspecialchars($produto['descricao']) ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="preco">
                            <i class="fas fa-dollar-sign"></i> Preço
                            <span class="required">*</span>
                        </label>
                        <input type="number" step="0.01" id="preco" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="estoque">
                            <i class="fas fa-boxes"></i> Estoque
                            <span class="required">*</span>
                        </label>
                        <input type="number" id="estoque" name="estoque" value="<?= htmlspecialchars($produto['estoque'] ?? 0) ?>" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria">
                            <i class="fas fa-folder"></i> Categoria
                        </label>
                        <input type="text" id="categoria" name="categoria" value="<?= htmlspecialchars($produto['categoria']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="badge">
                            <i class="fas fa-award"></i> Badge/Etiqueta
                        </label>
                        <input type="text" id="badge" name="badge" value="<?= htmlspecialchars($produto['badge']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="icon">
                        <i class="fas fa-icons"></i> Ícone (FontAwesome)
                    </label>
                    <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($produto['icon']) ?>">
                </div>
            </div>

            <!-- Especificações Técnicas -->
            <div class="form-section">
                <h2><i class="fas fa-cogs"></i> Especificações Técnicas</h2>

                <div class="form-row">
                    <div class="form-group">
                        <label for="marca">
                            <i class="fas fa-copyright"></i> Marca
                        </label>
                        <input type="text" id="marca" name="marca" value="<?= htmlspecialchars($produto['marca'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="modelo">
                            <i class="fas fa-hashtag"></i> Modelo
                        </label>
                        <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($produto['modelo'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="peso">
                            <i class="fas fa-weight"></i> Peso (kg)
                        </label>
                        <input type="number" id="peso" name="peso" step="0.01" value="<?= htmlspecialchars($produto['peso'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="dimensoes">
                            <i class="fas fa-ruler-combined"></i> Dimensões
                        </label>
                        <input type="text" id="dimensoes" name="dimensoes" value="<?= htmlspecialchars($produto['dimensoes'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="especificacoes">
                        <i class="fas fa-list-ul"></i> Especificações Adicionais
                    </label>
                    <textarea id="especificacoes" name="especificacoes" class="large"><?= htmlspecialchars($produto['especificacoes'] ?? '') ?></textarea>
                    <span class="helper-text">Digite uma especificação por linha</span>
                </div>
            </div>

            <!-- Imagens -->
            <div class="form-section">
                <h2><i class="fas fa-images"></i> Imagens do Produto</h2>

                <?php if (!empty($imagensExistentes)): ?>
                    <div class="form-group full-width">
                        <label><i class="fas fa-check-circle"></i> Imagens Atuais</label>
                        <div class="imagens-preview">
                            <?php foreach ($imagensExistentes as $img): ?>
                                <div class="imagem-item">
                                    <img src="../../<?= htmlspecialchars($img['imagem_url']) ?>" alt="Produto">
                                    <div class="imagem-overlay">
                                        <button type="button" class="btn-deletar-img" onclick="deletarImagemBD(event, <?= $img['id'] ?>)">
                                            <i class="fas fa-trash"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="form-group full-width">
                    <label for="imagens">
                        <i class="fas fa-cloud-upload-alt"></i> Adicionar Novas Imagens
                    </label>
                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-image"></i>
                        </div>
                        <p><strong>Clique ou arraste imagens aqui</strong></p>
                        <p style="color: #999; font-size: 0.9rem; margin-top: 8px;">PNG, JPG, GIF ou WebP - Máximo 5MB</p>
                    </div>
                    <input type="file" id="imagens" name="imagens[]" multiple accept="image/*" style="display: none;">
                    <div id="imagensPreview" class="imagens-preview"></div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>

            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar e Voltar
            </a>
        </form>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const inputImagens = document.getElementById('imagens');
        const previewContainer = document.getElementById('imagensPreview');

        uploadArea.addEventListener('click', () => inputImagens.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('drag-over');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('drag-over');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('drag-over');
            inputImagens.files = e.dataTransfer.files;
            mostrarPreview();
        });

        inputImagens.addEventListener('change', mostrarPreview);

        function mostrarPreview() {
            previewContainer.innerHTML = '';
            Array.from(inputImagens.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const div = document.createElement('div');
                    div.className = 'imagem-item';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview">
                        <div class="imagem-overlay">
                            <button type="button" class="btn-deletar-img" onclick="removerArquivo(event, ${index})">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </div>
                    `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        function removerArquivo(e, index) {
            e.preventDefault();
            const dataTransfer = new DataTransfer();
            Array.from(inputImagens.files).forEach((file, i) => {
                if (i !== index) {
                    dataTransfer.items.add(file);
                }
            });
            inputImagens.files = dataTransfer.files;
            mostrarPreview();
        }

        function deletarImagemBD(e, imagemId) {
            e.preventDefault();
            if (confirm('Tem certeza que deseja remover esta imagem?')) {
                fetch('deletar_imagem.php?id=' + imagemId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.sucesso) {
                            location.reload();
                        } else {
                            alert('Erro ao deletar imagem');
                        }
                    })
                    .catch(err => alert('Erro ao deletar imagem'));
            }
        }

        document.querySelectorAll('input[type="text"], input[type="number"], textarea').forEach(input => {
            input.addEventListener('focus', function() {
                if (this.parentElement) {
                    this.parentElement.style.transform = 'scale(1.01)';
                }
            });

            input.addEventListener('blur', function() {
                if (this.parentElement) {
                    this.parentElement.style.transform = 'scale(1)';
                }
            });
        });
    </script>
</body>

</html>
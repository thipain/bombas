<?php 
include '../../config/conexao.php';

$id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM produtos WHERE id=$id";
$result = $conn->query($sql);

if($result->num_rows == 0){
    die("Produto não encontrado!");
}
$produto = $result->fetch_assoc();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];
    $categoria = $_POST['categoria'];
    $badge = $_POST['badge'];
    $icon = $_POST['icon'];

    $sql = "UPDATE produtos 
            SET nome='$nome', descricao='$descricao', preco='$preco', categoria='$categoria', badge='$badge', icon='$icon'
            WHERE id=$id";

    if($conn->query($sql) === TRUE){
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
        /* Cole aqui todo o CSS fornecido acima */
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

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus {
            border-color: var(--primary);
            outline: none;
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
            transform: translateY(-2px);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .helper-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 6px;
            display: block;
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
            position: relative;
            overflow: hidden;
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

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #ddd, transparent);
            margin: 30px 0;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 35px 25px;
            }
            h1 {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 20px 10px;
            }
            .form-container {
                padding: 25px 20px;
            }
            h1 {
                font-size: 1.7rem;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1><i class="fas fa-edit"></i> Editar Produto</h1>
        <p class="form-subtitle">Atualize as informações do produto</p>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">
                    <i class="fas fa-tag"></i> Nome do Produto
                    <span class="required">*</span>
                </label>
                <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($produto['nome']) ?>" required placeholder="Ex: Martelo Profissional">
            </div>

            <div class="form-group">
                <label for="descricao">
                    <i class="fas fa-align-left"></i> Descrição
                </label>
                <textarea id="descricao" name="descricao" rows="4" placeholder="Descreva os detalhes e características do produto..."><?= htmlspecialchars($produto['descricao']) ?></textarea>
                <span class="helper-text">Forneça informações detalhadas sobre o produto</span>
            </div>

            <div class="form-group">
                <label for="preco">
                    <i class="fas fa-dollar-sign"></i> Preço
                    <span class="required">*</span>
                </label>
                <input type="number" step="0.01" id="preco" name="preco" value="<?= htmlspecialchars($produto['preco']) ?>" required placeholder="0.00">
                <span class="helper-text">Informe o preço em reais (R$)</span>
            </div>

            <div class="divider"></div>

            <div class="form-group">
                <label for="categoria">
                    <i class="fas fa-folder"></i> Categoria
                </label>
                <input type="text" id="categoria" name="categoria" value="<?= htmlspecialchars($produto['categoria']) ?>" placeholder="Ex: Ferramentas, Elétrica, Hidráulica">
            </div>

            <div class="form-group">
                <label for="badge">
                    <i class="fas fa-award"></i> Badge/Etiqueta
                </label>
                <input type="text" id="badge" name="badge" value="<?= htmlspecialchars($produto['badge']) ?>" placeholder="Ex: Novo, Promoção, Destaque">
                <span class="helper-text">Badge que aparecerá no card do produto</span>
            </div>

            <div class="form-group">
                <label for="icon">
                    <i class="fas fa-icons"></i> Ícone (FontAwesome)
                </label>
                <input type="text" id="icon" name="icon" value="<?= htmlspecialchars($produto['icon']) ?>" placeholder="Ex: fas fa-hammer">
                <span class="helper-text">Código do ícone FontAwesome (ex: fas fa-hammer)</span>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Salvar Alterações
            </button>
            
            <a href="index.php" class="btn-secondary">
                <i class="fas fa-arrow-left"></i> Cancelar e Voltar
            </a>
        </form>
    </div>
</body>
</html>
<?php
include '../../config/conexao.php';
require_once 'auth.php';

// Verificar se o ID foi passado
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: slide.php?erro=id_invalido");
    exit;
}

$id = intval($_GET['id']);

// Buscar informações do slide usando prepared statement
$stmt = $conn->prepare("SELECT titulo FROM slides WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: slide.php?erro=nao_encontrado");
    exit;
}

$slide = $result->fetch_assoc();

// Processar exclusão
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar'])) {
    $stmt = $conn->prepare("DELETE FROM slides WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: slide.php?sucesso=deletado");
        exit;
    } else {
        $erro = "Erro ao excluir o slide. Tente novamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Slide - Painel Administrativo</title>
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

        .confirm-container {
            background: var(--white);
            padding: 45px 50px;
            border-radius: 25px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
            max-width: 550px;
            width: 100%;
            animation: slideIn 0.4s ease-out;
            text-align: center;
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

        .warning-icon {
            font-size: 5rem;
            color: var(--error);
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        h1 {
            color: var(--dark);
            margin-bottom: 15px;
            font-size: 2rem;
            font-weight: 700;
        }

        .slide-name {
            background: #fff3cd;
            color: #856404;
            padding: 15px 20px;
            border-radius: 12px;
            margin: 25px 0;
            font-weight: 600;
            border-left: 4px solid #ffc107;
        }

        .warning-text {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .warning-text strong {
            color: var(--error);
            font-weight: 700;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border-left: 4px solid var(--error);
            text-align: left;
        }

        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 16px 30px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-delete {
            background: var(--error);
            color: var(--white);
            box-shadow: 0 8px 20px rgba(231, 76, 60, 0.3);
        }

        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(231, 76, 60, 0.4);
        }

        .btn-cancel {
            background: var(--white);
            color: var(--secondary);
            border: 2px solid var(--secondary);
        }

        .btn-cancel:hover {
            background: var(--secondary);
            color: var(--white);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 78, 137, 0.3);
        }

        @media (max-width: 768px) {
            .confirm-container {
                padding: 35px 25px;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            h1 {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="confirm-container">
        <div class="warning-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>

        <h1>Confirmar Exclusão</h1>

        <div class="slide-name">
            <i class="fas fa-image"></i> <?= htmlspecialchars($slide['titulo']) ?>
        </div>

        <?php if (isset($erro)): ?>
            <div class="alert-danger">
                <i class="fas fa-times-circle"></i>
                <strong>Erro:</strong> <?= htmlspecialchars($erro) ?>
            </div>
        <?php endif; ?>

        <p class="warning-text">
            Você está prestes a <strong>excluir permanentemente</strong> este slide.<br>
            Esta ação <strong>não pode ser desfeita</strong>!
        </p>

        <form method="POST" action="">
            <div class="button-group">
                <a href="slide.php" class="btn btn-cancel">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="submit" name="confirmar" class="btn btn-delete">
                    <i class="fas fa-trash-alt"></i>
                    Sim, Excluir
                </button>
            </div>
        </form>
    </div>
</body>

</html>
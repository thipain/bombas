<?php
session_start();
include '../includes/db.php'; // ajuste o caminho se necessário

// Buscar produtos no banco
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Admin - Produtos</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        :root {
            --primary: #264653;
            --secondary: #2a9d8f;
            --dark: #1a1a1a;
        }

        /* ===============================
           ESTILOS PARA PAINEL ADMIN (CRUD)
           =============================== */
        .admin-container {
            max-width: 1200px;
            margin: 40px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .admin-header h1 {
            color: var(--secondary);
            font-size: 1.8rem;
        }

        .btn {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.3s;
            margin: 2px;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
        }

        .btn-danger {
            background: #e63946;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        .btn-success {
            background: var(--secondary);
            color: white;
        }

        .btn-success:hover {
            background: #21867a;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table th,
        .table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .table th {
            background: var(--secondary);
            color: white;
        }

        .table tr:hover {
            background: #f9f9f9;
        }

        /* ===========================
                TOP BAR ADMIN
        =========================== */
        .top-bar {
            width: 100%;
            background-color: var(--primary);
            color: #ffffff;
            padding: 12px 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            font-family: Arial, Helvetica, sans-serif;
            font-size: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .top-bar-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-bar-content span {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .top-bar-links a {
            color: #ffffff;
            text-decoration: none;
            margin-left: 20px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .top-bar-links a:hover {
            color: var(--secondary);
        }

        /* Responsividade */
        @media (max-width: 600px) {
            .top-bar-content {
                flex-direction: column;
                gap: 8px;
                text-align: center;
            }

            .top-bar-links a {
                margin-left: 0;
                margin-right: 15px;
            }
        }

        /* ===========================
            FOOTER BOTTOM ADMIN
        =========================== */
        footer {
            background: var(--dark);
            color: #ccc;
            text-align: center;
            padding: 15px;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        footer .footer-bottom {
            color: #aaa;
            font-size: 0.85rem;
        }

        main,
        .admin-container {
            flex: 1;
            /* ocupa o espaço todo e empurra o footer para baixo */
        }

        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <span>Área Administrativa</span>
            <div class="top-bar-links">
                <a href="../index.php">Ver Loja</a>
                <a href="logout.php">Sair</a>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="admin-container">
        <div class="admin-header">
            <h1>Gerenciamento de Produtos</h1>
            <a href="produtos/criar.php" class="btn btn-primary">+ Novo Produto</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($produtos): ?>
                    <?php foreach ($produtos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= htmlspecialchars($p['categoria'] ?? '-') ?></td>
                            <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                            <td><?= date("d/m/Y H:i", strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="produtos/editar.php?id=<?= $p['id'] ?>" class="btn btn-success">Editar</a>
                                <a href="produtos/deletar.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Nenhum produto cadastrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <footer>
        <div class="footer-bottom">
            &copy; <?= date("Y") ?> - Sistema CRUD Admin
        </div>
    </footer>
</body>

</html>
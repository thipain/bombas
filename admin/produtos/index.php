<?php 
include '../../config/conexao.php';
require_once '../auth.php';

// Buscar produtos
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

// Contar total de produtos
$totalProdutos = $result->num_rows;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Gerenciar Produtos</title>
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
            --warning: #f39c12;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, var(--light) 0%, #e8e8e8 100%);
            min-height: 100vh;
            padding: 0;
        }

        /* Header Admin */
        .admin-header {
            background: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
            color: white;
            padding: 30px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .admin-header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .admin-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-title h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .admin-title i {
            font-size: 2.5rem;
            color: var(--accent);
        }

        .admin-stats {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .stat-box {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px 25px;
            border-radius: 15px;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .stat-box .number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--accent);
        }

        .stat-box .label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Container Principal */
        .admin-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 40px;
        }

        /* Action Bar */
        .action-bar {
            background: var(--white);
            padding: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .action-bar h2 {
            color: var(--dark);
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-add {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 14px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
            font-size: 1rem;
        }

        .btn-add:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
        }

        .btn-add i {
            font-size: 1.2rem;
        }

        /* Search Bar */
        .search-container {
            margin-bottom: 20px;
            position: relative;
        }

        .search-container input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #ddd;
            border-radius: 50px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: var(--white);
        }

        .search-container input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
        }

        .search-container i {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }

        /* Table Container */
        .table-container {
            background: var(--white);
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
        }

        thead th {
            padding: 20px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s ease;
        }

        tbody tr:hover {
            background: var(--light);
            transform: scale(1.01);
        }

        tbody td {
            padding: 20px;
            color: var(--dark);
        }

        .product-name {
            font-weight: 600;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .product-name i {
            color: var(--primary);
        }

        .product-category {
            display: inline-block;
            background: var(--light);
            color: var(--secondary);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--success);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-edit,
        .btn-delete {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-edit {
            background: var(--secondary);
            color: white;
        }

        .btn-edit:hover {
            background: #003d6e;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 78, 137, 0.3);
        }

        .btn-delete {
            background: var(--error);
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 25px;
        }

        /* ID Badge */
        .id-badge {
            background: var(--accent);
            color: white;
            padding: 5px 12px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .admin-container {
                padding: 0 20px;
            }

            .admin-header-content {
                padding: 0 20px;
            }

            table {
                font-size: 0.9rem;
            }

            thead th,
            tbody td {
                padding: 15px;
            }
        }

        @media (max-width: 768px) {
            .admin-stats {
                width: 100%;
                justify-content: center;
            }

            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-add {
                justify-content: center;
            }

            /* Table responsiva */
            .table-container {
                overflow-x: auto;
            }

            table {
                min-width: 600px;
            }
        }

        @media (max-width: 480px) {
            .admin-title h1 {
                font-size: 1.5rem;
            }

            .admin-title i {
                font-size: 2rem;
            }

            .action-bar h2 {
                font-size: 1.2rem;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-edit,
            .btn-delete {
                width: 100%;
                justify-content: center;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: fadeIn 0.5s ease-out;
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 60px;
            color: var(--primary);
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <i class="fas fa-tools"></i>
                <div>
                    <h1>Painel Administrativo</h1>
                    <p style="opacity: 0.8; font-size: 0.9rem;">Gerenciamento de Produtos</p>
                </div>
            </div>
            <div class="admin-stats">
                <div class="stat-box">
                    <div class="number"><?= $totalProdutos ?></div>
                    <div class="label">Produtos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Container Principal -->
    <div class="admin-container">
        <!-- Action Bar -->
        <div class="action-bar">
            <h2>
                <i class="fas fa-box"></i>
                Lista de Produtos
            </h2>
            <a href="criar.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Adicionar Novo Produto
            </a>
        </div>

        <!-- Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Buscar produtos por nome, categoria ou preço..." onkeyup="searchTable()">
            <i class="fas fa-search"></i>
        </div>

        <!-- Table -->
        <div class="table-container">
            <?php if($totalProdutos > 0): ?>
            <table id="productsTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-hashtag"></i> ID</th>
                        <th><i class="fas fa-tag"></i> Nome</th>
                        <th><i class="fas fa-folder"></i> Categoria</th>
                        <th><i class="fas fa-dollar-sign"></i> Preço</th>
                        <th><i class="fas fa-cog"></i> Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <span class="id-badge">#<?= $row['id'] ?></span>
                        </td>
                        <td>
                            <div class="product-name">
                                <i class="<?= $row['icon'] ?? 'fas fa-box' ?>"></i>
                                <?= htmlspecialchars($row['nome']) ?>
                            </div>
                        </td>
                        <td>
                            <span class="product-category">
                                <?= htmlspecialchars($row['categoria']) ?: 'Sem categoria' ?>
                            </span>
                        </td>
                        <td>
                            <span class="product-price">
                                R$ <?= number_format($row['preco'], 2, ',', '.') ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="editar.php?id=<?= $row['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="deletar.php?id=<?= $row['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('⚠️ Tem certeza que deseja excluir o produto \'<?= htmlspecialchars($row['nome']) ?>\'?\n\nEsta ação não pode ser desfeita!')">
                                    <i class="fas fa-trash-alt"></i> Excluir
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <h3>Nenhum produto cadastrado</h3>
                <p>Comece adicionando seu primeiro produto ao catálogo</p>
                <a href="criar.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    Adicionar Primeiro Produto
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Função de busca na tabela
        function searchTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const table = document.getElementById('productsTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                const td = tr[i].getElementsByTagName('td');
                let found = false;

                for (let j = 0; j < td.length; j++) {
                    if (td[j]) {
                        const txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                tr[i].style.display = found ? '' : 'none';
            }
        }

        // Animação suave ao carregar
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateX(0)';
                }, index * 50);
            });
        });

        // Highlight da linha ao passar o mouse
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
            });
            row.addEventListener('mouseleave', function() {
                this.style.boxShadow = 'none';
            });
        });
    </script>
</body>
</html>
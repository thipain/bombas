<?php
session_start();
include '../config/conexao.php';

// Estatísticas rápidas
$totalProdutos = $conn->query("SELECT COUNT(*) as total FROM produtos")->fetch_assoc()['total'];
$totalSlides = $conn->query("SELECT COUNT(*) as total FROM slides")->fetch_assoc()['total'];
$slidesAtivos = $conn->query("SELECT COUNT(*) as total FROM slides WHERE ativo = 1")->fetch_assoc()['total'];
$produtosEstoque = $conn->query("SELECT COUNT(*) as total FROM produtos WHERE estoque > 0")->fetch_assoc()['total'];

// Últimos produtos adicionados
$sqlUltimosProdutos = "SELECT * FROM produtos ORDER BY id DESC LIMIT 5";
$ultimosProdutos = $conn->query($sqlUltimosProdutos);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrativo - AB Bombas</title>
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
        }

        /* Top Bar */
        .top-bar {
            background: linear-gradient(135deg, var(--dark) 0%, var(--secondary) 100%);
            color: white;
            padding: 20px 0;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .top-bar-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-brand i {
            font-size: 2.5rem;
            color: var(--accent);
        }

        .admin-brand div h1 {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .admin-brand div p {
            opacity: 0.9;
            font-size: 0.9rem;
        }

        .top-bar-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .top-bar-actions a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-site {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-site:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn-logout {
            background: var(--error);
        }

        .btn-logout:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        /* Container Principal */
        .dashboard-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 40px;
        }

        /* Welcome Section */
        .welcome-section {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
            padding: 50px 40px;
            border-radius: 25px;
            margin-bottom: 40px;
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
            position: relative;
            overflow: hidden;
        }

        .welcome-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-section h2 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 2;
        }

        .welcome-section p {
            font-size: 1.2rem;
            opacity: 0.95;
            position: relative;
            z-index: 2;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 50px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            flex-shrink: 0;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: rgba(255, 107, 53, 0.1);
            color: var(--primary);
        }

        .stat-card:nth-child(2) .stat-icon {
            background: rgba(0, 78, 137, 0.1);
            color: var(--secondary);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: rgba(37, 211, 102, 0.1);
            color: var(--success);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: rgba(255, 166, 48, 0.1);
            color: var(--accent);
        }

        .stat-info h3 {
            font-size: 2.5rem;
            color: var(--dark);
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-info p {
            color: #666;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Action Cards */
        .section-title {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .section-title i {
            color: var(--primary);
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .action-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .action-card-header {
            padding: 30px;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
            text-align: center;
        }

        .action-card-header i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .action-card-header h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .action-card-header p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .action-card-body {
            padding: 25px 30px;
        }

        .action-card-body ul {
            list-style: none;
        }

        .action-card-body li {
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }

        .action-card-body li:last-child {
            border-bottom: none;
        }

        .action-card-body li i {
            color: var(--success);
            font-size: 0.9rem;
        }

        .action-card-footer {
            padding: 20px 30px;
            background: var(--light);
            text-align: center;
        }

        .btn-action {
            background: linear-gradient(135deg, var(--primary) 0%, var(--accent) 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
        }

        /* Últimos Produtos */
        .recent-products {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .recent-products h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recent-products h3 i {
            color: var(--primary);
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .product-item:last-child {
            border-bottom: none;
        }

        .product-item-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-item-icon {
            width: 50px;
            height: 50px;
            background: var(--light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.5rem;
        }

        .product-item-details h4 {
            color: var(--dark);
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .product-item-details span {
            color: #666;
            font-size: 0.85rem;
        }

        .product-item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--success);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .actions-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 0 20px;
            }

            .top-bar-content {
                padding: 0 20px;
            }

            .welcome-section {
                padding: 40px 25px;
            }

            .welcome-section h2 {
                font-size: 2rem;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .actions-grid {
                grid-template-columns: 1fr;
            }

            .top-bar-actions {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .admin-brand div h1 {
                font-size: 1.5rem;
            }

            .welcome-section h2 {
                font-size: 1.7rem;
            }

            .welcome-section p {
                font-size: 1rem;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 60px;
                height: 60px;
                font-size: 1.7rem;
            }

            .stat-info h3 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="top-bar-content">
            <div class="admin-brand">
                <i class="fas fa-shield-alt"></i>
                <div>
                    <h1>Painel Administrativo</h1>
                    <p>AB Bombas & Ferramentas</p>
                </div>
            </div>
            <div class="top-bar-actions">
                <a href="../../index.php" class="btn-site" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Ver Site
                </a>
                <a href="../logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i>
                    Sair
                </a>
            </div>
        </div>
    </div>

    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <h2><i class="fas fa-hand-wave"></i> Bem-vindo ao Painel!</h2>
            <p>Gerencie todos os aspectos do seu e-commerce de forma simples e eficiente.</p>
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalProdutos ?></h3>
                    <p>Total de Produtos</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-images"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $totalSlides ?></h3>
                    <p>Slides Cadastrados</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $slidesAtivos ?></h3>
                    <p>Slides Ativos</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-warehouse"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $produtosEstoque ?></h3>
                    <p>Produtos em Estoque</p>
                </div>
            </div>
        </div>

        <!-- Actions Section -->
        <h2 class="section-title">
            <i class="fas fa-tasks"></i>
            Áreas de Gerenciamento
        </h2>

        <div class="actions-grid">
            <!-- Card Produtos -->
            <a href="produtos/" class="action-card">
                <div class="action-card-header">
                    <i class="fas fa-boxes"></i>
                    <h3>Produtos</h3>
                    <p>Gerencie o catálogo completo</p>
                </div>
                <div class="action-card-body">
                    <ul>
                        <li><i class="fas fa-check"></i> Adicionar novos produtos</li>
                        <li><i class="fas fa-check"></i> Editar informações</li>
                        <li><i class="fas fa-check"></i> Gerenciar estoque</li>
                        <li><i class="fas fa-check"></i> Definir preços e categorias</li>
                        <li><i class="fas fa-check"></i> Adicionar imagens</li>
                    </ul>
                </div>
                <div class="action-card-footer">
                    <span class="btn-action">
                        <i class="fas fa-arrow-right"></i>
                        Acessar Produtos
                    </span>
                </div>
            </a>

            <!-- Card Slides -->
            <a href="slides/slide.php" class="action-card">
                <div class="action-card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-sliders-h"></i>
                    <h3>Carrossel de Slides</h3>
                    <p>Banner principal do site</p>
                </div>
                <div class="action-card-body">
                    <ul>
                        <li><i class="fas fa-check"></i> Criar novos slides</li>
                        <li><i class="fas fa-check"></i> Fazer upload de imagens</li>
                        <li><i class="fas fa-check"></i> Editar textos e botões</li>
                        <li><i class="fas fa-check"></i> Definir ordem de exibição</li>
                        <li><i class="fas fa-check"></i> Ativar/Desativar slides</li>
                    </ul>
                </div>
                <div class="action-card-footer">
                    <span class="btn-action">
                        <i class="fas fa-arrow-right"></i>
                        Gerenciar Slides
                    </span>
                </div>
            </a>

            <!-- Card Configurações (futuro) -->
            <div class="action-card" style="opacity: 0.6; pointer-events: none;">
                <div class="action-card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-cog"></i>
                    <h3>Configurações</h3>
                    <p>Em breve</p>
                </div>
                <div class="action-card-body">
                    <ul>
                        <li><i class="fas fa-clock"></i> Informações da loja</li>
                        <li><i class="fas fa-clock"></i> Dados de contato</li>
                        <li><i class="fas fa-clock"></i> Redes sociais</li>
                        <li><i class="fas fa-clock"></i> SEO e metadados</li>
                        <li><i class="fas fa-clock"></i> Integrações</li>
                    </ul>
                </div>
                <div class="action-card-footer">
                    <span class="btn-action" style="opacity: 0.6;">
                        <i class="fas fa-lock"></i>
                        Em Desenvolvimento
                    </span>
                </div>
            </div>
        </div>

        <!-- Últimos Produtos -->
        <div class="recent-products">
            <h3>
                <i class="fas fa-clock"></i>
                Últimos Produtos Adicionados
            </h3>

            <?php if ($ultimosProdutos->num_rows > 0): ?>
                <?php while ($produto = $ultimosProdutos->fetch_assoc()): ?>
                    <div class="product-item">
                        <div class="product-item-info">
                            <div class="product-item-icon">
                                <i class="<?= $produto['icon'] ?? 'fas fa-box' ?>"></i>
                            </div>
                            <div class="product-item-details">
                                <h4><?= htmlspecialchars($produto['nome']) ?></h4>
                                <span><?= htmlspecialchars($produto['categoria'] ?? 'Sem categoria') ?> • Estoque: <?= $produto['estoque'] ?></span>
                            </div>
                        </div>
                        <div class="product-item-price">
                            R$ <?= number_format($produto['preco'], 2, ',', '.') ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; color: #999; padding: 20px;">
                    <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                    Nenhum produto cadastrado ainda.
                </p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
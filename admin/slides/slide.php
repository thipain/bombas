<?php
include '../../config/conexao.php';

// Buscar slides ordenados
$sql = "SELECT * FROM slides ORDER BY ordem ASC, id ASC";
$result = $conn->query($sql);

// Contar total de slides
$totalSlides = $result->num_rows;
$slidesAtivos = $conn->query("SELECT COUNT(*) as total FROM slides WHERE ativo = 1")->fetch_assoc()['total'];

// Mensagens de feedback
$mensagens = [
    'criado' => ['tipo' => 'success', 'texto' => 'Slide criado com sucesso!'],
    'editado' => ['tipo' => 'success', 'texto' => 'Slide atualizado com sucesso!'],
    'deletado' => ['tipo' => 'success', 'texto' => 'Slide excluído com sucesso!'],
    'toggle' => ['tipo' => 'success', 'texto' => 'Status do slide alterado!'],
    'erro' => ['tipo' => 'error', 'texto' => 'Ocorreu um erro. Tente novamente.']
];
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Slides - Painel Administrativo</title>
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

        /* Alert Messages */
        .alert-message {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 18px 25px;
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            animation: slideInRight 0.4s ease-out, fadeOut 0.3s ease-in 2.7s;
            max-width: 400px;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(400px);
            }
        }

        .alert-success {
            background: var(--success);
            color: white;
        }

        .alert-error {
            background: var(--error);
            color: white;
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
            gap: 20px;
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

        /* Slides Grid */
        .slides-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .slide-card {
            background: var(--white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .slide-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .slide-preview {
            width: 100%;
            height: 200px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .slide-preview::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .slide-preview-content {
            position: relative;
            z-index: 2;
            padding: 20px;
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .slide-preview-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .slide-preview-content p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .slide-badges {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 3;
            display: flex;
            gap: 8px;
        }

        .badge-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-status.ativo {
            background: var(--success);
            color: white;
        }

        .badge-status.inativo {
            background: var(--error);
            color: white;
        }

        .badge-ordem {
            background: var(--accent);
            color: var(--dark);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .slide-info {
            padding: 20px;
        }

        .slide-details {
            margin-bottom: 15px;
        }

        .slide-detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 8px;
            color: #666;
            font-size: 0.9rem;
        }

        .slide-detail-item i {
            color: var(--primary);
            width: 20px;
        }

        .slide-actions {
            display: flex;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid #f0f0f0;
        }

        .btn-edit,
        .btn-delete,
        .btn-toggle {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
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
        }

        .btn-delete {
            background: var(--error);
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .btn-toggle {
            background: var(--warning);
            color: white;
        }

        .btn-toggle:hover {
            background: #e67e22;
            transform: translateY(-2px);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: var(--white);
            border-radius: 20px;
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

        /* Responsive */
        @media (max-width: 1024px) {
            .slides-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .admin-container {
                padding: 0 20px;
            }

            .admin-header-content {
                padding: 0 20px;
            }

            .admin-stats {
                width: 100%;
                justify-content: center;
            }

            .slides-grid {
                grid-template-columns: 1fr;
            }

            .alert-message {
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }

        @media (max-width: 480px) {
            .admin-title h1 {
                font-size: 1.5rem;
            }

            .slide-actions {
                flex-direction: column;
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

        .slide-card {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>

<body>
    <?php if (isset($_GET['sucesso']) && isset($mensagens[$_GET['sucesso']])): ?>
        <div class="alert-message alert-success">
            <i class="fas fa-check-circle"></i>
            <span><?= $mensagens[$_GET['sucesso']]['texto'] ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="alert-message alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>Ocorreu um erro. Tente novamente.</span>
        </div>
    <?php endif; ?>

    <!-- Header Admin -->
    <div class="admin-header">
        <div class="admin-header-content">
            <div class="admin-title">
                <i class="fas fa-images"></i>
                <div>
                    <h1>Gerenciar Slides</h1>
                    <p style="opacity: 0.8; font-size: 0.9rem;">Hero Slider da Página Inicial</p>
                </div>
            </div>
            <div class="admin-stats">
                <div class="stat-box">
                    <div class="number"><?= $totalSlides ?></div>
                    <div class="label">Total</div>
                </div>
                <div class="stat-box">
                    <div class="number"><?= $slidesAtivos ?></div>
                    <div class="label">Ativos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Container Principal -->
    <div class="admin-container">
        <!-- Action Bar -->
        <div class="action-bar">
            <h2>
                <i class="fas fa-sliders-h"></i>
                Slides do Banner
            </h2>
            <a href="criar_slide.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Adicionar Novo Slide
            </a>
        </div>

        <!-- Slides Grid -->
        <?php if ($totalSlides > 0): ?>
            <div class="slides-grid">
                <?php while ($slide = $result->fetch_assoc()): ?>
                    <div class="slide-card">
                        <div class="slide-preview" style="background-image: url('../../<?= htmlspecialchars($slide['imagem_url']) ?>');">
                            <div class="slide-badges">
                                <span class="badge-ordem">#<?= $slide['ordem'] ?></span>
                                <span class="badge-status <?= $slide['ativo'] ? 'ativo' : 'inativo' ?>">
                                    <?= $slide['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </div>
                            <div class="slide-preview-content">
                                <h3><?= htmlspecialchars($slide['titulo']) ?></h3>
                                <p><?= htmlspecialchars($slide['subtitulo']) ?></p>
                            </div>
                        </div>

                        <div class="slide-info">
                            <div class="slide-details">
                                <div class="slide-detail-item">
                                    <i class="fas fa-mouse-pointer"></i>
                                    <span><?= htmlspecialchars($slide['texto_botao']) ?></span>
                                </div>
                                <div class="slide-detail-item">
                                    <i class="fas fa-link"></i>
                                    <span><?= htmlspecialchars($slide['link_botao']) ?></span>
                                </div>
                                <div class="slide-detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><?= date('d/m/Y H:i', strtotime($slide['atualizado_em'])) ?></span>
                                </div>
                            </div>

                            <div class="slide-actions">
                                <a href="editar_slide.php?id=<?= $slide['id'] ?>" class="btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="toggle_slide.php?id=<?= $slide['id'] ?>" class="btn-toggle">
                                    <i class="fas fa-eye<?= $slide['ativo'] ? '-slash' : '' ?>"></i>
                                    <?= $slide['ativo'] ? 'Ocultar' : 'Ativar' ?>
                                </a>
                                <a href="deletar_slide.php?id=<?= $slide['id'] ?>" class="btn-delete">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-images"></i>
                <h3>Nenhum slide cadastrado</h3>
                <p>Comece adicionando seu primeiro slide ao banner</p>
                <br>
                <a href="criar_slide.php" class="btn-add">
                    <i class="fas fa-plus-circle"></i>
                    Adicionar Primeiro Slide
                </a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Animação suave ao carregar + Auto-hide de alertas
        document.addEventListener('DOMContentLoaded', function() {
            // Animar cards
            const cards = document.querySelectorAll('.slide-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });

            // Auto-remover alertas após 3 segundos
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.remove();
                }, 3000);
            });
        });
    </script>
</body>

</html>
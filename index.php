<?php
include 'config/conexao.php';

// Buscar produtos
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

// Buscar slides ativos ordenados
$sqlSlides = "SELECT * FROM slides WHERE ativo = 1 ORDER BY ordem ASC";
$resultSlides = $conn->query($sqlSlides);
$slides = [];
if ($resultSlides && $resultSlides->num_rows > 0) {
    while ($row = $resultSlides->fetch_assoc()) {
        $slides[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Loja</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://kit.fontawesome.com/a2d9d6d66a.js" crossorigin="anonymous"></script>
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <div class="hero-slider">
        <button class="slider-btn prev" onclick="prevSlide()">❮</button>
        <button class="slider-btn next" onclick="nextSlide()">❯</button>

        <?php if (!empty($slides)): ?>
            <?php foreach ($slides as $index => $slide): ?>
                <div class="slide <?= $index === 0 ? 'active' : '' ?>"
                    style="background-image: url('<?= htmlspecialchars($slide['imagem_url']) ?>');">
                    <div class="slide-content">
                        <h1><?= htmlspecialchars($slide['titulo']) ?></h1>
                        <?php if (!empty($slide['subtitulo'])): ?>
                            <p><?= htmlspecialchars($slide['subtitulo']) ?></p>
                        <?php endif; ?>
                        <a href="<?= htmlspecialchars($slide['link_botao']) ?>" class="cta-btn">
                            <?= htmlspecialchars($slide['texto_botao']) ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Slides padrão caso não haja slides no banco -->
            <div class="slide active">
                <div class="slide-content">
                    <h1>Ferramentas Profissionais</h1>
                    <p>As melhores marcas com até 40% de desconto</p>
                    <a href="#produtos" class="cta-btn">Ver Ofertas</a>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h1>Bombas D'água em Promoção</h1>
                    <p>Qualidade garantida e entrega rápida</p>
                    <a href="#produtos" class="cta-btn">Confira Agora</a>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h1>Compressores de Ar</h1>
                    <p>Para todos os tipos de trabalho</p>
                    <a href="#produtos" class="cta-btn">Comprar Agora</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="slider-dots">
            <?php
            $totalSlides = !empty($slides) ? count($slides) : 3;
            for ($i = 0; $i < $totalSlides; $i++):
            ?>
                <span class="dot <?= $i === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $i ?>)"></span>
            <?php endfor; ?>
        </div>
    </div>

    <!-- Categories Bar -->
    <div class="categories-bar">
        <div class="categories-container">
            <div class="categories-scroll">
                <div class="category-item">
                    <i class="fas fa-wrench"></i>
                    <span>Ferramentas Manuais</span>
                </div>
                <div class="category-item">
                    <i class="fas fa-bolt"></i>
                    <span>Ferramentas Elétricas</span>
                </div>
                <div class="category-item">
                    <i class="fas fa-water"></i>
                    <span>Bombas D'água</span>
                </div>
                <div class="category-item">
                    <i class="fas fa-wind"></i>
                    <span>Compressores</span>
                </div>
                <div class="category-item">
                    <i class="fas fa-screwdriver"></i>
                    <span>Acessórios</span>
                </div>
                <div class="category-item">
                    <i class="fas fa-helmet-safety"></i>
                    <span>Segurança</span>
                </div>
            </div>
        </div>
    </div>

    <div class="products-section" id="produtos">
        <div class="section-header">
            <h2>Nossos Produtos</h2>
        </div>
        <div class="products-grid" id="productsGrid">
            <!-- Produtos vão ser carregados via JS -->
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
    <script>
        // Função para integrar produtos do PHP com o carrinho do JS
        function addToCartJS(nome, preco) {
            const product = {
                name: nome,
                price: preco
            };
            cart.push(product);
            updateCartCount();

            // feedback no botão
            const btn = event.target.closest('.add-cart-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
            btn.style.background = '#25D366';

            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.style.background = '';
            }, 1500);
        }
    </script>
</body>

</html>
<?php
include 'config/conexao.php';
include 'includes/header.php';

// Buscar produtos
$sql = "SELECT * FROM produtos ORDER BY id DESC";
$result = $conn->query($sql);

// Buscar slides do banco
$sqlSlides = "SELECT * FROM slides WHERE ativo = 1 ORDER BY ordem";
$resultSlides = $conn->query($sqlSlides);
$slides = [];
while ($slide = $resultSlides->fetch_assoc()) {
    $slides[] = $slide;
}

// Se não houver slides, usar slides padrão
if (empty($slides)) {
    $slides = [
        [
            'titulo' => 'Ferramentas Profissionais',
            'subtitulo' => 'As melhores marcas com até 40% de desconto',
            'texto_botao' => 'Ver Ofertas',
            'link_botao' => '#produtos',
            'imagem_url' => 'assets/images/slider/ferramentas.jpg'
        ],
        [
            'titulo' => 'Bombas D\'Água em Promoção',
            'subtitulo' => 'Qualidade garantida e entrega rápida',
            'texto_botao' => 'Confira Agora',
            'link_botao' => '#produtos',
            'imagem_url' => 'assets/images/slider/bombas.jpg'
        ],
        [
            'titulo' => 'Compressores de Ar',
            'subtitulo' => 'Para todos os tipos de trabalho',
            'texto_botao' => 'Comprar Agora',
            'link_botao' => '#produtos',
            'imagem_url' => 'assets/images/slider/compressores.jpg'
        ]
    ];
}
?>

<link href="assets/css/carrinho.css" rel="stylesheet" />

<!-- Hero Slider -->
<section class="hero-slider">
    <?php foreach ($slides as $index => $slide): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?>">
            <div class="slide-content">
                <h1><?= htmlspecialchars($slide['titulo']) ?></h1>
                <p><?= htmlspecialchars($slide['subtitulo']) ?></p>
                <a class="cta-btn" href="<?= $slide['link_botao'] ?>">
                    <?= htmlspecialchars($slide['texto_botao']) ?>
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <!-- Botões de Navegação -->
    <button class="slider-btn prev" onclick="moveSlide(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slider-btn next" onclick="moveSlide(1)">
        <i class="fas fa-chevron-right"></i>
    </button>
    <!-- Dots de Navegação -->
    <div class="slider-dots">
        <?php foreach ($slides as $index => $slide): ?>
            <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="currentSlide(<?= $index ?>)"></span>
        <?php endforeach; ?>
    </div>
</section>
<!-- Barra de Categorias -->
<section class="categories-bar">
    <div class="categories-container">
        <div class="categories-scroll">
            <div class="category-item">
                <i class="fas fa-hammer"></i>
                <span>Ferramentas Manuais</span>
            </div>
            <div class="category-item">
                <i class="fas fa-bolt"></i>
                <span>Ferramentas Elétricas</span>
            </div>
            <div class="category-item">
                <i class="fas fa-tint"></i>
                <span>Bombas Dágua</span>
            </div>
            <div class="category-item">
                <i class="fas fa-wind"></i>
                <span>Compressores</span>
            </div>
            <div class="category-item">
                <i class="fas fa-wrench"></i>
                <span>Hidráulica</span>
            </div>
            <div class="category-item">
                <i class="fas fa-plug"></i>
                <span>Elétrica</span>
            </div>
            <div class="category-item">
                <i class="fas fa-paint-roller"></i>
                <span>Pintura</span>
            </div>
            <div class="category-item">
                <i class="fas fa-toolbox"></i>
                <span>Kits Completos</span>
            </div>
        </div>
    </div>
</section>
<!-- Seção de Produtos -->
<section class="products-section" id="produtos">
    <div class="section-header">
        <h2>Nossos Produtos</h2>
        <div class="filter-sort">
            <select id="categoryFilter" onchange="filterProducts()">
                <option value="all">Todas Categorias</option>
                <option value="Ferramentas">Ferramentas</option>
                <option value="Elétrica">Elétrica</option>
                <option value="Hidráulica">Hidráulica</option>
                <option value="Bombas">Bombas</option>
            </select>
            <select id="sortProducts" onchange="sortProducts()">
                <option value="default">Ordenar por</option>
                <option value="price-asc">Menor Preço</option>
                <option value="price-desc">Maior Preço</option>
                <option value="name-asc">Nome A-Z</option>
                <option value="name-desc">Nome Z-A</option>
            </select>
        </div>
    </div>
    <div class="products-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <!-- BUSCAR PRIMEIRA IMAGEM DO PRODUTO -->
                <?php
                $produtoId = $row['id'];
                $sqlImg = "SELECT imagem_url FROM produto_imagens WHERE produto_id = $produtoId ORDER BY ordem LIMIT 1";
                $resultImg = $conn->query($sqlImg);
                $primeiraImagem = null;
                if ($resultImg->num_rows > 0) {
                    $rowImg = $resultImg->fetch_assoc();
                    $primeiraImagem = $rowImg['imagem_url'];
                }
                ?>

                <div class="product-card" data-category="<?= htmlspecialchars($row['categoria']) ?>" data-name="<?= htmlspecialchars($row['nome']) ?>" data-price="<?= $row['preco'] ?>" data-produto-id="<?= $row['id'] ?>">
                    <?php if ($row['badge']): ?>
                        <span class="product-badge"><?= htmlspecialchars($row['badge']) ?></span>
                    <?php endif; ?>
                    <a href="produto.php?id=<?= $row['id'] ?>">
                        <div class="product-img">
                            <?php if ($primeiraImagem): ?>
                                <img alt="<?= htmlspecialchars($row['nome']) ?>" loading="lazy" src="<?= htmlspecialchars($primeiraImagem) ?>" />
                            <?php else: ?>
                                <i class="<?= $row['icon'] ?? 'fas fa-box' ?>"></i>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="product-details">
                        <div class="product-category"><?= htmlspecialchars($row['categoria']) ?: 'Geral' ?></div>
                        <a href="produto.php?id=<?= $row['id'] ?>">
                            <h3 class="product-name"><?= htmlspecialchars($row['nome']) ?></h3>
                        </a>
                        <p class="product-description">
                            <?= substr(htmlspecialchars($row['descricao']), 0, 80) ?>...
                        </p>
                        <div class="product-footer">
                            <span class="product-price">R$ <?= number_format($row['preco'], 2, ',', '.') ?></span>
                            <button class="add-cart-btn" onclick="event.stopPropagation(); adicionarAoCarrinho(<?= $row['id'] ?>)">
                                <i class="fas fa-shopping-cart"></i> Adicionar
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div>
                <i class="fas fa-inbox"></i>
                <h3>Nenhum produto cadastrado ainda</h3>
                <p>Em breve teremos novidades!</p>
            </div>
        <?php endif; ?>
    </div>
</section>
<!-- Botão Flutuante WhatsApp -->
<a class="floating-cart" href="https://wa.me/551141602205" target="_blank">
    <i class="fab fa-whatsapp"></i>
    <span class="floating-cart-badge">!</span>
</a>
<script src="assets/js/carrinho.js"></script>
<script>
    // ===== SLIDER =====
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    function showSlide(index) {
        slides.forEach(slide => {
            slide.classList.remove('slide-in-right', 'slide-in-left', 'slide-out-right', 'slide-out-left');
        });

        const isNext = index > currentSlideIndex || (index === 0 && currentSlideIndex === totalSlides - 1);

        if (isNext) {
            slides[currentSlideIndex].classList.add('slide-out-left');
        } else {
            slides[currentSlideIndex].classList.add('slide-out-right');
        }

        currentSlideIndex = index;
        if (currentSlideIndex >= totalSlides) currentSlideIndex = 0;
        if (currentSlideIndex < 0) currentSlideIndex = totalSlides - 1;

        setTimeout(() => {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === currentSlideIndex) {
                    slide.classList.add('active');
                    if (isNext) {
                        slide.classList.add('slide-in-right');
                    } else {
                        slide.classList.add('slide-in-left');
                    }
                }
            });

            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === currentSlideIndex);
            });
        }, 100);
    }

    function moveSlide(direction) {
        showSlide(currentSlideIndex + direction);
    }

    function currentSlide(index) {
        showSlide(index);
    }

    let autoPlayInterval = setInterval(() => {
        moveSlide(1);
    }, 5000);

    document.querySelector('.hero-slider').addEventListener('mouseenter', () => {
        clearInterval(autoPlayInterval);
    });

    document.querySelector('.hero-slider').addEventListener('mouseleave', () => {
        autoPlayInterval = setInterval(() => {
            moveSlide(1);
        }, 5000);
    });

    // ===== FILTRO E ORDENAÇÃO DE PRODUTOS =====
    function filterProducts() {
        const category = document.getElementById('categoryFilter').value;
        const products = document.querySelectorAll('.product-card');

        products.forEach(product => {
            const productCategory = product.getAttribute('data-category');

            if (category === 'all' || productCategory === category || !productCategory) {
                product.style.display = 'block';
                setTimeout(() => {
                    product.style.opacity = '1';
                    product.style.transform = 'scale(1)';
                }, 10);
            } else {
                product.style.opacity = '0';
                product.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    product.style.display = 'none';
                }, 300);
            }
        });
    }

    function sortProducts() {
        const sortValue = document.getElementById('sortProducts').value;
        const productsGrid = document.querySelector('.products-grid');
        const products = Array.from(document.querySelectorAll('.product-card'));

        products.sort((a, b) => {
            switch (sortValue) {
                case 'price-asc':
                    return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                case 'price-desc':
                    return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                case 'name-asc':
                    return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                case 'name-desc':
                    return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                default:
                    return 0;
            }
        });

        products.forEach(product => {
            productsGrid.appendChild(product);
        });

        products.forEach((product, index) => {
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';
            setTimeout(() => {
                product.style.transition = 'all 0.3s ease';
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }

    // ===== ANIMAÇÃO DOS CARDS AO SCROLL =====
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });

    // ===== CATEGORIAS INTERATIVAS =====
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', function() {
            const categoryName = this.querySelector('span').textContent;

            document.querySelectorAll('.category-item').forEach(cat => {
                cat.classList.remove('active');
            });

            this.classList.add('active');

            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');

                if (productCategory && productCategory.toLowerCase().includes(categoryName.toLowerCase().split(' ')[0])) {
                    product.style.display = 'block';
                    setTimeout(() => {
                        product.style.opacity = '1';
                        product.style.transform = 'scale(1)';
                    }, 10);
                } else {
                    product.style.opacity = '0';
                    product.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        product.style.display = 'none';
                    }, 300);
                }
            });

            document.getElementById('produtos').scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // ===== EFEITO HOVER NOS CARDS =====
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // ===== BUSCA NO HEADER =====
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const name = product.getAttribute('data-name').toLowerCase();
                const category = product.getAttribute('data-category').toLowerCase();

                if (name.includes(searchTerm) || category.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    }
</script>
<?php include 'includes/footer.php'; ?>
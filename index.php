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

<link rel="stylesheet" href="assets/css/carrinho.css">

<style>
    /* ===== PRODUTOS GRID ===== */
    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 25px;
        padding: 20px 0;
    }

    .product-card {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    }

    .product-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: linear-gradient(135deg, #FF6B35 0%, #FFA630 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 2;
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }

    /* ===== IMAGEM DO PRODUTO ===== */
    .product-img {
        position: relative;
        width: 100%;
        height: 200px;
        overflow: hidden;
        background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .product-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
    }

    .product-img i {
        font-size: 4rem;
        color: #ddd;
        opacity: 0.5;
    }

    .product-card:hover .product-img img {
        transform: scale(1.1);
    }

    /* ===== DETALHES DO PRODUTO ===== */
    .product-details {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        font-size: 0.75rem;
        color: #FF6B35;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
    }

    .product-name {
        font-size: 1rem;
        font-weight: 700;
        color: #1A1A2E;
        margin-bottom: 10px;
        line-height: 1.3;
        min-height: 2.6em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .product-description {
        font-size: 0.85rem;
        color: #666;
        margin-bottom: 15px;
        flex-grow: 1;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ===== RODAPÉ DO CARD ===== */
    .product-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
        margin-top: auto;
    }

    .product-price {
        font-size: 1.3rem;
        font-weight: 800;
        color: #004E89;
    }

    .add-cart-btn {
        background: linear-gradient(135deg, #FF6B35 0%, #FFA630 100%);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .add-cart-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(255, 107, 53, 0.3);
    }

    .add-cart-btn:active {
        transform: scale(0.95);
    }

    @media (max-width: 768px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }

        .product-img {
            height: 180px;
        }

        .product-img i {
            font-size: 3rem;
        }
    }

    @media (max-width: 480px) {
        .products-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .product-img {
            height: 150px;
        }

        .product-img i {
            font-size: 2.5rem;
        }

        .product-details {
            padding: 15px;
        }
    }
</style>

<!-- Hero Slider -->
<section class="hero-slider">
    <?php foreach ($slides as $index => $slide): ?>
        <div class="slide <?= $index === 0 ? 'active' : '' ?>" style="background-image: url('<?= $slide['imagem_url'] ?>');">
            <div class="slide-content">
                <h1><?= htmlspecialchars($slide['titulo']) ?></h1>
                <p><?= htmlspecialchars($slide['subtitulo']) ?></p>
                <a href="<?= $slide['link_botao'] ?>" class="cta-btn">
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
                <span>Bombas D'água</span>
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

                <div class="product-card"
                    data-category="<?= htmlspecialchars($row['categoria']) ?>"
                    data-price="<?= $row['preco'] ?>"
                    data-name="<?= htmlspecialchars($row['nome']) ?>"
                    data-produto-id="<?= $row['id'] ?>">
                    <?php if ($row['badge']): ?>
                        <span class="product-badge"><?= htmlspecialchars($row['badge']) ?></span>
                    <?php endif; ?>

                    <a href="produto.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-img">
                            <?php if ($primeiraImagem): ?>
                                <img src="<?= htmlspecialchars($primeiraImagem) ?>" alt="<?= htmlspecialchars($row['nome']) ?>" loading="lazy">
                            <?php else: ?>
                                <i class="<?= $row['icon'] ?? 'fas fa-box' ?>"></i>
                            <?php endif; ?>
                        </div>
                    </a>

                    <div class="product-details">
                        <div class="product-category"><?= htmlspecialchars($row['categoria']) ?: 'Geral' ?></div>

                        <a href="produto.php?id=<?= $row['id'] ?>" style="text-decoration: none; color: inherit;">
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
            <div style="grid-column: 1/-1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-inbox" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
                <h3 style="color: #666; font-size: 1.5rem;">Nenhum produto cadastrado ainda</h3>
                <p style="color: #999; margin-top: 10px;">Em breve teremos novidades!</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Botão Flutuante WhatsApp -->
<a href="https://wa.me/5511992810035" class="floating-cart" target="_blank">
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
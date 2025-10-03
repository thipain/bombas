// CONFIGURAÇÕES
const CONFIG = {
    whatsappNumber: '5511992810035',
    whatsappMessage: 'Olá! Gostaria de comprar:\n\n'
};

// Carrinho
let cart = [];
let products = [];

// Buscar produtos via AJAX
async function fetchProducts() {
    try {
        const response = await fetch('get_products.php');
        products = await response.json();
        renderProducts();
    } catch (error) {
        console.error("Erro ao carregar produtos:", error);
    }
}

// Renderizar produtos
function renderProducts() {
    const grid = document.getElementById('productsGrid');
    if (!grid) return;
    grid.innerHTML = '';

    products.forEach((product, index) => {
        const card = document.createElement('div');
        card.className = 'product-card';
        card.innerHTML = `
            <div class="product-badge">${product.badge ?? ''}</div>
            <div class="product-img">
                <i class="${product.icon ?? 'fas fa-box'}"></i>
            </div>
            <div class="product-details">
                <div class="product-category">${product.categoria ?? ''}</div>
                <div class="product-name">${product.nome}</div>
                <div class="product-description">${product.descricao ?? ''}</div>
                <div class="product-footer">
                    <div class="product-price">R$ ${parseFloat(product.preco).toFixed(2)}</div>
                    <button class="add-cart-btn" onclick="addToCart(${index}, event)">
                        <i class="fas fa-cart-plus"></i>
                        Adicionar
                    </button>
                </div>
            </div>
        `;
        grid.appendChild(card);
    });
}

// Adicionar ao carrinho
function addToCart(index, event) {
    const product = products[index];
    cart.push(product);
    updateCartCount();

    // Animação de feedback
    const btn = event.target.closest('.add-cart-btn');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Adicionado!';
    btn.style.background = '#25D366';

    setTimeout(() => {
        btn.innerHTML = originalText;
        btn.style.background = '';
    }, 1500);
}

// Atualizar contador do carrinho
function updateCartCount() {
    const count = cart.length;
    const cartCount = document.getElementById('cartCount');
    const floatingCartCount = document.getElementById('floatingCartCount');

    if (cartCount) cartCount.textContent = count;
    if (floatingCartCount) floatingCartCount.textContent = count;
}

// Abrir carrinho no WhatsApp
function openCart() {
    if (cart.length === 0) {
        alert('Seu carrinho está vazio!');
        return;
    }

    let message = CONFIG.whatsappMessage;
    cart.forEach((product, index) => {
        message += `${index + 1}. ${product.nome} - R$ ${parseFloat(product.preco).toFixed(2)}\n`;
    });
    message += `\nTotal de itens: ${cart.length}`;

    const url = `https://wa.me/${CONFIG.whatsappNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

// Iniciar ao carregar
document.addEventListener("DOMContentLoaded", () => {
    fetchProducts();
});

// Hero Slider Functionality
let currentSlideIndex = 0;
let slideInterval;
let isTransitioning = false;

// Função para mostrar um slide específico
function showSlide(index, direction = 'next') {
    if (isTransitioning) return;
    
    const slides = document.querySelectorAll('.hero-slider .slide');
    const dots = document.querySelectorAll('.slider-dots .dot');
    
    const oldIndex = currentSlideIndex;
    
    // Garante que o índice está dentro dos limites
    if (index >= slides.length) {
        currentSlideIndex = 0;
    } else if (index < 0) {
        currentSlideIndex = slides.length - 1;
    } else {
        currentSlideIndex = index;
    }
    
    // Determina a direção da transição
    if (currentSlideIndex > oldIndex || (oldIndex === slides.length - 1 && currentSlideIndex === 0)) {
        direction = 'next';
    } else if (currentSlideIndex < oldIndex || (oldIndex === 0 && currentSlideIndex === slides.length - 1)) {
        direction = 'prev';
    }
    
    isTransitioning = true;
    
    // Aplica classes de transição
    slides[oldIndex].classList.add(direction === 'next' ? 'slide-out-left' : 'slide-out-right');
    slides[currentSlideIndex].classList.add(direction === 'next' ? 'slide-in-right' : 'slide-in-left');
    slides[currentSlideIndex].classList.add('active');
    
    // Remove a classe 'active' do slide antigo após a animação começar
    setTimeout(() => {
        slides[oldIndex].classList.remove('active');
    }, 50);
    
    // Limpa as classes de transição após a animação
    setTimeout(() => {
        slides.forEach(slide => {
            slide.classList.remove('slide-out-left', 'slide-out-right', 'slide-in-left', 'slide-in-right');
        });
        isTransitioning = false;
    }, 600);
    
    // Atualiza os dots
    dots.forEach(dot => dot.classList.remove('active'));
    dots[currentSlideIndex].classList.add('active');
}

// Função para ir ao próximo slide
function nextSlide() {
    showSlide(currentSlideIndex + 1);
}

// Função para ir ao slide anterior
function prevSlide() {
    showSlide(currentSlideIndex - 1);
}

// Função para ir a um slide específico (usada pelos dots)
function currentSlide(index) {
    clearInterval(slideInterval);
    showSlide(index);
    startAutoSlide();
}

// Função para iniciar o slider automático
function startAutoSlide() {
    slideInterval = setInterval(nextSlide, 5000); // Troca a cada 5 segundos
}

// Inicializa o slider quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    showSlide(0);
    startAutoSlide();
    
    // Pausa o slider quando o mouse estiver sobre ele
    const heroSlider = document.querySelector('.hero-slider');
    if (heroSlider) {
        heroSlider.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });
        
        heroSlider.addEventListener('mouseleave', () => {
            startAutoSlide();
        });
    }
    
    // Adiciona suporte para navegação por teclado (opcional)
    document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
            clearInterval(slideInterval);
            prevSlide();
            startAutoSlide();
        } else if (e.key === 'ArrowRight') {
            clearInterval(slideInterval);
            nextSlide();
            startAutoSlide();
        }
    });
});

// Adiciona botões de navegação (opcional - adicione no HTML se quiser)
// <button class="slider-btn prev" onclick="prevSlide()">❮</button>
// <button class="slider-btn next" onclick="nextSlide()">❯</button>
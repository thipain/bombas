// ===== SISTEMA DE CARRINHO DE COMPRAS =====

class CarrinhoCompras {
    constructor() {
        this.itens = this.carregarCarrinho();
        this.atualizarContador();
    }

    // Carregar carrinho do armazenamento
    carregarCarrinho() {
        const carrinho = {};
        const keys = Object.keys(sessionStorage);
        
        keys.forEach(key => {
            if (key.startsWith('cart_')) {
                const itemData = sessionStorage.getItem(key);
                if (itemData) {
                    carrinho[key] = JSON.parse(itemData);
                }
            }
        });
        
        return carrinho;
    }

    // Salvar carrinho
    salvarCarrinho() {
        // Limpar itens antigos do carrinho
        const keys = Object.keys(sessionStorage);
        keys.forEach(key => {
            if (key.startsWith('cart_')) {
                sessionStorage.removeItem(key);
            }
        });
        
        // Salvar novos itens
        Object.keys(this.itens).forEach(key => {
            sessionStorage.setItem(key, JSON.stringify(this.itens[key]));
        });
        
        this.atualizarContador();
    }

    // Adicionar item ao carrinho
    adicionar(produto) {
        const key = `cart_${produto.id}`;
        
        if (this.itens[key]) {
            // Se já existe, aumenta a quantidade
            this.itens[key].quantidade += produto.quantidade;
        } else {
            // Adiciona novo item
            this.itens[key] = {
                id: produto.id,
                nome: produto.nome,
                preco: produto.preco,
                quantidade: produto.quantidade,
                imagem: produto.imagem || '',
                categoria: produto.categoria || ''
            };
        }
        
        this.salvarCarrinho();
        this.mostrarNotificacao('Produto adicionado ao carrinho!', 'success');
        return true;
    }

    // Remover item do carrinho
    remover(produtoId) {
        const key = `cart_${produtoId}`;
        delete this.itens[key];
        this.salvarCarrinho();
        this.mostrarNotificacao('Produto removido do carrinho', 'info');
    }

    // Atualizar quantidade
    atualizarQuantidade(produtoId, quantidade) {
        const key = `cart_${produtoId}`;
        
        if (this.itens[key]) {
            if (quantidade <= 0) {
                this.remover(produtoId);
            } else {
                this.itens[key].quantidade = quantidade;
                this.salvarCarrinho();
            }
        }
    }

    // Obter todos os itens
    obterItens() {
        return Object.values(this.itens);
    }

    // Obter total de itens
    obterTotalItens() {
        return Object.values(this.itens).reduce((total, item) => total + item.quantidade, 0);
    }

    // Obter valor total
    obterValorTotal() {
        return Object.values(this.itens).reduce((total, item) => {
            return total + (item.preco * item.quantidade);
        }, 0);
    }

    // Limpar carrinho
    limpar() {
        this.itens = {};
        const keys = Object.keys(sessionStorage);
        keys.forEach(key => {
            if (key.startsWith('cart_')) {
                sessionStorage.removeItem(key);
            }
        });
        this.atualizarContador();
        this.mostrarNotificacao('Carrinho esvaziado', 'info');
    }

    // Atualizar contador visual
    atualizarContador() {
        const contador = document.getElementById('cartCount');
        const contadorFlutuante = document.querySelector('.floating-cart-badge');
        const total = this.obterTotalItens();
        
        if (contador) {
            contador.textContent = total;
            contador.style.display = total > 0 ? 'flex' : 'none';
        }
        
        if (contadorFlutuante) {
            contadorFlutuante.textContent = total;
            contadorFlutuante.style.display = total > 0 ? 'flex' : 'none';
        }
    }

    // Mostrar notificação
    mostrarNotificacao(mensagem, tipo = 'success') {
        // Remover notificação existente
        const notifExistente = document.querySelector('.cart-notification');
        if (notifExistente) {
            notifExistente.remove();
        }

        // Criar nova notificação
        const notificacao = document.createElement('div');
        notificacao.className = `cart-notification ${tipo}`;
        notificacao.innerHTML = `
            <i class="fas fa-${tipo === 'success' ? 'check-circle' : 'info-circle'}"></i>
            <span>${mensagem}</span>
        `;
        
        document.body.appendChild(notificacao);
        
        // Animar entrada
        setTimeout(() => notificacao.classList.add('show'), 10);
        
        // Remover após 3 segundos
        setTimeout(() => {
            notificacao.classList.remove('show');
            setTimeout(() => notificacao.remove(), 300);
        }, 3000);
    }

    // Gerar mensagem para WhatsApp
    gerarMensagemWhatsApp() {
        const itens = this.obterItens();
        
        if (itens.length === 0) {
            return 'Olá! Gostaria de fazer um pedido.';
        }

        let mensagem = '🛒 *Meu Pedido AB Bombas*\n\n';
        
        itens.forEach((item, index) => {
            mensagem += `${index + 1}. *${item.nome}*\n`;
            mensagem += `   Qtd: ${item.quantidade}x\n`;
            mensagem += `   Valor: R$ ${this.formatarMoeda(item.preco)}\n`;
            mensagem += `   Subtotal: R$ ${this.formatarMoeda(item.preco * item.quantidade)}\n\n`;
        });
        
        mensagem += `💰 *TOTAL: R$ ${this.formatarMoeda(this.obterValorTotal())}*\n\n`;
        mensagem += 'Gostaria de finalizar este pedido!';
        
        return encodeURIComponent(mensagem);
    }

    // Formatar valor em moeda
    formatarMoeda(valor) {
        return valor.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
}

// Instanciar carrinho globalmente
const carrinho = new CarrinhoCompras();

// ===== FUNÇÕES AUXILIARES =====

// Função para adicionar produto ao carrinho (chamada pelos botões)
function adicionarAoCarrinho(produtoId) {
    // Buscar dados do produto
    const produtoCard = document.querySelector(`[data-produto-id="${produtoId}"]`);
    
    let produto;
    
    // Se estiver na página do produto
    const quantidadeInput = document.getElementById('quantidade');
    if (quantidadeInput) {
        produto = {
            id: produtoId,
            nome: document.querySelector('.produto-titulo')?.textContent || 'Produto',
            preco: parseFloat(document.querySelector('.preco-atual')?.textContent.replace('R$', '').replace('.', '').replace(',', '.').trim()) || 0,
            quantidade: parseInt(quantidadeInput.value) || 1,
            categoria: document.querySelector('.produto-categoria-tag')?.textContent || '',
            imagem: document.getElementById('imagemPrincipal')?.src || ''
        };
    } 
    // Se estiver na listagem
    else if (produtoCard) {
        produto = {
            id: produtoId,
            nome: produtoCard.querySelector('.product-name')?.textContent || 'Produto',
            preco: parseFloat(produtoCard.querySelector('.product-price')?.textContent.replace('R$', '').replace('.', '').replace(',', '.').trim()) || 0,
            quantidade: 1,
            categoria: produtoCard.querySelector('.product-category')?.textContent || '',
            imagem: ''
        };
    }
    // Fallback
    else {
        produto = {
            id: produtoId,
            nome: 'Produto',
            preco: 0,
            quantidade: 1,
            categoria: '',
            imagem: ''
        };
    }
    
    carrinho.adicionar(produto);
}

// Abrir modal do carrinho
function abrirCarrinho() {
    const modal = document.getElementById('carrinhoModal');
    if (modal) {
        modal.style.display = 'flex';
        atualizarCarrinhoModal();
    }
}

// Fechar modal do carrinho
function fecharCarrinho() {
    const modal = document.getElementById('carrinhoModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Atualizar conteúdo do modal do carrinho
function atualizarCarrinhoModal() {
    const itens = carrinho.obterItens();
    const listaItens = document.getElementById('carrinhoItens');
    const totalElement = document.getElementById('carrinhoTotal');
    const carrinhoVazio = document.getElementById('carrinhoVazio');
    const carrinhoConteudo = document.getElementById('carrinhoConteudo');
    
    if (itens.length === 0) {
        if (carrinhoVazio) carrinhoVazio.style.display = 'block';
        if (carrinhoConteudo) carrinhoConteudo.style.display = 'none';
        return;
    }
    
    if (carrinhoVazio) carrinhoVazio.style.display = 'none';
    if (carrinhoConteudo) carrinhoConteudo.style.display = 'block';
    
    // Atualizar lista de itens
    if (listaItens) {
        listaItens.innerHTML = itens.map(item => `
            <div class="carrinho-item" data-produto-id="${item.id}">
                <div class="carrinho-item-info">
                    <h4>${item.nome}</h4>
                    <p class="carrinho-item-preco">R$ ${carrinho.formatarMoeda(item.preco)}</p>
                </div>
                <div class="carrinho-item-acoes">
                    <div class="quantidade-controls">
                        <button onclick="alterarQuantidadeCarrinho(${item.id}, ${item.quantidade - 1})">
                            <i class="fas fa-minus"></i>
                        </button>
                        <span>${item.quantidade}</span>
                        <button onclick="alterarQuantidadeCarrinho(${item.id}, ${item.quantidade + 1})">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <button class="btn-remover" onclick="removerDoCarrinho(${item.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="carrinho-item-subtotal">
                    Subtotal: R$ ${carrinho.formatarMoeda(item.preco * item.quantidade)}
                </div>
            </div>
        `).join('');
    }
    
    // Atualizar total
    if (totalElement) {
        totalElement.textContent = `R$ ${carrinho.formatarMoeda(carrinho.obterValorTotal())}`;
    }
}

// Alterar quantidade no carrinho
function alterarQuantidadeCarrinho(produtoId, novaQuantidade) {
    carrinho.atualizarQuantidade(produtoId, novaQuantidade);
    atualizarCarrinhoModal();
}

// Remover item do carrinho
function removerDoCarrinho(produtoId) {
    if (confirm('Deseja realmente remover este item do carrinho?')) {
        carrinho.remover(produtoId);
        atualizarCarrinhoModal();
    }
}

// Limpar carrinho
function limparCarrinho() {
    if (confirm('Deseja realmente esvaziar o carrinho?')) {
        carrinho.limpar();
        atualizarCarrinhoModal();
    }
}

// Finalizar pedido via WhatsApp
function finalizarPedidoWhatsApp() {
    const mensagem = carrinho.gerarMensagemWhatsApp();
    const numeroWhatsApp = '551141602205'; // Seu número
    const url = `https://wa.me/${numeroWhatsApp}?text=${mensagem}`;
    window.open(url, '_blank');
}

// Inicializar ao carregar a página
document.addEventListener('DOMContentLoaded', function() {
    // Criar modal do carrinho se não existir
    if (!document.getElementById('carrinhoModal')) {
        criarModalCarrinho();
    }
    
    // Atualizar contador
    carrinho.atualizarContador();
    
    // Event listener para o botão do carrinho no header
    const cartBtn = document.querySelector('.cart-btn');
    if (cartBtn) {
        cartBtn.addEventListener('click', function(e) {
            e.preventDefault();
            abrirCarrinho();
        });
    }
});

// Criar modal do carrinho
function criarModalCarrinho() {
    const modal = document.createElement('div');
    modal.id = 'carrinhoModal';
    modal.className = 'carrinho-modal';
    modal.innerHTML = `
        <div class="carrinho-modal-content">
            <div class="carrinho-modal-header">
                <h2><i class="fas fa-shopping-cart"></i> Meu Carrinho</h2>
                <button class="btn-fechar" onclick="fecharCarrinho()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="carrinho-modal-body">
                <div id="carrinhoVazio" class="carrinho-vazio">
                    <i class="fas fa-shopping-cart"></i>
                    <p>Seu carrinho está vazio</p>
                    <button onclick="fecharCarrinho()" class="btn-continuar">
                        Continuar Comprando
                    </button>
                </div>
                
                <div id="carrinhoConteudo" class="carrinho-conteudo" style="display: none;">
                    <div id="carrinhoItens" class="carrinho-itens"></div>
                    
                    <div class="carrinho-total">
                        <span>Total:</span>
                        <span id="carrinhoTotal" class="total-valor">R$ 0,00</span>
                    </div>
                    
                    <div class="carrinho-acoes">
                        <button onclick="limparCarrinho()" class="btn-limpar">
                            <i class="fas fa-trash"></i> Limpar Carrinho
                        </button>
                        <button onclick="finalizarPedidoWhatsApp()" class="btn-finalizar">
                            <i class="fab fa-whatsapp"></i> Finalizar Pedido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Fechar ao clicar fora
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            fecharCarrinho();
        }
    });
}
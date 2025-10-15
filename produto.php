<?php
include 'config/conexao.php';
include 'includes/header.php';

$id = $_GET['id'] ?? 0;

// Buscar produto
$sql = "SELECT * FROM produtos WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div style='text-align:center; padding:100px; font-size:1.5rem;'>Produto não encontrado!</div>";
    include 'includes/footer.php';
    exit;
}

$produto = $result->fetch_assoc();

// Buscar imagens do produto
$sqlImagens = "SELECT * FROM produto_imagens WHERE produto_id = $id ORDER BY ordem";
$resultImagens = $conn->query($sqlImagens);
$imagens = [];
while ($img = $resultImagens->fetch_assoc()) {
    $imagens[] = $img['imagem_url'];
}

// Se não houver imagens cadastradas, usar imagem padrão
if (empty($imagens)) {
    $imagens[] = 'assets/images/default-product.jpg';
}
?>

<link rel="stylesheet" href="assets/css/produto.css">
<link rel="stylesheet" href="assets/css/carrinho.css">

<div class="produto-container" data-produto-id="<?= $id ?>">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="index.php"><i class="fas fa-home"></i> Início</a>
        <span>/</span>
        <a href="index.php#produtos">Produtos</a>
        <span>/</span>
        <span><?= htmlspecialchars($produto['nome']) ?></span>
    </div>

    <div class="produto-content">
        <!-- Galeria de Imagens -->
        <div class="produto-galeria">
            <div class="imagem-principal">
                <img id="imagemPrincipal" src="<?= $imagens[0] ?>" alt="<?= htmlspecialchars($produto['nome']) ?>">
                <?php if ($produto['badge']): ?>
                    <span class="produto-badge"><?= htmlspecialchars($produto['badge']) ?></span>
                <?php endif; ?>
            </div>

            <?php if (count($imagens) > 1): ?>
                <div class="miniaturas">
                    <?php foreach ($imagens as $index => $img): ?>
                        <img src="<?= $img ?>"
                            alt="Miniatura <?= $index + 1 ?>"
                            class="miniatura <?= $index === 0 ? 'active' : '' ?>"
                            onclick="trocarImagem('<?= $img ?>', this)">
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informações do Produto -->
        <div class="produto-info">
            <h1 class="produto-titulo"><?= htmlspecialchars($produto['nome']) ?></h1>

            <?php if ($produto['categoria']): ?>
                <div class="produto-categoria-tag">
                    <i class="fas fa-tag"></i>
                    <?= htmlspecialchars($produto['categoria']) ?>
                </div>
            <?php endif; ?>

            <div class="produto-preco-container">
                <span class="preco-antigo">R$ <?= number_format($produto['preco'] * 1.2, 2, ',', '.') ?></span>
                <span class="preco-atual">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></span>
                <span class="desconto-tag">20% OFF</span>
            </div>

            <?php if ($produto['descricao']): ?>
                <div class="produto-descricao">
                    <h3><i class="fas fa-info-circle"></i> Descrição</h3>
                    <p><?= nl2br(htmlspecialchars($produto['descricao'])) ?></p>
                </div>
            <?php endif; ?>

            <!-- Especificações -->
            <?php if ($produto['especificacoes'] || $produto['marca'] || $produto['modelo']): ?>
                <div class="produto-especificacoes">
                    <h3><i class="fas fa-list-ul"></i> Especificações</h3>
                    <ul>
                        <?php if ($produto['marca']): ?>
                            <li><strong>Marca:</strong> <?= htmlspecialchars($produto['marca']) ?></li>
                        <?php endif; ?>
                        <?php if ($produto['modelo']): ?>
                            <li><strong>Modelo:</strong> <?= htmlspecialchars($produto['modelo']) ?></li>
                        <?php endif; ?>
                        <?php if ($produto['peso']): ?>
                            <li><strong>Peso:</strong> <?= number_format($produto['peso'], 2, ',', '.') ?> kg</li>
                        <?php endif; ?>
                        <?php if ($produto['dimensoes']): ?>
                            <li><strong>Dimensões:</strong> <?= htmlspecialchars($produto['dimensoes']) ?></li>
                        <?php endif; ?>
                        <?php if ($produto['especificacoes']): ?>
                            <?php
                            $specs = explode("\n", $produto['especificacoes']);
                            foreach ($specs as $spec):
                                if (trim($spec)):
                            ?>
                                    <li><?= htmlspecialchars($spec) ?></li>
                            <?php
                                endif;
                            endforeach;
                            ?>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Estoque -->
            <div class="produto-estoque">
                <?php if ($produto['estoque'] > 0): ?>
                    <span class="estoque-disponivel">
                        <i class="fas fa-check-circle"></i>
                        <?= $produto['estoque'] ?> unidades disponíveis
                    </span>
                <?php else: ?>
                    <span class="estoque-indisponivel">
                        <i class="fas fa-times-circle"></i>
                        Produto esgotado
                    </span>
                <?php endif; ?>
            </div>

            <!-- Quantidade e Botões -->
            <div class="produto-acoes">
                <div class="quantidade-selector">
                    <button onclick="diminuirQuantidade()"><i class="fas fa-minus"></i></button>
                    <input type="number" id="quantidade" value="1" min="1" max="<?= $produto['estoque'] ?>">
                    <button onclick="aumentarQuantidade()"><i class="fas fa-plus"></i></button>
                </div>

                <button class="btn-add-carrinho" onclick="adicionarAoCarrinhoProduto()">
                    <i class="fas fa-shopping-cart"></i>
                    Adicionar ao Carrinho
                </button>

                <a href="https://wa.me/5511992810035?text=Olá! Tenho interesse no produto: <?= urlencode($produto['nome']) ?>"
                    class="btn-whatsapp"
                    target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    Comprar via WhatsApp
                </a>
            </div>

            <!-- Garantias e Informações -->
            <div class="produto-garantias">
                <div class="garantia-item">
                    <i class="fas fa-shipping-fast"></i>
                    <div>
                        <strong>Frete Grátis</strong>
                        <span>Para compras acima de R$ 299</span>
                    </div>
                </div>
                <div class="garantia-item">
                    <i class="fas fa-shield-alt"></i>
                    <div>
                        <strong>Garantia</strong>
                        <span>12 meses de garantia</span>
                    </div>
                </div>
                <div class="garantia-item">
                    <i class="fas fa-undo"></i>
                    <div>
                        <strong>Troca Grátis</strong>
                        <span>7 dias para trocar</span>
                    </div>
                </div>
                <div class="garantia-item">
                    <i class="fas fa-lock"></i>
                    <div>
                        <strong>Compra Segura</strong>
                        <span>Seus dados protegidos</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produtos Relacionados -->
    <div class="produtos-relacionados">
        <h2><i class="fas fa-boxes"></i> Produtos Relacionados</h2>
        <div class="produtos-grid">
            <?php
            $sqlRelacionados = "SELECT * FROM produtos WHERE categoria = '{$produto['categoria']}' AND id != $id LIMIT 4";
            $resultRelacionados = $conn->query($sqlRelacionados);

            while ($relacionado = $resultRelacionados->fetch_assoc()):
            ?>
                <div class="product-card" data-produto-id="<?= $relacionado['id'] ?>">
                    <?php if ($relacionado['badge']): ?>
                        <span class="product-badge"><?= htmlspecialchars($relacionado['badge']) ?></span>
                    <?php endif; ?>

                    <a href="produto.php?id=<?= $relacionado['id'] ?>" style="text-decoration: none; color: inherit;">
                        <div class="product-img">
                            <i class="<?= $relacionado['icon'] ?? 'fas fa-box' ?>"></i>
                        </div>
                    </a>

                    <div class="product-details">
                        <div class="product-category"><?= htmlspecialchars($relacionado['categoria']) ?></div>

                        <a href="produto.php?id=<?= $relacionado['id'] ?>" style="text-decoration: none; color: inherit;">
                            <h3 class="product-name"><?= htmlspecialchars($relacionado['nome']) ?></h3>
                        </a>

                        <p class="product-description"><?= substr(htmlspecialchars($relacionado['descricao']), 0, 80) ?>...</p>

                        <div class="product-footer">
                            <span class="product-price">R$ <?= number_format($relacionado['preco'], 2, ',', '.') ?></span>
                            <a href="produto.php?id=<?= $relacionado['id'] ?>" class="btn-ver-mais">
                                Ver Mais
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<!-- Carregar o script do carrinho ANTES dos scripts locais -->
<script src="assets/js/carrinho.js"></script>

<script>
    function trocarImagem(src, elemento) {
        document.getElementById('imagemPrincipal').src = src;
        document.querySelectorAll('.miniatura').forEach(m => m.classList.remove('active'));
        elemento.classList.add('active');
    }

    function diminuirQuantidade() {
        const input = document.getElementById('quantidade');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function aumentarQuantidade() {
        const input = document.getElementById('quantidade');
        const max = parseInt(input.max);
        if (input.value < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    // Função específica para adicionar ao carrinho na página do produto
    function adicionarAoCarrinhoProduto() {
        // Capturar ID do produto
        const produtoId = parseInt(document.querySelector('.produto-container').dataset.produtoId);

        // Capturar quantidade
        const quantidade = parseInt(document.getElementById('quantidade').value);

        // Capturar nome
        const nome = document.querySelector('.produto-titulo').textContent.trim();

        // Capturar preço (remover formatação)
        const precoTexto = document.querySelector('.preco-atual').textContent;
        const preco = parseFloat(precoTexto.replace('R$', '').replace(/\./g, '').replace(',', '.').trim());

        // Capturar categoria
        const categoriaElement = document.querySelector('.produto-categoria-tag');
        const categoria = categoriaElement ? categoriaElement.textContent.trim() : '';

        // Capturar imagem
        const imagem = document.getElementById('imagemPrincipal').src;

        // Criar objeto do produto
        const produto = {
            id: produtoId,
            nome: nome,
            preco: preco,
            quantidade: quantidade,
            categoria: categoria,
            imagem: imagem
        };

        // Verificar se o carrinho está disponível
        if (typeof carrinho !== 'undefined') {
            carrinho.adicionar(produto);
            console.log('Produto adicionado:', produto);
        } else {
            console.error('Objeto carrinho não está disponível');
            alert('Erro ao adicionar produto. Por favor, recarregue a página e tente novamente.');
        }
    }

    // Animação ao rolar a página
    window.addEventListener('scroll', function() {
        const elementos = document.querySelectorAll('.produto-especificacoes, .produto-garantias');
        elementos.forEach(el => {
            const posicao = el.getBoundingClientRect().top;
            const altura = window.innerHeight;
            if (posicao < altura - 100) {
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }
        });
    });

    // Log para debug - remover em produção
    console.log('Página do produto carregada. ID:', document.querySelector('.produto-container')?.dataset.produtoId);
</script>

<?php include 'includes/footer.php'; ?>
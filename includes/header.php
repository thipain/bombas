<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AB Bombas - Ferramentas Profissionais</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/carrinho.css">
</head>

<body>

    <div class="top-bar">
        <div class="top-bar-content">
            <div>
                <i class="fas fa-phone"></i> (11) 41602205
                <span style="margin-left: 20px;">
                    <i class="fas fa-envelope"></i> contato@abbombas.com.br
                </span>
            </div>
            <div class="top-bar-links">
                <a href="#">Rastrear Pedido</a>
                <a href="#">Ajuda</a>
                <a href="#">Conta</a>
            </div>
        </div>
    </div>

    <nav class="navbar">
        <div class="navbar-content">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/abbombas.png" alt="Logo AB Bombas">
                    AB Bombas
                </a>
            </div>

            <div class="search-bar">
                <input type="text" placeholder="Buscar ferramentas, bombas, compressores...">
                <button><i class="fas fa-search"></i></button>
            </div>

            <div class="cart-whatsapp">
                <a href="#" class="cart-btn" onclick="abrirCarrinho(); return false;">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-badge" id="cartCount">0</span>
                </a>
                <a href="https://wa.me/551141602205" class="whatsapp-header-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    WhatsApp
                </a>
            </div>
        </div>
    </nav>

    <!-- Script do Carrinho -->
    <script src="assets/js/carrinho.js"></script>
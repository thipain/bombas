<?php
// Arquivo: admin/auth.php
// Incluir este arquivo no topo de todas as páginas administrativas

session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    // Redirecionar para a página de login
    header('Location: ../login.php');
    exit;
}

// Atualizar tempo de atividade (opcional - para logout automático)
$_SESSION['ultima_atividade'] = time();

// Timeout de sessão - 2 horas (opcional)
$timeout = 7200; // 2 horas em segundos
if (isset($_SESSION['ultima_atividade']) && (time() - $_SESSION['ultima_atividade'] > $timeout)) {
    // Sessão expirou
    session_unset();
    session_destroy();
    header('Location: ../login.php?timeout=1');
    exit;
}

// Função para verificar se é uma requisição AJAX
function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

// Se for requisição AJAX e não estiver autenticado, retornar JSON
if (isAjax() && (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Não autenticado', 'redirect' => '../login.php']);
    exit;
}
?>
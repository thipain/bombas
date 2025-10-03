<?php
$host = "localhost";   // servidor do MySQL
$db   = "loja";        // nome do banco (igual ao do seu .sql)
$user = "root";        // usuário padrão do Laragon
$pass = "";            // senha padrão do Laragon (normalmente vazia)
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

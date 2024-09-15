<?php
// conexao.php

$host = 'serve';
$port = '5432';
$dbname = 'banco_vgbel';
$user = 'webadmin';
$pass = '******';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {

    echo 'Erro de conexão: ' . $e->getMessage();
    exit;
}
?>

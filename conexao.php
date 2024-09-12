<?php
// conexao.php

$host = '34.121.238.234';
$port = '5432';
$dbname = 'banco_vgbel';
$user = 'test';
$pass = '812663';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {

    echo 'Erro de conexão: ' . $e->getMessage();
    exit;
}
?>

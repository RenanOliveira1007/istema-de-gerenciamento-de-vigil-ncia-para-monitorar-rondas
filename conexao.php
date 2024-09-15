<?php
// conexao.php

$host = 'node201032-env-0724498.sp1.br.saveincloud.net.br';
$port = '11907';
$dbname = 'banco_vgbel';
$user = 'webadmin';
$pass = 'ipyUvBp2J2';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexão bem-sucedida!";
} catch (PDOException $e) {

    echo 'Erro de conexão: ' . $e->getMessage();
    exit;
}
?>

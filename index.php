<?php
// Inclui a conexão com o banco de dadoos
include 'conexao.php';
session_start();


// Obtém as 10 últimas rondas 
try {
    $sql = "
        SELECT 
            r.*, 
            v.nome AS vigilante_nome
        FROM 
            vgb_rondas r
        JOIN 
            vgb_vigilantes v ON r.vigilante_id = v.id
        ORDER BY 
            r.data_registro DESC, r.horario_inicio DESC
        LIMIT 10
    ";
    $stmt = $pdo->query($sql);
    $ultimas_rondas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Erro ao obter últimas rondas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
    <link rel="stylesheet" href="styles.css">
   
</head>
<body class="pagina-index">

<div class="container">
    <!-- Logo da empresa -->
    <img src="https://i.imgur.com/LG9X0st.png" alt="Logo" class="logo">
    
    <!-- Título  -->
    <h1>Página Principal</h1>
    
    <!-- Botões principais -->
    <div class="button-container">
        <a href="register_ronda.php">Registrar Ronda</a>
        <a href="vigilantes.php">Gerenciar Vigilantes</a>
        <a href="historico.php">Histórico de Rondas</a>
    </div>
    
    <!-- Últimas 10 Rondas-->
    <h2>Últimas 10 Rondas</h2>
    <?php if (isset($error_message)): ?>
        <p style="color: red; text-align: center;"><?php echo htmlspecialchars($error_message); ?></p>
    <?php else: ?>
        <?php if (count($ultimas_rondas) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome do Vigilante</th>
                        <th>Data</th>
                        <th>Hora Início</th>
                        <th>Hora Fim</th>
                        <th>Bairro</th>
                        <th>Observações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_rondas as $ronda): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ronda['vigilante_nome']); ?></td>
                            <td><?php echo htmlspecialchars($ronda['data_registro']); ?></td>
                            <td><?php echo htmlspecialchars($ronda['horario_inicio']); ?></td>
                            <td><?php echo htmlspecialchars($ronda['horario_fim']); ?></td>
                            <td><?php echo htmlspecialchars($ronda['bairro']); ?></td>
                            <td><?php echo htmlspecialchars($ronda['observacoes']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Nenhuma ronda agendada encontrada.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
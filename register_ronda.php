<?php
include 'conexao.php'; 


$bairros_permitidos = ['Ruínas', 'Parque Turístico', 'Vila Peruíbe', 'Centro'];

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
    $vigilante_id = $_POST['vigilante_id'];
    $data = $_POST['data'];
    $hora_inicio = $_POST['hora_inicio'];
    $hora_fim = $_POST['hora_fim'];
    $bairro = $_POST['bairro'];
    $observacoes = $_POST['observacoes'];

    // Valida se todos os campos estão preenchidos e o bairro é válido
    if ($vigilante_id && $data && $hora_inicio && $hora_fim && in_array($bairro, $bairros_permitidos)) {
        try {
            
            $sql = "INSERT INTO vgb_rondas (vigilante_id, data_registro, horario_inicio, horario_fim, bairro, observacoes) 
                    VALUES (:vigilante_id, :data, :hora_inicio, :hora_fim, :bairro, :observacoes)";
            $stmt = $pdo->prepare($sql);

            
            $stmt->execute([
                ':vigilante_id' => $vigilante_id,
                ':data' => $data,
                ':hora_inicio' => $hora_inicio,
                ':hora_fim' => $hora_fim,
                ':bairro' => $bairro,
                ':observacoes' => $observacoes
            ]);

            $message = "Ronda registrada com sucesso."; // Mensagem de sucesso
        } catch (PDOException $e) {
        
            $error_message = "Erro: " . $e->getMessage();
        }
    } else {
        // Mensagem de erro se os campos não forem preenchidos corretamente
        $error_message = "Preencha todos os campos corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Registrar Ronda</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="register_ronda">
    <div class="container">
        <img src="https://i.imgur.com/LG9X0st.png" alt="Logo" class="logo">
        <h1>Registrar Ronda</h1>

        <!-- Mensagens de erro ou sucesso -->
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

    
        <form method="POST">
            <div class="form-group">
                <label for="vigilante_id">Vigilante:</label>
                <select id="vigilante_id" name="vigilante_id" required>
                    <?php
                    
                    $vigilantes = $pdo->query("SELECT id, nome FROM vgb_vigilantes")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($vigilantes as $vigilante) {
                        echo "<option value=\"{$vigilante['id']}\">{$vigilante['nome']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" required>
            </div>
            <div class="form-group">
                <label for="hora_inicio">Início:</label>
                <input type="time" id="hora_inicio" name="hora_inicio" required>
            </div>
            <div class="form-group">
                <label for="hora_fim">Fim:</label>
                <input type="time" id="hora_fim" name="hora_fim" required>
            </div>
            <div class="form-group">
                <label for="bairro">Bairro:</label>
                <select id="bairro" name="bairro" required>
                    <?php
                    
                    foreach ($bairros_permitidos as $bairro_option) {
                        echo "<option value=\"{$bairro_option}\">{$bairro_option}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes" rows="4"></textarea>
            </div>
            <button type="submit" class="button">Registrar</button>
        </form>

   
        <div class="footer">
            <a href="index.php" class="button button-gray">Voltar</a>
        </div>
    </div>
</body>
</html>

<?php

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Exclusão de vigilante
    if (isset($_POST['excluir_vigilante'])) {
        $vigilante_id = $_POST['vigilante_id'];
        $senha_master = $_POST['senha_master'];

        // Verifica se a senha master está correta
        if ($senha_master === '1234') {
            try {
                // Exclui todas as rondas associadas ao vigilante
                $sql_delete_rondas = "DELETE FROM vgb_rondas WHERE vigilante_id = :vigilante_id";
                $stmt_rondas = $pdo->prepare($sql_delete_rondas);
                $stmt_rondas->bindParam(':vigilante_id', $vigilante_id);
                $stmt_rondas->execute();

                // Exclui o vigilante
                $sql_delete_vigilante = "DELETE FROM vgb_vigilantes WHERE id = :vigilante_id";
                $stmt_vigilante = $pdo->prepare($sql_delete_vigilante);
                $stmt_vigilante->bindParam(':vigilante_id', $vigilante_id);
                $stmt_vigilante->execute();

                $message = "Vigilante e suas rondas relacionadas foram excluídos com sucesso.";
            } catch (PDOException $e) {
                $error_message = "Erro ao excluir vigilante: " . $e->getMessage();
            }
        } else {
            // Mensagem de erro se a senha master estiver incorreta
            $error_message = "Senha master incorreta.";
        }
    }

    // Cadastro de vigilante
    if (isset($_POST['cadastrar_vigilante'])) {
        $nome = trim($_POST['nome']);
        $cpf = trim($_POST['cpf']);
        $turno = trim($_POST['turno']);
        
        // Remove a formatação do CPF (pontos e traços)
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (!empty($nome) && !empty($cpf) && !empty($turno)) {
            if (strlen($cpf) == 11) { // Verificando se o CPF tem exatamente 11 dígitos
                try {
                    // Verifica se o CPF já está cadastrado
                    $sql_check = "SELECT COUNT(*) FROM vgb_vigilantes WHERE cpf = :cpf";
                    $stmt_check = $pdo->prepare($sql_check);
                    $stmt_check->bindParam(':cpf', $cpf);
                    $stmt_check->execute();
                    $exists = $stmt_check->fetchColumn();

                    if ($exists == 0) {
                        // Insere novo vigilante
                        $sql = "INSERT INTO vgb_vigilantes (nome, cpf, turno) VALUES (:nome, :cpf, :turno)";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':nome', $nome);
                        $stmt->bindParam(':cpf', $cpf);
                        $stmt->bindParam(':turno', $turno);
                        $stmt->execute();

                        $message = "Vigilante cadastrado com sucesso.";
                    } else {
                        $error_message = "Vigilante com este CPF já está cadastrado.";
                    }
                } catch (PDOException $e) {
                    $error_message = "Erro ao cadastrar vigilante: " . $e->getMessage();
                }
            } else {
                $error_message = "O CPF fornecido deve conter 11 dígitos.";
            }
        } else {
            $error_message = "Todos os campos são obrigatórios.";
        }
    }
}

// Consulta para listar todos os vigilantes
$sql = "SELECT * FROM vgb_vigilantes";
$stmt = $pdo->query($sql);
$vigilantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Vigilantes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="pagina_vigilantes">
    <div class="container">
        <img src="https://i.imgur.com/LG9X0st.png" alt="Logo" class="logo">

        <h1>Gerenciar Vigilantes</h1>

        <!-- Exibe mensagens de sucesso ou erro -->
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- Formulário para cadastrar um novo vigilante -->
        <h2>Cadastrar Novo Vigilante</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" maxlength="14" required>
            </div>
            <div class="form-group">
                <label for="turno">Turno:</label>
                <select id="turno" name="turno" required>
                    <option value="">Selecione</option>
                    <option value="diurno">Diurno</option>
                    <option value="noturno">Noturno</option>
                </select>
            </div>
            <button type="submit" name="cadastrar_vigilante" class="button">Cadastrar Vigilante</button>
        </form>

        <h2>Vigilantes Cadastrados</h2>
        <?php if (!empty($vigilantes)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Turno</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($vigilantes as $vigilante): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($vigilante['nome']); ?></td>
                            <td><?php echo htmlspecialchars($vigilante['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($vigilante['turno']); ?></td>
                            <td>
                                <button class="button excluir-button" onclick="document.getElementById('senha-container-<?php echo htmlspecialchars($vigilante['id']); ?>').style.display='block'">Excluir</button>
                                <div id="senha-container-<?php echo htmlspecialchars($vigilante['id']); ?>" class="hidden">
                                    <form method="POST" action="">
                                        <input type="hidden" name="vigilante_id" value="<?php echo htmlspecialchars($vigilante['id']); ?>">
                                        <input type="password" name="senha_master" placeholder="Senha Master" required>
                                        <button type="submit" name="excluir_vigilante" class="button excluir-button">Confirmar Exclusão</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-data">Nenhum vigilante encontrado.</p>
        <?php endif; ?>

        <div class="footer">
            <a href="index.php">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>

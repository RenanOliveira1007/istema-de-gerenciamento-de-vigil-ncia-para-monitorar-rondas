<?php
include 'conexao.php';

// Processar a exclusão de vigilantes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['excluir_vigilante'])) {
        $vigilante_id = $_POST['vigilante_id'];
        $senha_master = $_POST['senha_master'];

        // Verificar a senha master
        if ($senha_master === '1234') {
            try {
                // Excluir vigilante
                $sql = "DELETE FROM vgb_vigilantes WHERE id = :vigilante_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':vigilante_id', $vigilante_id);
                $stmt->execute();

                $message = "Vigilante excluído com sucesso.";
            } catch (PDOException $e) {
                $error_message = "Erro ao excluir vigilante: " . $e->getMessage();
            }
        } else {
            $error_message = "Senha master incorreta.";
        }
    }
}

// Processar o cadastro de novos vigilantes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cadastrar_vigilante'])) {
        $nome = trim($_POST['nome']);
        $cpf = trim($_POST['cpf']);
        $turno = trim($_POST['turno']);

        // Verificar se os campos estão preenchidos
        if (!empty($nome) && !empty($cpf) && !empty($turno)) {
            try {
                // Verificar se o CPF já está cadastrado
                $sql_check = "SELECT COUNT(*) FROM vgb_vigilantes WHERE cpf = :cpf";
                $stmt_check = $pdo->prepare($sql_check);
                $stmt_check->bindParam(':cpf', $cpf);
                $stmt_check->execute();
                $exists = $stmt_check->fetchColumn();

                if ($exists == 0) {
                    // Inserir novo vigilante
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
            $error_message = "Todos os campos são obrigatórios.";
        }
    }
}

// Listar vigilantes
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

        <!-- Mensagens -->
        <?php if (isset($message)): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <!-- Formulário de Cadastro -->
        <h2>Cadastrar Novo Vigilante</h2>
        <form method="POST" action="">
            <div class="form-group">
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" id="cpf" name="cpf" required>
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

        <!-- Lista de Vigilantes -->
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

        <!-- Botão Voltar ao Início -->
        <div class="footer">
            <a href="index.php">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>

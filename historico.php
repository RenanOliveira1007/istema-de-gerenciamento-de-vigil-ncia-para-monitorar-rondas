<?php
// Inclui o arquivo de conexão
include 'conexao.php';

// Mensagens de sucesso e erro
$message = '';
$error_message = '';

// Checa se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['senha_master'])) {
    $senha_master = $_POST['senha_master']; // Senha master
    $periodo = $_POST['periodo_exclusao']; // Período de exclusão
    $data_especifica = $_POST['data_especifica']; // Data específica, se houver

    $senha_correta = '1234'; // Senha correta

    // Verifica a senha master
    if ($senha_master === $senha_correta) {
        try {
            // Define a query com base no período selecionado
            if ($periodo === 'data_especifica' && !empty($data_especifica)) {
                $data_especifica = date('Y-m-d', strtotime($data_especifica));
                $sql = "DELETE FROM vgb_rondas WHERE DATE(data_registro) = :data_especifica";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':data_especifica', $data_especifica);
            } else {
                // Query para períodos predefinidos
                switch ($periodo) {
                    case '30_minutos':
                        $sql = "DELETE FROM vgb_rondas WHERE data_registro >= NOW() - INTERVAL '30 minutes'";
                        break;
                    case '1_hora':
                        $sql = "DELETE FROM vgb_rondas WHERE data_registro >= NOW() - INTERVAL '1 hour'";
                        break;
                    case '2_horas':
                        $sql = "DELETE FROM vgb_rondas WHERE data_registro >= NOW() - INTERVAL '2 hours'";
                        break;
                    case '12_horas':
                        $sql = "DELETE FROM vgb_rondas WHERE data_registro >= NOW() - INTERVAL '12 hours'";
                        break;
                    case 'todo_dia':
                        $sql = "DELETE FROM vgb_rondas WHERE data_registro >= NOW() - INTERVAL '24 hours'";
                        break;
                    default:
                        $error_message = "Período inválido.";
                        break;
                }
                if (isset($sql)) {
                    $stmt = $pdo->prepare($sql);
                }
            }

            // Executa a query de exclusão
            if (isset($stmt)) {
                $stmt->execute();
                $message = "Histórico excluído para o período de $periodo.";
                if ($periodo === 'data_especifica') {
                    $message = "Histórico excluído para a data: $data_especifica.";
                }
            }
        } catch (PDOException $e) {
            $error_message = "Erro: " . $e->getMessage();
        }
    } else {
        $error_message = "Senha incorreta."; // Senha incorreta
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Histórico de Rondas</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Mostra campos de exclusão
        function mostrarCamposExclusao() {
            document.getElementById('campo_senha').classList.remove('hidden');
            document.getElementById('campo_periodo').classList.remove('hidden');
            document.getElementById('campo_confirmar').classList.remove('hidden');
        }

        // Mostra campo de data se a opção for "Data Específica"
        function mostrarCampoDataEspecifica() {
            var periodo = document.getElementById('periodo_exclusao').value;
            var campoData = document.getElementById('campo_data_especifica');
            if (periodo === 'data_especifica') {
                campoData.classList.remove('hidden');
            } else {
                campoData.classList.add('hidden');
            }
        }
    </script>
</head>
<body class="pagina-historico">
    <div class="container">
        <!-- Logo -->
        <img src="https://i.imgur.com/LG9X0st.png" alt="Logo" class="logo">

        <!-- Título -->
        <h1>Histórico de Rondas</h1>

        <!-- Mensagem de sucesso ou erro -->
        <?php if ($message): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php elseif ($error_message): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <!-- Tabela de rondas -->
        <table>
            <thead>
                <tr>
                    <th>Vigilante</th>
                    <th>Bairro</th>
                    <th>Data</th>
                    <th>Horário Início</th>
                    <th>Horário Fim</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Consulta o histórico de rondas
                $sql = "SELECT v.nome AS vigilante, r.bairro, r.data_registro, r.horario_inicio, r.horario_fim, r.observacoes 
                        FROM vgb_rondas r 
                        JOIN vgb_vigilantes v ON r.vigilante_id = v.id
                        ORDER BY r.data_registro DESC";
                $stmt = $pdo->query($sql);
                $rondas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rondas as $ronda) {
                    echo "<tr>
                            <td>{$ronda['vigilante']}</td>
                            <td>{$ronda['bairro']}</td>
                            <td>{$ronda['data_registro']}</td>
                            <td>{$ronda['horario_inicio']}</td>
                            <td>{$ronda['horario_fim']}</td>
                            <td>{$ronda['observacoes']}</td>
                          </tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Botão de exclusão -->
        <button class="button" onclick="mostrarCamposExclusao()">Excluir Histórico</button>

        <!-- Formulário de exclusão -->
        <form method="post" action="historico.php">
            <div id="campo_senha" class="hidden form-group">
                <label for="senha_master">Senha Master:</label>
                <input type="password" id="senha_master" name="senha_master" required>
            </div>

            <div id="campo_periodo" class="hidden form-group">
                <label for="periodo_exclusao">Período para exclusão:</label>
                <select id="periodo_exclusao" name="periodo_exclusao" onchange="mostrarCampoDataEspecifica()">
                    <option value="30_minutos">Últimos 30 minutos</option>
                    <option value="1_hora">Última 1 hora</option>
                    <option value="2_horas">Últimas 2 horas</option>
                    <option value="12_horas">Últimas 12 horas</option>
                    <option value="todo_dia">Todo o dia</option>
                    <option value="data_especifica">Data específica</option>
                </select>
            </div>

            <!-- Campo para data específica -->
            <div id="campo_data_especifica" class="hidden form-group">
                <label for="data_especifica">Selecione a data:</label>
                <input type="date" id="data_especifica" name="data_especifica">
            </div>

            <!-- Botão de confirmação -->
            <div id="campo_confirmar" class="hidden form-group">
                <button type="submit" class="button">Confirmar Exclusão</button>
            </div>
        </form>

        <!-- Botão para voltar ao início -->
        <div class="footer">
            <a href="index.php" class="button button-gray">Voltar ao Início</a>
        </div>
    </div>
</body>
</html>

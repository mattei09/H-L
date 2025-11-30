<?php
session_start();

// Check authenticated professional
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
$id_prof = $_SESSION['id'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Process POST: update or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'update';
    $id = intval($_POST['id']);

    // Verify ownership
    $stmt = $conn->prepare("SELECT id, id_prof FROM agenda WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    if (!$row || $row['id_prof'] != $id_prof) {
        die('Agendamento não encontrado ou acesso negado.');
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM agenda WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            header('Location: agenda-prof.php');
            exit;
        } else {
            echo 'Erro ao excluir: ' . $conn->error;
        }
    } else {
        // update
        $cliente = $_POST['cliente'];
        $telefone = $_POST['telefone'];
        $data = $_POST['data'];
        $horario = $_POST['horario'];
        $id_serv = intval($_POST['id_serv']);
        $tempo = $_POST['tempo'];
        // normalize tempo to minutes
        if (is_numeric($tempo)) {
            $tempo = intval($tempo);
        } elseif (strpos($tempo, ':') !== false) {
            $parts = explode(':', $tempo);
            $tempo = intval($parts[0]) * 60 + intval($parts[1] ?? 0);
        } else {
            $tempo = intval($tempo);
        }

        // Check for conflicts: same professional, same date/time but not same id
        $conf = $conn->prepare("SELECT id FROM agenda WHERE id_prof = ? AND data = ? AND horario = ? AND id != ? LIMIT 1");
        $conf->bind_param('issi', $id_prof, $data, $horario, $id);
        $conf->execute();
        $resconf = $conf->get_result();
        if ($resconf->num_rows > 0) {
            echo '<p style="color: red;">Horário já está ocupado por outro agendamento.</p>';
        } else {
            $stmtu = $conn->prepare("UPDATE agenda SET id_serv = ?, cliente = ?, telefone = ?, data = ?, horario = ?, tempo = ? WHERE id = ?");
            $stmtu->bind_param('issssii', $id_serv, $cliente, $telefone, $data, $horario, $tempo, $id);
            if ($stmtu->execute()) {
                header('Location: agenda-prof.php');
                exit;
            } else {
                echo 'Erro ao atualizar: ' . $conn->error;
            }
        }
    }
}

// GET (show form)
$id = intval($_GET['id'] ?? 0);
if (!$id) die('ID do agendamento não informado.');

$stmt = $conn->prepare("SELECT * FROM agenda WHERE id = ? AND id_prof = ?");
$stmt->bind_param('ii', $id, $id_prof);
$stmt->execute();
$res = $stmt->get_result();
$agenda = $res->fetch_assoc();
$stmt->close();

if (!$agenda) die('Agendamento não encontrado ou acesso negado.');

// Get all services for select
$services = [];
$sqls = "SELECT id, `serviços` FROM `serviços` ORDER BY `serviços`";
$rs = $conn->query($sqls);
if ($rs) {
    while ($rw = $rs->fetch_assoc()) {
        $services[] = $rw;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Editar Agendamento</title>
<link rel="stylesheet" href="style.css">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />
</head>
<body>
<h2>Editar Agendamento</h2>
<p>Profissional ID: <?php echo htmlspecialchars($id_prof); ?></p>
<form method="post" action="editar_agenda.php">
    <input type="hidden" name="id" value="<?php echo $agenda['id']; ?>">
    <label>Cliente:</label>
    <input type="text" name="cliente" value="<?php echo htmlspecialchars($agenda['cliente']); ?>" required>

    <label>Telefone:</label>
    <input type="text" name="telefone" value="<?php echo htmlspecialchars($agenda['telefone']); ?>" required>

    <label>Serviço:</label>
    <select name="id_serv">
        <?php foreach ($services as $s): ?>
            <option value="<?php echo $s['id']; ?>" <?php echo ($s['id'] == $agenda['id_serv']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($s['serviços']); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Data:</label>
    <input type="date" name="data" value="<?php echo htmlspecialchars($agenda['data']); ?>" required>

    <label>Horário:</label>
    <input type="time" name="horario" value="<?php echo htmlspecialchars(substr($agenda['horario'],0,5)); ?>" required>

    <label>Tempo (minutos):</label>
    <input type="number" name="tempo" min="1" value="<?php echo htmlspecialchars($agenda['tempo']); ?>">
    <?php
    // mostra hora de término calculada
    $tempo_calculo = $agenda['tempo'];
    $minutes_calc = 30;
    if (is_numeric($tempo_calculo)) {
        $minutes_calc = intval($tempo_calculo);
    } elseif (strpos($tempo_calculo, ':') !== false) {
        $parts = explode(':', $tempo_calculo);
        $hours = intval($parts[0]);
        $mins = intval($parts[1] ?? 0);
        $minutes_calc = $hours * 60 + $mins;
    }
    $end_time = date('H:i', strtotime($agenda['data'] . ' ' . substr($agenda['horario'],0,5)) + ($minutes_calc * 60));
    echo '<p>Fim estimado: <strong>' . $end_time . '</strong></p>';
    ?>

    <button type="submit" name="action" value="update" title="Salvar" aria-label="Salvar"><i class="fa-solid fa-save" aria-hidden="true"></i></button>
    <button type="submit" name="action" value="delete" onclick="return confirm('Tem certeza que deseja excluir esse agendamento?');" title="Excluir" aria-label="Excluir"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
</form>

<p><a href="agenda-prof.php" title="Voltar à Agenda" aria-label="Voltar à Agenda"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a></p>

</body>
</html>

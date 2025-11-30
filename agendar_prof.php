<?php
session_start();
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
if ($conn->connect_error) die('Connection failed: ' . $conn->connect_error);

// Handle POST (create appointment)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente = $_POST['cliente'];
    $telefone = $_POST['telefone'];
    $id_serv = intval($_POST['id_serv']);
    $data = $_POST['data'];
    $horario = $_POST['horario'];
    $tempo = intval($_POST['tempo']);

    // Check conflict
    $stmt = $conn->prepare("SELECT id FROM agenda WHERE id_prof = ? AND data = ? AND horario = ? LIMIT 1");
    $stmt->bind_param('iss', $id_prof, $data, $horario);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows > 0) {
        $msg = 'Horário ocupado';
    } else {
        $stmt->close();
        $sql = "INSERT INTO agenda (id_prof, id_serv, cliente, telefone, data, horario, tempo) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iissssi', $id_prof, $id_serv, $cliente, $telefone, $data, $horario, $tempo);
        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: agenda-prof.php');
            exit;
        } else {
            $msg = 'Erro ao cadastrar: ' . $conn->error;
        }
    }
}

// GET - show form
$data = $_GET['data'] ?? date('Y-m-d');
$horario = $_GET['horario'] ?? '';
$default_tempo = $_GET['tempo'] ?? 30;

// load services
$services = [];
$rs = $conn->query("SELECT id, `serviços`, tempo FROM `serviços` ORDER BY `serviços`");
if ($rs) while ($rw = $rs->fetch_assoc()) $services[] = $rw;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<title>Agendar Cliente</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Agendar Cliente</h2>
<?php if (!empty($msg)): ?>
    <p style="color:red"><?php echo htmlspecialchars($msg); ?></p>
<?php endif; ?>
<form method="post" action="agendar_prof.php">
    <label>Cliente:</label>
    <input type="text" name="cliente" placeholder="Nome completo do cliente" required>

    <label>Telefone:</label>
    <input type="text" name="telefone" placeholder="Ex: (12) 99999-9999">

    <label>Serviço:</label>
    <select name="id_serv">
        <option value="">Selecione um serviço</option>
        <?php foreach ($services as $s): ?>
            <option value="<?php echo $s['id']; ?>" data-tempo="<?php echo $s['tempo']; ?>"><?php echo htmlspecialchars($s['serviços']); ?></option>
        <?php endforeach; ?>
    </select>

    <label>Data:</label>
    <input type="date" name="data" value="<?php echo htmlspecialchars($data); ?>" required min="<?php echo date('Y-m-d'); ?>">

    <label>Horário:</label>
    <input type="time" name="horario" value="<?php echo htmlspecialchars($horario); ?>" required step="1800">

    <label>Tempo (minutos):</label>
    <input type="number" name="tempo" value="<?php echo htmlspecialchars($default_tempo); ?>" min="1" placeholder="Duração em minutos">

    <input type="submit" value="Agendar">
</form>
<p><a href="agenda-prof.php">Voltar</a></p>
<script>
    // auto apply tempo param from selected service if decorated with data-tempo
    document.addEventListener('DOMContentLoaded', function() {
        var sel = document.querySelector('select[name="id_serv"]');
        var tempo = document.querySelector('input[name="tempo"]');
        if (!sel || !tempo) return;
        sel.addEventListener('change', function() {
            var opt = sel.options[sel.selectedIndex];
            var t = opt ? opt.getAttribute('data-tempo') : null;
            if (t && t.length > 0) tempo.value = t;
        });
    });
</script>
</body>
</html>

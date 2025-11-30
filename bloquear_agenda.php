<?php
session_start();
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit;
}
$id_prof = $_SESSION['id'];

// Accept GET or POST
$data = $_REQUEST['data'] ?? null;
$horario = $_REQUEST['horario'] ?? null;
$tempo = $_REQUEST['tempo'] ?? 30;
$id_serv = $_REQUEST['id_serv'] ?? 0; // default 0

if (!$data || !$horario) {
    die('Data ou horário não informado.');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Check conflicts
$stmt = $conn->prepare("SELECT id FROM agenda WHERE id_prof = ? AND data = ? AND horario = ? LIMIT 1");
$stmt->bind_param('iss', $id_prof, $data, $horario);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows > 0) {
    // Already has an appointment - don't block
    $stmt->close();
    $conn->close();
    header("Location: agenda-prof.php?msg=occupied");
    exit;
}
$stmt->close();

// Insert blocked entry
$cliente = 'Bloqueado';
$telefone = '';
$sql = "INSERT INTO agenda (id_prof, id_serv, cliente, telefone, data, horario, tempo) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iissssi', $id_prof, $id_serv, $cliente, $telefone, $data, $horario, $tempo);
if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header('Location: agenda-prof.php');
    exit;
} else {
    echo "Erro ao bloquear: " . $conn->error;
    $stmt->close();
    $conn->close();
}
?>
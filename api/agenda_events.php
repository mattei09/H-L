<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Use session-provided professional ID if available, otherwise accept id_prof param
$id_prof = $_SESSION['id'] ?? null;
if (!$id_prof && isset($_GET['id_prof'])) {
    $id_prof = intval($_GET['id_prof']);
}

if (!$id_prof) {
    echo json_encode(['error' => 'Profissional não autenticado ou não informado']);
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection error']);
    exit;
}

$sql = "SELECT a.id, a.id_serv, s.serviços AS servico, a.cliente, a.telefone, a.data, a.horario, a.tempo FROM agenda a LEFT JOIN `serviços` s ON a.id_serv = s.id WHERE a.id_prof = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id_prof);
$stmt->execute();
$result = $stmt->get_result();

$events = [];
while ($row = $result->fetch_assoc()) {
    // build start datetime (ISO string)
    $horario = $row['horario'];
    $horario = (strlen($horario) == 5) ? $horario . ":00" : $horario; // add seconds if missing
    $start = $row['data'] . 'T' . $horario;

    // compute end time based on tempo
    $tempo = $row['tempo'];
    $minutes = 30; // default duration
    if (is_numeric($tempo)) {
        $minutes = intval($tempo);
    } elseif (strpos($tempo, ':') !== false) {
        // parse HH:MM or HH:MM:SS into minutes
        $parts = explode(':', $tempo);
        $hours = intval($parts[0]);
        $mins = intval($parts[1] ?? 0);
        $minutes = $hours * 60 + $mins;
    }
    $end = date('Y-m-d\TH:i:s', strtotime($row['data'] . ' ' . $horario) + ($minutes * 60));
    $title = ($row['cliente'] ? $row['cliente'] . ' - ' : '') . ($row['servico'] ? $row['servico'] : 'Agendado');
    $events[] = [
        'id' => $row['id'],
        'title' => $title,
        'start' => $start,
        'end' => $end,
        'extendedProps' => [
            'telefone' => $row['telefone'],
            'tempo' => $row['tempo'],
            'id_serv' => $row['id_serv'],
            'servico' => $row['servico']
        ]
    ];
}

$stmt->close();
$conn->close();

echo json_encode($events);

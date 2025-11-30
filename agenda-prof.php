<?php
session_start();


// Exemplo: id do profissional logado
if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit;
}
$id_prof = $_SESSION["id"];

$conn = new mysqli("localhost", "root", "", "profissionais");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style.css">
    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
    <style>
        #calendar { max-width: 1100px; margin: 40px auto; }
    </style>
    <title>Agenda Profissional</title>
</head>
<body>
    <div id="calendar"></div>
    

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: {
                    url: 'api/agenda_events.php',
                    method: 'GET',
                    extraParams: {
                        id_prof: '<?php echo $id_prof; ?>'
                    },
                    failure: function() {
                        alert('Falha ao carregar eventos.');
                    }
                },
                eventClick: function(info) {
                    // Open edit page
                    var id = info.event.id;
                    window.location.href = 'editar_agenda.php?id=' + id;
                }
            });

            calendar.render();
                });
            </script>

            <!-- FILTERS & TABLE BELOW -->
            <div id="agenda-table">

<?php
// 🔹 Filtros de visualização
$view = $_GET['view'] ?? '15dias';
$data_inicio = $_GET['data'] ?? date('Y-m-d');

switch ($view) {
    case 'dia':
        $dias_exibir = 1;
        break;
    case 'semana':
        $dias_exibir = 7;
        break;
    default:
        $dias_exibir = 15;
}

// 🔹 Intervalos de tempo
$hora_inicio = strtotime("08:00");
$hora_fim = strtotime("18:00");
$intervalos = [];

for ($t = $hora_inicio; $t <= $hora_fim; $t += 30 * 60) {
    $intervalos[] = date("H:i", $t);
}

// 🔹 Interface de filtro
echo "<h2>Agenda do Profissional</h2>";
echo "<form method='GET' style='margin-bottom:15px;'>
        <label>Data:</label>
        <input type='date' name='data' value='$data_inicio'>
        <select name='view'>
            <option value='dia' " . ($view=='dia'?'selected':'') . ">Dia</option>
            <option value='semana' " . ($view=='semana'?'selected':'') . ">Semana</option>
            <option value='15dias' " . ($view=='15dias'?'selected':'') . ">15 Dias</option>
        </select>
        <button type='submit'>Filtrar</button>
      </form>";

echo "<table border='1' cellpadding='8' cellspacing='0'>";
echo "<tr><th>Data</th>";
foreach ($intervalos as $h) echo "<th>$h</th>";
echo "</tr>";

for ($d = 0; $d < $dias_exibir; $d++) {
    $data = date('Y-m-d', strtotime("+$d day", strtotime($data_inicio)));
    echo "<tr>";
    echo "<td>" . date('d/m/Y (D)', strtotime($data)) . "</td>";

    // 🔹 Busca horários ocupados
    $stmt = $conn->prepare("SELECT * FROM agenda WHERE id_prof = ? AND data = ? ");
    $stmt->bind_param("is", $id_prof, $data);
    $stmt->execute();
    $result = $stmt->get_result();

    $ocupados = [];
    while ($row = $result->fetch_assoc()) {
        // Normaliza horário para HH:MM (existem registros que podem ter HH:MM:SS)
        $horario_key = substr($row['horario'], 0, 5);
        $ocupados[$horario_key] = [
            'id' => $row['id'],
            'cliente' => $row['cliente'],
            'telefone' => $row['telefone'],
            'id_serv' => $row['id_serv'],
            'tempo' => $row['tempo'],
            'horario' => substr($row['horario'],0,5)
        ];
    }

    foreach ($intervalos as $h) {
        if ($h >= "12:00" && $h < "13:00") {
            echo "<td style='background:#ffeeba;'>Intervalo</td>";
        } elseif (array_key_exists($h, $ocupados)) {
            $data_id = $ocupados[$h]['id'];
            $cliente = htmlspecialchars($ocupados[$h]['cliente']);
            $horario_cliente = htmlspecialchars($ocupados[$h]['horario']);
            echo "<td style='background:#f8d7da;'>";
            echo "<a href='editar_agenda.php?id=$data_id' style='color:red;'>$cliente - $horario_cliente</a>";
            echo "</td>";
        } else {
            // free: show actions: Agendar / Bloquear
            $link_agendar = "agendar_prof.php?id_prof=$id_prof&data=" . urlencode($data) . "&horario=" . urlencode($h);
                        $link_bloquear = "bloquear_agenda.php?data=" . urlencode($data) . "&horario=" . urlencode($h);
                        echo "<td style='background:#d4edda;'>
                                        <a href='$link_agendar' style='margin-right:6px;'>Agendar</a>
                                        <a href='$link_bloquear' style='color:darkred;' onclick='return confirm(" . '"' . "Bloquear esse horário?" . '"' . ");'>Bloquear</a>
                                    </td>";
        }
    }
    echo "</tr>";
}

echo "</table>";
// debug removed: production mode only
echo '</div>';

$conn->close();
?>

</body>
</html>

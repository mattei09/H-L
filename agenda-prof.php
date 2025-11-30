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
<body>
  <link rel="stylesheet" href="style.css">
</body>  
</html>

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
    $stmt->bind_param("id", $id_prof, $d);
    $stmt->execute();
    $result = $stmt->get_result();

    $ocupados = [];
    while ($row = $result->fetch_assoc()) {
        $ocupados[$row['horario']] = $row['id'];
        
    }

    foreach ($intervalos as $h) {
        if ($h >= "12:00" && $h < "13:00") {
            echo "<td style='background:#ffeeba;'>Intervalo</td>";
        } elseif (array_key_exists($h, $ocupados)) {
            $id_agenda = $ocupados[$h];
            echo $ocupados[$h];
            echo "<td style='background:#f8d7da;'>
                    <a href='reagendar.php?id=$id_agenda' style='color:red;'>Agendado $ocupados[$h]</a>
                  </td>";
        } else {
            echo "<td style='background:#d4edda;'>Livre</td>";
        }
    }
    echo "</tr>";
}

echo "</table>";

$conn->close();
?>

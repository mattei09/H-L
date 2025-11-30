 <!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Serviços - Salão</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header>
    <h2>Agenda do Profissional</h2>
<a href="agenda.php?id=<?= $id_profissional ?>&id_serv=<?= $id_serv ?>&tempo=<?= $tempo_servico ?>">

</a>
    <nav>
      <a href="profissionais.php">Profissionais</a>
        <a href="index.html">Inicio</a>
    </nav>
  </header>
  
  <main>
    <section class="servicos">
<?php
// --- Conexão com o banco de dados ---
$conn = new mysqli("localhost", "root", "", "profissionais");
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// --- Configuração ---
$id_profissional = $_GET["id"];
$id_serv = $_GET["id_serv"];
 // ID do profissional que queremos exibir
$data_inicio = date('Y-m-d'); // hoje
$dias_exibir = 15; // quantos dias mostrar (ex: 7 = uma semana)
$tempo = $_GET["tempo"];
?>

<?php
// --- Gera os horários de meia em meia hora ---
$hora_inicio = strtotime("08:00");
$hora_fim = strtotime("18:00");
$intervalos = [];
for ($t = $hora_inicio; $t <= $hora_fim; $t += 30 * 60) {
     $hora = date("H:i", $t);
    $intervalos[] = $hora;
}
   




// --- Monta a tabela ---
echo "<table class='card-agenda schedule-table' border='1'>";
echo "<tr><th>Data</th>";
foreach ($intervalos as $h) echo "<th>$h</th>";
echo "</tr>";

// --- Loop pelos dias ---
for ($d = 0; $d < $dias_exibir; $d++) {
    $data = date('Y-m-d', strtotime("+$d day", strtotime($data_inicio)));

    echo "<tr>";
    echo "<td>" . date('d/m/Y', strtotime($data)) . "</td>";

    // Busca os agendamentos desse dia e profissional
    $sql = "SELECT horario FROM agenda
            WHERE id_prof = $id_profissional 
            AND data = '$data'";
    $result = $conn->query($sql);

    // Cria array de horários ocupados
    $ocupados = [];
    while ($row = $result->fetch_assoc()) {
        $ocupados[] = substr($row['horario'], 0, 5); // pega HH:MM
    }

    $mostrarBotao = true;
    
    // Gera colunas de horários
    foreach ($intervalos as $h) {
       // Defina aqui o intervalo de almoço (ex: 12h às 13h)
    if ($h >= "12:00" && $h < "13:00") {
        echo "<td style='color:black;'>Intervalo</td>";
    } 

     elseif (in_array($h, $ocupados)) {
        echo "<td style='color:red;'>Agendado</td>";
    } else {
    
         echo "<td style=';'><a href='conf_agenda.php?id_prof=$id_profissional&data=$data&id_serv=$id_serv&horario=$h&tempo=$tempo'>Livre</a></td>";
    } } 
    }

    

    echo "</tr>";


echo "</table>";

$conn->close();
?>


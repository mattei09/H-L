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
    <h1>Procedimentos</h1>
    <nav>
      <a href="profissionais.php">Profissionais</a>
    </nav>
  </header>
  
  <main>
    <section class="servicos">

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";
$id = $_GET["id"];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT s.id, s.serviços, s.descrição, s.valor, s.tempo
FROM serviços s
INNER JOIN especialidade e ON e.id_serv = s.id

INNER JOIN login p ON p.id = e.id_prof
WHERE p.id =".$id;


$result = $conn->query($sql);

if ($result->num_rows > 0) {

  
  // output data of each row
  while($row = $result->fetch_assoc()) {
   
 ?>

    <div class="card-servico">
        <h3><?php echo $row["serviços"]; ?> </h3>
        <h3><?php echo $row["valor"]; ?></h3>
        <h3><?php echo $row["tempo"]; ?> </h3>
<?php
echo "<a class='btn-agendar' href='agenda.php?id=".$id."&id_serv=".$row["id"]."&tempo=".$row["tempo"]."'>Procedimentos</a><br>";
  ?>


      </div>
      <?php
  }
} else {
  echo "0 results";
}
$conn->close();
?>
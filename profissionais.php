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
    <h1>Nossos Profissionais</h1>
    <nav>
      <a href="index.html">Início</a>
    </nav>
  </header>
  
  <main>
    <section class="servicos">

      <?php
 $servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM login";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    if ($row ["tipo"] == 0 )  {
    ?>
   <div class="card-servico">
        <h3><?php echo $row["especialidade"]; ?> </h3>
        <img src="<?php echo $row["imagem"]; ?>" width="90" height="50" alt="">
        <h3><?php echo $row["nome"]; ?> </h3>

        <a href="procedimento.php?id=<?php echo $row["id"]; ?>" class="btn-agendar">Procedimentos</a>
      </div>
<?php
} }
} else {
  echo "0 results";
}
$conn->close();
?>
 
    </section>
  </main>

  <footer>
    <p>&copy; © 2025 - H&L Agende Já. Todos os direitos reservados </p>
  </footer>
</body>
</html>

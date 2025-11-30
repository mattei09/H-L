

<?php
// ===== CONFIGURAÇÃO DO BANCO =====
$host = "localhost";
$user = "root";         // seu usuário do MySQL
$pass = "";             // sua senha do MySQL
$dbname = "profissionais";  // nome do banco

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// ===== INSERIR CONFIRMAÇÃO =====
if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // $id_agenda = $_POST["id_agenda"];
    $cliente = $_POST["cliente"];
    $telefone = $_POST["telefone"];
     $id_prof=$_POST["id_prof"];
    $data= $_POST["data"];
    $id_serv=$_POST["id_serv"];
    $horario=$_POST["horario"];
    $tempo=$_POST["tempo"];


    $sql = "INSERT INTO agenda (id_prof,id_serv,cliente, telefone,data,horario,tempo) VALUES (?, ?, ?,?, ?, ?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssss",$id_prof,$id_serv, $cliente,$telefone,$data, $horario, $tempo);

    if ($stmt->execute()) {
        echo "<p style='color: green;'>✅ Confirmação salva com sucesso!</p>"; 
        header ("location: https://wa.me/12988297635?text=IAgendamento%20");  
    } else {
        echo "<p style='color: red;'>❌ Erro ao salvar: " . $conn->error . "</p>";
    }

    $stmt->close();
} else {
    $id_prof=$_GET["id_prof"];
    $data= $_GET["data"];
    $id_serv=$_GET["id_serv"];   
    $horario=$_GET["horario"];
    $tempo=$_GET["tempo"];


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

$sql = "SELECT * FROM login where id=". $id_prof;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $nome=$row["nome"];
  }}

  $sql = "SELECT * FROM serviços where id=". $id_serv;
$result = $conn->query($sql);
if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    $serviços=$row["serviços"];
  }}


    ?>
   


 
   
  
  
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmação de Agendamento</title>
    <link rel="stylesheet" href="style.css"/> 
    
    <main>
    
</head>
<body>     
 
  <header>
    <h2>Confirmação de Agendamento</h2>
     <nav> 
        <a href="profissionais.php">Nossos Prosissionais </a>
        <a href="index.html">Início</a>
    </nav>
</header>
<section class="resumo">
    <h3>Confirmação do Agendamento</h3>
               <p> Profissional <?php echo $nome; ?></p>
               <p> Data <?php echo $data; ?></p>
               <p> Serviço <?php echo $serviços; ?></p>
               <p> Horário Marcado <?php echo $horario; ?></p>
               <p> Tempo de procedimento <?php echo $tempo; ?></p>
</section>
               <section class="servico">
               <h3>Agendamento</h3>  
            <form ACTION="conf_agenda.php" Method='post'>
  <label for="cliente">Nome:</label>
        <input type="text" name="cliente" id="cliente" required>

        <label for="telefone">Telefone:</label>
        <input type="text" name="telefone" id="telefone" required>

        <input type='hidden' name = "id_prof" value=" <?php echo $id_prof; ?>">
        <input type='hidden' name = "data" value=" <?php echo $data; ?>">
        <input type='hidden' name = "id_serv" value=" <?php echo $id_serv; ?>">  
        <input type='hidden' name = "horario" value=" <?php echo $horario; ?>">
        <input type='hidden' name = "tempo" value=" <?php echo $tempo; ?>">

   <input type="submit" name="submit" value="enviar">  
            </form>
</section>
  <footer>
    <p>&copy; © 2025 - H&L Agende Já. Todos os direitos reservados </p>
  </footer>
</body>
</html>
    <?php
 }   

// ===== BUSCAR IDs DA TABELA AGENDA =====
//$result = $conn->query("SELECT id, id_serv, id_agenda FROM agenda ORDER BY id ASC");

$conn->close();
?>

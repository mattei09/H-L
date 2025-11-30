<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";


$serviços = $_POST["servico"];
$descrição = $_POST["descricao"];
$valor = $_POST["valor"];
$tempo = (int)$_POST["tempo"];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO serviços (serviços, descrição, valor, tempo)
VALUES ('$serviços', '$descrição','$valor', '$tempo')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
  header("location:cad-serv.html");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>

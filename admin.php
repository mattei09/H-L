<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";


$nome = $_POST["name"];
$email = $_POST["email"];
$senha = $_POST["password"];
$genero = $_POST["gender"];

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO login (nome, email, senha, tipo, genero)
VALUES ('$nome', '$email', '$senha', 0, '$genero')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
  header("location:admin.html");
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
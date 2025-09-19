<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";


$serviços = $_POST["serviços"];
$prof = $_SESSION["usuario"];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO especialidade (id_serv, id_prof)
VALUES ('$serviços', '$prof')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
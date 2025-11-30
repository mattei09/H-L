
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "profissionais";


$data = $_GET["data"];
$horario = $_GET["horario"];
$id_prof = $_GET["id_prof"];
$id_serv = $_GET["id_serv"];
$tempo = $_GET["tempo"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "INSERT INTO agenda (id_prof, id_serv, data, horario, tempo)
        VALUES ('$id_prof', '$id_serv', '$data', '$horario', '$tempo')";

if ($conn->query($sql) === TRUE) {
  echo "New record created successfully";
  
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
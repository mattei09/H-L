
<?php
session_start();
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

// Conexão com banco de dados
$conn = new mysqli("localhost", "root", "", "profissionais");

if ($conn->connect_error) {
  die("Falha na conexão: " . $conn->connect_error);
}

// Proteção contra SQL Injection

$stmt = $conn->prepare("SELECT * FROM login WHERE email = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
  $row = $result->fetch_assoc();
  if (password_verify($senha, $row['senha'])) {
    if($row["tipo"] === 1){
        echo "Bem-vindo, administrador!";
        header ("Location: admin.html");
    } else {
        $_SESSION["usuario"] = $row ["email"];
        $_SESSION["id"] = $row ["id"];
        header ("Location: area-prof.html");
    }
    echo "Login bem-sucedido!";
  } else {
    echo "Usuário ou senha incorretos.";
  }
} else {
  echo "Usuário ou senha incorretos.";
}

$stmt->close();
$conn->close();
?>
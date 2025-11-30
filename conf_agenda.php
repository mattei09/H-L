

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

    ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Confirmação de Agendamento</title>
    <link rel="stylesheet" href="style.css"/> 
    <style>
        body { font-family: Arial; background-color: #f4f4f4; padding: 30px; }
        form { background: white; padding: 20px; border-radius: 8px; width: 380px; margin: auto; box-shadow: 0 0 10px #ccc; }
        h2 { text-align: center; }
        label { font-weight: bold; }
        input, select, button { width: 100%; padding: 8px; margin: 6px 0; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        
    </style>
    <main>
    <section class="servicos">
</head>
<body>     
       <div>
            <h2>Confirmação do Agendamento</h2>
               <p> Profissional <?php echo $id_prof; ?></p>
               <p> Data<?php echo $data; ?></p>
               <p> Serviço <?php echo $id_serv; ?></p>
               <p> Horário Marcado <?php echo $horario; ?></p>
               <p> Tempo de procedimento <?php echo $tempo; ?></p>

            <form ACTION="conf_agenda.php" Method='post'>
  <label for="cliente">Nome do Cliente:</label>
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

</body>
</html>
    <?php
 }   

// ===== BUSCAR IDs DA TABELA AGENDA =====
//$result = $conn->query("SELECT id, id_serv, id_agenda FROM agenda ORDER BY id ASC");

$conn->close();
?>

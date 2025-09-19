<!DOCTYPE html>
<html>
<body>

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

 $sql = "SELECT *  FROM serviços";
$result = $conn->query($sql);
 ?>
  <form action="esp_cad.php"method="post">

  <label for="serviços">ESCOLHA O SERVIÇO</label>
        
<select id="serviços" name="serviços">
<?php
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<option value='".$row["id"]."'>".$row["serviços"]." </option>";
    }     
?>
<input type="submit" value="Submit">
    </form>
        <?php
    
} else {
    echo "0 results";
}
 echo "</select>";
$conn->close();
?>
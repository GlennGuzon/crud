<?php
if(isset($_GET["id"])) {
    $id = $_GET["id"];

    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "myshop";

    // Create connection
    $connection = new mysqli($servername, $username, $password, $database);
    
    // FIXED TYPO HERE: Changed $connectoion to $connection
    $sql = "DELETE FROM clients WHERE id = $id";
    $connection->query($sql);
    
    $connection->close();
}

header("location: /myshop/index.php");
exit;
?>
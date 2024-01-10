<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";

try {
  $conn = new PDO("mysql:host=$servername;dbname=database_system", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully";
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
}

if (isset($_POST['delete'])) {
    $idToDelete = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: alluser.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
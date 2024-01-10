<?php
session_start();
require_once 'config/db.php';

if (isset($_POST['deletetype'])) {
    $idToDelete = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM prefix WHERE id = :id");
        $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: editprefix.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
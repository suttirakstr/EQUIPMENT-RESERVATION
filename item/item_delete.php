<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['delete'])) {
    $idToDelete = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM items WHERE id = :id");
        $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: allitemadmin.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
if (isset($_POST['deletetype'])) {
    $idToDelete = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM typei WHERE id = :id");
        $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: edittype.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>
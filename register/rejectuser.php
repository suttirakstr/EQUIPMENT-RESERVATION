// reject.php
<?php
session_start();
require_once '../config/db.php';

if (isset($_POST['delete'])) {
    $idToDelete = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $idToDelete, PDO::PARAM_INT);
        $stmt->execute();

        // Redirect or display a success message
        header("Location: confirmregister.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>

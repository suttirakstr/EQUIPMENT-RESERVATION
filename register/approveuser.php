// approve.php
<?php
session_start();
require_once '../config/db.php';
// ตรวจสอบการส่งพารามิเตอร์ id ผ่าน URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

 

    // อัปเดตสถานะการสมัครสมาชิกเป็น "approved"
    $sql = "UPDATE users SET urole = 'user' WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    echo "การสมัครสมาชิกถูกอนุมัติแล้ว";
    header("location: confirmregister.php");

} else {
    echo "ไม่พบพารามิเตอร์ id";
}
?>

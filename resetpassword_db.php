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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // รับข้อมูลจากแบบฟอร์ม
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $user_id = $_SESSION['user_id']; // ใช้ Session เพื่อระบุผู้ใช้ที่เข้าสู่ระบบ

        // ค้นหาผู้ใช้ในฐานข้อมูล
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $row = $stmt->fetch();

        if ($row && password_verify($old_password, $row['password'])) {
            // ตรวจสอบว่ารหัสผ่านใหม่ตรงกับยืนยันรหัสผ่านใหม่
            if ($new_password == $confirm_password) {
                if (strlen($new_password) >= 5 && strlen($new_password) <= 20) {
                // เข้ารหัสรหัสผ่านใหม่
                $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                // อัปเดตรหัสผ่านในฐานข้อมูล
                $update_password_query = "UPDATE users SET password = :password WHERE id = :user_id";
                $stmt = $conn->prepare($update_password_query);
                $stmt->execute([':password' => $hashed_password, ':user_id' => $user_id]);

                $_SESSION['success'] = "รหัสผ่านถูกเปลี่ยนแปลงเรียบร้อยแล้ว.";
                header("location: resetpassword.php");
                }else{
                $_SESSION['danger'] = "รหัสผ่านใหม่ต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร..";
                header("location: resetpassword.php");
                }
            } else {
                $_SESSION['danger'] = "รหัสผ่านใหม่และยืนยันรหัสผ่านใหม่ไม่ตรงกัน.";
                header("location: resetpassword.php");

            }
        } else {
            $_SESSION['danger1'] = "รหัสผ่านเดิมไม่ถูกต้อง.";
            header("location: resetpassword.php");

        }
    } catch (PDOException $e) {
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    // ปิดการเชื่อมต่อฐานข้อมูล
    $conn = null;
}
?>
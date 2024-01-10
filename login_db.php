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


if (isset($_POST['singin'])) {
    $idstudent = $_POST['idstudent'];
    $password = $_POST['password'];


    if (empty($idstudent)) {
        $_SESSION['error'] = 'กรุณากรอก Username';
        header("location: login.php");
    } else if (empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
        header("location: login.php");
    } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
        $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
        header("location: login.php");
    } else {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE idstudent = :idstudent ");
            $stmt->bindParam(':idstudent', $idstudent);
            $stmt->execute();
            $row = $stmt->fetch();

            if ($row && password_verify($password, $row['password'])) {
                // เข้าสู่ระบบสำเร็จ
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['urole'];

                // นำผู้ใช้ไปยังหน้าที่ถูกต้องตามบทบาท
                if ($row['urole'] === 'admin') {
                    header('Location: index.php');
                } elseif ($row['urole'] === 'user') {
                    header('Location: item/allitem.php');
                } elseif ($row['urole'] === 'teacher') {
                    header('Location: item/approveitem.php');
                }else {
                    $_SESSION['warning'] = "ไม่มีสิทธ์เข้าใช้งาน รอการอนุมัติเข้าใช้งานจากเจ้าหน้าที่.";
                    header('Location: login.php');
                }
            } else {
                $_SESSION['error'] = "เข้าสู่ระบบล้มเหลว: ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง ";
                header('Location: login.php');
            }
        } catch (PDOException $e) {
            echo "เกิดข้อผิดพลาด: " . $e->getMessage();
        }

        // ปิดการเชื่อมต่อฐานข้อมูล
        $conn = null;
    }

}


?>
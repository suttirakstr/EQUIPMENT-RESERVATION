<?php 

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $new_typename = $_POST['prefix'];
    
        if (!is_numeric($id)) {
            $_SESSION['error'] = 'รหัสประเภทไม่ถูกต้อง';
            header("location: editprefix.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
        } else {
            // ตรวจสอบว่าชื่อประเภทไม่ซ้ำกัน
            $check_typename_query = "SELECT COUNT(*) FROM prefix WHERE prefix = :prefix"; // แทนค่า 'prefix' ด้วยชื่อตารางของคุณ
            $stmt = $conn->prepare($check_typename_query);
            $stmt->execute([':prefix' => $new_typename]);
            $count = $stmt->fetchColumn();
    
            if ($count > 0) {
                $_SESSION['error'] = 'ชื่อประเภทพัสดุซ้ำกัน';
                header("location: editprefix.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
            } else {
                // ดำเนินการอัปเดตข้อมูลในฐานข้อมูล
                $update_type_query = "UPDATE prefix SET prefix = :prefix WHERE id = :id"; // แทนค่า 'prefix' ด้วยชื่อตารางของคุณ
                $stmt = $conn->prepare($update_type_query);
                $stmt->execute([':prefix' => $new_typename, ':id' => $id]);
    
                $_SESSION['success'] = 'ประเภทพัสดุถูกอัปเดตเรียบร้อยแล้ว';
                header("location: editprefix.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
            }
        }
    }
?>

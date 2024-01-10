<?php 

    session_start();
    require_once '../config/db.php';

    if (isset($_POST['submit'])) {
        $id = $_POST['id'];
        $new_typename = $_POST['typename'];
    
        if (!is_numeric($id)) {
            $_SESSION['error'] = 'รหัสประเภทไม่ถูกต้อง';
            header("location: edittype.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
        } else {
            // ตรวจสอบว่าชื่อประเภทไม่ซ้ำกัน
            $check_typename_query = "SELECT COUNT(*) FROM typei WHERE typename = :typename"; // แทนค่า 'typei' ด้วยชื่อตารางของคุณ
            $stmt = $conn->prepare($check_typename_query);
            $stmt->execute([':typename' => $new_typename]);
            $count = $stmt->fetchColumn();
    
            if ($count > 0) {
                $_SESSION['error'] = 'ชื่อประเภทพัสดุซ้ำกัน';
                header("location: edittype.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
            } else {
                // ดำเนินการอัปเดตข้อมูลในฐานข้อมูล
                $update_type_query = "UPDATE typei SET typename = :typename WHERE id = :id"; // แทนค่า 'typei' ด้วยชื่อตารางของคุณ
                $stmt = $conn->prepare($update_type_query);
                $stmt->execute([':typename' => $new_typename, ':id' => $id]);
    
                $_SESSION['success'] = 'ประเภทพัสดุถูกอัปเดตเรียบร้อยแล้ว';
                header("location: edittype.php"); // แทนค่า 'your_page.php' ด้วยหน้าที่ต้องการกลับไปหลังจากอัปเดต
            }
        }
    }
?>

<?php 

    session_start();
    require_once 'config/db.php';

    if (isset($_POST['submittype'])) {
        $prefix = $_POST['prefix'];
      
        if (empty($prefix)) {
            $_SESSION['error'] = 'กรุณากรอกคำนำหน้า';
            header("location: editprefix.php");
        }  else {
            try {
                $check_typename = $conn->prepare("SELECT prefix FROM prefix WHERE prefix = :prefix");
                $check_typename->bindParam(":prefix", $prefix);
                $check_typename->execute();
                $row = $check_typename->fetch(PDO::FETCH_ASSOC);

                if ($row['prefix'] == $prefix) {
                    $_SESSION['warning'] = "ชื่อประเภทพัสดุซ้ำ";
                    header("location: editprefix.php");
                } else if (!isset($_SESSION['error'])) {
                    $stmt = $conn->prepare("INSERT INTO prefix(prefix) 
                                            VALUES(:prefix)");
                    $stmt->bindParam(":prefix", $prefix);
                    $stmt->execute();
                    $_SESSION['success'] = "เพิ่มประเภทพัสดุเสร็จสิ้น";
                    header("location: editprefix.php");
                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: editprefix.php");
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>
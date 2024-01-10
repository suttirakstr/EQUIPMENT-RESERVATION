<?php 

    session_start();
    require_once '../config/db.php';

    if (isset($_POST['submittype'])) {
        $typename = $_POST['typename'];
      
        if (empty($typename)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';
            header("location: createitem.php");
        }  else {
            try {
                $check_typename = $conn->prepare("SELECT typename FROM typei WHERE typename = :typename");
                $check_typename->bindParam(":typename", $typename);
                $check_typename->execute();
                $row = $check_typename->fetch(PDO::FETCH_ASSOC);

                if ($row['typename'] == $typename) {
                    $_SESSION['warning'] = "ชื่อประเภทพัสดุซ้ำ";
                    header("location: createitem.php");
                } else if (!isset($_SESSION['error'])) {
                    $stmt = $conn->prepare("INSERT INTO typei(typename) 
                                            VALUES(:typename)");
                    $stmt->bindParam(":typename", $typename);
                    $stmt->execute();
                    $_SESSION['success'] = "เพิ่มประเภทพัสดุเสร็จสิ้น";
                    header("location: createitem.php");
                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: createitem.php");
                }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>
<?php

    session_start();
    require_once '../config/db.php';

    if (isset($_POST['submit'])) {
        $iditem = $_POST['iditem'];
        $nameitem = $_POST['nameitem'];
        $typeitem = $_POST['typeitem'];
        $unititem = $_POST['unititem'];
        $status = 'ยังไม่ได้ใช้งาน';

        if (empty($iditem)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';
            header("location: createitem.php");
        } else if (empty($nameitem)) {
            $_SESSION['error'] = 'กรุณากรอกนามสกุล';
            header("location: createitem.php");
        } else if (empty($typeitem)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสนักศึกษา';
            header("location: createitem.php");
        }else if (empty($unititem)) {
            $_SESSION['error'] = 'กรุณากรอกเบอร์โทรศัพท์';
            header("location: createitem.php");
        } else {
            try {

                $check_iditem = $conn->prepare("SELECT iditem FROM items WHERE iditem = :iditem");
                $check_iditem->bindParam(":iditem", $iditem);
                $check_iditem->execute();
                $row = $check_iditem->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $_SESSION['warning'] = "มีรหัสพัสดุนี้อยู่ในระบบแล้ว";
                    header("location: createitem.php");
                } else {
                    // Check if nameitem already exists in the database
                    $check_nameitem = $conn->prepare("SELECT nameitem FROM items WHERE nameitem = :nameitem");
                    $check_nameitem->bindParam(":nameitem", $nameitem);
                    $check_nameitem->execute();
                    $row_nameitem = $check_nameitem->fetch(PDO::FETCH_ASSOC);

                if ($row_nameitem) {
                    $_SESSION['warning'] = "มีพัสดุนี้อยู่ในระบบแล้ว ";
                    header("location: createitem.php");
                } else if (!isset($_SESSION['error'])) {
                    $stmt = $conn->prepare("INSERT INTO items(iditem, nameitem, typeitem, unititem, status) 
                                            VALUES(:iditem, :nameitem, :typeitem, :unititem, :status)");
                    $stmt->bindParam(":iditem", $iditem);
                    $stmt->bindParam(":nameitem", $nameitem);
                    $stmt->bindParam(":typeitem", $typeitem);
                    $stmt->bindParam(":unititem", $unititem);
                    $stmt->bindParam(":status", $status);
                    $stmt->execute();
                    $_SESSION['success'] = "เพิ่มพัสดุเรียบร้อยแล้ว!  ";
                    header("location: createitem.php");
                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: createitem.php");
                }
            }
            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>
<?php

    session_start();
    require_once '../config/db.php';

    if (isset($_POST['singup'])) {
        $prefix = $_POST['prefix'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $idstudent = $_POST['idstudent'];
        $phone = $_POST['phone'];
        $nameteacher = $_POST['nameteacher'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $c_password = $_POST['c_password'];
        $urole = 'user';

        if (empty($firstname)) {
            $_SESSION['error'] = 'กรุณากรอกชื่อ';
            header("location: registeruser.php");
        } else if (empty($prefix)) {
            $_SESSION['error'] = 'กรุณากรอกคำนำหน้าชื่อ';
            header("location: registeruser.php");
        } else if (empty($lastname)) {
            $_SESSION['error'] = 'กรุณากรอกนามสกุล';
            header("location: registeruser.php");
        }else if (!preg_match("/^[0-9-]{14}$/", $_POST['idstudent'])) {
            $_SESSION['error'] = 'รหัสนักศึกษา 11 ตัว ใส่ - เฉพาะตัวเลขและ -';
            header("location: registeruser.php");
        }else if (empty($phone)) {
            $_SESSION['error'] = 'กรุณากรอกเบอร์โทรศัพท์';
            header("location: registeruser.php");
        }else if (empty($nameteacher)) {
            $_SESSION['error'] = 'กรุณากรอกชื่ออาจารย์ที่ปรึกษา';
            header("location: registeruser.php");
        }else if (empty($email)) {
            $_SESSION['error'] = 'กรุณากรอกอีเมล';
            header("location: registeruser.php");
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
            header("location: registeruser.php");
        } else if (empty($password)) {
            $_SESSION['error'] = 'กรุณากรอกรหัสผ่าน';
            header("location: registeruser.php");
        } else if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
            $_SESSION['error'] = 'รหัสผ่านต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร';
            header("location: registeruser.php");
        } else if (empty($c_password)) {
            $_SESSION['error'] = 'กรุณายืนยันรหัสผ่าน';
            header("location: registeruser.php");
        } else if ($password != $c_password) {
            $_SESSION['error'] = 'รหัสผ่านไม่ตรงกัน';
            header("location: registeruser.php");
        } else {
            try {
                $check_duplicate = $conn->prepare("SELECT idstudent, email FROM users WHERE idstudent = :idstudent OR email = :email");
                $check_duplicate->bindParam(":idstudent", $idstudent);
                $check_duplicate->bindParam(":email", $email);
                $check_duplicate->execute();
                $row = $check_duplicate->fetch(PDO::FETCH_ASSOC);

                if ($row['idstudent'] == $idstudent) {
                    $_SESSION['warning'] = "รหัสนักศึกษานี้มีอยู่ในระบบแล้ว";
                    header("location: registeruser.php");
                } else if ($row['email'] == $email) {
                    $_SESSION['warning'] = "มีอีเมลนี้อยู่ในระบบแล้ว";
                    header("location: registeruser.php");
                } else if (!isset($_SESSION['error'])) {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("INSERT INTO users(prefix, firstname, lastname, idstudent, phone, nameteacher, email, password, urole) 
                                            VALUES(:prefix, :firstname, :lastname, :idstudent, :phone, :nameteacher, :email, :password, :urole)");
                    $stmt->bindParam(":prefix", $prefix);
                    $stmt->bindParam(":firstname", $firstname);
                    $stmt->bindParam(":lastname", $lastname);
                    $stmt->bindParam(":idstudent", $idstudent);
                    $stmt->bindParam(":phone", $phone);
                    $stmt->bindParam(":nameteacher", $nameteacher);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":password", $passwordHash);
                    $stmt->bindParam(":urole", $urole);
                    $stmt->execute();
           
                    header("location: ../alluser.php");
                } else {
                    $_SESSION['error'] = "มีบางอย่างผิดพลาด";
                    header("location: registeruser.php");
                }

            } catch(PDOException $e) {
                echo $e->getMessage();
            }
        }
    }


?>
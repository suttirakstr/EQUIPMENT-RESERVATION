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
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Custom fonts for this template-->
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body class="bg-gradient-primary">

    <div class="container col-xl-5 col-lg-12 col-md-9 my-5">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">

                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account</h1>
                            </div>
                            <form action="register_db.php" method="post">

                                <?php if (isset($_SESSION['error'])) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php
                                        echo $_SESSION['error'];
                                        unset($_SESSION['error']);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if (isset($_SESSION['success'])) { ?>
                                    <div class="alert alert-success" role="alert">
                                        <?php
                                        echo $_SESSION['success'];
                                        unset($_SESSION['success']);
                                        ?>
                                    </div>
                                <?php } ?>
                                <?php if (isset($_SESSION['warning'])) { ?>
                                    <div class="alert alert-warning" role="alert">
                                        <?php
                                        echo $_SESSION['warning'];
                                        unset($_SESSION['warning']);
                                        ?>
                                    </div>
                                <?php } ?>
                                <div class="mb-3">
                                    <div class="input-group mb-2">
                                        <label class="input-group-text" for="inputGroupSelect01">คำนำหน้าชื่อ:</label>
                                        <select class="form-select col-sm-3" id="inputGroupSelect01" name="prefix"
                                            aria-describedby="prefix">
                                            <option value="นาย">นาย</option>
                                            <option value="นางสาว">นางสาว</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="firstname" class="form-label">First name</label>
                                    <input type="text" class="form-control" name="firstname"
                                        aria-describedby="firstname">
                                </div>
                                <div class="mb-3">
                                    <label for="lastname" class="form-label">Last name</label>
                                    <input type="text" class="form-control" name="lastname" aria-describedby="lastname">
                                </div>
                                <div class="mb-3">
                                    <label for="idstudent" class="form-label">รหัสนักศึกษา</label>
                                    <input type="text" class="form-control" name="idstudent"
                                        aria-describedby="idstudent">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="text" class="form-control" name="phone" aria-describedby="phone">
                                </div>
                                <div class="input-group col-sm-12 mb-3">
                                    <label class="input-group-text"
                                        for="inputGroupSelect01">ชื่ออาจารย์ที่ปรึกษาโครงงาน</label>

                                    <?php
                                    $sql = "SELECT * FROM users WHERE urole = 'teacher'";
                                    $stmt = $conn->query($sql);
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <select class="form-select col-sm-4" id="inputGroupSelect01" name="nameteacher"
                                        aria-describedby="urole">
                                        <option disabled selected> --เลือกอาจารย์ที่ปรึกษา--</option>
                                        <?php foreach ($data as $row): ?>
                                            <option>
                                                <?= $row['prefix'] . $row['firstname'] . ' ' . $row['lastname'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" aria-describedby="email">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password">
                                </div>
                                <div class="mb-3">
                                    <label for="confirm password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" name="c_password">
                                </div>

                                <div class="text-center">
                                    <button type="submit" name="singup" class="btn btn-primary ">สมัครสมาชิก</button>
                                </div>
                            </form>
                            <hr>

                            <div class="text-center">
                                <a class="small" href="../login.php">เข้าสู่ระบบ</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="../vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="../js/sb-admin-2.min.js"></script>

</body>

</html>
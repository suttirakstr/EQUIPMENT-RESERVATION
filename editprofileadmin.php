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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $query->execute(array(':id' => $id));
    $data = $query->fetch();

    if (!$data) {
        die('ไม่พบข้อมูลที่ต้องการแก้ไข');
    }
} else {
    die('ไม่ระบุ ID');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prefix = $_POST['prefix'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $idstudent = $_POST['idstudent'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $urole = $_POST['urole'];

    if ($data['urole'] == 'user') {
        $nameteacher = $_POST['nameteacher'];
    } else {
        $nameteacher = null; // ให้ชื่ออาจารย์เป็น null ถ้าไม่ใช่ "user"
    }

    // ตรวจสอบว่า email ไม่ซ้ำ
    $emailCheckQuery = $conn->prepare("SELECT id FROM users WHERE email = :email AND id != :id");
    $emailCheckQuery->execute(array(':email' => $email, ':id' => $id));
    $existingEmail = $emailCheckQuery->fetch();

    // ตรวจสอบว่า idstudent ไม่ซ้ำ
    $idStudentCheckQuery = $conn->prepare("SELECT id FROM users WHERE idstudent = :idstudent AND id != :id");
    $idStudentCheckQuery->execute(array(':idstudent' => $idstudent, ':id' => $id));
    $existingIdStudent = $idStudentCheckQuery->fetch();

    if (empty($prefix) || empty($firstname) || empty($lastname) || empty($idstudent) || empty($phone) || empty($email)) {
        $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif ($existingEmail) {
        $_SESSION['error'] = 'อีเมลนี้มีอยู่ในระบบแล้ว';
    } elseif ($existingIdStudent) {
        $_SESSION['error'] = 'รหัสนักศึกษาหรือชื่อผู้ใช้งานนี้มีอยู่ในระบบแล้ว';
    } elseif ($data['urole'] == "user" && !preg_match("/^[0-9-]{14}$/", $_POST['idstudent'])) {
        $_SESSION['error'] = 'รหัสนักศึกษาควรประกอบด้วยตัวเลขและเครื่องหมายลบ (-) เท่านั้น';
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง';
    } else {
        $query = $conn->prepare("UPDATE users SET prefix = :prefix, firstname = :firstname, lastname = :lastname, idstudent = :idstudent, phone = :phone, nameteacher = :nameteacher, email = :email, urole = :urole WHERE id = :id");
        $query->execute(array(':id' => $id, ':prefix' => $prefix, ':firstname' => $firstname, ':lastname' => $lastname, ':idstudent' => $idstudent, ':phone' => $phone, ':nameteacher' => $nameteacher, ':email' => $email, ':urole' => $urole));

        header('Location: alluser.php');
    }
}

?>
<?php
$role = $_SESSION['role'];
if ($role !== 'admin') {
    // ถ้าบทบาทไม่ใช่ "admin" ให้ส่งกลับไปยังหน้า Dashboard หรือหน้า Login
    header('Location: logout.php');
}
$userID = $_SESSION['user_id']; // หรือเป็นอะไรที่คุณใช้สำหรับการระบุผู้ใช้
$sql = "SELECT * FROM users WHERE id = :userID";

// เตรียมและส่งคำสั่ง SQL โดยใช้ PDO
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC)
    ?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>Dashboard</title>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />

</head>

<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-text mx-3">
                    <h5>ระบบยืม คืนพัสดุ</h5>
                </div>
            </a>

            <?php if ($role == 'user' || $role == 'teacher') {
                echo '<li class="nav-item active">
                <a class="nav-link" href="item/allitem.php">
                    <span>ยืมพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item active">
                <a class="nav-link" href="item/reitem.php">
                    <span>คืนพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="item/myhistory.php">
                    <span>ดูประวัติการทำรายการพัสดุ</span></a>
            </li>';
            } ?>

            <hr class="sidebar-divider" />
            <?php if ($role == 'admin') {
                echo '<li class="nav-item">
                <a class="nav-link" href="item/confirmitem.php">
                    <span>ยืนยันการคืนพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="alluser.php">
                    <span>รายชื่อผู้ใช้งานทั้งหมด</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="register/confirmregister.php">
                    <span>อนุมัติการสมัครสมาชิก</span></a>
            </li>';
                echo ' <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <span>Dashboard</span></a>
        </li>';
            } ?>

            <?php if ($role == 'teacher') {
                echo ' <li class="nav-item">
                <a class="nav-link" href="item/approveitem.php">
                    <span>ยืนยันการยืมพัสดุ</span></a>
            </li>';
            } ?>


            <?php if ($role == 'admin' || $role == 'teacher') {

                echo ' <li class="nav-item active">
                <a class="nav-link" href="item/createitem.php">
                    <span>เพิ่มพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="item/allitemadmin.php">
                    <span>ตารางพัสดุทั้งหมด</span></a>
            </li>';


                echo ' <hr class="sidebar-divider d-none d-md-block" /> ';
            } ?>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>


                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo $row['firstname'] . ' ' . $row['lastname'] ?>
                                </span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg" />
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>

                                <a class="dropdown-item" href="resetpassword.php">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Reset Password
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container col-xl-7 col-lg-12 col-md-9 my-5">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <?php if (isset($_SESSION['error'])) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php
                                    echo $_SESSION['error'];
                                    unset($_SESSION['error']);
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
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">โปรไฟล์</h1>
                                        </div>
                                        <form class="user" action="" method="POST">
                                            <?php if ($data['urole'] == 'user'): ?>
                                                <div class="input-group mb-2">
                                                    <label class="input-group-text"
                                                        for="inputGroupSelect01">คำนำหน้าชื่อ:</label>
                                                    <select class="form-select col-sm-3" id="inputGroupSelect01"
                                                        name="prefix" aria-describedby="prefix">
                                                        <option value="<?= $data['prefix'] ?>">
                                                            <?php echo $data['prefix'] ?>
                                                        </option>
                                                        <option value="นาย">นาย</option>
                                                        <option value="นางสาว">นางสาว</option>
                                                    </select>
                                                </div>
                                            <?php elseif ($data['urole'] == 'admin' || $data['urole'] == 'teacher'): ?>
                                                <div class="input-group mb-2">
                                                    <label class="input-group-text "
                                                        for="inputGroupSelect01">คำนำหน้าชื่อ</label>
                                                    <?php
                                                    $sql = "SELECT * FROM prefix";
                                                    $stmt = $conn->query($sql);
                                                    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <select class="form-select col-sm-3" id="inputGroupSelect01"
                                                        name="prefix" aria-describedby="prefix">
                                                        <option value="<?= $data['prefix'] ?>" class="col-lg-5">
                                                            <?php echo $data['prefix'] ?>
                                                        </option>
                                                        <?php foreach ($datas as $row): ?>
                                                            <option>
                                                                <?= $row['prefix'] ?>
                                                            </option>

                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                            <div class="form-group row">
                                                <div class="col-sm-3 ">
                                                    <h4 class="textcolor">ชื่อจริง: </h4>
                                                </div>
                                                <div>
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['firstname'] ?>" aria-label="firstname"
                                                        aria-describedby="basic-addon1" name="firstname">
                                                </div>
                                                <div class="col-sm-2 ">
                                                    <h4 class="textcolor">นามสกุล: </h4>
                                                </div>
                                                <div>
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['lastname'] ?>" aria-label="lastname"
                                                        aria-describedby="basic-addon1" name="lastname">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-3 ">
                                                    <?php if ($data['urole'] == 'user'): ?>
                                                        <h4 class="textcolor">รหัสนักศึกษา:

                                                        </h4>
                                                    <?php elseif ($data['urole'] == 'admin' || $data['urole'] == 'teacher'): ?>
                                                        <h4 class="textcolor">Username:

                                                        </h4>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['idstudent'] ?>" aria-label="idstudent"
                                                        aria-describedby="basic-addon1" name="idstudent">
                                                </div>

                                                <div class="col-sm-2 ">
                                                    <h4 class="textcolor">อีเมล: </h4>
                                                </div>
                                                <div>
                                                    <input type="email" class="form-control"
                                                        value="<?= $data['email'] ?>" aria-label="email"
                                                        aria-describedby="basic-addon1" name="email">
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="col-sm-3 ">
                                                    <h4 class="textcolor">เบอร์โทรศักท์: </h4>
                                                </div>
                                                <div>
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['phone'] ?>" aria-label="phone"
                                                        aria-describedby="basic-addon1" name="phone">
                                                </div>
                                            </div>
                                            <div class="input-group  mb-3">

                                                <?php if ($data['urole'] == 'user'): ?>
                                                    <label class="input-group-text "
                                                        for="inputGroupSelect01">อาจารย์ที่ปรึกษา</label>
                                                    <?php
                                                    $sql = "SELECT * FROM users WHERE urole = 'teacher'";
                                                    $stmt = $conn->query($sql);
                                                    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    ?>
                                                    <select class="form-select col-sm-4" id="inputGroupSelect01"
                                                        name="nameteacher" aria-describedby="nameteacher">
                                                        <option value="<?= $data['nameteacher'] ?>" class="col-lg-5">
                                                            <?php echo $data['nameteacher'] ?>
                                                        </option>
                                                        <?php foreach ($datas as $row): ?>
                                                            <option>
                                                                <?= $row['prefix'] . $row['firstname'] . ' ' . $row['lastname'] ?>
                                                            </option>

                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php endif; ?>
                                            </div>

                                            <div class="input-group  mb-3">
                                                <label class="input-group-text "
                                                    for="inputGroupSelect01">ตำแหน่ง</label>
                                                <?php
                                                $sql = "SELECT id, urole FROM users";
                                                $stmt = $conn->query($sql);
                                                $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                ?>
                                                <select class="form-select col-sm-4" id="inputGroupSelect01"
                                                    name="urole" aria-describedby="urole">
                                                    <option value="<?= $data['urole'] ?>" class="col-lg-5">
                                                        <?php echo $data['urole'] ?>
                                                    </option>
                                                    <option value="user">
                                                        user
                                                    </option>
                                                    <option value="admin">
                                                        admin
                                                    </option>
                                                    <option value="teacher">
                                                        teacher
                                                    </option>
                                                </select>

                                            </div>

                                            <hr>

                                            <div class="button">
                                                <button name="submit" type="submit"
                                                    class="btn btn-primary btn-user btn-block my-4 col-lg-2 ">
                                                    บันทึกข้อมูล
                                                </button>
                                            </div>


                                        </form>
                                        <hr>
                                        <div class="button"
                                            onclick="window.location.href='resetpasswordadmin.php?id=<?php echo $data['id']; ?>'">
                                            <a class="btn btn-user btn-block my-4 col-lg-2">แก้ไขรหัสผ่าน</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">ต้องการออกจากระบบหรือไม่</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                ถ้าต้องการออกจากระบบให้กดปุ่ม Logout ถ้าไม่ต้องการออกจากระบบให้กด Cancel
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">
                                    Cancel
                                </button>
                                <a class="btn btn-primary" href="logout.php">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>
</body>


<style>
    .textcolor {
        color: black;
    }

    .button {
        display: flex;
        justify-content: center;
    }
</style>

</html>
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

if (isset($_POST['save'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($new_password) >= 5 && strlen($new_password) <= 20) {
        if ($new_password != $confirm_password) {
            $_SESSION['error'] = 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน';
            header("location: resetpasswordadmin.php?id=" . $_GET['id']);
            exit();
        } else {
            $id = $_GET['id'];
            // เข้ารหัสรหัสผ่านใหม่
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // อัปเดตรหัสผ่านในฐานข้อมูล
            $update_password_query = "UPDATE users SET password = :password WHERE id = :id";
            $stmt = $conn->prepare($update_password_query);
            $stmt->execute([':password' => $hashed_password, ':id' => $id]);

            $_SESSION['success'] = 'รหัสผ่านถูกเปลี่ยนแปลงเรียบร้อยแล้ว.';
            header('location: resetpasswordadmin.php?id=' . $id);
            exit();
        }
    } else {
        $_SESSION['error'] = 'รหัสผ่านใหม่ต้องมีความยาวระหว่าง 5 ถึง 20 ตัวอักษร.';
        header("location: resetpasswordadmin.php?id=" . $_GET['id']);
        exit();
    }
}
$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
$user = $stmt->fetch();
?>

<?php
$role = $_SESSION['role'];
if ($role !== 'admin') {
    // ถ้าบทบาทไม่ใช่ "admin" ให้ส่งกลับไปยังหน้า Dashboard หรือหน้า Login
    header('Location: logout.php');
}

$userID = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :userID";

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
            </li>';} ?>

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
                                    <?php echo $row['prefix'] . $row['firstname'] . ' ' . $row['lastname'] ?>
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
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">เปลี่ยนรหัสผ่านใหม่
                                                <?php echo $user['prefix'].$user['firstname']. ' ' .$user['lastname'] ?>
                                            </h1>
                                        </div>
                                        <form class="user" action="" method="POST">

                                            <?php
                                            if (isset($_SESSION['error'])) {
                                                echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
                                                unset($_SESSION['error']); // เอาออกเพื่อไม่ให้แสดงอีก
                                            }
                                            if (isset($_SESSION['success'])) {
                                                echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
                                                unset($_SESSION['success']); // เอาออกเพื่อไม่ให้แสดงอีก
                                            }
                                            ?>

                                            <div class="form-group row">
                                                <div class="col-sm-5 ">
                                                    <h4 class="textcolor">รหัสนักศึกษา
                                                        <?php echo $user['idstudent'] ?>
                                                    </h4>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4 ">
                                                    <h4 class="textcolor">รหัสผ่านใหม่: </h4>
                                                </div>
                                                <div>
                                                    <input type="password" class="form-control"
                                                        placeholder="รหัสผ่านใหม่" name="new_password">
                                                </div>
                                            </div>

                                            <div class="form-group row ">
                                                <div class="col-sm-4 ">
                                                    <h4 class="textcolor">ยืนยันรหัสผ่านใหม่: </h4>
                                                </div>
                                                <div>
                                                    <input type="password" class="form-control"
                                                        placeholder="ยืนยันรหัสผ่านใหม่" name="confirm_password">
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-success"
                                                name="save">บันทึกข้อมูล</button>
                                        </form>
                                        <hr>
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
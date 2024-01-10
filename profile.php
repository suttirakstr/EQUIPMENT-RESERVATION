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

?>
<?php 
$role = $_SESSION['role'];

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
            <a class="sidebar-brand d-flex align-items-center justify-content-center" >
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
                                    <?php echo $row['prefix'] .$row['firstname'] . ' ' . $row['lastname'] ?>
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
                                            <h1 class="h4 text-gray-900 mb-4">โปรไฟล์</h1>
                                        </div>
                                        <form class="user">
                                            <div class="form-group row">
                                                <div class="col-sm-12 mb-8 mb-sm-0">
                                                    <h4 class="textcolor">ชื่อจริง-นามสกุล: <span
                                                            class="text-primary"><?php echo $row['prefix'] . $row['firstname'] . ' ' . $row['lastname'] ?></span></h4>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12 mb-12 mb-sm-0">
                                                <?php if ($role == 'admin'): ?>
                                                 <h4 class="textcolor">ชื่อผู้ใช้งาน: <span
                                                            class="text-primary"><?php echo $row['idstudent'] ?></span></h4>
                                                <?php endif; ?>
                                                <?php if ($role == 'user'): ?>
                                                 <h4 class="textcolor">รหัสนักศึกษา: <span
                                                            class="text-primary"><?php echo $row['idstudent'] ?></span></h4>
                                                <?php endif; ?>
                                                <?php if ($role == 'teacher'): ?>
                                                 <h4 class="textcolor">ชื่อผู้ใช้งาน: <span
                                                            class="text-primary"><?php echo $row['idstudent'] ?></span></h4>
                                                <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-5 mb-sm-0">
                                                    <h4 class="textcolor">อีเมล: <span
                                                            class="text-primary"><?php echo $row['email'] ?></span></h4>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    <h4 class="textcolor">เบอร์โทรศัพท์: <span
                                                            class="text-primary"><?php echo $row['phone'] ?></span></h4>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-12 mb-12 mb-sm-0">
                                                <?php if ($role == 'user'): ?>
                                                 <h4 class="textcolor">อาจารย์ที่ปรึกษา: <span
                                                            class="text-primary"><?php echo $row['nameteacher'] ?></span></h4>
                                                <?php endif; ?>
                                                </div>
                                            </div>

                                            <a onclick="window.location.href='editprofile.php?id=<?php echo $row['id'];?>'"
                                                class="btn btn-primary btn-user btn-block my-4 col-lg-2 ">
                                                แก้ไขข้อมูล
                                            </a>

                                        </form>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.5.0/dist/js/bootstrap.min.js"></script>

<style>
    .textcolor {
        color: black;
    }
</style>

</html>
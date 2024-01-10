<?php
session_start();
require_once '../config/db.php';
$num = 1;
// จำกัดเฉพาะแอดมิน
$role = $_SESSION['role'];
if ($role !== 'admin' && $role !== 'teacher') {
    // ถ้าบทบาทไม่ใช่ "admin" ให้ส่งกลับไปยังหน้า Dashboard หรือหน้า Login
    header('Location: ../logout.php');
}
$userID = $_SESSION['user_id']; // หรือเป็นอะไรที่คุณใช้สำหรับการระบุผู้ใช้
$sql = "SELECT * FROM users WHERE id = :userID";

// เตรียมและส่งคำสั่ง SQL โดยใช้ PDO
$stmt = $conn->prepare($sql);
$stmt->bindParam(':userID', $userID, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC)
?>
<!-- ดึงข้อมูลในตาราง -->
<?php
$sql = "SELECT id,iditem, typeitem, nameitem, unititem FROM items";
$stmt = $conn->query($sql);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!-- ดึงข้อมูลมาค้นหา -->



<!DOCTYPE html>
<html lang="en">

<head>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet" />

    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>

    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>เพิ่มนักศึกษา</title>



</head>


<body id="page-top">
    <div id="wrapper">
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
                <div class="sidebar-brand-text mx-3">
                    <h5>ระบบยืม คืนพัสดุ</h5>
                </div>
            </a>

            <?php if ($role == 'user' || $role == 'teacher') {
            echo '<li class="nav-item active">
                <a class="nav-link" href="../item/allitem.php">
                    <span>ยืมพัสดุ</span></a>
            </li>';
            echo ' <li class="nav-item active">
                <a class="nav-link" href="../item/reitem.php">
                    <span>คืนพัสดุ</span></a>
            </li>';
            echo '<li class="nav-item">
                <a class="nav-link" href="../item/myhistory.php">
                    <span>ดูประวัติการทำรายการพัสดุ</span></a>
            </li>';} ?>

            <hr class="sidebar-divider" />
            <?php if ($role == 'admin') {
                echo '<li class="nav-item">
                <a class="nav-link" href="../item/confirmitem.php">
                    <span>ยืนยันการคืนพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="../alluser.php">
                    <span>รายชื่อผู้ใช้งานทั้งหมด</span></a>
            </li>';
            echo '<li class="nav-item">
                <a class="nav-link" href="confirmregister.php">
                    <span>อนุมัติการสมัครสมาชิก</span></a>
            </li>';
            echo ' <li class="nav-item active">
            <a class="nav-link" href="../index.php">
                <span>Dashboard</span></a>
              </li>';
            } ?>

            <?php if ($role == 'teacher') {
                echo ' <li class="nav-item">
                <a class="nav-link" href="../item/approveitem.php">
                    <span>ยืนยันการยืมพัสดุ</span></a>
            </li>';
            } ?>


            <?php if ($role == 'admin' || $role == 'teacher') {
               
                echo ' <li class="nav-item active">
                <a class="nav-link" href="../item/createitem.php">
                    <span>เพิ่มพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="../item/allitemadmin.php">
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
                                <img class="img-profile rounded-circle" src="../img/undraw_profile.svg" />
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="../profile.php">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                
                                <a class="dropdown-item" href="../resetpassword.php">
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
                    <div class="container col-xl-5 col-lg-12 col-md-9 my-5">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">

                    <div class="col-lg-12">
                        <div class="p-5">
                        <h1 class="h3 mb-2 text-gray-800">เพิ่มนักศึกษา</h1>
                            <form action="register_dbuser.php" method="post">

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
                                <div class="mb-3">
                                   
                                                    <div class="input-group mb-2">
                                                        <label class="input-group-text"
                                                            for="inputGroupSelect01">คำนำหน้าชื่อ:</label>
                                                        <select class="form-select col-sm-3" id="inputGroupSelect01"
                                                            name="prefix" aria-describedby="prefix">
                                                            
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
                                    <input type="text" class="form-control" name="idstudent" aria-describedby="idstudent">
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                    <input type="text" class="form-control" name="phone" aria-describedby="phone">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" aria-describedby="email">
                                </div>
                                <div class="input-group col-sm-12 mb-3">
                                    <label class="input-group-text"
                                        for="inputGroupSelect01">ชื่ออาจารย์ที่ปรึกษาโครงงาน</label>

                                    <?php
                                    $sql = "SELECT * FROM users WHERE urole = 'teacher'";
                                    $stmt = $conn->query($sql);
                                    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>

                                    <select class="form-select col-sm-8" id="inputGroupSelect01" name="nameteacher"
                                        aria-describedby="urole">
                                        <option disabled selected> --เลือกอาจารย์ที่ปรึกษา--</option>
                                        <?php foreach ($data as $row): ?>
                                            <option>
                                                <?= $row['prefix']. $row['firstname']. ' ' .$row['lastname']?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

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

                        
                        </div>
                    </div>
                </div>
            </div>
        </div>
                        </div>
                        
                        <!-- Scroll to Top Button-->
                        <div class="my-4"></div>

                        <!-- Logout Modal-->
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
                                        <a class="btn btn-primary" href="../logout.php">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
</body>

<script>
    // ฟังก์ชันสำหรับเพิ่มข้อมูลลงในตาราง

</script>

</html>
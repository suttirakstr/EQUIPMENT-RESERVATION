<?php
session_start();
require_once '../config/db.php';
$num = 1;

$sql = "SELECT * FROM approve WHERE status = 'รอการรับพัสดุ'";
$stmt = $conn->query($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php 
$role = $_SESSION['role'];
if ($role !== 'admin' ) {
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>ยืนยันการให้ยืมพัสดุ</title>

    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet" />
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
    </script>
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
                <a class="nav-link" href="allitem.php">
                    <span>ยืมพัสดุ</span></a>
            </li>';
            echo ' <li class="nav-item active">
                <a class="nav-link" href="reitem.php">
                    <span>คืนพัสดุ</span></a>
            </li>';
            echo ' <li class="nav-item">
                <a class="nav-link" href="myhistory.php">
                    <span>ดูประวัติการทำรายการพัสดุ</span></a>
            </li>';} ?>

            <hr class="sidebar-divider" />
            <?php if ($role == 'admin') {
                echo '<li class="nav-item">
                <a class="nav-link" href="confirmitem.php">
                    <span>ยืนยันการคืนพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="../alluser.php">
                    <span>รายชื่อผู้ใช้งานทั้งหมด</span></a>
            </li>';
            echo '<li class="nav-item">
                <a class="nav-link" href="../register/confirmregister.php">
                    <span>อนุมัติการสมัครสมาชิก</span></a>
            </li>';
            echo ' <li class="nav-item active">
                <a class="nav-link" href="../index.php">
                    <span>Dashboard</span></a>
            </li>';
            } ?>

            <?php if ($role == 'teacher') {
                echo ' <li class="nav-item">
                <a class="nav-link" href="approveitem.php">
                    <span>ยืนยันการยืมพัสดุ</span></a>
            </li>';
            } ?>


            <?php if ($role == 'admin' || $role == 'teacher') {
                
                echo ' <li class="nav-item active">
                <a class="nav-link" href="createitem.php">
                    <span>เพิ่มพัสดุ</span></a>
            </li>';
                echo '<li class="nav-item">
                <a class="nav-link" href="allitemadmin.php">
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
                <!-- End of Topbar -->

                <!-- Begin Page Content -->

                <!-- /.container-fluid -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">ตารางการยืนยันให้ยืมพัสดุ</h1>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="form-group row">
                            <div class="card-header py-3 col-md-10">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    รายชื่อพัสดุ
                                </h6>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div class="table-responsive">
                                    <table class="display table table-bordered my-4" id="example" width="100%"
                                        cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th scope="col">ลำดับ</th>
                                                <th scope="col">สถานะ</th>
                                                <th scope="col">ประเภทพัสดุ</th>
                                                <th scope="col">ชื่อพัสดุ</th>
                                                <th scope="col">ชื่อผู้ยืมพัสดุ</th>
                                                <th scope="col">จำนวนพัสดุ</th>
                                                <th class="col-md-1" scope="col">ให้ยืมเรียบร้อย</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body">
                                            <?php foreach ($data as $s): ?>
                                                <tr>
                                                    <td scope="row">
                                                        <?php echo $num++; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $s['status']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $s['typeitem']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $s['nameitem']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $s['units']; ?>
                                                    </td>
                                                    <td ><button type="button" class="btn btn-success btn-sm"
                                                            onclick="window.location.href='approveadmin.php?id=<?php echo $s['id']; ?>'">
                                                            <a>ให้ยืมเสร็จสิ้น</a>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                </div>
                            </div>

                        </div>                  
                        <button class="btn btn-primary "><a href="confirmitem.php"
                                class="text-white">ดูรายชื่อรอคืนพัสดุ</a></button>
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







</script>

</html>
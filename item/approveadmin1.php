<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = $conn->prepare("SELECT * FROM approve WHERE id = :id");
    $query->execute(array(':id' => $id));
    $data = $query->fetch();

    if (!$data) {
        die('ไม่พบข้อมูลที่ต้องการแก้ไข');
    }
} else {
    die('ไม่ระบุ ID');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = 'ยังไม่ได้ใช้งาน';
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $nameitem = $_POST['nameitem'];
    $typeitem = $_POST['typeitem'];
    $units = $_POST['units'];

    $query = $conn->prepare("UPDATE approve SET status = :status, firstname = :firstname, lastname = :lastname, nameitem = :nameitem, typeitem = :typeitem,  units = :units WHERE id = :id");
    $query->execute(array(':id' => $id, ':status' => $status, ':firstname' => $firstname, ':lastname' => $lastname, ':nameitem' => $nameitem, ':typeitem' => $typeitem,  ':units' => $units));

    header('Location: confirmitem.php');
}
?>
<?php
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
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['return'])) {
        $recordId = $_POST['record'];
        if (isset($_POST['units'])) {
            $quantityReturned = $_POST['units'];
            if (returnMaterial($recordId, $quantityReturned)) {
                echo "<p>คืนวัสดุสำเร็จ</p>";
            } else {
                echo "<p>เกิดข้อผิดพลาดในการคืนวัสดุ</p>";
            }
        } else {
            echo "<p>กรุณาเลือกจำนวนที่คืน</p>";
        }

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />

    <title>ยืนยันการคืนพัสดุ</title>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <link href="../css/sb-admin-2.min.css" rel="stylesheet" />

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
                <div class="container col-xl-7 col-lg-12 col-md-9 my-5">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-5">ยืนยันการคืนพัสดุ</h1>
                                        </div>
                                        <form class="user" action="" method="post">
                                            <?php
                                            function returnMaterial($recordId, $quantityReturned)
                                            {
                                                global $conn;
                                             

                                                $conn->beginTransaction();

                                                $stmt = $conn->prepare("SELECT item_id, units FROM approve WHERE id = ?");
                                                $stmt->execute([$recordId]);
                                                $record = $stmt->fetch();

                                                $stmt = $conn->prepare("UPDATE approve SET return_date = CURRENT_TIMESTAMP() WHERE id = ?");
                                                $stmt->execute([$recordId]);

                                                $stmt = $conn->prepare("UPDATE items SET units = units - ? WHERE id = ?");
                                                $stmt->execute([$quantityReturned, $record['item_id']]);

                                                $conn->commit();

                                                return true;
                                            }
                                            ?>
                                            <div class="form-group row">
                                                <label for="firstname" class="col-form-label">ชื่อผู้ใช้:</label>
                                                <div class="col-sm-6 ">
                                                    <input type="text" class="form-control"
                                                        value="<?php echo $data['firstname']; ?>" aria-label="firstname"
                                                        aria-describedby="iditem" name="firstname" readonly>
                                                </div>
                                                <div class="col-md-4 ">
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['lastname'] ?>" aria-label="iditem"
                                                        aria-describedby="iditem" name="lastname" readonly>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="typeitem" class="col-form-label">ประเภทพัสดุ:</label>
                                                <div class="col-sm-5 ">
                                                    <input type="text" class="form-control"
                                                        value="<?php echo $data['typeitem']; ?>" aria-label="typeitem"
                                                        aria-describedby="typeitem" name="typeitem" readonly>
                                                </div>
                                                <label for="nameitem" class="col-form-label">ชื่อพัสดุ:</label>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control col-md-12"
                                                        value="<?= $data['nameitem'] ?>" aria-label="nameitem"
                                                        aria-describedby="nameitem" name="nameitem" readonly>
                                                </div>
                                            </div>

                                            <div class="form-group row">

                                                <label for="nameitem" class="col-form-label">จำนวนพัสดุ:</label>
                                                <div class="col-md-3">
                                                    <input type="hidden" value="<?= $data['id'] ?>" name="record" id="record">
                                                    <input type="text" class="form-control"
                                                        value="<?= $data['units'] ?>" aria-label="units"
                                                        aria-describedby="units" name="units" readonly>
                                                </div>
                                            </div>



                                            <hr>

                                            <div class="button">
                                                <button type="submit" name="return">
                                                    ยืนยันการคืนพัสดุ
                                                </button>
                                            </div>


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
                                <a class="btn btn-primary" href="../logout.php">Logout</a>
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

    .button {
        display: flex;
        justify-content: center;
    }
</style>

</html>
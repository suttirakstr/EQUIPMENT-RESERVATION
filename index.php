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
$num = 1;

$sql = "SELECT * FROM approve ";
$stmt = $conn->query($sql);
$stmt->execute();
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
$role = $_SESSION['role'];
if ($role !== 'admin' && $role !== 'teacher') {
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
<?php
$sql = "SELECT *FROM items";
$stmt = $conn->query($sql);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script charset="utf8" src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.js"></script>
    <link href="css/sb-admin-2.min.css" rel="stylesheet" />

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

    <title>Dashboard</title>



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
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*)  as dataitem FROM items ";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datai = $query->fetch();

                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(unititem)  as unititem FROM items";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnits = $datas['unititem'];
                                            if ($totalUnits == 0) {
                                                $totalUnits = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                <h5>จำนวนพัสดุทั้งหมด</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnits ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $datai['dataitem'] ?> รายการพัสดุ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) AS mismatched_items
                                            FROM items
                                            WHERE units != unititem";
                                            $stmt = $conn->query($sql);
                                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $mismatchedItemCount = $result['mismatched_items'];

                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM items";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $unitsData = $query->fetch();
                                            $units = $unitsData['units'];

                                            $sql = "SELECT SUM(unititem)  as unititem FROM items";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $totalUnitsData = $query->fetch();
                                            $totalUnits = $totalUnitsData['unititem'];


                                            $difference = $totalUnits - $units;
                                            if ($difference == 0) {
                                                $difference = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่ว่างยังไม่ได้ใช้งาน</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $difference ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $mismatchedItemCount ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) as units FROM approve WHERE status IN ('กำลังใช้งาน', 'รอการอนุมัติ', 'รออนุมัติการคืน', 'รอการรับพัสดุ')";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $dataapprove = $query->fetch();
                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM items";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnit = $datas['units'];
                                            if ($totalUnit == 0) {
                                                $totalUnit = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่มีการยืมทั้งหมด</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnit ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $dataapprove['units'] ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) as units FROM approve WHERE status IN ('กำลังใช้งาน')";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datass = $query->fetch();
                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM approve WHERE status = 'กำลังใช้งาน'";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnit = $datas['units'];
                                            if ($totalUnit == 0) {
                                                $totalUnit = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่กำลังใช้งาน</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnit ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $datass['units'] ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-dark shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) as units FROM approve WHERE status IN ('รอการอนุมัติ')";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datasi = $query->fetch();
                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM approve WHERE status = 'รอการอนุมัติ'";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnit = $datas['units'];
                                            if ($totalUnit == 0) {
                                                $totalUnit = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่รออนุมัติการยืม</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnit ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $datasi['units'] ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) as units FROM approve WHERE status IN ('รอการรับพัสดุ')";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datasis = $query->fetch();
                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM approve WHERE status = 'รอการรับพัสดุ'";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnit = $datas['units'];
                                            if ($totalUnit == 0) {
                                                $totalUnit = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่รอรับพัสดุ</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnit ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $datasis['units'] ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <?php
                                            $sql = "SELECT COUNT(*) as units FROM approve WHERE status IN ('รออนุมัติการคืน')";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datay = $query->fetch();
                                            ?>
                                            <?php
                                            $sql = "SELECT SUM(units)  as units FROM approve WHERE status = 'รออนุมัติการคืน'";
                                            $query = $conn->prepare($sql);
                                            $query->execute();
                                            $datas = $query->fetch();

                                            $totalUnit = $datas['units'];
                                            if ($totalUnit == 0) {
                                                $totalUnit = 0;
                                            }
                                            ?>
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                <h5>จำนวนพัสดุที่รอยืนยันการคืน</h5>
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?= $totalUnit ?> ชิ้น / <i
                                                    class="fas fa-calendar fa-2x text-gray-600">จำนวน:
                                                    <?= $datay['units'] ?> รายการ
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.container-fluid -->
                    <div class="container-fluid">
                        <!-- Page Heading -->
                        <h1 class="h3 mb-2 text-gray-800">ตารางคืน คืนพัสดุทั้งหมด</h1>
                        <!-- DataTales Example -->
                        <div class="card shadow mb-4">
                            <div class="form-group row">
                                <div class="card-header py-3 col-md-12">
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
                                                    <th scope="col">จำนวนพัสดุที่ยืม</th>
                                                    <th scope="col">อาจารย์ที่ปรึกษา</th>
                                                    <th scope="col">วันที่ยืมพัสดุ</th>
                                                    <th scope="col">วันที่คืนพัสดุ</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">

                                                <?php
                                                // Sort the data by the "create_at" field in descending order to display the latest first
                                                usort($data, function ($a, $b) {
                                                    return strtotime($b['create_at']) - strtotime($a['create_at']);
                                                });
                                                foreach ($data as $s): ?>
                                                    <tr>
                                                        <td scope="row">
                                                            <?php echo $num++; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($s['status'] === 'ยังไม่ได้ใช้งาน'): ?>
                                                                <span>คืนเสร็จสิ้น</span>
                                                            <?php else: ?>
                                                                <?php echo $s['status']; ?>
                                                            <?php endif; ?>
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
                                                        <td>
                                                            <?php echo $s['nameteacher']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $s['create_at']; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $s['return_date']; ?>
                                                        </td>

                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                    </div>
                                </div>
                            </div>


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
                                            <a class="btn btn-primary" href="logout.php">Logout</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>


</body>

</html>
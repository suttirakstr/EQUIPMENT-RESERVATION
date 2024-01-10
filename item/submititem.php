<?php
session_start();
require_once '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
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
$row = $stmt->fetch(PDO::FETCH_ASSOC);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['borrow'])) {
        $itemId = $_POST['item_id'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $typeitem = $_POST['typeitem'];
        $nameitem = $_POST['nameitem'];
        $units = $_POST['units'];
        if (isTeacher()) {
            $status = 'รอการรับพัสดุ';
            $nameteacher = '';
        } else {
            $status = 'รอการอนุมัติ';
            $nameteacher = $_POST['nameteacher'];
        }

        if (borrowMaterial($itemId, $firstname, $lastname, $typeitem, $nameitem, $nameteacher, $units, $status)) {
            echo "<p>ยืมวัสดุสำเร็จ</p>";
            header('Location: myhistory.php');
        } else {
            $_SESSION['error'] = "<p>จำนวนวัสดุไม่เพียงพอ</p>";
        }
    }
}
function isTeacher()
{
    // ในกรณีนี้เราสมมุติว่ามีตัวแปร $userRole ที่เก็บบทบาทของผู้ใช้
    // เช่น ถ้าครูเป็นค่า 'teacher' ให้คืนค่า true
    // ถ้าไม่ใช่ครูให้คืนค่า false
    $role = $_SESSION['role']; // ดึงข้อมูลบทบาทจากฐานข้อมูลหรือแหล่งข้อมูลอื่น ๆ

    if ($role === 'teacher') {
        return true;
    } else {
        return false;
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

    <title>ยืมพัสดุ</title>

    <!-- Custom fonts for this template-->
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
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
                <!-- End of Topbar -->
                <div class="container col-xl-8 col-lg-12 col-md-9 my-5">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">

                                <div class="col-lg-12">
                                    <div class="p-5">
                                        <div class="text-center">
                                            <h1 class="h4 text-gray-900 mb-4">ยืมพัสดุ</h1>
                                        </div>
                                        <div>
                                            <?php
                                            function borrowMaterial($itemId, $firstname, $lastname, $typeitem, $nameitem, $nameteacher, $units, $status)
                                            {
                                                global $conn;

                                                // ตรวจสอบว่ามีจำนวนวัสดุที่เพียงพอหรือไม่
                                                $stmt = $conn->prepare("SELECT unititem, units FROM items WHERE id = ?");
                                                $stmt->execute([$itemId]);
                                                $material = $stmt->fetch();

                                                if ($material['unititem'] - $material['units'] >= $units) {
                                                    // ทำการยืมวัสดุ
                                                    $conn->beginTransaction();

                                                    $stmt = $conn->prepare("INSERT INTO approve (item_id, firstname, lastname, typeitem, nameitem , nameteacher, units, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                                    $stmt->execute([$itemId, $firstname, $lastname, $typeitem, $nameitem, $nameteacher, $units, $status]);

                                                    $stmt = $conn->prepare("UPDATE items SET units = units + ? WHERE id = ?");
                                                    $stmt->execute([$units, $itemId]);

                                                    $conn->commit();

                                                    return true;
                                                } else {
                                                    return false; // จำนวนวัสดุไม่เพียงพอ
                                                }
                                            }
                                            ?>
                                            <form action="" method="post">
                                                <?php if (isset($_SESSION['error'])) { ?>
                                                    <div class="alert alert-danger text-center" role="alert">
                                                        <?php
                                                        echo $_SESSION['error'];
                                                        unset($_SESSION['error']);
                                                        ?>
                                                    </div>
                                                <?php } ?>
                                                <?php
                                                if (isset($_GET['id'])) {
                                                    $id = $_GET['id'];

                                                    $query = $conn->prepare("SELECT * FROM items WHERE id = :id");
                                                    $query->execute(array(':id' => $id));
                                                    $item1 = $query->fetch();
                                                }
                                                ?>
                                                <div class="form-group row">
                                                    <div class="col-md-4 ">
                                                        <label for="status">สถานะ:</label>
                                                        <input type="text" id="status" name="status" class="form-control"
                                                            value="<?= $item1['status'] ?>" readonly>
                                                    </div>

                                                </div>
                                                <div class="form-group row">
                                                    <div class="col-md-4 ">
                                                        <label for="firstname">ชื่อจริง:</label>
                                                        <input type="text" id="firstname" name="firstname" class="form-control"
                                                            value="<?= $row['firstname'] ?>" readonly>
                                                    </div>
                                                    <div class="col-sm-6 ">
                                                        <label for="lastname">นามสกุล:</label>
                                                        <input type="text" id="lastname" name="lastname" class="col-md-6 form-control"
                                                            value="<?= $row['lastname'] ?>" readonly>
                                                    </div>
                                                </div>
                                        </div>

                                        <?php if ($role == 'user'): ?>
                                        <div class="form-group row">
                                            <div class="col-md-8 ">
                                                <label for="nameteacher">ชื่ออาจารย์ที่ปรึกษา:</label>
                                                <input class="col-md-6 form-control" type="text" id="nameteacher" name="nameteacher"
                                                    value="<?= $row['nameteacher'] ?>" readonly>
                                            </div>
                                        </div>
                                        <?php endif; ?>

                                        <div class="form-group row">
                                            <div class="col-md-4 ">
                                                <label for="lastname">ประเภท:</label>
                                                <input type="text" id="lastname" name="typeitem" class="form-control"
                                                    value="<?= $item1['typeitem'] ?>" readonly>
                                            </div>
                                            <div class="col-sm-6 ">

                                                <label for="nameteacher">ชื่อพัสดุ:</label>
                                                <input class="col-md-6 form-control" type="text" id="nameteacher" name="nameitem"
                                                    value="<?= $item1['nameitem'] ?>" readonly>
                                            </div>
                                        </div>
                                        <?php
                                        $sql = "SELECT (unititem - units) AS remaining_quantity FROM
                                        items WHERE id = :id";

                                        // เตรียมและสร้างคำสั่ง SQL
                                        $stmt = $conn->prepare($sql);
                                        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
                                        $stmt->execute();
                                        $remaining_quantity = $stmt->fetchColumn();
                                        ?>
                                        <div id="remaining_quantity">จำนวนพัสดุที่เหลือ: <?php echo $remaining_quantity; ?></div>
                                        <div class="form-group row">

                                            <div class="col-sm-6 ">
                                                <input type="hidden" value="<?= $item1['id'] ?>" name="item_id">
                                                <input type="number" class="form-control form-control-user"
                                                    id="exampleFirstName" name="units" aria-describedby="units"
                                                    placeholder="ใส่จำนวนที่ต้องการจะยืม">
                                            </div>

                                        </div>


                                        <div class="button mt-5">
                                            <button type="submit" name="borrow"
                                                class="btn btn-primary">บันทึกข้อมูล</button>
                                        </div>
                                        </form>
                                    </div>
                                    <hr>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Logout Modal-->
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
<style scoped>
    .button {
        display: flex;
        justify-content: center;
    }
</style>

</html>
<?php

if(!isset($_GET['id'])){
    header('Location:index.php');
    }
require_once('../includes/connect.php');

$id = $conn->real_escape_string($_GET['id']);

$sql = "
SELECT
	p.pea_sname,
	s.budget_12m,
	s.budget_total,
	s.criteria_expenses,
	s.budget_12m - criteria_expenses AS balance,
	( s.criteria_expenses / s.budget_total ) * 100 AS percent_savings,
	( s.criteria_expenses / s.budget_12m ) * 100 AS percent_budget 
FROM
	account_result_2024 s
	JOIN profit p ON s.profit_code = p.profit_code 
WHERE
	acc_code = '$id' 
	AND p.pea_sname IS NOT NULL 
GROUP BY
	s.profit_code 
";
$result = $conn->query($sql);

$sql_header = "
SELECT acc
FROM 
    `zbudr091`
WHERE 
    `acc_code` = '$id'
LIMIT 1
";
$result_header = $conn->query($sql_header);

?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD -->

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="description" content="PEA NE2 Budget" />
    <meta name="author" content="Tom Skidrow" />
    <title>Budget</title>
    <!-- google font -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
    <!-- icons -->
    <link href="../../fonts/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../../fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="../../fonts/font-awesome/v6/css/all.css" rel="stylesheet" type="text/css" />
    <link href="../../fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
    <!--bootstrap -->
    <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- data tables -->
    <link href="../../assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!-- Material Design Lite CSS -->
    <link rel="stylesheet" href="../../assets/plugins/material/material.min.css">
    <link rel="stylesheet" href="../../assets/css/material_style.css">
    <!-- Theme Styles -->
    <link href="../../assets/css/theme/full/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
    <link href="../../assets/css/theme/full/style.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/theme/full/theme-color.css" rel="stylesheet" type="text/css" />
    <!-- favicon -->
    <link rel="shortcut icon" href="../../assets/img/favicon.ico" />
</head>
<!-- END HEAD -->
<style>
    #chartdiv {
        width: 100%;
        height: 300px;
    }

        <style>
        .progress-bar-success {
            background-color: #5cb85c; /* Green */
        }

        .progress-bar-yellow {
            background-color: #f0ad4e; /* Yellow */
        }

        .progress-bar-warning {
            background-color: #f0ad4e; /* Orange */
        }

        .progress-bar-danger {
            background-color: #d9534f; /* Red */
        }

        .progress-bar-secondary {
            background-color: #6c757d; /* Gray */
        }

        .progress-bar-purple {
            background-color: purple; /* Purple */
        }

        .progress-value {
            position: absolute;
            left: 105%; /* Adjust position next to the progress bar */
            top: 50%;
            transform: translateY(-50%);
            white-space: nowrap;
            color: black; /* Ensure it's readable */
            font-weight: bold; /* Make it stand out */
        }

</style>

<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md page-full-width header-white white-sidebar-color logo-indigo">
    <div class="page-wrapper">
        <!-- start header -->

        <!-- end mobile menu -->
        <!-- start header menu -->
        <?php include_once('../includes/header.php') ?>

        <!-- end header -->

        <!-- start page container -->
        <div class="page-container">
            <!-- start sidebar menu -->

            <!-- end sidebar menu -->
            <!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../data1">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                 <li class="active"><a href="#" id="goBack">กลับไปหน้าที่แล้ว</a></li>
                            </ol>
                        </div>
                    </div>
                    <!-- add content here -->


                    <!-- start data table -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-topline-purple">
                                <div class="card-head">

                                    <header><?php 
                                                    $row = $result_header->fetch_assoc();
                                                    echo $row['acc']; 
                                                    $result_header->free(); ?></header>
                                    <?php //} ?>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body ">

                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="example4">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>หน่วยงาน</th>
                                                <th>งบประมาณทั้งหมด</th>
                                                <th>งบประมาณสะสม</th>
                                                <th>ค่าใช้จ่าย</th>
                                                <th>คงเหลือ</th>
                                                <th>ร้อยละการใช้จ่าย</th>
                                                <th>ร้อยละคงเหลือ</th>
                                                <th>ปริมาตรการใช้งบ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                              $num = 0;
                                                        while($row = $result->fetch_assoc()){
                                                        $num++;
                                                            $balance = $row['balance'];
                                                            if ($balance < 0) { 
                                                                $formatted_balance = "(" . number_format(abs($balance), 2) . ")";  $row['balance'] = $formatted_balance;
                                                            } else {
                                                                $formatted_balance = number_format(($balance), 2);  
                                                                $row['balance'] = $formatted_balance;
                                                            }
                                                            $criteria_expenses = $row['criteria_expenses'];
                                                            if ($criteria_expenses < 0) { 
                                                                $formatted_pay = "(" . number_format(abs($criteria_expenses), 2) . ")";  
                                                                $row['criteria_expenses'] = $formatted_pay;
                                                            } else {
                                                                $formatted_pay = number_format(($criteria_expenses), 2);  
                                                                $row['criteria_expenses'] = $formatted_pay;
                                                            }
                                            ?>
                                            <tr class="odd gradeX">
                                                <td><?php echo $num; ?></td>
                                                <td><?php echo $row['pea_sname']; ?></td>
                                                <td><?php echo number_format($row['budget_12m'],2); ?></td>
                                                <td><?php echo number_format($row['budget_total'],2); ?></td>
                                                <!--                                                <td><?php //echo number_format($row['sum_pay_savings'],2); ?></td>-->
                                                <!--                                                <td><?php //echo number_format($row['except'],2); ?></td>-->
                                                <td><?php echo $row['criteria_expenses']; ?></td>
                                                <td><?php echo $row['balance']; ?></td>
                                                <td><?php echo number_format($row['percent_savings'],2); ?></td>
                                                <td><?php echo number_format($row['percent_budget'],2); ?></td>
                                                <td>
<?php
$percentage = $row['percent_budget']; // Using abs() to get the absolute value

if ($percentage < 0) {
    $progressClass = 'progress-bar-black'; // Set black color for negative values
} elseif ($percentage > 100) {
    $progressClass = 'progress-bar-purple';
} elseif ($percentage >= 0 && $percentage <= 25) {
    $progressClass = 'progress-bar-success'; 
} elseif ($percentage > 25 && $percentage <= 50) {
    $progressClass = 'progress-bar-yellow'; 
} elseif ($percentage > 50 && $percentage <= 80) {
    $progressClass = 'progress-bar-warning'; 
} elseif ($percentage > 80 && $percentage <= 100) {
    $progressClass = 'progress-bar-danger'; 
} else {
    $progressClass = 'progress-bar-secondary';
}
?>

<div class="progress" style="position: relative;">
        <div class="progress-bar <?php echo $progressClass; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo min($percentage, 100); ?>%;">
            <span class="sr-only"><?php echo $percentage; ?>% Complete</span>
        </div>
        <?php if ($percentage > 100): ?>
            <span class="progress-value"><?php echo $percentage; ?>%</span>
        <?php endif; ?>
    </div>
                                                </td>
                                            </tr>
                                            <?php } ?>

                                        </tbody>

                                        <!--
                                        <tfoot>
                                            <?php //while ($row = $result_sum->fetch_assoc()) { ?>
                                            <tr>

                                                <td colspan="2"></td>

                                                <td><mark><?php //echo number_format($row['sum_budget'],2); ?></mark></td>
-->
                                        <!--                                        <td><mark><?php //echo number_format($row['sum_savings'],2); ?></mark></td>-->
                                        <!--                                                <td><mark><?php //echo number_format($row['sum_pay_savings'],2); ?></mark></td>-->
                                        <!--                                                <td><mark><?php //echo number_format($row3['sum_except'],2); ?></mark></td>-->
                                        <!--
                                        <td><mark><?php //echo number_format($row['pay'],2); ?></mark></td>
                                        <td><mark><?php //echo number_format($row['balance'],2); ?></mark></td>
-->
                                        <!--
                                                <td colspan="2"></td>
                                            </tr>
                                            <?php //} ?>
                                        </tfoot>
-->
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end data table -->
                </div>
            </div>
            <!-- end page content -->




        </div>
        <!-- end page container -->

        <!-- start footer -->

        <?php include_once('../includes/footer.php') ?>

        <!-- end footer -->
    </div>
    <!-- start js include path -->
    <script src="../../assets/plugins/jquery/jquery.min.js"></script>
    <script src="../../assets/plugins/popper/popper.js"></script>
    <script src="../../assets/plugins/jquery-blockui/jquery.blockui.min.js"></script>
    <script src="../../assets/plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="../../assets/plugins/feather/feather.min.js"></script>
    <!-- bootstrap -->
    <script src="../../assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../assets/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
    <!-- Amchart js -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <!-- Common js-->
    <script src="../../assets/js/app.js"></script>
    <script src="../../assets/js/layout.js"></script>
    <script src="../../assets/js/theme-color.js"></script>
    <!-- Material -->
    <script src="../../assets/plugins/material/material.min.js"></script>
    <!-- data tables -->
    <script src="../../assets/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../assets/plugins/datatables/plugins/bootstrap/dataTables.bootstrap5.min.js"></script>
    <!-- end js include path -->

    <script>
        $("#example4").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });

    </script>
    <script>
        document.getElementById("goBack").addEventListener("click", function(event) {
            event.preventDefault(); // Prevent the default behavior of the link
            window.history.back(); // Go back to the previous page
        });
    </script>
</body>

</html>

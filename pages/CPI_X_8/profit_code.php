<?php

if(!isset($_GET['id'])){
    header('Location:index.php');
    }
require_once('../includes/connect.php');

$id = $conn->real_escape_string($_GET['id']);

$sql = "
SELECT  
    SUM(budget_12m) AS budget,
    SUM(real_expenses_total) - SUM(counterpart_fund+disaster_pm_zcb+disaster_oms_zcb+sabotage_pm_zcf+disaster+policy_acc+ministry_trip+policy_gov+election+riot_s3+pea_level_up+`union`+support+BD10+BD11+BD12+BD13+BD14+BD15+BD16) AS pay,
    SUM(budget_12m) - (SUM(real_expenses_total) - SUM(counterpart_fund+disaster_pm_zcb+disaster_oms_zcb+sabotage_pm_zcf+disaster+policy_acc+ministry_trip+policy_gov+election+riot_s3+pea_level_up+`union`+support+BD10+BD11+BD12+BD13+BD14+BD15+BD16)) AS balance,
    ((SUM(real_expenses_total) - SUM(counterpart_fund+disaster_pm_zcb+disaster_oms_zcb+sabotage_pm_zcf+disaster+policy_acc+ministry_trip+policy_gov+election+riot_s3+pea_level_up+`union`+support+BD10+BD11+BD12+BD13+BD14+BD15+BD16))
    - SUM(budget_12m)) / SUM(budget_12m) * 100 AS percent
FROM zbudr091
WHERE 
    year = (SELECT MAX(year) FROM zbudr091)
    AND month = (SELECT MAX(month) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091))
    AND round_count = (SELECT MAX(round_count) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091) AND month = (SELECT MAX(month) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091)))
    AND time_stamp = (SELECT MAX(time_stamp) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091) AND month = (SELECT MAX(month) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091)) AND round_count = (SELECT MAX(round_count) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091) AND month = (SELECT MAX(month) FROM zbudr091 WHERE year = (SELECT MAX(year) FROM zbudr091))))
SELECT *
FROM 
    profit
WHERE 
    SUBSTR(profit_code,1,2) = 'E3'    
";
$result = $conn->query($sql);

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
    <title>IDSS</title>
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
                                <div class="page-title">Account Code</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard/">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Account Code</li>
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
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <div class="btn-group">
                                                <button id="addRow1" class="btn btn-info">
                                                    Add New <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <div class="btn-group pull-right">
                                                <button class="btn deepPink-bgcolor  btn-outline dropdown-toggle" data-bs-toggle="dropdown">Tools
                                                    <i class="fa fa-angle-down"></i>
                                                </button>
                                                <ul class="dropdown-menu pull-right">
                                                    <li>
                                                        <a href="javascript:;">
                                                            <i class="fa fa-print"></i> Print </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">
                                                            <i class="fa fa-file-pdf-o"></i> Save as PDF </a>
                                                    </li>
                                                    <li>
                                                        <a href="javascript:;">
                                                            <i class="fa fa-file-excel-o"></i> Export to Excel </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="example4">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th> กลุ่มบัญชี </th>
                                                <th> งบประมาณ 12 เดือน </th>
                                                <th> งบประมาณสะสม </th>
                                                <!--                                                <th> จ่ายจริงสะสม </th>-->
                                                <!--                                                <th> รวมค่ายกเว้น </th>-->
                                                <th> เบิกจ่ายสุทธิ </th>
                                                <th> คงเหลือ </th>
                                                <th> % </th>
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
                                                            $pay = $row['pay'];
                                                            if ($pay < 0) { 
                                                                $formatted_pay = "(" . number_format(abs($pay), 2) . ")";  
                                                                $row['pay'] = $formatted_pay;
                                                            } else {
                                                                $formatted_pay = number_format(($pay), 2);  
                                                                $row['pay'] = $formatted_pay;
                                                            }
                                            ?>
                                            <tr class="odd gradeX">
                                                <td><?php echo $num; ?></td>
                                                <td><?php echo $row['pea_name']; ?></td>
                                                <td><?php echo number_format($row['sum_budget'],2); ?></td>
                                                <td><?php echo number_format($row['sum_savings'],2); ?></td>
                                                <!--                                                <td><?php //echo number_format($row['sum_pay_savings'],2); ?></td>-->
                                                <!--                                                <td><?php //echo number_format($row['except'],2); ?></td>-->
                                                <td><?php echo $row['pay']; ?></td>
                                                <td><?php echo $row['balance']; ?></td>
                                                <td><?php echo abs(number_format($row['percent'],2)); ?></td>
                                                <td>
                                                    <?php
                                                    if ($row['percent'] > 100) {
                echo abs(number_format($row['percent']));
            }
                                                    $percentage = abs(number_format($row['percent'],2));

                                                    if ($percentage >= 0 && $percentage <= 25) {
                                                        $progressClass = 'progress-bar-danger'; 
                                                    } elseif ($percentage > 25 && $percentage <= 50) {
                                                        $progressClass = 'progress-bar-warning'; 
                                                    } elseif ($percentage > 50 && $percentage <= 75) {
                                                        $progressClass = 'progress-bar-yellow'; 
                                                    } else {
                                                        $progressClass = 'progress-bar-success'; 
                                                    }
                                                    ?>

                                                    <div class="progress">
                                                        <div class="progress-bar <?php echo $progressClass; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo abs(number_format($row['percent'])); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo abs(number_format($row['percent'])); ?>%;"> <span class="sr-only"><?php echo abs(number_format($row['percent'])); ?>% Complete</span> </div>
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

</body>

</html>

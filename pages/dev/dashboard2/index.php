<?php
require_once('../includes/connect.php');

$sql2 = "
SELECT

SUBSTR(a.pea_name, 24, 20) AS pea,
a.total_criteria_expenses AS total_criteria_expenses_2023,
b.total_budget_total AS total_budget_total_2024,
b.total_criteria_expenses AS total_criteria_expenses_2024

FROM
aggregated_data_2023 a
    
JOIN
aggregated_data b ON a.profit_code = b.profit_code

WHERE
a.profit_code IN ('E3101034','E3101044','E3101054','E3101064','E3101104','E3102044','E3111034','E3111044','E3111054','E3112024','E3141024',
'E3141034','E3141044')

GROUP BY
a.profit_code

ORDER BY
a.profit_code ASC

";

$result2 = $conn->query( $sql2 );

$data = array();
if ( $result2->num_rows > 0 ) {
    while( $row = $result2->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData2 = json_encode( $data );


$sql2_2 = "
SELECT

SUBSTR(a.pea_name, 24, 20) AS pea,
a.total_criteria_expenses AS total_criteria_expenses_2023,
b.total_budget_total AS total_budget_total_2024,
b.total_criteria_expenses AS total_criteria_expenses_2024,

CASE

WHEN 
b.total_budget_total = 0 THEN 0

ELSE 
b.total_criteria_expenses / b.total_budget_total * 100

END AS percentage_expenses_2024,
    ((b.total_criteria_expenses - a.total_criteria_expenses) / a.total_criteria_expenses) * 100 AS percentage_change

FROM
aggregated_data_2023 a

JOIN
aggregated_data b ON a.profit_code = b.profit_code

WHERE
a.profit_code IN ('E3101034','E3101044','E3101054','E3101064','E3101104','E3102044','E3111034','E3111044','E3111054','E3112024','E3141024',
'E3141034','E3141044')

GROUP BY
a.profit_code

ORDER BY
a.profit_code ASC

";

$result2_2 = $conn->query( $sql2_2 );



$sql3 = "
SELECT

SUBSTR(a.pea_name, 24, 20) AS pea,
a.total_criteria_expenses AS total_criteria_expenses_2023,
b.total_budget_total AS total_budget_total_2024,
b.total_criteria_expenses AS total_criteria_expenses_2024,

CASE

WHEN 
b.total_budget_total = 0 THEN 0

ELSE 
b.total_criteria_expenses / b.total_budget_total * 100

END AS percentage_expenses_2024,
    ((b.total_criteria_expenses - a.total_criteria_expenses) / a.total_criteria_expenses) * 100 AS percentage_change
FROM

aggregated_data_2023 a
    
JOIN
aggregated_data b ON a.profit_code = b.profit_code

WHERE
a.profit_code IN ('E3101034','E3101044','E3101054','E3101064','E3101104','E3102044','E3111034','E3111044','E3111054','E3112024','E3141024',
'E3141034','E3141044')

GROUP BY
a.profit_code

ORDER BY 
percentage_expenses_2024 DESC
";

$result3 = $conn->query( $sql3 );

$data = array();
if ( $result3->num_rows > 0 ) {
    while( $row = $result3->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData3 = json_encode( $data );


$sql3_2 = "
SELECT

SUBSTR(a.pea_name, 24, 20) AS pea,
a.total_criteria_expenses AS total_criteria_expenses_2023,
b.total_budget_total AS total_budget_total_2024,
b.total_criteria_expenses AS total_criteria_expenses_2024,

CASE

WHEN 
b.total_budget_total = 0 THEN 0

ELSE 
b.total_criteria_expenses / b.total_budget_total * 100

END AS percentage_expenses_2024,
    ((b.total_criteria_expenses - a.total_criteria_expenses) / a.total_criteria_expenses) * 100 AS percentage_change
FROM

aggregated_data_2023 a
    
JOIN
aggregated_data b ON a.profit_code = b.profit_code

WHERE
a.profit_code IN ('E3101034','E3101044','E3101054','E3101064','E3101104','E3102044','E3111034','E3111044','E3111054','E3112024','E3141024',
'E3141034','E3141044')

GROUP BY
a.profit_code

ORDER BY 
percentage_expenses_2024 DESC
";

$result3_2 = $conn->query( $sql3_2 );
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
    <title>CPI-X</title>
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
    <!-- ApexChart Styles -->
    <link href="../assets/styles.css" rel="stylesheet" />
    <!-- Theme Styles -->
    <link href="../../assets/css/theme/full/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
    <link href="../../assets/css/theme/full/style.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/theme/full/theme-color.css" rel="stylesheet" type="text/css" />
    <!-- favicon -->
    <link rel="shortcut icon" href="../../assets/img/favicon.ico" />

    <!-- Script ApexChart -->
    <script>
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/promise-polyfill@8/dist/polyfill.min.js"><\/script>'
            )
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/eligrey-classlist-js-polyfill@1.2.20171210/classList.min.js"><\/script>'
            )
        window.Promise ||
            document.write(
                '<script src="https://cdn.jsdelivr.net/npm/findindex_polyfill_mdn"><\/script>'
            )

    </script>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<!-- END HEAD -->

<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md page-full-width header-white white-sidebar-color logo-indigo">
    <div class="page-wrapper">
        <!-- start header -->

        <!-- end mobile menu -->
        <!-- start header menu -->
        <?php include_once( '../includes/header.php' ) ?>

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
                                <div class="page-title">การบริหารค่าใช้จ่าย CPI-X</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;
                                    <a class="parent-item" href="../dashboard1">Home</a>&nbsp;
                                    <i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                    <!-- add content here -->

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div class="card-head">
                                    <header>กฟย.</header>
                                </div>
                                <div class="card-body ">
                                    <div class="mdl-tabs mdl-js-tabs">
                                        <div class="mdl-tabs__tab-bar tab-left-side">
                                            <a href="#tab4-panel" class="mdl-tabs__tab tabs_three is-active">กฟย</a>
                                            <!--                                            <a href="#tab5-panel" class="mdl-tabs__tab tabs_three">กฟส. only</a>-->
                                            <a href="#tab6-panel" class="mdl-tabs__tab tabs_three">Order by</a>
                                        </div>
                                        <div class="mdl-tabs__panel is-active p-t-20" id="tab4-panel">

                                            <!-- start Apex Chart -->
                                            <div id="chart2"></div>
                                            <!-- end Apex Chart -->

                                            <!-- start data table -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card card-topline-red">
                                                        <div class="card-head">
                                                            <header></header>
                                                            <div class="tools">
                                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
                                                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                                            </div>
                                                        </div>
                                                        <div class="card-body ">

                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="table2">
                                                                <thead>
                                                                    <tr>
                                                                    <th></th>
                                                                        <th> หน่วยงาน </th>
                                                                        <th> งบประมาณสะสม </th>
                                                                        <th> ค่าใช้จ่ายตามเกณฑ์ </th>
                                                                        <th> ค่าใช้จ่ายตามเกณฑ์ ปี 66 </th>
                                                                        <th> เปรียบเทียบงบประมาณสะสม</th>
                                                                        <th> เปรียบเทียบ ค่าใช้จ่ายตามเกณฑ์ ปี 66</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                              $num = 0;
                                                        while($row = $result2_2->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                                    <tr class="odd gradeX">
                                                                        <td><?php echo $num; ?></td>

                                                                        <td><?php echo $row['pea']; ?></td>
                                                                        <td><?php echo number_format($row['total_budget_total_2024'],2); ?></td>
                                                                        <td><?php echo number_format($row['total_criteria_expenses_2024'],2); ?></td>

                                                                        <td><?php echo number_format($row['total_criteria_expenses_2023'],2); ?></td>
                                                                        <td><?php echo number_format($row['percentage_expenses_2024'],2); ?></td>
                                                                        <td><?php echo number_format($row['percentage_change'],2); ?></td>

                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end data table -->


                                        </div>
                                        <!--                                        <div class="mdl-tabs__panel p-t-20" id="tab5-panel">-->
                                        <!-- start Apex Chart -->
                                        <!--                                            <div id="chart2"></div>-->
                                        <!-- end Apex Chart -->
                                        <!-- start data table -->

                                        <!-- end data table -->
                                        <!--                                        </div>-->
                                        <div class="mdl-tabs__panel p-t-20" id="tab6-panel">
                                            <!-- start Apex Chart -->
                                            <div id="chart3"></div>
                                            <!-- end Apex Chart -->
                                            <!-- start data table -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="card card-topline-red">
                                                        <div class="card-head">
                                                            <header></header>
                                                            <div class="tools">
                                                                <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
                                                                <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                                                <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                                            </div>
                                                        </div>
                                                        <div class="card-body ">

                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="table3">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        <th> หน่วยงาน </th>
                                                                        <th> งบประมาณสะสม </th>
                                                                        <th> ค่าใช้จ่ายตามเกณฑ์ </th>
                                                                        <th> ค่าใช้จ่ายตามเกณฑ์ ปี 66 </th>
                                                                        <th> เปรียบเทียบงบประมาณสะสม</th>
                                                                        <th> เปรียบเทียบ ค่าใช้จ่ายตามเกณฑ์ ปี 66</th>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                              $num = 0;
                                                        while($row = $result3_2->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                                    <tr class="odd gradeX">
                                                                        <td><?php echo $num; ?></td>

                                                                        <td><?php echo $row['pea']; ?></td>
                                                                        <td><?php echo number_format($row['total_budget_total_2024'],2); ?></td>
                                                                        <td><?php echo number_format($row['total_criteria_expenses_2024'],2); ?></td>

                                                                        <td><?php echo number_format($row['total_criteria_expenses_2023'],2); ?></td>
                                                                        <td><?php echo number_format($row['percentage_expenses_2024'],2); ?></td>
                                                                        <td><?php echo number_format($row['percentage_change'],2); ?></td>

                                                                    </tr>
                                                                    <?php } ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end data table -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- end page content -->

        </div>
        <!-- end page container -->

        <!-- start footer -->

        <?php include_once( '../includes/footer.php' ) ?>

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

    <!-- Start script datatable -->

    <script>
        //        $("#table1").DataTable({
        //            columnDefs: [{
        //                width: 10,
        //                targets: 0
        //            }],
        //        });
        $("#table2").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });
        $("#table3").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });

    </script>
    <!-- Start ApexChart -->
    <script>
        // Function to initialize chart
        function initializeChart(chartId, jsonData) {
            // Extract data from JSON
            var categories = jsonData.map(function(item) {
                return item.pea;
            });
            var totalBudgetData = jsonData.map(function(item) {
                return parseFloat(item.total_budget_total_2024);
            });
            var totalExpensesData2024 = jsonData.map(function(item) {
                return parseFloat(item.total_criteria_expenses_2024);
            });
            var totalExpensesData2023 = jsonData.map(function(item) {
                return parseFloat(item.total_criteria_expenses_2023);
            });

            // Convert data to millions
            totalBudgetData = totalBudgetData.map(function(value) {
                return (value / 1000000).toFixed(2);
            });
            totalExpensesData2024 = totalExpensesData2024.map(function(value) {
                return (value / 1000000).toFixed(2);
            });
            totalExpensesData2023 = totalExpensesData2023.map(function(value) {
                return (value / 1000000).toFixed(2);
            });

            // Chart options
            var options = {
                chart: {
                    type: 'bar',
                    height: 800,
                    stacked: true
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: {
                            enabled: true,
                            formatter: function(val) {
                                return parseFloat(val).toLocaleString() + 'M';
                            }
                        }
                    }
                },
                stroke: {
                    width: 1,
                    colors: ['#fff']
                },
                series: [{
                    name: 'งบประมาณสะสม',
                    data: totalBudgetData
                }, {
                    name: 'ค่าใช้จ่ายตามเกณฑ์',
                    data: totalExpensesData2024
                }, {
                    name: 'ค่าใช้จ่ายตามเกณฑ์ ปี 2566',
                    data: totalExpensesData2023
                }],
                xaxis: {
                    categories: categories,
                    title: {
                        text: 'หน่วยเป็น: ล้านบาท'
                    },
                    labels: {
                        formatter: function(value) {
                            return value + 'M';
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'PEA'
                    }
                },
                legend: {
                    position: 'top'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return parseFloat(val).toLocaleString() + 'M';
                        }
                    }
                }
            };

            // Initialize chart
            var chart = new ApexCharts(document.querySelector(chartId), options);
            chart.render();
        }

        // Fetch the data from PHP
        //        var jsonData1 = <?php //echo $jsonData1; ?>;
        var jsonData2 = <?php echo $jsonData2; ?>;
        var jsonData3 = <?php echo $jsonData3; ?>;

        // Initialize charts
        //        initializeChart("#chart1", jsonData1);
        initializeChart("#chart2", jsonData2);
        initializeChart("#chart3", jsonData3);

    </script>

</body>

</html>

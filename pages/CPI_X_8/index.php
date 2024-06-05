<?php 
require_once('../includes/connect.php');
$sql_table = "
SELECT
	`group`,
	group_name,
	SUM( budget_12m ) AS sum_budget_12m,
	SUM( budget_total ) AS sum_budget_total,
	SUM( criteria_expenses ) AS sum_criteria_expenses,
	SUM( budget_12m ) - SUM( criteria_expenses ) AS balance,
	SUM( criteria_expenses ) / SUM( budget_total ) * 100 AS percent_savings,
	SUM( criteria_expenses ) / SUM( budget_12m ) * 100 AS percent_budget 
FROM
	account_result_2024 
GROUP BY
	`group`
";
$result_table = $conn->query($sql_table);

$sql_total = "
SELECT

	SUM( budget_12m ) AS sum_budget_12m,
	SUM( budget_total ) AS sum_budget_total,
	SUM( criteria_expenses ) AS sum_criteria_expenses,
	SUM( budget_12m ) - SUM( criteria_expenses ) AS balance,
	SUM( criteria_expenses ) / SUM( budget_total ) * 100 AS percent_savings,
	

	 
	( ( SUM( criteria_expenses ) - SUM( budget_12m ) ) / SUM( budget_12m ) ) * 100 AS percent_budget 
FROM
	account_result_2024 
";
$result_total = $conn->query($sql_total);


$sql = "
SELECT 
    year,
	SUM( budget_12m ) AS sum_budget_12m,
	SUM( budget_total ) AS sum_budget_total,
	SUM( criteria_expenses ) AS sum_criteria_expenses
FROM account_result_2024

";
$result = $conn->query($sql);



$data = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$json_data = json_encode($data);


$data = json_decode($json_data, true);


foreach ($data as &$item) {
    foreach ($item as $key => &$value) {
        
        if ($key !== "year" && is_numeric($value)) {
            
            $value = (float) $value;
        }
    }
}


$new_json_data = json_encode($data); 

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
            <!-- start page content -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="page-bar">
                        <div class="page-title-breadcrumb">
                            <div class=" pull-left">
                                <div class="page-title"></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a href="#" id="goBack">กลับไปหน้าที่แล้ว</a></li>
                            </ol>
                        </div>
                    </div>

                    <!-- start widget -->
                    <div class="row">
                        <div class="col-xl-5">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h4 class="info-box-title">งบประมาณ 12 เดือน</h4>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="l-bg-green info-icon">
                                                            <i class="fa fa-users pull-left col-orange font-30"></i>
                                                        </div>
                                                    </div>
                                                </div><br>
                                                <?php while ($row = $result_total->fetch_assoc()) { ?>
                                                <h1 class="mt-1 mb-3 info-box-title"><?php echo number_format($row['sum_budget_12m'],2); ?></h1>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h4 class="info-box-title">คงเหลือทั้งหมด</h4>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="col-indigo info-icon">
                                                            <i class="fa fa-book pull-left card-icon font-30"></i>
                                                        </div>
                                                    </div>
                                                </div><br>
                                                <h1 class="mt-1 mb-3 info-box-title"><?php echo number_format($row['balance'],2); ?></h1>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h4 class="info-box-title">เบิกจ่าย</h4>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="col-teal info-icon">
                                                            <i class="fa fa-user pull-left card-icon font-30"></i>
                                                        </div>
                                                    </div>
                                                </div><br>
                                                <h1 class="mt-1 mb-3 info-box-title"><?php echo number_format($row['sum_criteria_expenses'],2); ?></h1>

                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col mt-0">
                                                        <h4 class="info-box-title">คงเหลือ</h4>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="col-pink info-icon">
                                                            <i class="fa fa-coffee pull-left card-icon font-30"></i>
                                                        </div>
                                                    </div>
                                                </div><br>
                                                <h1 class="mt-1 mb-3 info-box-title"><?php echo abs(number_format($row['percent_budget'],2)); ?>%</h1>
                                                <?php } ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-7">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header></header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <div class="card-body no-padding height-9">
                                    <div class="recent-report__chart">
                                        <div id="chartdiv"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end widget -->

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
                                    <div class="row">

                                        <table class="table table-striped table-bordered table-hover table-checkable order-column" id="example4">
                                            <thead>
                                                <tr>
                                                    <th></th>
                                                    <th> กลุ่มบัญชี </th>
                                                    <th> งบประมาณ 12 เดือน </th>
                                                    <th> งบสะสมปี 67 </th>
                                                    <th> ค่าใช้จ่ายปี 67 </th>
                                                    <th> คงเหลือ </th>
                                                    <th> % การใช้ </th>
                                                    <th> Progress </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                              $num = 0;
                                                        while($row = $result_table->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                <tr class="odd gradeX">
                                                    <td><?php echo $num; ?></td>
                                                    <td><a href="grouping.php?id=<?php echo $row['group']; ?>"><?php echo $row['group_name']; ?></a></td>
                                                    <td><?php echo number_format($row['sum_budget_12m'],2); ?></td>
                                                    <td><?php echo number_format($row['sum_budget_total'],2); ?></td>
                                                    <!--                                                <td><?php //echo number_format($row['sum_pay_savings'],2); ?></td>-->
                                                    <!--                                                <td><?php //echo number_format($row['sum_except'],2); ?></td>-->
                                                    <td><?php echo number_format($row['sum_criteria_expenses'],2); ?></td>
                                                    <td><?php echo number_format($row['balance'],2); ?></td>
                                                    <td><?php echo abs(number_format($row['percent_budget'],2)); ?></td>
                                                    <td>
                                                        <?php

                                                    $percentage = abs(number_format($row['percent_budget'], 2));

                                                    if ($percentage >= 0 && $percentage <= 25) {
                                                        $progressClass = 'progress-bar-success'; 
                                                    } elseif ($percentage > 25 && $percentage <= 50) {
                                                        $progressClass = 'progress-bar-yellow'; 
                                                    } elseif ($percentage > 50 && $percentage <= 75) {
                                                        $progressClass = 'progress-bar-warning'; 
                                                    } else {
                                                        $progressClass = 'progress-bar-danger'; 
                                                    }
                                                    ?>

                                                        <div class="progress">
                                                            <div class="progress-bar <?php echo $progressClass; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php echo abs(number_format($row['percent_budget'],2)); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo abs(number_format($row['percent_budget'],2)); ?>%;"> <span class="sr-only"><?php echo abs(number_format($row['percent_budget'],2)); ?>% Complete</span> </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end data table -->
                        <!-- add content here -->
                    </div>
                </div>
                <!-- end page content -->

                <!-- start footer -->

                <?php include_once('../includes/footer.php') ?>

                <!-- end footer -->
            </div>
        </div>
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

    <script>
        am5.ready(function() {
            var root = am5.Root.new("chartdiv");

            root.setThemes([am5themes_Animated.new(root)]);

            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                paddingLeft: 0,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout
            }));

            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50
                })
            );

            var data = <?php echo $new_json_data; ?>;

            var xRenderer = am5xy.AxisRendererX.new(root, {
                cellStartLocation: 0.1,
                cellEndLocation: 0.9,
                minorGridEnabled: true
            });

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "year",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            }));

            xRenderer.grid.template.setAll({
                location: 1
            });
            xAxis.data.setAll(data);

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    strokeOpacity: 0.1
                }),
                min: 0, // Set minimum value for better scaling
                extraMax: 0.1, // Add some extra space for better visualization
                numberFormat: "#a", // Format numbers with suffixes like "k" for thousand
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{valueY}"
                })
            }));

            function makeSeries(name, fieldName, color) {
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: fieldName,
                    categoryXField: "year",
                    fill: color,
                    stroke: color
                }));

                series.columns.template.setAll({
                    tooltipText: "{name}= {valueY}",
                    width: am5.percent(90),
                    tooltipY: 0,
                    strokeOpacity: 0,
                    fill: color, // Explicitly set the fill color
                    stroke: color // Explicitly set the stroke color
                });

                series.data.setAll(data);

                series.appear();

                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationY: 0,
                        sprite: am5.Label.new(root, {
                            text: "{valueY.formatNumber('#,###')}",
                            fill: root.interfaceColors.get("alternativeText"),
                            centerY: 0,
                            centerX: am5.p50,
                            populateText: true
                        })
                    });
                });

                legend.data.push(series);
            }

            makeSeries("งบประมาณ", "sum_budget_12m", am5.color(0x808080)); // Gray color for budget
            makeSeries("งบสะสม", "sum_budget_total", am5.color(0x1f77b4)); // Default blue color
            //makeSeries("จ่ายจริงสะสม", "pay_savings", am5.color(0xff7f0e));  // Default orange color
            makeSeries("จ่ายจริงสุทธิ", "sum_criteria_expenses", am5.color(0x2ca02c)); // Default green color

            chart.appear(1000, 100);
        });
    </script>

</body>

</html>
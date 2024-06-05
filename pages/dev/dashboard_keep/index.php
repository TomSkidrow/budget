<?php 
require_once('../includes/connect.php');
$sql = "
SELECT 
    a.node_description,
    a.total_criteria_expenses AS total_criteria_expenses_2023,
    b.total_budget_total AS total_budget_total_2024,
    b.total_criteria_expenses AS total_criteria_expenses_2024
FROM 
    aggregated_data_2023 a
JOIN 
    aggregated_data b ON a.profit_code = b.profit_code
WHERE 
    a.year = 2023
    AND b.year = 2024
GROUP BY 
    a.node_description
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

$jsonData = json_encode($data);

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
        height: 500px;
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
                                <div class="page-title">Dashboard</div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active">Dashboard</li>
                            </ol>
                        </div>
                    </div>
                    <!-- start widget -->
                    <div class="row">
                        <?php echo $jsonData; ?>
                        <div class="col-xl-12">
                            <div class="card card-box">
                                <div class="card-head">
                                    <header>การบริหารค่าใช้จ่าย CPI-X</header>
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
                                    <header>การบริหารค่าใช้จ่าย CPI-X</header>
                                    <div class="tools">
                                        <a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
                                        <a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
                                        <a class="t-close btn-color fa fa-times" href="javascript:;"></a>
                                    </div>
                                </div>
                                <!--                                <div class="card-body ">-->
                                <!--
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
-->
                                <!--
                                    <table class="table table-striped table-bordered table-hover table-checkable order-column" id="example4">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th> กลุ่มบัญชี </th>
                                                <th> งบประมาณ 12 เดือน </th>
                                                <th> งบสะสม </th>
-->
                                <!--                                                <th> จ่ายจริงสะสม </th>-->
                                <!--                                                <th> รวมค่ายกเว้น </th>-->
                                <!--
                                                <th> จ่ายจริงสุทธิ </th>
                                                <th> คงเหลือ </th>
                                                <th> % </th>
                                                <th> Progress </th>
                                            </tr>
                                        </thead>
                                        <tbody>
-->
                                <?php 
                                              //$num = 0;
                                                        //while($row = $result_table->fetch_assoc()){
                                                        //$num++;
                                            ?>
                                <!--
                                            <tr class="odd gradeX">
                                                <td><?php //echo $num; ?></td>
                                                <td><a href="grouping.php?id=<?php //echo $row['group']; ?>"><?php //echo $row['group_name']; ?></a></td>
                                                <td><?php //echo number_format($row['sum_budget'],2); ?></td>
                                                <td><?php //echo number_format($row['sum_savings'],2); ?></td>
-->
                                <!--                                                <td><?php //echo number_format($row['sum_pay_savings'],2); ?></td>-->
                                <!--                                                <td><?php //echo number_format($row['sum_except'],2); ?></td>-->
                                <!--
                                                <td><?php //echo number_format($row['pay'],2); ?></td>
                                                <td><?php //echo number_format($row['balance'],2); ?></td>
                                                <td><?php //echo abs(number_format($row['percent'],2)); ?></td>
                                                <td>
-->
                                <?php

                                                    //$percentage = abs(number_format($row['percent'], 2));

                                                    //if ($percentage >= 0 && $percentage <= 25) {
                                                    //    $progressClass = 'progress-bar-danger'; 
                                                    //} elseif ($percentage > 25 && $percentage <= 50) {
                                                    //    $progressClass = 'progress-bar-warning'; 
                                                    //} elseif ($percentage > 50 && $percentage <= 75) {
                                                    //    $progressClass = 'progress-bar-yellow'; 
                                                    //} else {
                                                    //    $progressClass = 'progress-bar-success'; 
                                                    //}
                                                    ?>

                                <!--
                                                    <div class="progress">
                                                        <div class="progress-bar <?php //echo $progressClass; ?> progress-bar-striped active" role="progressbar" aria-valuenow="<?php //echo abs(number_format($row['percent'],2)); ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php //echo abs(number_format($row['percent'],2)); ?>%;"> <span class="sr-only"><?php //echo abs(number_format($row['percent'],2)); ?>% Complete</span> </div>
-->
                                <!--
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php //} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
-->
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

                <!--
    <script>
        am5.ready(function() {


            // Create root element
            // https://www.amcharts.com/docs/v5/getting-started/#Root_element
            var root = am5.Root.new("chartdiv");


            // Set themes
            // https://www.amcharts.com/docs/v5/concepts/themes/
            root.setThemes([
                am5themes_Animated.new(root)
            ]);


            // Create chart
            // https://www.amcharts.com/docs/v5/charts/xy-chart/
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                paddingLeft: 0,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout
            }));


            // Add legend
            // https://www.amcharts.com/docs/v5/charts/xy-chart/legend-xy-series/
            var legend = chart.children.push(
                am5.Legend.new(root, {
                    centerX: am5.p50,
                    x: am5.p50
                })
            );

            var data = <?php //echo $new_json_data; ?>; // Embed PHP data into JavaScript

            // Create axes
            // https://www.amcharts.com/docs/v5/charts/xy-chart/axes/
            var xRenderer = am5xy.AxisRendererX.new(root, {
                cellStartLocation: 0.1,
                cellEndLocation: 0.9,
                minorGridEnabled: true
            })

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "year",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            }));

            xRenderer.grid.template.setAll({
                location: 1
            })

            xAxis.data.setAll(data);

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    strokeOpacity: 0.1
                })
            }));


            // Add series
            // https://www.amcharts.com/docs/v5/charts/xy-chart/series/
            function makeSeries(name, fieldName) {
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: fieldName,
                    categoryXField: "year"
                }));

                series.columns.template.setAll({
                    tooltipText: "{name}= {valueY}",
                    width: am5.percent(90),
                    tooltipY: 0,
                    strokeOpacity: 0
                });

                series.data.setAll(data);

                // Make stuff animate on load
                // https://www.amcharts.com/docs/v5/concepts/animations/
                series.appear();

                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationY: 0,
                        sprite: am5.Label.new(root, {
                            text: "{valueY}",
                            fill: root.interfaceColors.get("alternativeText"),
                            centerY: 0,
                            centerX: am5.p50,
                            populateText: true
                        })
                    });
                });

                legend.data.push(series);
            }

            makeSeries("งบประมาณ", "budget");
            makeSeries("งบสะสม", "savings");
            makeSeries("จ่ายจริงสะสม", "pay_saving");
            //makeSeries("ค่ายกเว้น", "except");
            makeSeries("จ่ายจริงสุทธิ", "pay");
            //makeSeries("ค่าใช้จ่ายตามเกณฑ์", "expenses");
            // Make stuff animate on load
            // https://www.amcharts.com/docs/v5/concepts/animations/
            chart.appear(1000, 100);

        }); // end am5.ready()
    </script>
-->

                <script>
                    // PHP data embedded as JSON
                    var chartData = <?php echo $jsonData; ?>;

                    am5.ready(function() {
                        var root = am5.Root.new("chartdiv");

                        root.setThemes([
                            am5themes_Animated.new(root)
                        ]);

                        var chart = root.container.children.push(am5xy.XYChart.new(root, {
                            panX: false,
                            panY: false,
                            wheelX: "panX",
                            wheelY: "zoomX",
                            paddingLeft: 0,
                            layout: root.verticalLayout
                        }));

                        var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                            categoryField: "node_description",
                            renderer: am5xy.AxisRendererY.new(root, {
                                cellStartLocation: 0.1,
                                cellEndLocation: 0.9
                            })
                        }));

                        yAxis.data.setAll(chartData);

                        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                            min: 0,
                            renderer: am5xy.AxisRendererX.new(root, {
                                minGridDistance: 70
                            })
                        }));

                        var series1 = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: "Total Criteria Expenses 2023",
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueXField: "total_criteria_expenses_2023",
                            categoryYField: "node_description",
                            sequencedInterpolation: true,
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                            })
                        }));

                        series1.columns.template.setAll({
                            height: am5.percent(70)
                        });

                        var series2 = chart.series.push(am5xy.ColumnSeries.new(root, {
                            name: "Total Criteria Expenses 2024",
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueXField: "total_criteria_expenses_2024",
                            categoryYField: "node_description",
                            sequencedInterpolation: true,
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                            })
                        }));

                        series2.columns.template.setAll({
                            height: am5.percent(70)
                        });

                        var series3 = chart.series.push(am5xy.LineSeries.new(root, {
                            name: "Total Budget Total 2024",
                            xAxis: xAxis,
                            yAxis: yAxis,
                            valueXField: "total_budget_total_2024",
                            categoryYField: "node_description",
                            sequencedInterpolation: true,
                            tooltip: am5.Tooltip.new(root, {
                                pointerOrientation: "horizontal",
                                labelText: "[bold]{name}[/]\n{categoryY}: {valueX}"
                            })
                        }));

                        series3.strokes.template.setAll({
                            strokeWidth: 2
                        });

                        series3.bullets.push(function() {
                            return am5.Bullet.new(root, {
                                locationY: 0.5,
                                sprite: am5.Circle.new(root, {
                                    radius: 5,
                                    stroke: series3.get("stroke"),
                                    strokeWidth: 2,
                                    fill: root.interfaceColors.get("background")
                                })
                            });
                        });

                        chart.data.setAll(chartData);

                        var legend = chart.children.push(am5.Legend.new(root, {
                            centerX: am5.p50,
                            x: am5.p50
                        }));

                        legend.data.setAll(chart.series.values);

                        chart.appear(1000, 100);
                    });

                </script>

</body>

</html>

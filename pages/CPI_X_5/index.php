<?php
require_once('../includes/connect.php');

$sql2 = "
SELECT
	b.pea_sname AS pea,
	a.total_criteria_expenses AS total_criteria_expenses_2023,
	b.total_budget_total AS total_budget_total_2024,
	b.total_criteria_expenses AS total_criteria_expenses_2024,
	b.total_budget_12m AS total_budget_12m_2024 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3011084',
		'E3016024',
		'E3021034',
		'E3021054',
		'E3021144',
		'E3021174',
		'E3022044',
		'E3022054',
		'E3023024',
		'E3023044',
		'E3031024',
		'E3031034',
		'E3031054',
		'E3031074',
		'E3031084',
		'E3032064',
		'E3033024',
		'E3041114',
		'E3042024',
		'E3042034',
		'E3043074',
		'E3045024',
		'E3046024',
		'E3051024',
		'E3051044',
		'E3051054',
		'E3051064',
		'E3052024',
		'E3052044',
		'E3053024',
		'E3061024',
		'E3061044',
		'E3061064',
		'E3061074',
		'E3061084',
		'E3061104',
		'E3061114',
		'E3062054',
		'E3063024',
		'E3071024',
		'E3071034',
		'E3071084',
		'E3072024',
		'E3072034',
		'E3072044',
		'E3081074',
		'E3081084',
		'E3081094',
		'E3081114',
		'E3081124',
		'E3081134',
		'E3091024',
		'E3092034',
		'E3092044',
		'E3092054',
		'E3101034',
		'E3101044',
		'E3101054',
		'E3101064',
		'E3101104',
		'E3102044',
		'E3111034',
		'E3111044',
		'E3111054',
		'E3112024',
		'E3121084',
		'E3121094',
		'E3122024',
		'E3122034',
		'E3131024',
		'E3131034',
		'E3131044',
		'E3132024',
		'E3132034',
		'E3141024',
		'E3141034',
		'E3141044' 
	) 
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
	b.pea_sname AS pea,
	a.total_criteria_expenses AS total_criteria_expenses_2023,
	b.total_budget_total AS total_budget_total_2024,
	b.total_criteria_expenses AS total_criteria_expenses_2024,
CASE
		
		WHEN b.total_budget_total = 0 THEN
		0 ELSE b.total_criteria_expenses / b.total_budget_total * 100 
	END AS percentage_expenses_2024,
	( ( b.total_criteria_expenses - a.total_criteria_expenses ) / a.total_criteria_expenses ) * 100 AS percentage_change 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3011084',
		'E3016024',
		'E3021034',
		'E3021054',
		'E3021144',
		'E3021174',
		'E3022044',
		'E3022054',
		'E3023024',
		'E3023044',
		'E3031024',
		'E3031034',
		'E3031054',
		'E3031074',
		'E3031084',
		'E3032064',
		'E3033024',
		'E3041114',
		'E3042024',
		'E3042034',
		'E3043074',
		'E3045024',
		'E3046024',
		'E3051024',
		'E3051044',
		'E3051054',
		'E3051064',
		'E3052024',
		'E3052044',
		'E3053024',
		'E3061024',
		'E3061044',
		'E3061064',
		'E3061074',
		'E3061084',
		'E3061104',
		'E3061114',
		'E3062054',
		'E3063024',
		'E3071024',
		'E3071034',
		'E3071084',
		'E3072024',
		'E3072034',
		'E3072044',
		'E3081074',
		'E3081084',
		'E3081094',
		'E3081114',
		'E3081124',
		'E3081134',
		'E3091024',
		'E3092034',
		'E3092044',
		'E3092054',
		'E3101034',
		'E3101044',
		'E3101054',
		'E3101064',
		'E3101104',
		'E3102044',
		'E3111034',
		'E3111044',
		'E3111054',
		'E3112024',
		'E3121084',
		'E3121094',
		'E3122024',
		'E3122034',
		'E3131024',
		'E3131034',
		'E3131044',
		'E3132024',
		'E3132034',
		'E3141024',
		'E3141034',
		'E3141044' 
	) 
GROUP BY
	a.profit_code 
ORDER BY
	a.profit_code ASC

";

$result2_2 = $conn->query( $sql2_2 );



$sql3 = "
SELECT
	b.pea_sname AS pea,
	a.total_criteria_expenses AS total_criteria_expenses_2023,
	b.total_budget_total AS total_budget_total_2024,
	b.total_criteria_expenses AS total_criteria_expenses_2024,
	b.total_budget_12m AS total_budget_12m_2024,
CASE
		
		WHEN b.total_budget_total = 0 THEN
		0 ELSE b.total_criteria_expenses / b.total_budget_total * 100 
	END AS percentage_expenses_2024,
	( ( b.total_criteria_expenses - a.total_criteria_expenses ) / a.total_criteria_expenses ) * 100 AS percentage_change 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3011084',
		'E3016024',
		'E3021034',
		'E3021054',
		'E3021144',
		'E3021174',
		'E3022044',
		'E3022054',
		'E3023024',
		'E3023044',
		'E3031024',
		'E3031034',
		'E3031054',
		'E3031074',
		'E3031084',
		'E3032064',
		'E3033024',
		'E3041114',
		'E3042024',
		'E3042034',
		'E3043074',
		'E3045024',
		'E3046024',
		'E3051024',
		'E3051044',
		'E3051054',
		'E3051064',
		'E3052024',
		'E3052044',
		'E3053024',
		'E3061024',
		'E3061044',
		'E3061064',
		'E3061074',
		'E3061084',
		'E3061104',
		'E3061114',
		'E3062054',
		'E3063024',
		'E3071024',
		'E3071034',
		'E3071084',
		'E3072024',
		'E3072034',
		'E3072044',
		'E3081074',
		'E3081084',
		'E3081094',
		'E3081114',
		'E3081124',
		'E3081134',
		'E3091024',
		'E3092034',
		'E3092044',
		'E3092054',
		'E3101034',
		'E3101044',
		'E3101054',
		'E3101064',
		'E3101104',
		'E3102044',
		'E3111034',
		'E3111044',
		'E3111054',
		'E3112024',
		'E3121084',
		'E3121094',
		'E3122024',
		'E3122034',
		'E3131024',
		'E3131034',
		'E3131044',
		'E3132024',
		'E3132034',
		'E3141024',
		'E3141034',
		'E3141044' 
	) 
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
	b.pea_sname AS pea,
	a.total_criteria_expenses AS total_criteria_expenses_2023,
	b.total_budget_total AS total_budget_total_2024,
	b.total_criteria_expenses AS total_criteria_expenses_2024,
CASE
		
		WHEN b.total_budget_total = 0 THEN
		0 ELSE b.total_criteria_expenses / b.total_budget_total * 100 
	END AS percentage_expenses_2024,
	( ( b.total_criteria_expenses - a.total_criteria_expenses ) / a.total_criteria_expenses ) * 100 AS percentage_change 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3011084',
		'E3016024',
		'E3021034',
		'E3021054',
		'E3021144',
		'E3021174',
		'E3022044',
		'E3022054',
		'E3023024',
		'E3023044',
		'E3031024',
		'E3031034',
		'E3031054',
		'E3031074',
		'E3031084',
		'E3032064',
		'E3033024',
		'E3041114',
		'E3042024',
		'E3042034',
		'E3043074',
		'E3045024',
		'E3046024',
		'E3051024',
		'E3051044',
		'E3051054',
		'E3051064',
		'E3052024',
		'E3052044',
		'E3053024',
		'E3061024',
		'E3061044',
		'E3061064',
		'E3061074',
		'E3061084',
		'E3061104',
		'E3061114',
		'E3062054',
		'E3063024',
		'E3071024',
		'E3071034',
		'E3071084',
		'E3072024',
		'E3072034',
		'E3072044',
		'E3081074',
		'E3081084',
		'E3081094',
		'E3081114',
		'E3081124',
		'E3081134',
		'E3091024',
		'E3092034',
		'E3092044',
		'E3092054',
		'E3101034',
		'E3101044',
		'E3101054',
		'E3101064',
		'E3101104',
		'E3102044',
		'E3111034',
		'E3111044',
		'E3111054',
		'E3112024',
		'E3121084',
		'E3121094',
		'E3122024',
		'E3122034',
		'E3131024',
		'E3131034',
		'E3131044',
		'E3132024',
		'E3132034',
		'E3141024',
		'E3141034',
		'E3141044' 
	) 
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
<style>
    #chart1,
    #chart2 {
        width: 100%;
        margin: 35px auto;
    }

    .apexcharts-tooltip-title {
        /*            display: none;*/
    }

    #chart1 .apexcharts-tooltip,
    #chart2 .apexcharts-tooltip {
        display: flex;
        border: 0;
        box-shadow: none;
    }

    #chart1 .apexcharts-text,
    #chart2 .apexcharts-text {
        font-family: 'Prompt', sans-serif !important;
    }

</style>

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
                                <div class="page-title"></div>
                            </div>
                            <ol class="breadcrumb page-breadcrumb pull-right">
                                <li><i class="fa fa-home"></i>&nbsp;
                                    <a class="parent-item" href="../dashboard1">Home</a>&nbsp;
                                    <i class="fa fa-angle-right"></i>
                                </li>
                                <li class="active"><a href="#" id="goBack">กลับไปหน้าที่แล้ว</a></li>
                            </ol>
                        </div>
                    </div>
                    <!-- add content here -->

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card-box">
                                <div class="card-head right">
                                    <header></header>
                                </div>
                                <div class="card-body ">
                                    <div class="mdl-tabs mdl-js-tabs">
                                        <div class="mdl-tabs__tab-bar tab-left-side">
                                            <a href="#tab4-panel" class="mdl-tabs__tab tabs_three is-active">กฟส. ขนาด XS ทุกแห่ง</a>
                                            <a href="#tab5-panel" class="mdl-tabs__tab tabs_three">เรียงลำดับ</a>

                                        </div>
                                        <div class="mdl-tabs__panel is-active p-t-20" id="tab4-panel">

                                            <!-- start Apex Chart -->
                                            <div id="chart1"></div>
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

                                                            <table class="table table-striped table-bordered table-hover table-checkable order-column" id="table1">
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
                                        <div class="mdl-tabs__panel p-t-20" id="tab5-panel">
                                            <!-- start Apex Chart -->
                                            <div id="chart2"></div>
                                            <!-- end Apex Chart -->
                                            <!-- start data table -->
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

   
   	<script>
		document.getElementById("goBack").addEventListener("click", function(event) {
			event.preventDefault();
			window.history.back();
		});
	</script>
   
    <!-- Start script datatable -->

    <script>
        $("#table1").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });
        $("#table2").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });

    </script>

    <!-- Start ApexChart -->
    <script>
        var jsonData2 = <?php echo $jsonData2; ?>;
        var jsonData3 = <?php echo $jsonData3; ?>;

        var options1 = {
            series: [{
                name: 'ค่าใช้จ่าย ปี67',
                type: 'column',
                data: jsonData2.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'ค่าใช้จ่าย ปี66',
                type: 'area',
                data: jsonData2.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'งบสะสมปี67',
                type: 'line',
                data: jsonData2.map(item => item.total_budget_total_2024)
            }, {
                name: 'งบประมาณปี67',
                type: 'line',
                data: jsonData2.map(item => item.total_budget_12m_2024)
            }],
            chart: {
                height: 600,
                type: 'line',
                stacked: false
            },
            stroke: {
                width: [1, 1, 3, 3],
                //width: 1, // Set a consistent line width
                curve: 'smooth'
            },
            plotOptions: {
                column: {
                    columnWidth: '30%' // Adjust the column width to make it slim
                }
            },
            markers: {
                size: 4, // Decrease marker size
                strokeWidth: 0 // Remove marker border
            },
            fill: {
                opacity: [0.85, 0.25, 1, 0.85], // Set the opacity for each series
                gradient: {
                    inverseColors: false,
                    shade: 'light',
                    type: 'vertical',
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            },
            labels: jsonData2.map(item => item.pea),
            xaxis: {
                categories: jsonData2.map(item => item.pea),
            },
            yaxis: {
                title: {
                    text: 'ล้านบาท',
                },
                labels: {
                    formatter: function(val) {
                        return (val / 1000000).toLocaleString('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        }); // Format y-axis values in million scale without decimal places
                    }
                },
                min: 0
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(y) {
                        if (typeof y !== "undefined") {
                            return y.toLocaleString() + ' บาท';
                        }
                        return y;
                    }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0], // Enable data labels only for the first series
                formatter: function(val) {
                    return (val / 1000000).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }); // Format data label values
                }
            }
        };

        var options2 = {
            series: [{
                name: 'ค่าใช้จ่าย ปี67',
                type: 'column',
                data: jsonData3.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'ค่าใช้จ่าย ปี66',
                type: 'area',
                data: jsonData3.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'งบสะสมปี67',
                type: 'line',
                data: jsonData3.map(item => item.total_budget_total_2024)
            }, {
                name: 'งบประมาณปี67',
                type: 'line',
                data: jsonData3.map(item => item.total_budget_12m_2024)
            }],
            chart: {
                height: 600,
                type: 'line',
                stacked: false
            },
            stroke: {
                width: [1, 1, 3, 3],
                //width: 1, // Set a consistent line width
                curve: 'smooth'
            },
            plotOptions: {
                column: {
                    columnWidth: '30%' // Adjust the column width to make it slim
                }
            },
            markers: {
                size: 4, // Decrease marker size
                strokeWidth: 0 // Remove marker border
            },
            fill: {
                opacity: [0.85, 0.25, 1, 0.85], // Set the opacity for each series
                gradient: {
                    inverseColors: false,
                    shade: 'light',
                    type: 'vertical',
                    opacityFrom: 0.85,
                    opacityTo: 0.85,
                    stops: [0, 100]
                }
            },
            labels: jsonData3.map(item => item.pea),
            xaxis: {
                categories: jsonData3.map(item => item.pea),
            },
            yaxis: {
                title: {
                    text: 'ล้านบาท',
                },
                labels: {
                    formatter: function(val) {
                        return (val / 1000000).toLocaleString('en-US', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1
                        }); // Format y-axis values in million scale without decimal places
                    }
                },
                min: 0
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function(y) {
                        if (typeof y !== "undefined") {
                            return y.toLocaleString() + ' บาท';
                        }
                        return y;
                    }
                }
            },
            dataLabels: {
                enabled: true,
                enabledOnSeries: [0], // Enable data labels only for the first series
                formatter: function(val) {
                    return (val / 1000000).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }); // Format data label values
                }
            }
        };


        var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
        chart1.render();
        var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
        chart2.render();

    </script>


</body>

</html>

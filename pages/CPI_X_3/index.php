<?php
require_once('../includes/connect.php');

$latestYearMonthQuery = "
SELECT
	MAX( `year` ) AS latest_year,
	MAX( `month` ) AS latest_month 
FROM
	total_result 
WHERE
	`year` = ( SELECT MAX( `year` ) FROM total_result )
";

$previousYearMonthQuery = "
SELECT
	( SELECT MAX( `year` ) - 1 FROM total_result ) AS prev_year,
	( SELECT latest_month FROM ( $latestYearMonthQuery ) AS LatestYearMonth ) AS prev_month
";

try {
    
    $stmtLatest = $conn->query($latestYearMonthQuery);
    $stmtPrevious = $conn->query($previousYearMonthQuery);


    $lastRecord = $stmtLatest->fetch_assoc();

 
    $firstRecord = $stmtPrevious->fetch_assoc();

   
    $mergedRecords = array_merge($lastRecord, $firstRecord);

   
    if (!empty($mergedRecords)) {
        
        $latestYear = $mergedRecords['latest_year'];
        $latestMonth = $mergedRecords['latest_month'];
        $prevYear = $mergedRecords['prev_year'];
        $prevMonth = $mergedRecords['prev_month'];

    } else {
        echo "No records found.";
    }
} catch (Exception $e) {
    echo 'Query failed: ' . $e->getMessage();
}


$sql1 = "
SELECT
	SUBSTR( a.node_description, 6, 30 ) AS pea,
	SUM( a.total_criteria_expenses ) AS total_criteria_expenses_2023,
	SUM( b.total_criteria_expenses ) AS total_criteria_expenses_2024,
	SUM( b.total_budget_total ) AS total_budget_total_2024,
	SUM( b.total_budget_12m ) AS total_budget_12m_2024 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.level4 IN (
		'E3015',
		'E3016',
		'E3022',
		'E3023',
		'E3024',
		'E3032',
		'E3033',
		'E3042',
		'E3043',
		'E3044',
		'E3045',
		'E3046',
		'E3047',
		'E3052',
		'E3053',
		'E3062',
		'E3063',
		'E3064',
		'E3072',
		'E3083',
		'E3092',
		'E3102',
		'E3112',
		'E3122',
		'E3123',
		'E3132',
		'E3142' 
	) 
GROUP BY
	a.node_description 
ORDER BY
	a.profit_code ASC

";

$result1 = $conn->query( $sql1 );

$data = array();
if ( $result1->num_rows > 0 ) {
    while( $row = $result1->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData1 = json_encode( $data );


$sql1_2 = "
SELECT
	SUBSTR( a.node_description, 6, 30 ) AS pea,
	SUM( a.total_criteria_expenses/1000000 ) AS total_criteria_expenses_2023,
	SUM( b.total_budget_total/1000000 ) AS total_budget_total_2024,
	SUM( b.total_criteria_expenses/1000000 ) AS total_criteria_expenses_2024,
CASE
		
		WHEN SUM( b.total_budget_total ) = 0 THEN
		0 ELSE SUM( b.total_criteria_expenses ) / SUM( b.total_budget_total ) * 100 
	END AS percentage_expenses_2024,
	( SUM( b.total_criteria_expenses - a.total_criteria_expenses ) / SUM( a.total_criteria_expenses ) ) * 100 AS percentage_change 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.level4 IN (
		'E3015',
		'E3016',
		'E3022',
		'E3023',
		'E3024',
		'E3032',
		'E3033',
		'E3042',
		'E3043',
		'E3044',
		'E3045',
		'E3046',
		'E3047',
		'E3052',
		'E3053',
		'E3062',
		'E3063',
		'E3064',
		'E3072',
		'E3083',
		'E3092',
		'E3102',
		'E3112',
		'E3122',
		'E3123',
		'E3132',
		'E3142' 
	) 
GROUP BY
	a.node_description 
ORDER BY
	a.profit_code ASC

";

$result1_2 = $conn->query( $sql1_2 );



$sql2 = "
SELECT
	a.pea_sname AS pea,
	a.total_criteria_expenses AS total_criteria_expenses_2023,
	b.total_budget_total AS total_budget_total_2024,
	b.total_criteria_expenses AS total_criteria_expenses_2024,
	b.total_budget_12m AS total_budget_12m_2024 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
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
	a.pea_sname AS pea,
	a.total_criteria_expenses/1000000 AS total_criteria_expenses_2023,
	b.total_budget_total/1000000 AS total_budget_total_2024,
	b.total_criteria_expenses/1000000 AS total_criteria_expenses_2024,
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
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
	) 
GROUP BY
	a.profit_code 
ORDER BY
	a.profit_code ASC

";

$result2_2 = $conn->query( $sql2_2 );



$sql3 = "
SELECT
	a.pea_sname AS pea,
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
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
	) 
GROUP BY
	a.profit_code 
ORDER BY
	percentage_change ASC
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
	a.pea_sname AS pea,
	a.total_criteria_expenses/1000000 AS total_criteria_expenses_2023,
	b.total_budget_total/1000000 AS total_budget_total_2024,
	b.total_criteria_expenses/1000000 AS total_criteria_expenses_2024,
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
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
	) 
GROUP BY
	a.profit_code 
ORDER BY
	percentage_change ASC
";

$result3_2 = $conn->query( $sql3_2 );
?>
<?php
    function convertToThaiMonth($monthNumber)
    {
        $thaiMonths = [1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.', 5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.', 9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'];

        return isset($thaiMonths[$monthNumber]) ? $thaiMonths[$monthNumber] : 'ข้อมูลเดือนไม่ถูกต้อง';
    }
    function convertToThaiYear($ThaiYear) {
        return $ThaiYear + 543;
    }

    function formatValue($value) {
        if ($value < 0) {
            $formattedValue = number_format($value, 2);
            return '<span class="negative">' . $formattedValue . '</span>';
        } else if ($value > 0) {
            return '<span class="green">' . number_format($value, 2) . '</span>';
        } else {
            return number_format($value, 2);
        }
    }

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
    <!--    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />-->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
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
    <style>
        body {
            font-family: 'Prompt', sans-serif;
        }

    </style>
</head>
<!-- END HEAD -->
<style>
    #chart1,
    #chart2,
    #chart3 {
        width: 100%;
        margin: 35px auto;
    }

    .apexcharts-tooltip-title {
        /*            display: none;*/
    }

    #chart1 .apexcharts-tooltip,
    #chart2 .apexcharts-tooltip,
    #chart3 .apexcharts-tooltip {
        display: flex;
        border: 0;
        box-shadow: none;
    }

    #chart1 .apexcharts-text,
    #chart2 .apexcharts-text,
    #chart3 .apexcharts-text {
        font-family: 'Prompt', sans-serif !important;
    }
	/* Add border style for the table */
	.mdl-tabs__panel#tab4-panel table {
		border-collapse: collapse;
		width: 100%;
	}

	/* Add border style for table header cells */
	.mdl-tabs__panel#tab4-panel th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Add border style for table body cells */
	.mdl-tabs__panel#tab4-panel td {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Remove border from first table row */
	.mdl-tabs__panel#tab4-panel tr:first-child {
		border-top: none;
	}

	/* Remove border from last table row */
	.mdl-tabs__panel#tab4-panel tr:last-child {
		border-bottom: none;
	}

	.mdl-tabs__panel#tab5-panel table {
		border-collapse: collapse;
		width: 100%;
	}

	/* Add border style for table header cells */
	.mdl-tabs__panel#tab5-panel th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Add border style for table body cells */
	.mdl-tabs__panel#tab5-panel td {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Remove border from first table row */
	.mdl-tabs__panel#tab5-panel tr:first-child {
		border-top: none;
	}

	/* Remove border from last table row */
	.mdl-tabs__panel#tab5-panel tr:last-child {
		border-bottom: none;
	}

	/* Add border style for the table */
	.mdl-tabs__panel#tab6-panel table {
		border-collapse: collapse;
		width: 100%;
	}

	/* Add border style for table header cells */
	.mdl-tabs__panel#tab6-panel th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Add border style for table body cells */
	.mdl-tabs__panel#tab6-panel td {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Remove border from first table row */
	.mdl-tabs__panel#tab6-panel tr:first-child {
		border-top: none;
	}

	/* Remove border from last table row */
	.mdl-tabs__panel#tab6-panel tr:last-child {
		border-bottom: none;
	}
	

	.negative {
		color: red;
		font-weight: bold;
	}

	.green {
		color: #4c967d;
		font-weight: bold;
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
                                    <a class="parent-item" href="../dashboard/">Home</a>&nbsp;
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
                                <div class="card-head">
                                    <header></header>
                                </div>
                                <div class="card-body ">
                                    <div class="mdl-tabs mdl-js-tabs">
                                        <div class="mdl-tabs__tab-bar tab-left-side">
                                            <a href="#tab4-panel" class="mdl-tabs__tab tabs_three is-active">กฟส.ขนาด S รวมในสังกัด</a>
                                            <a href="#tab5-panel" class="mdl-tabs__tab tabs_three">กฟส.ขนาด S</a>
                                            <a href="#tab6-panel" class="mdl-tabs__tab tabs_three">เรียงลำดับ</a>
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
																		<th style="text-align: center;">ลำดับ</th>
																		<th>หน่วยงาน</th>
																		<th style="text-align: center;"> งบประมาณสะสม <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($prevYear); ?> </th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($latestYear); ?> </th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ งบสะสมฯ ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($prevYear); ?></th>
																	</tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                              $num = 0;
                                                        while($row = $result1_2->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                                    <tr class="odd gradeX">
																		<td style="text-align: center;"><?php echo $num; ?></td>

																		<td><?php echo $row['pea']; ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_budget_total_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2023'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_change'],2)); ?></td>

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
																		<th style="text-align: center;">ลำดับ</th>
																		<th>หน่วยงาน</th>
																		<th style="text-align: center;"> งบประมาณสะสม <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($prevYear); ?> </th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($latestYear); ?> </th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ งบสะสมฯ ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($prevYear); ?></th>
																	</tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                              $num = 0;
                                                        while($row = $result2_2->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                                    <tr class="odd gradeX">
 																		<td style="text-align: center;"><?php echo $num; ?></td>

																		<td><?php echo $row['pea']; ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_budget_total_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2023'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_change'],2)); ?></td>

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
																		<th style="text-align: center;">ลำดับ</th>
																		<th>หน่วยงาน</th>
																		<th style="text-align: center;"> งบประมาณสะสม <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($prevYear); ?> </th>
																		<th style="text-align: center;"> เบิกจ่ายสุทธิ <br>ปี <?php echo convertToThaiYear($latestYear); ?> </th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ งบสะสมฯ ปี <?php echo convertToThaiYear($latestYear); ?></th>
																		<th style="text-align: center;"> % เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($latestYear); ?><br>เปรียบเทียบ เบิกจ่ายสุทธิ ปี <?php echo convertToThaiYear($prevYear); ?></th>
																	</tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php 
                                              $num = 0;
                                                        while($row = $result3_2->fetch_assoc()){
                                                        $num++;
                                            ?>
                                                                    <tr class="odd gradeX">
																		<td style="text-align: center;"><?php echo $num; ?></td>

																		<td><?php echo $row['pea']; ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_budget_total_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2023'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_expenses_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_change'],2)); ?></td>

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
		document.getElementById("goBack").addEventListener("click", function(event) {
			event.preventDefault();
			window.history.back();
		});
	</script>
   
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
        $("#table3").DataTable({
            columnDefs: [{
                width: 10,
                targets: 0
            }],
        });

    </script>
    <!-- Start ApexChart -->
    <script>
        var jsonData1 = <?php echo $jsonData1; ?>;
        var jsonData2 = <?php echo $jsonData2; ?>;
        var jsonData3 = <?php echo $jsonData3; ?>;

        var options1 = {
            series: [{
                name: 'เบิกจ่ายสุทธิ ปี67',
                type: 'column',
                data: jsonData1.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'เบิกจ่ายสุทธิ ปี66',
                type: 'area',
                data: jsonData1.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'งบสะสม ปี67',
                type: 'line',
                data: jsonData1.map(item => item.total_budget_total_2024)
            }, {
                name: 'งบประมาณ ปี67',
                type: 'line',
                data: jsonData1.map(item => item.total_budget_12m_2024)
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
            labels: jsonData1.map(item => item.pea),
            xaxis: {
                categories: jsonData1.map(item => item.pea),
            },
    yaxis: {
        title: {
            text: 'ล้านบาท',
            style: {
                fontSize: '16px', // Increase the size of the y-axis title
                fontWeight: 'bold'
            }
        },
								

labels: {
            formatter: function(val) {
                return (val / 1000000).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            },
            style: {
                fontSize: '14px', // Increase the size of the y-axis labels
                fontWeight: 'bold'
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
                name: 'เบิกจ่ายสุทธิ ปี67',
                type: 'column',
                data: jsonData2.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'เบิกจ่ายสุทธิ ปี66',
                type: 'area',
                data: jsonData2.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'งบสะสม ปี67',
                type: 'line',
                data: jsonData2.map(item => item.total_budget_total_2024)
            }, {
                name: 'งบประมาณ ปี67',
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
            style: {
                fontSize: '16px', // Increase the size of the y-axis title
                fontWeight: 'bold'
            }
        },
								

labels: {
            formatter: function(val) {
                return (val / 1000000).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            },
            style: {
                fontSize: '14px', // Increase the size of the y-axis labels
                fontWeight: 'bold'
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

        var options3 = {
            series: [{
                name: 'เบิกจ่ายสุทธิ ปี67',
                type: 'column',
                data: jsonData3.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'เบิกจ่ายสุทธิ ปี66',
                type: 'area',
                data: jsonData3.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'งบสะสม ปี67',
                type: 'line',
                data: jsonData3.map(item => item.total_budget_total_2024)
            }, {
                name: 'งบประมาณ ปี67',
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
            style: {
                fontSize: '16px', // Increase the size of the y-axis title
                fontWeight: 'bold'
            }
        },
								

labels: {
            formatter: function(val) {
                return (val / 1000000).toLocaleString('en-US', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0
                });
            },
            style: {
                fontSize: '14px', // Increase the size of the y-axis labels
                fontWeight: 'bold'
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
        var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
        chart3.render();

    </script>

</body>

</html>

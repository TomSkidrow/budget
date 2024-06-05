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
	SUBSTR( a.node_description, 6, 30 ) AS node_description,
	SUM( a.total_criteria_expenses ) AS total_criteria_expenses_2023,
	SUM( b.total_criteria_expenses ) AS total_criteria_expenses_2024,
	SUM( b.total_budget_total ) AS total_budget_total_2024,
	SUM( b.total_budget_12m ) AS total_budget_12m_2024 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.level4 IN (
		'E3011',
		'E3021',
		'E3031',
		'E3041',
		'E3051',
		'E3061',
		'E3071',
		'E3081',
		'E3091',
		'E3101',
		'E3111',
		'E3121',
		'E3131',
		'E3141',
		'E3151' 
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
	SUBSTR( a.node_description, 6, 30 ) AS node_description,
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
		'E3011',
		'E3021',
		'E3031',
		'E3041',
		'E3051',
		'E3061',
		'E3071',
		'E3081',
		'E3091',
		'E3101',
		'E3111',
		'E3121',
		'E3131',
		'E3141',
		'E3151' 
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
	b.total_criteria_expenses AS total_criteria_expenses_2024,
	b.total_budget_total AS total_budget_total_2024,
	b.total_budget_12m AS total_budget_12m_2024 
FROM
	aggregated_data_2023 a
	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
WHERE
	a.profit_code IN (
		'E3011011',
		'E3021011',
		'E3031011',
		'E3041011',
		'E3051011',
		'E3061011',
		'E3071011',
		'E3081011',
		'E3091012',
		'E3101012',
		'E3111012',
		'E3121012',
		'E3131010',
		'E3141010',
		'E3151010' 
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
	b.total_budget_total/10000000 AS total_budget_total_2024,
	b.total_criteria_expenses/10000000 AS total_criteria_expenses_2024,
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
		'E3011011',
		'E3021011',
		'E3031011',
		'E3041011',
		'E3051011',
		'E3061011',
		'E3071011',
		'E3081011',
		'E3091012',
		'E3101012',
		'E3111012',
		'E3121012',
		'E3131010',
		'E3141010',
		'E3151010' 
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
	b.total_criteria_expenses AS total_criteria_expenses_2024,
	b.total_budget_total AS total_budget_total_2024,
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
		'E3011011',
		'E3021011',
		'E3031011',
		'E3041011',
		'E3051011',
		'E3061011',
		'E3071011',
		'E3081011',
		'E3091012',
		'E3101012',
		'E3111012',
		'E3121012',
		'E3131010',
		'E3141010',
		'E3151010' 
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


//$sql3_2 = "
//SELECT
//	a.pea_sname AS pea,
//	a.total_criteria_expenses AS total_criteria_expenses_2023,
//	b.total_budget_total AS total_budget_total_2024,
//	b.total_criteria_expenses AS total_criteria_expenses_2024,
//CASE
//		
//		WHEN b.total_budget_total = 0 THEN
//		0 ELSE b.total_criteria_expenses / b.total_budget_total * 100 
//	END AS percentage_expenses_2024,
//	( ( b.total_criteria_expenses - a.total_criteria_expenses ) / a.total_criteria_expenses ) * 100 AS percentage_change 
//FROM
//	aggregated_data_2023 a
//	JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
//WHERE
//	a.profit_code IN (
//		'E3011011',
//		'E3021011',
//		'E3031011',
//		'E3041011',
//		'E3051011',
//		'E3061011',
//		'E3071011',
//		'E3081011',
//		'E3091012',
//		'E3101012',
//		'E3111012',
//		'E3121012',
//		'E3131010',
//		'E3141010',
//		'E3151010' 
//	) 
//GROUP BY
//	a.profit_code 
//ORDER BY
//	percentage_change ASC
//";
//
//$result3_2 = $conn->query( $sql3_2 );

$sql_sum = "
SELECT
CASE
		
	WHEN
		a.profit_code = 'E3011011' THEN
			'กฟส.ขนาด(L,M)รวมสังกัด' ELSE a.profit_code 
			END AS pea,
		SUM( a.total_criteria_expenses ) AS total_criteria_expenses_2023,
		SUM( b.total_budget_total ) AS total_budget_total_2024,
		SUM( b.total_criteria_expenses ) AS total_criteria_expenses_2024,
		SUM( b.total_budget_12m ) AS total_budget_12m_2024,
	CASE
			
			WHEN SUM( b.total_budget_total ) = 0 THEN
			0 ELSE SUM( b.total_criteria_expenses ) / SUM( b.total_budget_total ) * 100 
		END AS percentage_expenses_2024,
		( ( SUM( b.total_criteria_expenses ) - SUM( a.total_criteria_expenses ) ) / SUM( a.total_criteria_expenses ) ) * 100 AS percentage_change 
	FROM
		aggregated_data_2023 a
		JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
	WHERE
		a.level4 IN (
			'E3011',
			'E3021',
			'E3031',
			'E3041',
			'E3051',
			'E3061',
			'E3071',
			'E3081',
			'E3091',
			'E3101',
			'E3111',
			'E3121',
			'E3131',
			'E3141',
		    'E3151' 
	)

";
$result_sum = $conn->query($sql_sum);

$data = array();
if ( $result_sum->num_rows > 0 ) {
    while( $row = $result_sum->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData4 = json_encode( $data );

$sql_sum2 = "
SELECT
CASE
		
	WHEN
		a.profit_code = 'E3011011' THEN
			'กฟส.ขนาด(L,M)' ELSE a.profit_code 
			END AS pea,
		SUM( a.total_criteria_expenses ) AS total_criteria_expenses_2023,
		SUM( b.total_budget_total ) AS total_budget_total_2024,
		SUM( b.total_criteria_expenses ) AS total_criteria_expenses_2024,
		SUM( b.total_budget_12m ) AS total_budget_12m_2024,
	CASE
			
			WHEN SUM( b.total_budget_total ) = 0 THEN
			0 ELSE SUM( b.total_criteria_expenses ) / SUM( b.total_budget_total ) * 100 
		END AS percentage_expenses_2024,
		( ( SUM( b.total_criteria_expenses ) - SUM( a.total_criteria_expenses ) ) / SUM( a.total_criteria_expenses ) ) * 100 AS percentage_change 
	FROM
		aggregated_data_2023 a
		JOIN aggregated_data_2024 b ON a.profit_code = b.profit_code 
	WHERE
		a.profit_code IN (
		'E3011011',
		'E3021011',
		'E3031011',
		'E3041011',
		'E3051011',
		'E3061011',
		'E3071011',
		'E3081011',
		'E3091012',
		'E3101012',
		'E3111012',
		'E3121012',
		'E3131010',
		'E3141010',
		'E3151010' 
	) 

";
$result_sum2 = $conn->query($sql_sum2);

$data = array();
if ( $result_sum2->num_rows > 0 ) {
    while( $row = $result_sum2->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData5 = json_encode( $data );
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
//    function formatValue($value) {
//        if ($value < 0) {
//            $formattedValue = '(' . number_format(abs($value), 2) . ')';
//            return '<span class="negative">' . $formattedValue . '</span>';
//        } else {
//            return number_format($value, 2);
//        }
//    }
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
	<link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen" />
	<link rel="stylesheet" href="../../assets/plugins/flatpicker/css/flatpickr.min.css" />
	<link href="../../assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="../../assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
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
	<!-- Style ApexChart in Pages -->



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
	#chart3,
	#chart4,
	#chart5 {
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
	#chart3 .apexcharts-text,
	#chart4 .apexcharts-text,
	#chart5 .apexcharts-text {
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
									<a class="parent-item" href="../dashboard">Home</a>&nbsp;
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
											<a href="#tab4-panel" class="mdl-tabs__tab tabs_three is-active">กฟส.ขนาด L,M รวมในสังกัด</a>
											<a href="#tab5-panel" class="mdl-tabs__tab tabs_three">กฟส.ขนาด L,M</a>
											<a href="#tab6-panel" class="mdl-tabs__tab tabs_three">เรียงลำดับ</a>
										</div>
										<div class="mdl-tabs__panel is-active p-t-20" id="tab4-panel">

											<!-- start Apex Chart -->
											<div id="chart1"></div>
											<!-- end Apex Chart -->

											<!-- start data table -->
											<div class="row">
												<div class="col-xl-8 col-md-8 col-12">
													<div class="card card-topline-red">
														<div class="card-head">
															<header>หน่วย : ล้านบาท</header>
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
while ($row = $result1_2->fetch_assoc())
{
    $num++;

?>

																	<tr class="odd gradeX">
																		<td style="text-align: center;"><?php echo $num; ?></td>

																		<td><?php echo $row['node_description']; ?></td>
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

											
											<!-- end data table -->
											
												<div class="col-xl-4 col-md-4 col-12">
													<div class="card card-box">
														<div class="card-head">
															<header></header>
															<div class="tools">
																<a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
																<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
																<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
															</div>
														</div>
														<div class="card-body ">

															<!-- start Apex Chart -->
															<div id="chart4"></div>
															<!-- end Apex Chart -->
														</div>
													</div>
												</div>
											
										</div>
									</div>
									<!-- end tab4-panel -->
									
										<div class="mdl-tabs__panel p-t-20" id="tab5-panel">
											<!-- start Apex Chart -->
											<div id="chart2"></div>
											<!-- end Apex Chart -->
											<!-- start data table -->
											<div class="row">
												<div class="col-xl-8 col-md-8 col-12">
													<div class="card card-topline-red">
														<div class="card-head">
															<header>หน่วย : ล้านบาท</header>
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
while ($row = $result2_2->fetch_assoc())
{
    $num++;
?>

																	<tr class="odd gradeX">
																		<td style="text-align: center;"><?php echo $num; ?></td>

																		<td><?php echo $row['pea']; ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_budget_total_2024'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2024'],2)); ?></td>

																		<td style="text-align: right;"><?php echo formatValue(number_format($row['total_criteria_expenses_2023'],2)); ?></td>
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_expenses_2024'],2)); ?> </td >
																		<td style="text-align: right;"><?php echo formatValue(number_format($row['percentage_change'],2)); ?> </td>

																	</tr>
																	<?php } ?>
																</tbody>
															</table>
														</div>
													</div>
												</div>
												<div class="col-xl-4 col-md-4 col-12">
													<div class="card card-box">
														<div class="card-head">
															<header></header>
															<div class="tools">
																<a class="fa fa-repeat btn-color box-refresh" href="javascript:;" onclick="window.location.reload()"></a>
																<a class="t-collapse btn-color fa fa-chevron-down" href="javascript:;"></a>
																<a class="t-close btn-color fa fa-times" href="javascript:;"></a>
															</div>
														</div>
														<div class="card-body ">

															<!-- start Apex Chart -->
															<div id="chart5"></div>
															<!-- end Apex Chart -->
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
<!--
											<div class="row">
												<div class="col-md-12">
													<div class="card card-box">
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
																		<th> งบสะสม </th>
																		<th> ค่าใช้จ่ายปี67 </th>
																		<th> ค่าใช้จ่ายปี66 </th>
																		<th> ค่าใช้จ่ายปี67เทียบงบสะสม</th>
																		<th> ค่าใช้จ่ายปี67เทียบค่าใช้จ่ายปี66</th>

																	</tr>
																</thead>
																<tbody>
-->
																	<?php
//$num = 0;
//while ($row = $result3_2->fetch_assoc())
//{
    //$num++;
?>
<!--

																	<tr class="odd gradeX">
																		<td><?php //echo $num; ?></td>
																		<td><?php //echo $row['pea']; ?></td>
																		<td><?php //echo number_format($row['total_budget_total_2024'],2); ?></td>
																		<td><?php //echo number_format($row['total_criteria_expenses_2024'],2); ?></td>
																		<td><?php //echo number_format($row['total_criteria_expenses_2023'],2); ?></td>
																		<td><?php //echo number_format($row['percentage_expenses_2024'],2); ?> %</td>
																		<td><?php //echo number_format($row['percentage_change'],2); ?> %</td>
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
	<script src="../../assets/plugins/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
	<script src="../../assets/plugins/flatpicker/js/flatpicker.min.js"></script>
	<script src="../../assets/js/pages/date-time/date-time.init.js"></script>
	<script src="../../assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js" charset="UTF-8"></script>
	<script src="../../assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker-init.js" charset="UTF-8"></script>
	<script src="../../assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
	<script src="../../assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8"></script>
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
		$("#table3").DataTable({
			columnDefs: [{
				width: 10,
				targets: 0
			}],
		});
	</script>
	<!-- Start ApexChart -->
	<script>
		// Parse PHP JSON data
		var jsonData1 = <?php echo $jsonData1; ?>;
		var jsonData2 = <?php echo $jsonData2; ?>;
		var jsonData3 = <?php echo $jsonData3; ?>;
		var jsonData4 = <?php echo $jsonData4; ?>;
		var jsonData5 = <?php echo $jsonData5; ?>;

		var options1 = {
			series: [{
				name: 'เบิกจ่ายสุทธิ ปี66',
				type: 'column',
				data: jsonData1.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'เบิกจ่ายสุทธิ ปี67',
				type: 'column',
				data: jsonData1.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสม ปี67',
				type: 'column',
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
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2],
				formatter: function(val, opts) {
					return (val / 1000000).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '12px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10, // Move labels up
				dropShadow: {
					enabled: true,
					top: 1,
					left: 1,
					blur: 1,
					opacity: 0.45
				},
				background: {
					enabled: true,
					foreColor: '#000000',
					padding: 4,
					borderRadius: 2,
					borderWidth: 1,
					borderColor: '#c3c3c3',
				}
			},
			stroke: {
				width: [1, 1, 1, 2],
				curve: 'smooth'
			},
			markers: {
				size: 5,
				colors: ['#FF4560'],
				strokeColor: '#FF4560',
				strokeWidth: 2,
			},
			tooltip: {
				style: {
					fontFamily: 'Prompt, sans-serif',
				},
				y: {
					formatter: function(value, {
						series,
						seriesIndex,
						dataPointIndex,
						w
					}) {
						// Check if the series has less than 3 data points
						if (w.globals.series[seriesIndex].length < 2) {
							return ''; // Return an empty string to hide the tooltip content
						} else {
							return value.toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							}) + ' บาท';
						}
					}
				}
			},


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val / 1000000).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '13px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData1.map(item => item.node_description),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between', // Ensure ticks are between the categories
				tooltip: {
					enabled: false // Disable x-axis tooltip to avoid clutter
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30 // Adjust top padding to make space for labels
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // Place data labels on top of the bars
					},
				}
			},
			legend: {
				show: true, // Ensure the legend is shown
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true // Enable toggling series on click
				}
			}
		};

		var options2 = {
			series: [{
				name: 'เบิกจ่ายสุทธิ ปี66',
				type: 'column',
				data: jsonData2.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'เบิกจ่ายสุทธิ ปี67',
				type: 'column',
				data: jsonData2.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสม ปี67',
				type: 'column',
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
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2],
				formatter: function(val, opts) {
					return (val / 1000000).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '12px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10, // Move labels up
				dropShadow: {
					enabled: true,
					top: 1,
					left: 1,
					blur: 1,
					opacity: 0.45
				},
				background: {
					enabled: true,
					foreColor: '#000000',
					padding: 4,
					borderRadius: 2,
					borderWidth: 1,
					borderColor: '#c3c3c3',
				}
			},
			stroke: {
				width: [1, 1, 1, 2],
				curve: 'smooth'
			},
			markers: {
				size: 5,
				colors: ['#FF4560'],
				strokeColor: '#FF4560',
				strokeWidth: 2,
			},
			tooltip: {
				style: {
					fontFamily: 'Prompt, sans-serif',
				},
				y: {
					formatter: function(value, {
						series,
						seriesIndex,
						dataPointIndex,
						w
					}) {
						// Check if the series has less than 3 data points
						if (w.globals.series[seriesIndex].length < 2) {
							return ''; // Return an empty string to hide the tooltip content
						} else {
							return value.toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							}) + ' บาท';
						}
					}
				}
			},


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val / 1000000).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '13px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData2.map(item => item.pea),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between', // Ensure ticks are between the categories
				tooltip: {
					enabled: false // Disable x-axis tooltip to avoid clutter
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30 // Adjust top padding to make space for labels
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // Place data labels on top of the bars
					},
				}
			},
			legend: {
				show: true, // Ensure the legend is shown
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true // Enable toggling series on click
				}
			}
		};

		var options3 = {
			series: [{
				name: 'เบิกจ่ายสุทธิ ปี66',
				type: 'column',
				data: jsonData3.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'เบิกจ่ายสุทธิ ปี67',
				type: 'column',
				data: jsonData3.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสม ปี67',
				type: 'column',
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
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2],
				formatter: function(val, opts) {
					return (val / 1000000).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '12px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10, // Move labels up
				dropShadow: {
					enabled: true,
					top: 1,
					left: 1,
					blur: 1,
					opacity: 0.45
				},
				background: {
					enabled: true,
					foreColor: '#000000',
					padding: 4,
					borderRadius: 2,
					borderWidth: 1,
					borderColor: '#c3c3c3',
				}
			},
			stroke: {
				width: [1, 1, 1, 2],
				curve: 'smooth'
			},
			markers: {
				size: 5,
				colors: ['#FF4560'],
				strokeColor: '#FF4560',
				strokeWidth: 2,
			},
			tooltip: {
				style: {
					fontFamily: 'Prompt, sans-serif',
				},
				y: {
					formatter: function(value, {
						series,
						seriesIndex,
						dataPointIndex,
						w
					}) {
						// Check if the series has less than 3 data points
						if (w.globals.series[seriesIndex].length < 2) {
							return ''; // Return an empty string to hide the tooltip content
						} else {
							return value.toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							}) + ' บาท';
						}
					}
				}
			},


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val / 1000000).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '13px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData3.map(item => item.pea),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between', // Ensure ticks are between the categories
				tooltip: {
					enabled: false // Disable x-axis tooltip to avoid clutter
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30 // Adjust top padding to make space for labels
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // Place data labels on top of the bars
					},
				}
			},
			legend: {
				show: true, // Ensure the legend is shown
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true // Enable toggling series on click
				}
			}
		};

		var options4 = {
			series: [{
				name: 'เบิกจ่ายสุทธิ ปี66',
				type: 'column',
				data: jsonData4.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'เบิกจ่ายสุทธิ ปี67',
				type: 'column',
				data: jsonData4.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสม ปี67',
				type: 'column',
				data: jsonData4.map(item => item.total_budget_total_2024)
			}, {
				name: 'งบประมาณ ปี67',
				type: 'column',
				data: jsonData4.map(item => item.total_budget_12m_2024)
			}],
			chart: {
				height: 400,
				type: 'line',
				stacked: false,
				toolbar: {
					show: true,
					tools: {
						zoom: true,
						zoomin: true,
						zoomout: true,
						pan: true,
						reset: true
					}
				}
			},
			tooltip: {
				enabled: false // Disable tooltip globally
			},
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2, 3],
				formatter: function(val, opts) {
					return (val / 1000000).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '10px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10, // Move labels up
				dropShadow: {
					enabled: true,
					top: 1,
					left: 1,
					blur: 1,
					opacity: 0.45
				},
				background: {
					enabled: true,
					foreColor: '#000000',
					padding: 4,
					borderRadius: 2,
					borderWidth: 1,
					borderColor: '#c3c3c3',
				}
			},
			stroke: {
				width: [1, 3, 1, 1],
				curve: 'smooth'
			},
			markers: {
				size: 5,
				colors: ['#FF4560'],
				strokeColor: '#FF4560',
				strokeWidth: 2,
			},
			// tooltip: {
			// style: {
			// fontFamily: 'Prompt, sans-serif',
			// },
			// y: {
			// formatter: function(value, {
			// series,
			// seriesIndex,
			// dataPointIndex,
			// w
			// }) {
			// // Check if the series has less than 3 data points
			// if (w.globals.series[seriesIndex].length < 2) { // return '' ; // Return an empty string to hide the tooltip content // } else { // return value.toLocaleString('en-US', { // minimumFractionDigits: 2, // maximumFractionDigits: 2 // }) + ' บาท' ; // } // } // } // },


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val / 1000000).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '12px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData4.map(item => item.pea),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between', // Ensure ticks are between the categories
				tooltip: {
					enabled: false // Disable x-axis tooltip to avoid clutter
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30 // Adjust top padding to make space for labels
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // Place data labels on top of the bars
					},
				}
			},
			legend: {
				show: true, // Ensure the legend is shown
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true // Enable toggling series on click
				}
			}
		};

		var options5 = {
			series: [{
				name: 'เบิกจ่ายสุทธิ ปี66',
				type: 'column',
				data: jsonData5.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'เบิกจ่ายสุทธิ ปี67',
				type: 'column',
				data: jsonData5.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสม ปี67',
				type: 'column',
				data: jsonData5.map(item => item.total_budget_total_2024)
			}, {
				name: 'งบประมาณ ปี67',
				type: 'column',
				data: jsonData5.map(item => item.total_budget_12m_2024)
			}],
			chart: {
				height: 400,
				type: 'line',
				stacked: false,
				toolbar: {
					show: true,
					tools: {
						zoom: true,
						zoomin: true,
						zoomout: true,
						pan: true,
						reset: true
					}
				}
			},
			tooltip: {
				enabled: false // Disable tooltip globally
			},
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2, 3],
				formatter: function(val, opts) {
					return (val / 1000000).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '10px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10, // Move labels up
				dropShadow: {
					enabled: true,
					top: 1,
					left: 1,
					blur: 1,
					opacity: 0.45
				},
				background: {
					enabled: true,
					foreColor: '#000000',
					padding: 4,
					borderRadius: 2,
					borderWidth: 1,
					borderColor: '#c3c3c3',
				}
			},
			stroke: {
				width: [1, 3, 1, 1],
				curve: 'smooth'
			},
			markers: {
				size: 5,
				colors: ['#FF4560'],
				strokeColor: '#FF4560',
				strokeWidth: 2,
			},
			// tooltip: {
			// style: {
			// fontFamily: 'Prompt, sans-serif',
			// },
			// y: {
			// formatter: function(value, {
			// series,
			// seriesIndex,
			// dataPointIndex,
			// w
			// }) {
			// // Check if the series has less than 3 data points
			// if (w.globals.series[seriesIndex].length < 2) { // return '' ; // Return an empty string to hide the tooltip content // } else { // return value.toLocaleString('en-US', { // minimumFractionDigits: 2, // maximumFractionDigits: 2 // }) + ' บาท' ; // } // } // } // },


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val / 1000000).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '12px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData5.map(item => item.pea),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between', // Ensure ticks are between the categories
				tooltip: {
					enabled: false // Disable x-axis tooltip to avoid clutter
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30 // Adjust top padding to make space for labels
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top', // Place data labels on top of the bars
					},
				}
			},
			legend: {
				show: true, // Ensure the legend is shown
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true // Enable toggling series on click
				}
			}
		};

		var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
		chart1.render();

		var chart2 = new ApexCharts(document.querySelector("#chart2"), options2);
		chart2.render();

		var chart3 = new ApexCharts(document.querySelector("#chart3"), options3);
		chart3.render();

		var chart4 = new ApexCharts(document.querySelector("#chart4"), options4);
		chart4.render();

		var chart5 = new ApexCharts(document.querySelector("#chart5"), options5);
		chart5.render();
	</script>
</body>

</html>
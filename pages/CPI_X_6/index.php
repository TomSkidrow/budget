<?php 
require_once('../includes/connect.php');
$selectedCategories = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['profit_code']) && count($_POST['profit_code']) > 0) {
        $selectedCategories = array_map(function($item) use ($conn) {
            return $conn->real_escape_string($item);
        }, $_POST['profit_code']);
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
	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" type="text/css" />
	<!--    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">-->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fntneves/jquery-labelauty/source/jquery-labelauty.css">
	<!-- icons -->
	<link href="../../fonts/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/font-awesome/v6/css/all.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
	<!--bootstrap -->
	<!--        <link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" media="screen" />-->
	<link rel="stylesheet" href="../../assets/plugins/flatpicker/css/flatpickr.min.css" />
	<link href="../../assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
	<link href="../../assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.css" rel="stylesheet" media="screen">
	<!-- Material Design Lite CSS -->
	<link rel="stylesheet" href="../../assets/plugins/material/material.min.css">
	<link rel="stylesheet" href="../../assets/css/material_style.css">
	<!-- Theme Styles -->
	<link href="../../assets/css/theme/full/theme_style.css" rel="stylesheet" id="rt_style_components" type="text/css" />
	<link href="../../assets/css/theme/full/style.css" rel="stylesheet" type="text/css" />
	<link href="../../assets/css/plugins.min.css" rel="stylesheet" type="text/css" />
	<link href="../../assets/css/pages/formlayout.css" rel="stylesheet" type="text/css" />
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
		/* Override default Labelauty styles for horizontal alignment */
		.labelauty {
			display: inline-block;
			margin-right: 20px;
		}

		.labelauty>input {
			display: none;
		}

		.labelauty>input+label {
			position: relative;
			padding-left: 30px;
			cursor: pointer;

		}

		.labelauty>input+label:before {
			content: '';
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 20px;
			height: 20px;
			border: 2px solid #28a745;
			/* Change border color here */
			border-radius: 4px;
			background-color: white;
		}

		.labelauty>input:checked+label:before {
			background-color: #28a745;
			/* Change background color here */
			border-color: #28a745;
		}

		.labelauty>input:checked+label:after {
			content: '';
			position: absolute;
			left: 5px;
			top: 50%;
			transform: translateY(-50%) rotate(45deg);
			width: 10px;
			height: 5px;
			border: solid white;
			border-width: 0 0.2em 0.2em 0;
		}
	</style>
</head>

<body class="page-header-fixed sidemenu-closed-hidelogo page-content-white page-md page-full-width header-white white-sidebar-color logo-indigo">
	<div class="page-wrapper">
		<!-- start header -->

		<!-- start header menu -->
		<?php include_once('../includes/header.php') ?>

		<!-- end header -->

		<!-- start page container -->
		<div class="page-container">

			<div class="page-content-wrapper">
				<div class="page-content">
					<div class="page-bar">
						<div class="page-title-breadcrumb">
							<div class="pull-left">
								<div class="page-title"></div>
							</div>
							<ol class="breadcrumb page-breadcrumb pull-right">
								<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard/">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
								</li>
								<li class="active"><a href="#" id="goBack">กลับไปหน้าที่แล้ว</a></li>
							</ol>
						</div>
					</div>
					<!-- add content here -->
					<div class="state-overview">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-12">
								<div class="info-box">
									<span class="info-box-icon push-bottom"><i data-feather="users"></i></span>
									<div class="info-box-content">
										<div class="card-body" id="bar-parent">
											<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
												<div class="form-group row">
													<?php
$sql_select = "
SELECT
	pea_sname AS pea,
	profit_code 
FROM
	profit 
WHERE
	profit_code IN (
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
ORDER BY
	profit_code ASC                                    
";
                                    $result_select = $conn->query($sql_select);
                                    while ($row = $result_select->fetch_assoc()) {
                                        $checked = '';
                                        if (isset($_POST['profit_code']) && in_array($row['profit_code'], $_POST['profit_code'])) {
                                            $checked = 'checked';
                                        }
                                        echo "<div class='form-check form-check-inline'>
                                                  <input class='form-check-input' type='checkbox' name='profit_code[]' value='".$row['profit_code']."' $checked data-labelauty='".$row['pea']."'>
                                              </div>";
                                    }
                                    
                                    ?>
												</div>
												<button type="submit" class="btn btn-primary">OK</button>
												<button type="button" class="btn btn-secondary" onclick="uncheckAll()">ยกเลิกทั้งหมด</button>
											</form>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 col-sm-12">
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
										<div id="chart1"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    if (isset($_POST['profit_code']) && count($_POST['profit_code']) > 0) {
                        $selectedCategories = array_map(function($item) use ($conn) {
                            return $conn->real_escape_string($item);
                        }, $_POST['profit_code']);
                        $whereClause = implode("','", $selectedCategories);
$sql_search = "

SELECT
	a.pea_sname AS pea,
	b.profit_code,
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
	b.profit_code IN ( '$whereClause' ) 
ORDER BY
	percentage_change ASC

";
                        //echo "<pre>$sql_search</pre>"; 
$result_search = $conn->query($sql_search);
                        if ($result_search->num_rows > 0) {
                            $data = [];
                            while ($row = $result_search->fetch_assoc()) {
                                $data[] = [
                                    'pea' => $row['pea'],
                                    'total_criteria_expenses_2023' => (float) $row['total_criteria_expenses_2023'],
                                    'total_criteria_expenses_2024' => (float) $row['total_criteria_expenses_2024'],
                                    'total_budget_total_2024' => (float) $row['total_budget_total_2024'],
                                    'total_budget_12m_2024' => (float) $row['total_budget_12m_2024']
                                ];
                            }
                            // Encode data as JSON
                            $jsonData1 = json_encode($data);
                            echo "<script>var jsonData1 = $jsonData1;</script>";
                        } else {
                            echo "ไม่พบข้อมูลหน่วยงาน";
                        }
                    } else {
                        echo "";
                    }
                }

            ?>
				</div>
				<!-- end page content -->
			</div>
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

	<!--       <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->
	<!-- Include Labelauty JS -->
	<script src="https://cdn.jsdelivr.net/gh/fntneves/jquery-labelauty/source/jquery-labelauty.js"></script>
	<script>
		document.getElementById("goBack").addEventListener("click", function(event) {
			event.preventDefault();
			window.history.back();
		});
	</script>
	<script>
		$(document).ready(function() {
			$(':checkbox').labelauty();
		});

		function uncheckAll() {
			$(':checkbox').prop('checked', false);
			$(':checkbox').labelauty('refresh');
		}
	</script>
	<script>
		function uncheckAll() {
			var checkboxes = document.querySelectorAll('input[type="checkbox"]');
			checkboxes.forEach(function(checkbox) {
				checkbox.checked = false;
			});
		}
	</script>

	<script>
		// Parse PHP JSON data
		var jsonData1 = <?php echo $jsonData1; ?>;

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
				width: [1, 1, 1, 3],
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
						if (w.globals.series[seriesIndex].length < 1) {
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
							minimumFractionDigits: 1,
							maximumFractionDigits: 1
						}) + ' ล้านบาท';
					},
					style: {
						fontFamily: 'Prompt, sans-serif',
						fontSize: '13px'
					}
				}
			},
			xaxis: {
				categories: jsonData1.map(item => item.pea),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
						fontSize: '13px', // Increase the size of the x-axis labels
                fontWeight: 'bold'
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
	</script>
</body>

</html>
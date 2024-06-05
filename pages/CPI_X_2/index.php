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
	<title>CPI-X</title>
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
	<!--tagsinput-->
	<link href="../../assets/plugins/jquery-tags-input/jquery-tags-input.css" rel="stylesheet">
	<!--select2-->
	<link href="../../assets/plugins/select2/css/select2.css" rel="stylesheet" type="text/css" />
	<link href="../../assets/plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />

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
<style>
	#chart1 {
		width: 100%;
		margin: 35px auto;
	}

	.apexcharts-tooltip-title {
		/*        display: none;*/
	}

	#chart1 .apexcharts-tooltip {
		display: flex;
		border: 0;
		box-shadow: none;
	}

	#chart1 .apexcharts-text {
		font-family: 'Prompt', sans-serif !important;
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
								<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard/">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
								</li>
								<li class="active"><a href="#" id="goBack">กลับไปหน้าที่แล้ว</a></li>
							</ol>
						</div>
					</div>
					<!-- add content here -->

					<div class="state-overview">
						<div class="row">
							<div class="col-xl-8 col-md-8 col-12">
								<div class="info-box bg-b-green">
									<span class="info-box-icon push-bottom"><i data-feather="users"></i></span>
									<div class="info-box-content">
										<div class="card-body " id="bar-parent">
											<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
												<div class="form-group row">
													<select id="profit_code" class="form-control select2-multiple" name="profit_code[]" multiple>
														<?php
$sql_select = "
SELECT
	pea_sname AS pea,
	profit_code 
FROM
	profit 
WHERE
	profit_code IN (
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
ORDER BY
	profit_code ASC

";
$result_select = $conn->query($sql_select);
while ($row = $result_select->fetch_assoc()) {
    echo "<option value='".$row['profit_code']."'>".$row['pea']."</option>";
}
?>
													</select>
												</div>
												<button type="submit" class="btn btn-primary">ตกลง</button>

											</form>
										</div>
										<span class="progress-description">
											เลือกหน่วยงานตามที่ต้องการสูงสุด 5 หน่วยงาน
										</span>
									</div>
									<!-- /.info-box-content -->
								</div>
								<!-- /.info-box -->
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
	percentage_expenses_2024 DESC
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
                echo "ไม่พบข้อมูลหน่วยงานที่ท่านเลือก";
            }
        } else {
            echo "ยังไม่ได้เลือกหน่วยงาน";
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
	<!--tags input-->
	<script src="../../assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
	<script src="../../assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
	<!--select2-->
	<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>-->
	<script src="../../assets/plugins/select2/js/select2.js"></script>
	<script src="../../assets/js/pages/select2/select2-init.js"></script>

	<!-- end js include path -->

	<script>
		document.getElementById("goBack").addEventListener("click", function(event) {
			event.preventDefault();
			window.history.back();
		});
	</script>
	<script>
		var placeholder = "เลือกหน่วยงานหรือพิมพ์ชื่อหน่วยงานที่ต้องการ";
		$('.select2, .select2-multiple').select2({
			theme: "bootstrap",
			placeholder: placeholder,
			maximumSelectionLength: 5,

		});


		$("#selitemIcon").select2({
			theme: "bootstrap",
			templateResult: format,
			formatSelection: format,
			escapeMarkup: function(m) {
				return m;
			}
		});
		$('.select2-allow-clear').select2({
			theme: "bootstrap",
			allowClear: true,
			placeholder: placeholder
		});
		$("button[data-select2-open]").click(function() {
			$("#" + $(this).data("select2-open")).select2("open");
		});

		$(":checkbox").on("click", function() {
			$(this).parent().nextAll("select").prop("disabled", !this.checked);
		});

		$('.select2, .select2-multiple, .select2-allow-clear, #selitemIcon').on('select2:open', function(event) {
			var $searchfield = $(this).parent().find('.select2-search__field');
			$searchfield.prop('disabled', false);
			$searchfield.focus();
		});
		// JavaScript to set the previously selected values
		$(document).ready(function() {
			var selectedValues = <?php echo json_encode($selectedCategories); ?>;
			if (selectedValues.length > 0) {
				$('#profit_code').val(selectedValues).trigger('change');
			}
		});
	</script>



	<!--
    <script>
        $(document).ready(function() {
            var placeholder = "เลือกหน่วยงานหรือพิมพ์ชื่อหน่วยงานที่ต้องการ";
            var selectedValues = <?php //echo json_encode($selectedCategories); ?>;

            $('.select2-multiple').select2({
                theme: "bootstrap",
                placeholder: "",
                maximumSelectionLength: 5,
                allowClear: true
            });

 if (selectedValues.length > 0) {
 $('#profit_code').val(selectedValues).trigger('change');
 }
        });

        $("#selitemIcon").select2({
            theme: "bootstrap",
            templateResult: format,
            formatSelection: format,
            escapeMarkup: function(m) {
                return m;
            }
        });
        $('.select2-allow-clear').select2({
            theme: "bootstrap",
            allowClear: true,
            placeholder: ""
        });
        $("button[data-select2-open]").click(function() {
            $("#" + $(this).data("select2-open")).select2("open");
        });
        $(".select2, .select2-multiple, .select2-allow-clear, #selitemIcon").on('select2:opening select2:closing', function(event) {
            var $searchfield = $(this).parent().find('.select2-search__field');
            $searchfield.prop('disabled', true);
        });
        $('.select2, .select2-multiple, .select2-allow-clear, #selitemIcon').on('select2:open', function(event) {
            var $searchfield = $(this).parent().find('.select2-search__field');
            $searchfield.prop('disabled', false);
            $searchfield.focus();
        });

        // JavaScript to set the previously selected values
        $(document).ready(function() {
            var selectedValues = <?php //echo json_encode($selectedCategories); ?>;
            if (selectedValues.length > 0) {
                $('#profit_code').val(selectedValues).trigger('change');
            }
        });

    </script>
-->


	<!-- Start ApexChart -->
	<script>
		// Parse PHP JSON data
		var jsonData1 = <?php echo $jsonData1; ?>;

		var options1 = {
			series: [{
				name: 'ค่าใช้จ่าย ปี66',
				type: 'column',
				data: jsonData1.map(item => item.total_criteria_expenses_2023)
			}, {
				name: 'ค่าใช้จ่าย ปี67',
				type: 'column',
				data: jsonData1.map(item => item.total_criteria_expenses_2024)
			}, {
				name: 'งบสะสมปี67',
				type: 'column',
				data: jsonData1.map(item => item.total_budget_total_2024)
			}, {
				name: 'งบประมาณปี67',
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
						fontSize: '13px' // Change font size to 16px
					}
				}
			},
			xaxis: {
				categories: jsonData1.map(item => item.pea),
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
	</script>
</body>

</html>
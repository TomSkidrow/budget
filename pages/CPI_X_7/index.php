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
    #chart1, #chart2, #chart3 ,#chart4{
        width: 100%;
        margin: 35px auto;
    }

    .apexcharts-tooltip-title {
        /* display: none; */
    }

    .apexcharts-tooltip {
        display: flex;
        border: 0;
        box-shadow: none;
    }

    .apexcharts-text {
        font-family: 'Prompt', sans-serif !important;
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
	level4 IN (
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
		'E3151',
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
	AND profit_code LIKE '%E3%' 
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
                                            เพื่อความสะดวก แนะนำให้พิมพ์ชื่อหน่วยงานตามที่ต้องการ
                                        </span>
                                    </div>
                                    <!-- /.info-box-content -->
                                </div>
                                <!-- /.info-box -->
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xl-6 col-md-6 col-12">
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
                                                <div class="col-xl-6 col-md-6 col-12">
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
                                        <div id="chart2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                                        <div class="row">
                        <div class="col-xl-6 col-md-6 col-12">
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
                                        <div id="chart4"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                                                <div class="col-xl-6 col-md-6 col-12">
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
                                        <div id="chart3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if categories are selected
        if (isset($_POST['profit_code']) && count($_POST['profit_code']) > 0) {
            // Sanitize the input
            $selectedCategories = array_map(function($item) use ($conn) {
                return $conn->real_escape_string($item);
            }, $_POST['profit_code']);
            
            // Construct the WHERE clause for SQL query
            $whereClause = implode("','", $selectedCategories);
            
            // Construct the SQL query
            $sql_search = "
SELECT

a.pea_sname AS pea,
b.profit_code,
a.total_criteria_expenses AS total_criteria_expenses_2023,
b.total_budget_total AS total_budget_total_2024,
b.total_criteria_expenses AS total_criteria_expenses_2024,
b.total_budget_12m AS total_budget_12m_2024,

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
aggregated_data_2024 b ON a.profit_code = b.profit_code

WHERE 
b.profit_code IN ('$whereClause')

ORDER BY 
percentage_expenses_2024 DESC
       
";
            //echo "<pre>$sql_search</pre>"; // Debug: Print the SQL query
            
            $result_search = $conn->query($sql_search);

            // Check if the query returned results
            if ($result_search->num_rows > 0) {
                // Fetch data and prepare for JSON encoding
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
                echo "<script>var jsonData1 = $jsonData1;</script>"; // Embed JSON data into JavaScript
            } else {
                echo "ไม่พบข้อมูลหน่วยงานที่ท่านเลือก";
            }
        } else {
            echo "ยังไม่ได้เลือกหน่วยงาน";
        }
    }
                    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if categories are selected
        if (isset($_POST['profit_code']) && count($_POST['profit_code']) > 0) {
            // Sanitize the input
            $selectedCategories = array_map(function($item) use ($conn) {
                return $conn->real_escape_string($item);
            }, $_POST['profit_code']);
            
            // Construct the WHERE clause for SQL query
            $whereClause = implode("','", $selectedCategories);                    
                    // Construct the SQL query
$sql_line = "

SELECT
	pea_sname AS pea,
	sum_budget_12m,
	sum_budget_total,
	sum_criteria_expenses,
	`month`,
	`year` 
FROM
	budget_result 
WHERE
	profit_code IN ( '$whereClause' )
       
";
//            echo "<pre>$sql_line</pre>"; // Debug: Print the SQL query
            
            $result_line = $conn->query($sql_line);

            // Check if the query returned results
            if ($result_line->num_rows > 0) {
                // Fetch data and prepare for JSON encoding
                $data = [];
                while ($row = $result_line->fetch_assoc()) {
                    $data[] = [
                        'pea' => $row['pea'],
                        'sum_budget_12m' => (float) $row['sum_budget_12m'],
                        'sum_budget_total' => (float) $row['sum_budget_total'],
                        'sum_criteria_expenses' => (float) $row['sum_criteria_expenses'],
                        'month' => (float) $row['month'],
                        'year' => (float) $row['year']
                    ];
                }

                // Encode data as JSON
                $jsonData2 = json_encode($data);
                echo "<script>var jsonData2 = $jsonData2;</script>"; // Embed JSON data into JavaScript
            }    
            else {
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
    <script src="../../assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker-init.js" charset="UTF-8">
    </script>
    <!-- Common js-->
    <script src="../../assets/js/app.js"></script>
    <script src="../../assets/js/layout.js"></script>
    <script src="../../assets/js/theme-color.js"></script>
    <!-- Material -->
    <script src="../../assets/plugins/material/material.min.js"></script>
    <!-- dropzone -->
    <script src="../../assets/plugins/dropzone/dropzone.js"></script>
    <!--tags input-->
    <script src="../../assets/plugins/jquery-tags-input/jquery-tags-input.js"></script>
    <script src="../../assets/plugins/jquery-tags-input/jquery-tags-input-init.js"></script>
    <!--select2-->
    <script src="../../assets/plugins/select2/js/select2.js"></script>
    <script src="../../assets/js/pages/select2/select2-init.js"></script>

    <!-- end js include path -->
    <script>
        var placeholder = "เลือกหน่วยงานหรือพิมพ์ชื่อหน่วยงานที่ต้องการ";
        $('.select2, .select2-multiple').select2({
            theme: "bootstrap",
            placeholder: placeholder,
            maximumSelectionLength: 1,

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


    <!-- Start ApexChart -->
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
                type: 'column',
                data: jsonData1.map(item => item.total_budget_12m_2024)
            }],
            chart: {
                height: 350,
                type: 'line',
                stacked: false
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
                        fontSize: '12px' // Change font size to 16px
                    }
                }
            },
            xaxis: {
                categories: jsonData1.map(item => item.pea),
                labels: {
                    style: {
                        fontFamily: 'Prompt, sans-serif',
						fontSize: '14px', // Increase the size of the x-axis labels
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
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof jsonData2 !== 'undefined' && jsonData2.length > 0) {
            // Prepare data for 2023 and 2024
            var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            var data2023 = new Array(12).fill(0);
            var data2024 = new Array(12).fill(0);

            jsonData2.forEach(function(item) {
                if (item.year === 2023) {
                    data2023[item.month - 1] += item.sum_criteria_expenses;
                } else if (item.year === 2024) {
                    data2024[item.month - 1] += item.sum_criteria_expenses;
                }
            });

            var options = {
                series: [{
                    name: "เบิกจ่ายสุทธิ ปี 2566",
                    data: data2023
                }, {
                    name: "เบิกจ่ายสุทธิ ปี 2567",
                    data: data2024
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 3],
                    curve: 'smooth'
                },
                    title: {
        text: 'เปรียบเทียบ เบิกจ่ายสุทธิ (ปี 2566 vs 2567)',
        align: 'left',
        style: {
            fontFamily: 'Prompt, sans-serif'
        }
    },
                xaxis: {
                    categories: months,
                },
                 tooltip: {
        style: {
            fontFamily: 'Prompt, sans-serif',
        },
        y: {
            formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                if (w.globals.series[seriesIndex].length < 1) {
                    return '';
                } else {
                    return '' + value.toLocaleString('en-US', {
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
                }) + ' ล้านบาท'; // 'ล้าน' for million in Thai
            },
            style: {
                fontSize: '13px' // Change font size if needed
            }
        }
    },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var chart2 = new ApexCharts(document.querySelector("#chart2"), options);
            chart2.render();
        } else {
            console.log("No data found or data is not defined.");
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof jsonData2 !== 'undefined' && jsonData2.length > 0) {
            // Prepare data for 2024
            var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            var data2024_criteria_expenses = new Array(12).fill(0);
            var data2024_budget_total = new Array(12).fill(0);

            jsonData2.forEach(function(item) {
                if (item.year === 2024) {
                    data2024_criteria_expenses[item.month - 1] = item.sum_criteria_expenses;
                    data2024_budget_total[item.month - 1] = item.sum_budget_total;
                }
            });

            var options = {
                series: [{
                    name: "งบประมาณสะสม ปี 2567",
                    data: data2024_budget_total
                }, {
                    name: "เบิกจ่ายสุทธิ ปี 2567",
                    data: data2024_criteria_expenses
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 3],
                    curve: 'smooth'
                },
                title: {
                    text: 'เปรียบเทียบ เบิกจ่ายสุทธิ กับ งบประมาณสะสม (ปี 2567)',
                    align: 'left',
                    style: {
                        fontFamily: 'Prompt, sans-serif'
                    }
                },
                xaxis: {
                    categories: months,
                },
                tooltip: {
                    style: {
                        fontFamily: 'Prompt, sans-serif',
                    },
                    y: {
                        formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                            if (w.globals.series[seriesIndex].length < 1) {
                                return '';
                            } else {
                                return '' + value.toLocaleString('en-US', {
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
                            }) + ' ล้านบาท'; // 'ล้าน' for million in Thai
                        },
                        style: {
                            fontSize: '13px' // Change font size if needed
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var chart3 = new ApexCharts(document.querySelector("#chart3"), options);
            chart3.render();
        } else {
            console.log("No data found or data is not defined.");
        }
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof jsonData2 !== 'undefined' && jsonData2.length > 0) {
            // Prepare data for 2024
            var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            var data2024_criteria_expenses = new Array(12).fill(0);
            var data2024_budget_total = new Array(12).fill(0);
            var data2024_budget_12m = new Array(12).fill(0);

            jsonData2.forEach(function(item) {
                if (item.year === 2024) {
                    data2024_criteria_expenses[item.month - 1] = item.sum_criteria_expenses;
                    data2024_budget_total[item.month - 1] = item.sum_budget_total;
                    data2024_budget_12m[item.month - 1] = item.sum_budget_12m;
                }
            });

            var options = {
                series: [{
                    name: "งบประมาณ ปี 2567",
                    data: data2024_budget_12m
                }, {
                    name: "เบิกจ่ายสุทธิ ปี 2567",
                    data: data2024_criteria_expenses
                } , {
                    name: "งบสะสม ปี 2567",
                    data: data2024_budget_total
                }],
                chart: {
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    width: [3, 3, 3],
                    curve: 'smooth',
                    dashArray: [8, 0, 5]
                },
                title: {
                    text: 'เปรียบเทียบ เบิกจ่ายสุทธิ กับ งบสะสมฯ,งบประมาณปี 2567',
                    align: 'left',
                    style: {
                        fontFamily: 'Prompt, sans-serif'
                    }
                },
                xaxis: {
                    categories: months,
                },
                tooltip: {
                    style: {
                        fontFamily: 'Prompt, sans-serif',
                    },
                    y: {
                        formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                            if (w.globals.series[seriesIndex].length < 1) {
                                return '';
                            } else {
                                return '' + value.toLocaleString('en-US', {
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
                            }) + ' ล้านบาท'; // 'ล้าน' for million in Thai
                        },
                        style: {
                            fontSize: '13px' // Change font size if needed
                        }
                    }
                },
                grid: {
                    borderColor: '#f1f1f1',
                }
            };

            var chart4 = new ApexCharts(document.querySelector("#chart4"), options);
            chart4.render();
        } else {
            console.log("No data found or data is not defined.");
        }
    });
</script>

</body>

</html>

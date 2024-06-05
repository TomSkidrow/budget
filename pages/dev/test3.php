<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "budget";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
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
    a.year = 2023
AND b.year = 2024
ORDER BY
    total_budget_total_2024 DESC
LIMIT 10 ;
";

$result = $conn->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}
$conn->close();
$jsonData = json_encode($data);
echo $jsonData;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Chart</title>

    <link href="../assets/styles.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap">


    <style>
        #chart {
            width: 100%;
            margin: 35px auto;
        }

        .apexcharts-tooltip-title {
            display: none;
        }

        #chart .apexcharts-tooltip {
            display: flex;
            border: 0;
            box-shadow: none;
        }

        .apexcharts-text {
            font-family: 'Prompt', sans-serif !important;
        }

    </style>

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

<body>
    <div id="chart"></div>

    <script>
        var jsonData = <?php echo $jsonData; ?>;


        var options = {
            series: [{
                name: 'ค่าใช้จ่าย ปี66 ',
                type: 'column',
                data: jsonData.map(item => item.total_criteria_expenses_2023)
            }, {
                name: 'ค่าใช้จ่าย ปี67',
                type: 'column',
                data: jsonData.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'งบสะสมปี67',
                type: 'column',
                data: jsonData.map(item => item.total_criteria_expenses_2024)
            }, {
                name: 'งบประมาณปี67',
                type: 'line',
                data: jsonData.map(item => item.total_budget_total_2024)
            }],
            chart: {
                height: 500,
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
                }
            },
            stroke: {
                width: [1, 1, 1, 3], // Adjusted thickness: columns are thicker and line is even thicker
                curve: 'smooth'
            },
            markers: {
                size: 5, // Adjust the size of the markers
                colors: ['#FF4560'], // Customize marker color, you can add more colors if needed
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
                        if (w.globals.series[seriesIndex].length < 3) {
                            return ''; // Return an empty string to hide the tooltip content
                        } else {
                            return '<span style="font-family: sans-serif;">' + value.toLocaleString() + '</span>';
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
                        }) + ' ล้านบาท'; // Format y-axis values in million scale without decimal places
                    }
                }
            },
            xaxis: {
                categories: jsonData.map(item => item.pea),
                labels: {
                    formatter: function(value) {
                        return value;
                    }
                }
            }
        };


        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

    </script>

</body>

</html>

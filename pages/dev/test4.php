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
LIMIT 14 ;

";

$result = $conn->query( $sql );

$data = array();
if ( $result->num_rows > 0 ) {
    while( $row = $result->fetch_assoc() ) {
        $data[] = $row;
    }
} else {
    echo "0 results";
}

$jsonData = json_encode( $data );
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graph Toggle Example</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        body {
            font-family: 'Prompt', sans-serif;
        }

    </style>
</head>

<body>


    <div id="chart"></div>


    <script>
        // Assuming jsonData1 is already defined in PHP
        var jsonData = <?php echo $jsonData; ?>;

        var options = {
            series: [{
                name: 'ค่าใช้จ่าย ปี66',
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
                    formatter: function(value) {
                        return value.toLocaleString('en-US', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' บาท';
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
                    }
                }
            },
            xaxis: {
                categories: jsonData.map(item => item.pea),
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
                floating: false,
                offsetY: 5,
                offsetX: 0,
                onItemClick: {
                    toggleDataSeries: true // Enable toggling series on click
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();

    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Chart with PHP Data</title>
    <style>
        #chartdiv {
            width: 100%;
            height: 800px;
        }

    </style>
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
</head>

<body>
    <div id="chartdiv"></div>

    <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "budget";

        $conn = new mysqli($servername, $username, $password, $dbname);

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
        LIMIT 14
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
        $conn->close();
    ?>

    <script>
        am5.ready(function() {
            // Create root element
            var root = am5.Root.new("chartdiv");

            // Set themes
            root.setThemes([am5themes_Animated.new(root)]);

            // Create chart
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                paddingLeft: 0,
                wheelX: "panX",
                wheelY: "zoomX",
                layout: root.verticalLayout
            }));

            // Add legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            // Get data from PHP
            var data = <?php echo $jsonData; ?>;

            // Prepare data for chart
            var chartData = data.map(function(item) {
                return {
                    "pea": item.pea,
                    "total_criteria_expenses_2023": item.total_criteria_expenses_2023 / 1000000,
                    "total_budget_total_2024": item.total_budget_total_2024 / 1000000,
                    "total_criteria_expenses_2024": item.total_criteria_expenses_2024 / 1000000
                };
            });

            // Create axes
            var xRenderer = am5xy.AxisRendererX.new(root, {
                cellStartLocation: 0.1,
                cellEndLocation: 0.9,
                minorGridEnabled: true
            });

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "pea",
                renderer: xRenderer,
                tooltip: am5.Tooltip.new(root, {})
            }));

            xRenderer.grid.template.setAll({
                location: 1
            });

            xAxis.data.setAll(chartData);

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {
                    strokeOpacity: 0.1
                }),
                numberFormatter: am5.NumberFormatter.new(root, {
                    numberFormat: "#a"
                })
            }));

            // Add series
            function makeSeries(name, fieldName) {
                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                    name: name,
                    xAxis: xAxis,
                    yAxis: yAxis,
                    valueYField: fieldName,
                    categoryXField: "pea"
                }));

                series.columns.template.setAll({
                    tooltipText: "{name}, {categoryX}:{valueY}M",
                    width: am5.percent(90),
                    tooltipY: 0,
                    strokeOpacity: 0
                });

                series.data.setAll(chartData);

                // Make stuff animate on load
                series.appear();

                series.bullets.push(function() {
                    return am5.Bullet.new(root, {
                        locationY: 0,
                        sprite: am5.Label.new(root, {
                            text: "{valueY}M",
                            fill: root.interfaceColors.get("alternativeText"),
                            centerY: 0,
                            centerX: am5.p50,
                            populateText: true
                        })
                    });
                });

                legend.data.push(series);
            }

            makeSeries("Total Criteria Expenses 2023", "total_criteria_expenses_2023");
            makeSeries("Total Budget Total 2024", "total_budget_total_2024");
            makeSeries("Total Criteria Expenses 2024", "total_criteria_expenses_2024");

            // Make stuff animate on load
            chart.appear(1000, 100);
        });

    </script>
</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Chart</title>
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
        // PHP code to fetch data from database
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
            root.setThemes([
                am5themes_Animated.new(root)
            ]);

            // Create chart
            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: false,
                panY: false,
                wheelX: "panX",
                wheelY: "zoomX",
                paddingLeft: 0,
                layout: root.verticalLayout
            }));

            // Add legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            // Data
            var data = <?php echo $jsonData; ?>;

            // Convert values to millions
            data.forEach(function(item) {
                item.total_criteria_expenses_2023 = item.total_criteria_expenses_2023 / 1000000;
                item.total_budget_total_2024 = item.total_budget_total_2024 / 1000000;
                item.total_criteria_expenses_2024 = item.total_criteria_expenses_2024 / 1000000;
            });

            // Create axes
            var yAxis = chart.yAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "pea",
                renderer: am5xy.AxisRendererY.new(root, {
                    inversed: true,
                    cellStartLocation: 0.1,
                    cellEndLocation: 0.9,
                    minorGridEnabled: true
                })
            }));

            yAxis.data.setAll(data);

            var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererX.new(root, {
                    strokeOpacity: 0.1,
                    minGridDistance: 50
                }),
                min: 0
            }));

            // Format axis labels to display values in millions
            xAxis.get("renderer").labels.template.setAll({
                text: "{valueX}M"
            });

            // Add series
            function createSeries(field, name, isLine) {
                if (isLine) {
                    var series = chart.series.push(am5xy.LineSeries.new(root, {
                        name: name,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueXField: field,
                        categoryYField: "pea",
                        sequencedInterpolation: true,
                        tooltip: am5.Tooltip.new(root, {
                            pointerOrientation: "horizontal",
                            labelText: "[bold]{name}[/]\n{categoryY}: {valueX}M"
                        })
                    }));

                    series.strokes.template.setAll({
                        strokeWidth: 2,
                    });

                    series.bullets.push(function() {
                        return am5.Bullet.new(root, {
                            locationY: 0.5,
                            sprite: am5.Circle.new(root, {
                                radius: 5,
                                stroke: series.get("stroke"),
                                strokeWidth: 2,
                                fill: root.interfaceColors.get("background")
                            })
                        });
                    });
                } else {
                    var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                        name: name,
                        xAxis: xAxis,
                        yAxis: yAxis,
                        valueXField: field,
                        categoryYField: "pea",
                        sequencedInterpolation: true,
                        tooltip: am5.Tooltip.new(root, {
                            pointerOrientation: "horizontal",
                            labelText: "[bold]{name}[/]\n{categoryY}: {valueX}M"
                        })
                    }));

                    series.columns.template.setAll({
                        height: am5.p100,
                        strokeOpacity: 0
                    });

                    series.bullets.push(function() {
                        return am5.Bullet.new(root, {
                            locationX: 1,
                            locationY: 0.5,
                            sprite: am5.Label.new(root, {
                                centerY: am5.p50,
                                text: "{valueX}",
                                populateText: true
                            })
                        });
                    });

                    series.bullets.push(function() {
                        return am5.Bullet.new(root, {
                            locationX: 1,
                            locationY: 0.5,
                            sprite: am5.Label.new(root, {
                                centerX: am5.p100,
                                centerY: am5.p50,
                                text: "{name}",
                                fill: am5.color(0xffffff),
                                populateText: true
                            })
                        });
                    });
                }

                series.data.setAll(data);
                series.appear();

                return series;
            }

            createSeries("total_criteria_expenses_2023", "Total Criteria Expenses 2023", false);
            createSeries("total_criteria_expenses_2024", "Total Criteria Expenses 2024", false);
            createSeries("total_budget_total_2024", "Total Budget Total 2024", true);

            // Add legend
            var legend = chart.children.push(am5.Legend.new(root, {
                centerX: am5.p50,
                x: am5.p50
            }));

            legend.data.setAll(chart.series.values);

            // Add cursor
            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "zoomY"
            }));
            cursor.lineY.set("forceHidden", true);
            cursor.lineX.set("forceHidden", true);

            // Make stuff animate on load
            chart.appear(1000, 100);
        }); // end am5.ready()

    </script>
</body>

</html>

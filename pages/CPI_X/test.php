<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Value Formatting</title>
    <style>
        .negative {
            color: red;
        }
    </style>
</head>
<body>
    <?php
    function formatValue($value) {
        if ($value < 0) {
            $formattedValue = '(' . number_format(abs($value), 2) . ')';
            return '<span class="negative">' . $formattedValue . '</span>';
        } else {
            return number_format($value, 2);
        }
    }

    // Example usage
    $values = [10.5, -7.27, 3.14, -15.99];

    echo '<table class="table table-bordered">';
    echo '<thead><tr><th>Value</th></tr></thead>';
    echo '<tbody>';
    foreach ($values as $value) {
        echo '<tr><td>' . formatValue($value) . '</td></tr>';
    }
    echo '</tbody></table>';
    ?>
</body>
</html>

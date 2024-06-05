<?php
//$servername = "localhost";
//$username = "root";
//$password = "";
//$dbname = "budget";
//
//// Create connection
//$conn = new mysqli($servername, $username, $password, $dbname);
//
//// Check connection
//if ($conn->connect_error) {
// die("Connection failed: " . $conn->connect_error);
//}
//
//$sql = "
//SELECT
//
// a.node_description,
// a.total_criteria_expenses AS total_criteria_expenses_2023,
// b.total_budget_total AS total_budget_total_2024,
// b.total_criteria_expenses AS total_criteria_expenses_2024
//
//FROM
// aggregated_data_2023 a
//JOIN
// aggregated_data b ON a.profit_code = b.profit_code
//WHERE
// a.year = 2023
// AND b.year = 2024
//GROUP BY
//
// a.node_description
//
//";
//
//$result = $conn->query($sql);
//
//$data = array();
//if ($result->num_rows > 0) {
// while($row = $result->fetch_assoc()) {
// $data[] = $row;
// }
//} else {
// echo "0 results";
//}
////$conn->close();
//$jsonData = json_encode($data);
//echo $jsonData;



?>
<?php
// Database connection and query execution

// Sample data for demonstration purposes
$data = [
    ["node_description" => "Node 1", "total_criteria_expenses_2023" => 1362137.35, "total_budget_total_2024" => 6280957.92, "total_criteria_expenses_2024" => 820638.56],
    ["node_description" => "Node 2", "total_criteria_expenses_2023" => 1152073.07, "total_budget_total_2024" => 2089909.34, "total_criteria_expenses_2024" => 539978.24],
    // Add more data as needed
];

header('Content-Type: application/json');
echo json_encode($data);
?>

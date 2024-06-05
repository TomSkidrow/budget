<?php
require_once ('../includes/connect.php');

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


$sql_total = "
WITH LatestYearMonth AS (
	SELECT
		MAX( `year` ) AS latest_year,
		MAX( `month` ) AS latest_month 
	FROM
		total_result 
	WHERE
		`year` = ( SELECT MAX( `year` ) FROM total_result ) 
	),
	PreviousYearMonth AS ( SELECT ( SELECT MAX( `year` ) - 1 FROM total_result ) AS prev_year, ( SELECT latest_month FROM LatestYearMonth ) AS prev_month ) SELECT
	`year`,
	`month`,
	sum_budget_12m,
	sum_budget_total,
	sum_criteria_expenses,
	( sum_budget_12m * 0.80 - sum_criteria_expenses ) / 1000000 AS balance_budget_12m,
	( sum_criteria_expenses / ( sum_budget_12m * 0.80 ) ) * 100 AS percent_expense_vs_budget_12m,
	( sum_budget_12m * 0.80 ) / 1000000 AS budget_12m_80_million,
	( sum_criteria_expenses ) / 1000000 AS criteria_expenses_million,
	( sum_budget_total - sum_criteria_expenses ) / 1000000 AS balance_budget_total,
	sum_budget_total / 1000000 AS budget_total_million,
	( sum_criteria_expenses / sum_budget_total ) * 100 AS percent_expense_vs_budget_total
FROM
	total_result 
WHERE
	( `year`, `month` ) IN ( SELECT latest_year, latest_month FROM LatestYearMonth ) 
	OR ( `year`, `month` ) IN ( SELECT prev_year, prev_month FROM PreviousYearMonth ) 
ORDER BY
	`year` DESC,
	`month` DESC 
	LIMIT 1 
";
$result_total = $conn->query($sql_total);

if ($result_total->num_rows > 0) {
            $row = $result_total->fetch_assoc(); 
$budget_12m_80_million = $row['budget_12m_80_million'];
$criteria_expenses_million = $row['criteria_expenses_million'];
$balance_budget_12m = $row['balance_budget_12m'];
$percent_expense_vs_budget_12m = $row['percent_expense_vs_budget_12m'];
$budget_total_million = $row['budget_total_million'];
$criteria_expenses_million = $row['criteria_expenses_million'];
$balance_budget_total = $row['balance_budget_total'];
$percent_expense_vs_budget_total = $row['percent_expense_vs_budget_total'];
$budget_spent_percent = (($row['sum_budget_12m'] / 1000000) - $row['budget_12m_80_million']) / $row['budget_12m_80_million'] * 100;
$budget_spent_percent2 = (($row['budget_12m_80_million'] - ($row['sum_budget_total']  / 1000000)) / ($row['sum_budget_total'] / 1000000)) * 100;
}



$sql_table = "
WITH SpecifiedAcc AS (
	SELECT
		`month`,
		`year`,
		acc_code,
		acc,
		SUM( criteria_expenses ) AS sum_criteria_expense 
	FROM
		account_result 
	WHERE
		acc_code IN (
			'53010070',
			'53010090',
			'53050020',
			'53051010',
			'53010040',
			'53039010',
			'53031030',
			'52010030',
			'53010100',
			'53050010',
			'53030010',
			'53010080',
			'53051050',
			'52022030',
			'52022020',
			'52022050' 
		) 
	GROUP BY
		acc_code,
		`month`,
		`year` 
	),
	TotalSpecified AS ( SELECT `month`, `year`, SUM( sum_criteria_expense ) AS total_specified_expense FROM SpecifiedAcc GROUP BY `month`, `year` ),
	TotalAcc AS ( SELECT `month`, `year`, SUM( criteria_expenses ) AS total_expense FROM account_result GROUP BY `month`, `year` ),
	TotalSpecified2019 AS ( SELECT SUM( sum_criteria_expense ) AS total_specified_expense_2023, 2023 AS `year` FROM SpecifiedAcc WHERE `year` = 2023 ),
	CombinedResults AS (
	SELECT
		S.`month`,
		S.`year`,
		S.acc_code,
		S.acc,
		S.sum_criteria_expense,
		T.total_specified_expense,
		A.total_expense,
		( S.sum_criteria_expense / A.total_expense ) * 100 AS percentage_of_total,
		( S.sum_criteria_expense / T.total_specified_expense ) * 100 AS percentage_of_specified_total,
		( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) AS sum_criteria_expense_2024,
		( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) AS sum_criteria_expense_2023,
		(
			( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) - ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
		) AS difference,
		(
			(
				( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) - ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
			) / ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
		) * 100 AS percentage_change 
	FROM
		SpecifiedAcc S
		JOIN TotalSpecified T ON S.`month` = T.`month` 
		AND S.`year` = T.`year`
		JOIN TotalAcc A ON S.`month` = A.`month` 
		AND S.`year` = A.`year` 
	),
	CombinedResultsWithTotal AS (
	SELECT
		CR.`month`,
		CR.`year`,
		acc_code,
		acc,
		sum_criteria_expense,
		total_specified_expense,
		A.total_expense AS total_expense,-- Added table alias to make it unambiguous
		percentage_of_total,
		percentage_of_specified_total,
		sum_criteria_expense_2023,
		difference,
		percentage_change,
		( SELECT total_specified_expense_2023 FROM TotalSpecified2019 ) AS total_specified_expense_2023 
	FROM
		CombinedResults CR
		JOIN TotalAcc A ON CR.`year` = A.`year` -- Added table alias to make it unambiguous
		
	) SELECT
	CRWT.`month`,
	CRWT.`year`,
	acc_code,
	acc,
	sum_criteria_expense / 1000000 AS sum_criteria_expense,
	total_specified_expense / 1000000 AS total_specified_expense,
	FORMAT( total_expense, 2 ) AS total_expense,
	FORMAT( percentage_of_total, 2 ) AS percentage_of_total,
	percentage_of_specified_total AS percentage_of_specified_total,
	sum_criteria_expense_2023 / 1000000 AS sum_criteria_expense_2023,
	FORMAT( total_specified_expense_2023, 2 ) AS total_specified_expense_2023,
	difference / 1000000 AS difference,
	FORMAT( percentage_change, 2 ) AS percentage_change,
	( SELECT total_expense / 1000000 FROM TotalAcc WHERE `year` = 2023 ) AS total_expense_2023
FROM
	CombinedResultsWithTotal CRWT 
WHERE
	CRWT.`year` = 2024 
ORDER BY
	CRWT.sum_criteria_expense DESC
    
";
$result_table = $conn->query($sql_table);


$sql_table2 = "
WITH SpecifiedAcc AS (
	SELECT
		`month`,
		`year`,
		acc_code,
		acc,
		SUM( criteria_expenses ) AS sum_criteria_expense 
	FROM
		account_result 
	WHERE
		acc_code IN (
			'53010070',
			'53010090',
			'53050020',
			'53051010',
			'53010040',
			'53039010',
			'53031030',
			'52010030',
			'53010100',
			'53050010',
			'53030010',
			'53010080',
			'53051050',
			'52022030',
			'52022020',
			'52022050' 
		) 
	GROUP BY
		acc_code,
		`month`,
		`year` 
	),
	TotalSpecified AS ( SELECT `month`, `year`, SUM( sum_criteria_expense ) AS total_specified_expense FROM SpecifiedAcc GROUP BY `month`, `year` ),
	TotalAcc AS ( SELECT `month`, `year`, SUM( criteria_expenses ) AS total_expense FROM account_result GROUP BY `month`, `year` ),
	TotalSpecified2019 AS ( SELECT SUM( sum_criteria_expense ) AS total_specified_expense_2023, 2023 AS `year` FROM SpecifiedAcc WHERE `year` = 2023 ),
	CombinedResults AS (
	SELECT
		S.`month`,
		S.`year`,
		S.acc_code,
		S.acc,
		S.sum_criteria_expense,
		T.total_specified_expense,
		A.total_expense,
		( S.sum_criteria_expense / A.total_expense ) * 100 AS percentage_of_total,
		( S.sum_criteria_expense / T.total_specified_expense ) * 100 AS percentage_of_specified_total,
		( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) AS sum_criteria_expense_2024,
		( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) AS sum_criteria_expense_2023,
		(
			( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) - ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
		) AS difference,
		(
			(
				( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2024 AND acc_code = S.acc_code ) - ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
			) / ( SELECT SUM( criteria_expenses ) FROM account_result WHERE `year` = 2023 AND acc_code = S.acc_code ) 
		) * 100 AS percentage_change 
	FROM
		SpecifiedAcc S
		JOIN TotalSpecified T ON S.`month` = T.`month` 
		AND S.`year` = T.`year`
		JOIN TotalAcc A ON S.`month` = A.`month` 
		AND S.`year` = A.`year` 
	),
	CombinedResultsWithTotal AS (
	SELECT
		CR.`month`,
		CR.`year`,
		acc_code,
		acc,
		sum_criteria_expense,
		total_specified_expense,
		A.total_expense AS total_expense,
		percentage_of_total,
		percentage_of_specified_total,
		sum_criteria_expense_2023,
		difference,
		percentage_change,
		( SELECT total_specified_expense_2023 FROM TotalSpecified2019 ) AS total_specified_expense_2023 
	FROM
		CombinedResults CR
		JOIN TotalAcc A ON CR.`year` = A.`year` -- Added table alias to make it unambiguous
		
	) SELECT
	CRWT.`month`,
	CRWT.`year`,
	acc_code,
	acc,
	sum_criteria_expense / 1000000 AS sum_criteria_expense,
	total_specified_expense / 1000000 AS total_specified_expense,
	total_expense / 1000000 AS total_expense,
	percentage_of_total AS percentage_of_total,
	percentage_of_specified_total AS percentage_of_specified_total,
	sum_criteria_expense_2023 / 1000000 AS sum_criteria_expense_2023,
	total_specified_expense_2023 / 1000000 AS total_specified_expense_2023,
	difference / 1000000 AS difference,
	percentage_change AS percentage_change,
	( SELECT total_expense / 1000000 FROM TotalAcc WHERE `year` = 2023 ) AS total_expense_2023 
FROM
	CombinedResultsWithTotal CRWT 
WHERE
	CRWT.`year` = 2024 
ORDER BY
	CRWT.sum_criteria_expense DESC
    
";
$result_table2 = $conn->query($sql_table2);


$sql_table_LM = "
SELECT
	a.profit_code,
	p.pea_sname,
	(SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2024,
	(SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
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
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
    
";
$result_table_LM = $conn->query($sql_table_LM);


$sql_table_S_15 = "
SELECT
	a.profit_code,
	p.pea_sname,
	( SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000 ) AS sum_criteria_expense_2024,
	( SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000 ) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
	) 
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 15 OFFSET 0 

";
$result_table_S_15 = $conn->query($sql_table_S_15);

$sql_table_S_27 = "
SELECT
	a.profit_code,
	p.pea_sname,
	( SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000 ) AS sum_criteria_expense_2024,
	( SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000 ) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
		'E3015013',
		'E3016013',
		'E3022013',
		'E3023013',
		'E3024013',
		'E3032013',
		'E3033013',
		'E3042013',
		'E3043013',
		'E3044013',
		'E3045013',
		'E3046013',
		'E3047013',
		'E3052013',
		'E3053013',
		'E3062013',
		'E3063013',
		'E3064013',
		'E3072013',
		'E3083013',
		'E3092013',
		'E3102013',
		'E3112013',
		'E3122013',
		'E3123013',
		'E3132013',
		'E3142013' 
	) 
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 15 OFFSET 15 

";
$result_table_S_27 = $conn->query($sql_table_S_27);



$sql_table_XS_20 = "
SELECT
	a.profit_code,
	p.pea_sname,
	(SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2024,
	(SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
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
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 20 OFFSET 0  
";
$result_table_XS_20 = $conn->query($sql_table_XS_20);


$sql_table_XS_40 = "
SELECT
	a.profit_code,
	p.pea_sname,
	(SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2024,
	(SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
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
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 20 OFFSET 20  
";
$result_table_XS_40 = $conn->query($sql_table_XS_40);

$sql_table_XS_60 = "
SELECT
	a.profit_code,
	p.pea_sname,
	(SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2024,
	(SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
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
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 20 OFFSET 40   
";
$result_table_XS_60 = $conn->query($sql_table_XS_60);


$sql_table_XS_77 = "
SELECT
	a.profit_code,
	p.pea_sname,
	(SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2024,
	(SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) / 1000000) AS sum_criteria_expense_2023,
	(
		SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
	) / 1000000 AS difference,
	(
		(
			SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
		) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
	) AS percent_difference 
FROM
	account_result a
	JOIN profit p ON a.profit_code = p.profit_code 
WHERE
	a.profit_code IN (
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
	AND a.acc_code IN (
		'53010070',
		'53010090',
		'53050020',
		'53051010',
		'53010040',
		'53039010',
		'53031030',
		'52010030',
		'53010100',
		'53050010',
		'53030010',
		'53010080',
		'53051050',
		'52022030',
		'52022020',
		'52022050' 
	) 
GROUP BY
	a.profit_code,
	p.pea_sname 
ORDER BY
CASE
		
		WHEN (
			(
				SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
			) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
			) < 0 THEN
			0 ELSE 1 
		END ASC,
	CASE
			
			WHEN (
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
				) < 0 THEN
				(
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
					) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 ELSE (
					SUM( CASE WHEN a.`year` = 2024 THEN a.criteria_expenses ELSE 0 END ) - SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) 
				) / SUM( CASE WHEN a.`year` = 2023 THEN a.criteria_expenses ELSE 0 END ) * 100 
END
LIMIT 17 OFFSET 60   
";
$result_table_XS_77 = $conn->query($sql_table_XS_77);


$sql_graph1 = "
WITH LatestYearMonth AS (
	SELECT
		MAX( `year` ) AS latest_year,
		MAX( `month` ) AS latest_month 
	FROM
		total_result 
	WHERE
		`year` = ( SELECT MAX( `year` ) FROM total_result ) 
	),
	PreviousYearMonth AS ( SELECT ( SELECT MAX( `year` ) - 1 FROM total_result ) AS prev_year, ( SELECT latest_month FROM LatestYearMonth ) AS prev_month ) SELECT
	`year`,
	`month`,
	sum_budget_12m,
	sum_budget_total,
	sum_criteria_expenses,
	( sum_budget_12m * 0.80 - sum_criteria_expenses ) / 1000000 AS balance_budget_12m,
	( sum_criteria_expenses / ( sum_budget_12m * 0.80 ) ) * 100 AS percent_expense_vs_budget_12m,
	( sum_budget_12m * 0.80 ) / 1000000 AS budget_12m_80_million,
	( sum_criteria_expenses ) / 1000000 AS criteria_expenses_million,
	( sum_budget_total - sum_criteria_expenses ) / 1000000 AS balance_budget_total,
	sum_budget_total / 1000000 AS budget_total_million,
	( sum_criteria_expenses / sum_budget_total ) * 100 AS percent_expense_vs_budget_total 
FROM
	total_result 
WHERE
	( `year`, `month` ) IN ( SELECT latest_year, latest_month FROM LatestYearMonth ) 
	OR ( `year`, `month` ) IN ( SELECT prev_year, prev_month FROM PreviousYearMonth ) 
ORDER BY
	`year` DESC,
	`month` DESC  
       
";

$result_graph1 = $conn->query($sql_graph1);

if ($result_graph1->num_rows > 0)
{

    $data = [];
    while ($row = $result_graph1->fetch_assoc())
    {
        $data[] = ['year' => convertToThaiYear($row['year']), 'month' => convertToThaiMonth($row['month']), 'budget_12m_80_million' => (float)$row['budget_12m_80_million'], 'criteria_expenses_million' => (float)$row['criteria_expenses_million'], 'budget_total_million' => (float)$row['budget_total_million'], 'balance_budget_12m' => (float)$row['balance_budget_total'],'balance_budget_total' => (float)$row['balance_budget_12m']];
    }

}
    $jsonData1 = json_encode($data);
    echo "<script>var jsonData1 = $jsonData1;</script>"; 


$sql_graph2 = "
SELECT
	`year`,
	`month`,
	( sum_criteria_expenses / ( sum_budget_12m * 0.80 ) ) * 100 AS percent_expense_vs_budget_12m,
	( sum_criteria_expenses / sum_budget_total ) * 100 AS percent_expense_vs_budget_total 
FROM
	total_result 
GROUP BY
	`month`,
	`year` 
ORDER BY
	`year` DESC,
	`month` DESC
       
";
//            echo "<pre>$sql_line</pre>"; 
            
$result_graph2 = $conn->query($sql_graph2);

            if ($result_graph2->num_rows > 0) {
                $data = [];
                while ($row = $result_graph2->fetch_assoc()) {
                    $data[] = [
                        'percent_expense_vs_budget_12m' => (float) $row['percent_expense_vs_budget_12m'],
                        'percent_expense_vs_budget_total' => (float) $row['percent_expense_vs_budget_total'],
                        'month' => (float) $row['month'],
                        'year' => (float) $row['year']
                    ];
                }
                
                $jsonData2 = json_encode($data);
                
                echo "<script>var jsonData2 = $jsonData2;</script>"; 
            }

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
            $formattedValue = '(' . number_format(abs($value), 2) . ')';
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
	<link href="https://fonts.googleapis.com/css2?family=Prompt:wght@400;500;600&display=swap" rel="stylesheet">
	<!-- icons -->
	<link href="../../fonts/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/font-awesome/v6/css/all.css" rel="stylesheet" type="text/css" />
	<link href="../../fonts/material-design-icons/material-icon.css" rel="stylesheet" type="text/css" />
	<!--bootstrap -->
	<link href="../../assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<!-- Material Design Lite CSS -->
	<link rel="stylesheet" href="../../assets/plugins/material/material.min.css">
	<link rel="stylesheet" href="../../assets/css/material_style.css">
	<!-- Script ApexChart -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
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

	<!-- Script ApexChart -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
<!-- END HEAD -->
<style>
	#chart1,
	#chart2,
	#chart3,
	#chart4 {
		width: 100%;
		margin: 35px auto;
	}

	.apexcharts-tooltip-title {
		display: none;
	}

	.apexcharts-tooltip {
		display: flex;
		border: 0;
		box-shadow: none;
	}

	.apexcharts-text {
		font-family: 'Prompt', sans-serif !important;
	}

	/* Add border style for the table */
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


	/* Add border style for the table */
	.mdl-tabs__panel#tab7-panel table {
		border-collapse: collapse;
		width: 100%;
	}

	/* Add border style for table header cells */
	.mdl-tabs__panel#tab7-panel th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Add border style for table body cells */
	.mdl-tabs__panel#tab7-panel td {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Remove border from first table row */
	.mdl-tabs__panel#tab7-panel tr:first-child {
		border-top: none;
	}

	/* Remove border from last table row */
	.mdl-tabs__panel#tab7-panel tr:last-child {
		border-bottom: none;
	}

	/* Add border style for the table */
	.mdl-tabs__panel#tab8-panel table {
		border-collapse: collapse;
		width: 100%;
	}

	/* Add border style for table header cells */
	.mdl-tabs__panel#tab8-panel th {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Add border style for table body cells */
	.mdl-tabs__panel#tab8-panel td {
		border: 1px solid #ddd;
		padding: 8px;
		text-align: left;
	}

	/* Remove border from first table row */
	.mdl-tabs__panel#tab8-panel tr:first-child {
		border-top: none;
	}

	/* Remove border from last table row */
	.mdl-tabs__panel#tab8-panel tr:last-child {
		border-bottom: none;
	}

	.wrap-text {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
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
								<li><i class="fa fa-home"></i>&nbsp;<a class="parent-item" href="../dashboard">Home</a>&nbsp;<i class="fa fa-angle-right"></i>
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
									<header>การบริหารค่าใช้จ่ายจากการดำเนินงาน (CPI-X)</header>
								</div>
								<div class="card-body ">
									<div class="mdl-tabs mdl-js-tabs">
										<div class="mdl-tabs__tab-bar tab-left-side">
											<a href="#tab4-panel" class="mdl-tabs__tab tabs_three is-active">ผลการเบิกจ่ายเปรียบเทียบงบประมาณ,งบสะสม</a>
											<a href="#tab5-panel" class="mdl-tabs__tab tabs_three">ผลดำเนินการ 16 บัญชี</a>
											<a href="#tab6-panel" class="mdl-tabs__tab tabs_three">กฟส.ขนาด (L,M)</a>
											<a href="#tab7-panel" class="mdl-tabs__tab tabs_three">กฟส.ขนาด (S)</a>
											<a href="#tab8-panel" class="mdl-tabs__tab tabs_three">กฟส.ขนาด (XS)</a>
										</div>
										<div class="mdl-tabs__panel is-active p-t-20" id="tab4-panel">

											<div class="row"><br></div>
											<!-- start widget -->
											<div class="row">
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">งบประมาณปี <?php echo convertToThaiYear($latestYear); ?> (80%)</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($budget_12m_80_million,2); ?></h2>

																</div>


															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">เบิกจ่ายสุทธิ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($criteria_expenses_million,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">คงเหลือจากงบประมาณ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green">
																		<?php echo number_format($balance_budget_12m,2); ?></h2>
																</div>

															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">% เบิกจ่ายกับงบประมาณ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($percent_expense_vs_budget_12m,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
											</div>
											<!-- end widget -->
											<!-- start widget -->
											<div class="row">
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">งบประมาณสะสม มค-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($budget_total_million,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">เบิกจ่ายสุทธิ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($criteria_expenses_million,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">คงเหลือจากงบประมาณ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($balance_budget_total,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
												<div class="col-xl-3 col-lg-6">
													<div class="card comp-card">
														<div class="card-body">
															<div class="row align-items-center">
																<div class="col">
																	<div class="col mt-0">
																		<h4 class="info-box-title">% เบิกจ่ายกับงบประมาณ</h4>
																	</div>
																	<h2 class="mt-1 mb-3 info-box-title col-green"><?php echo number_format($percent_expense_vs_budget_total,2); ?></h2>

																</div>

															</div>
														</div>
													</div>
												</div>
											</div>

											<!-- end widget -->

											<!-- chart start -->
											<div class="row">
												<div class="col-12 col-sm-12 col-lg-6">
													<div class="card">
														<div class="card-head">
															<header>% เบิกจ่ายเปรียบเทียบงบประมาณ,งบประมาณสะสม</header>
														</div>
														<div class="card-body">
															<div id="chart2"></div>
														</div>
													</div>
												</div>
												<div class="col-12 col-sm-12 col-lg-6">
													<div class="card">
														<div class="card-head">
															<header>ผลการเบิกจ่ายเปรียบเทียบงบประมาณระหว่างปีและเดือนเดียวกัน</header>
														</div>
														<div class="card-body">
															<div id="chart1"></div>
														</div>
													</div>
												</div>
											</div>
											<!-- Chart end -->
										</div>


										<div class="mdl-tabs__panel p-t-20" id="tab5-panel">
											<div class="row"><br></div>
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th rowspan="2" style="text-align: center;">ลำดับ</th>
																					<th rowspan="2">ชื่อบัญชี</th>
																					<th rowspan="2" style="text-align: center;">สัดส่วนเบิกจ่ายจาก<br>CPI-X 16บัญชี</th>
																					<th rowspan="2" style="text-align: center;">สัดส่วนเบิกจ่ายจาก<br>CPI-X ทั้งหมด</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th colspan="2" style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																				</tr>
																				<tr>

																					<th style="text-align: center;">จำนวน</th>
																					<th style="text-align: center;">%</th>
																				</tr>

																			</thead>
																			<tbody>
																				<?php
$num = 0;
while ($row = $result_table->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['acc']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percentage_of_specified_total']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percentage_of_total']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?> </td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?> </td>
																					<td style="text-align: right;"><?php echo formatValue($row['percentage_change']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																			<tfoot>
																				<?php
$total = 0;
$total2 = 0;
$total3 = 0;
$total4 = 0; 
$total5 = 0;
$total6 = 0;
$total7 = 0;
                                                                                
$num_rows = $result_table2->num_rows;

if ($num_rows > 0) {
    $first_row_for_total4 = true; 
    $first_row_for_total5 = true;
    $first_row_for_total6 = true;

    while ($row = $result_table2->fetch_assoc()) {
        
        $raw_value  = $row['percentage_of_specified_total'];
        $raw_value2 = $row['percentage_of_total'];
        $raw_value3 = $row['sum_criteria_expense'];
        $raw_value4 = $row['total_expense'];
        $raw_value5 = $row['total_specified_expense_2023'];
        $raw_value6 = $row['total_expense_2023'];
        $raw_value7 = $row['difference'];
        
        
        $value  = floatval($raw_value);
        $value2 = floatval($raw_value2);
        $value3 = floatval($raw_value3);
        $value4 = floatval($raw_value4);
        $value5 = floatval($raw_value5);
        $value6 = floatval($raw_value6);
        $value7 = floatval($raw_value7);

        if (is_numeric($value)) {
            $total += $value;
        }
        if (is_numeric($value2)) {
            $total2 += $value2;
        }
        if (is_numeric($value3)) {
            $total3 += $value3;
        }
        if ($first_row_for_total4 && is_numeric($value4)) {
            $total4 = $value4;
            $first_row_for_total4 = false; 
        }
        if ($first_row_for_total5 && is_numeric($value5)) {
            $total5 = $value5;
            $first_row_for_total5 = false; 
        if ($first_row_for_total6 && is_numeric($value6)) {
            $total6 = $value6;
            $first_row_for_total6 = false; 
        }
        if (is_numeric($value7)) {
            $total7 += $value7;
        }
    }
  }
}

$percent_of_specified_total = formatValue($total);
$percentage_of_total = formatValue($total2);
$difference = formatValue(100 - $total2);
$total_percentage_of_total = formatValue((100 - $total2) + $total2);
$total_sum_criteria_expense = formatValue($total3);                                                                           
$total_expense = formatValue($total4);
$all_sum_criteria_expense = formatValue($total4 - $total3);
$total_specified_expense_2023 = formatValue($total5);
$total_expense_2023 =  formatValue($total6);
$other_acc_expense_2023 = formatValue($total6 - $total5);
$sum_difference = formatValue($total7); 
$other_acc_difference = formatValue(($total4 - $total3) - ($total6 - $total5));
$all_expense_difference = formatValue($total4 - $total6);
$acc16_percent_change = formatValue((($total3 - $total5) / $total5) * 100);          
$other_acc_percent_change = formatValue(((($total4 - $total3) - ($total6 - $total5)) / ($total6 - $total5)) * 100);
$all_percent_change = formatValue((($total4 - $total6) / $total6) * 100);
    
//echo "Percent of Specified Total: $percent_of_specified_total<br>";
//echo "Percentage of Total: $percentage_of_total<br>";
//echo "Difference: $difference<br>";
//echo "Total Percentage of Total: $total_percentage_of_total<br>";
//echo "Total Sum Criteria Expense: $total_sum_criteria_expense<br>";
//echo "All Sum Criteria Expense: $all_sum_criteria_expense<br>";
//echo "Total Expense : $total_expense<br>";
//echo "Total Specified Expense 2023 (in millions): $total_specified_expense_2023<br>";
//echo "Total Expense 2023 (in millions): $total_expense_2023<br>";
//echo "Other Accounted Expense 2023 (in millions): $other_acc_expense_2023<br>";
//echo "Sum of Differences: $sum_difference<br>";
//echo "other_acc_difference: $other_acc_difference<br>";
//echo "all_expense_difference: $all_expense_difference<br>";
//echo "acc16_percent_change: $acc16_percent_change<br>";
//echo "other_acc_percent_change: $other_acc_percent_change<br>";
//echo "all_percent_change: $all_percent_change<br>";                                                                                
?>


																				<tr>
																					<td colspan="2" style="text-align: center;">รวม 16 บัญชี</td>
																					<td style="text-align: right; text-decoration: underline;"> <?php echo "$percent_of_specified_total"; ?></td>
																					<td style="text-align: right;"><?php echo "$percentage_of_total"; ?></td>
																					<td style="text-align: right;"><?php echo "$total_sum_criteria_expense"; ?></td>
																					<td style="text-align: right;"><?php echo "$total_specified_expense_2023"; ?></td>
																					<td style="text-align: right;"><?php echo "$sum_difference"; ?></td>
																					<td style="text-align: right;"><?php echo "$acc16_percent_change"; ?></td>
																				</tr>
																				<tr>
																					<td colspan="2" style="text-align: center;">บัญชีอื่นๆ</td>
																					<td></td>
																					<td style="text-align: right;"><?php echo "$difference"; ?></td>
																					<td style="text-align: right;"><?php echo "$all_sum_criteria_expense"; ?></td>
																					<td style="text-align: right;"><?php echo "$other_acc_expense_2023"; ?></td>
																					<td style="text-align: right;"><?php echo "$other_acc_difference"; ?></td>
																					<td style="text-align: right;"><?php echo "$other_acc_percent_change"; ?></td>
																				</tr>
																				<tr>
																					<td colspan="2" style="text-align: center;">รวมทั้งสิ้น</td>
																					<td></td>
																					<td style="text-align: right; text-decoration: underline;"><?php echo "$total_percentage_of_total"; ?></td>
																					<td style="text-align: right; text-decoration: underline;"><?php echo "$total_expense"; ?></td>
																					<td style="text-align: right; text-decoration: underline;"><?php echo "$total_expense_2023"; ?></td>
																					<td style="text-align: right; text-decoration: underline;"><?php echo "$all_expense_difference"; ?></td>
																					<td style="text-align: right; text-decoration: underline;"><?php echo "$all_percent_change"; ?></td>
																				</tr>

																			</tfoot>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>


										<div class="mdl-tabs__panel p-t-20" id="tab6-panel">
											<div class="row"><br></div>
											<div class="row">
												<div class="col-md-12">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 0;
while ($row = $result_table_LM->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>


										<div class="mdl-tabs__panel p-t-20" id="tab7-panel">
											<div class="row"><br></div>
											<div class="row">
												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 0;
while ($row = $result_table_S_15->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>


												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 15;
while ($row = $result_table_S_27->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>


											</div>
										</div>





										<div class="mdl-tabs__panel p-t-20" id="tab8-panel">
											<div class="row"><br></div>
											<div class="row">

												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 0;
while ($row = $result_table_XS_20->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>


												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 20;
while ($row = $result_table_XS_40->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>



												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 40;
while ($row = $result_table_XS_60->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>


												<div class="col-sm-6">
													<div class="row">
														<div class="col-sm-12">
															<div class="card">
																<div class="card-head">
																	<header></header>
																	<div class="tools">
																		หน่วย : ล้านบาท
																	</div>
																</div>
																<div class="card-body ">
																	<div class="table-scrollable">
																		<table class="table">
																			<thead>
																				<tr>
																					<th style="text-align: center;">ลำดับ</th>
																					<th>หน่วยงาน</th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($latestMonth); ?> ปี <?php echo convertToThaiYear($latestYear); ?></th>
																					<th rowspan="2" style="text-align: center;">เบิกจ่ายสุทธิ <br>ม.ค.-<?php echo convertToThaiMonth($prevMonth); ?> ปี <?php echo convertToThaiYear($prevYear); ?></th>
																					<th style="text-align: center;">เพิ่มขึ้น/(ลดลง)</th>
																					<th style="text-align: center;">%</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php
$num = 60;
while ($row = $result_table_XS_77->fetch_assoc())
{
    $num++;
?>
																				<tr>
																					<td style="text-align: center;"><?php echo $num; ?></td>
																					<td><?php echo $row['pea_sname']; ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2024']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['sum_criteria_expense_2023']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['difference']); ?></td>
																					<td style="text-align: right;"><?php echo formatValue($row['percent_difference']); ?> </td>
																				</tr>
																				<?php } ?>

																			</tbody>
																		</table>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
												<!-- div row -->

											</div>
										</div>

										<!-- mdl tabs -->
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
	<!-- Common js-->
	<script src="../../assets/js/app.js"></script>
	<script src="../../assets/js/layout.js"></script>
	<script src="../../assets/js/theme-color.js"></script>
	<!-- Material -->
	<script src="../../assets/plugins/material/material.min.js"></script>
	<!-- chart js -->
	<script src="../../assets/plugins/sparkline/jquery.sparkline.js"></script>
	<script src="../../assets/js/pages/sparkline/sparkline-data.js"></script>
	<!-- end js include path -->

	<script>
		document.getElementById("goBack").addEventListener("click", function(event) {
			event.preventDefault();
			window.history.back();
		});
	</script>
	

	<!-- Start ApexChart -->
	<script>
		var jsonData1 = <?php echo $jsonData1; ?>;

		var options1 = {
			series: [{
				name: 'งบประมาณ (80%)',
				type: 'column',
				data: jsonData1.map(item => item.budget_12m_80_million)
			}, {
				name: 'เบิกจ่ายสุทธิ',
				type: 'column',
				data: jsonData1.map(item => item.criteria_expenses_million)
			}, {
				name: 'งบประมาณสะสม',
				type: 'column',
				data: jsonData1.map(item => item.budget_total_million)
			}, {
				name: 'คงเหลือจากงบประมาณ',
				type: 'column',
				data: jsonData1.map(item => item.balance_budget_12m)
			}, {
				name: 'คงเหลือจากงบประมาณสะสม',
				type: 'column',
				data: jsonData1.map(item => item.balance_budget_total)
			}],
			chart: {
				height: 350,
				type: 'line',
				stacked: false
			},
			dataLabels: {
				enabled: true,
				enabledOnSeries: [0, 1, 2, 3, 4],
				formatter: function(val, opts) {
					return (val).toLocaleString('en-US', {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2
					});
				},
				style: {
					fontSize: '12px',
					colors: ["#FFFFFF"]
				},
				offsetY: -10,
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
				width: [1, 1, 1, 1, 1],
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

						if (w.globals.series[seriesIndex].length < 1) {
							return '';
						} else {
							return value.toLocaleString('en-US', {
								minimumFractionDigits: 2,
								maximumFractionDigits: 2
							}) + ' ล้านบาท';
						}
					}
				}
			},


			yaxis: {
				labels: {
					formatter: function(val) {
						return (val).toLocaleString('en-US', {
							minimumFractionDigits: 0,
							maximumFractionDigits: 0
						}) + ' ล้านบาท';
					},
					style: {
						fontSize: '12px'
					}
				}
			},
			xaxis: {
				categories: jsonData1.map(item => `(${item.month} ${item.year})`),
				labels: {
					style: {
						fontFamily: 'Prompt, sans-serif',
					}
				},
				tickPlacement: 'between',
				tooltip: {
					enabled: false
				}
			},
			grid: {
				padding: {
					left: 10,
					right: 10,
					bottom: 30,
					top: 30
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						position: 'top',
					},
				}
			},
			legend: {
				show: true,
				position: 'bottom',
				horizontalAlign: 'center',
				floating: true,
				offsetY: 5,
				offsetX: 0,
				onItemClick: {
					toggleDataSeries: true
				}
			}
		};

		var chart1 = new ApexCharts(document.querySelector("#chart1"), options1);
		chart1.render();
	</script>

	<script>
		document.addEventListener("DOMContentLoaded", function() {
			if (typeof jsonData2 !== 'undefined' && jsonData2.length > 0) {

				var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
				var data2023_budget_12m = new Array(12).fill(0);
				var data2024_budget_12m = new Array(12).fill(0);
				var data2023_budget_total = new Array(12).fill(0);
				var data2024_budget_total = new Array(12).fill(0);

				jsonData2.forEach(function(item) {
					if (item.year === 2023) {
						data2023_budget_12m[item.month - 1] += parseFloat(item.percent_expense_vs_budget_12m);
						data2023_budget_total[item.month - 1] += parseFloat(item.percent_expense_vs_budget_total);
					} else if (item.year === 2024) {
						data2024_budget_12m[item.month - 1] += parseFloat(item.percent_expense_vs_budget_12m);
						data2024_budget_total[item.month - 1] += parseFloat(item.percent_expense_vs_budget_total);
					}
				});

				var options = {
					series: [{
						name: "% เบิกจ่ายเปรียบเทียบงบประมาณปี 66",
						data: data2023_budget_12m,
						color: '#D2D3D2'

					}, {
						name: "% เบิกจ่ายเปรียบเทียบงบประมาณสะสมปี 66",
						data: data2023_budget_total,
						color: '#808080'
					}, {
						name: "% เบิกจ่ายเปรียบเทียบงบประมาณปี 67",
						data: data2024_budget_12m,
						color: '#39FF33'
					}, {
						name: "% เบิกจ่ายเปรียบเทียบงบประมาณสะสมปี 67",
						data: data2024_budget_total,
						color: '#33A2FF'
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
						width: [3, 3, 3, 3],
						curve: 'smooth'
					},
					title: {
						text: '',
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
							formatter: function(value, {
								series,
								seriesIndex,
								dataPointIndex,
								w
							}) {
								if (w.globals.series[seriesIndex].length < 1) {
									return '';
								} else {
									return '' + value.toLocaleString('en-US', {
										minimumFractionDigits: 2,
										maximumFractionDigits: 2
									}) + ' %';
								}
							}
						}
					},
					yaxis: {
						labels: {
							formatter: function(val) {
								return (val).toLocaleString('en-US', {
									minimumFractionDigits: 0,
									maximumFractionDigits: 0
								}) + ' %';
							},
							style: {
								fontSize: '13px'
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
				console.log("ไม่พบข้อมูลหรือยังไม่ได้กำหนดค่าข้อมูล");
			}
		});
	</script>


</body>

</html>
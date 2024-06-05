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
	FORMAT( sum_criteria_expense, 2 ) AS sum_criteria_expense,
	FORMAT( total_specified_expense, 2 ) AS total_specified_expense,
	FORMAT( total_expense, 2 ) AS total_expense,
	FORMAT( percentage_of_total, 2 ) AS percentage_of_total,
	FORMAT( percentage_of_specified_total, 2 ) AS percentage_of_specified_total,
	FORMAT( sum_criteria_expense_2023, 2 ) AS sum_criteria_expense_2023,
	FORMAT( total_specified_expense_2023, 2 ) AS total_specified_expense_2023,
	FORMAT( difference, 2 ) AS difference,
	FORMAT( percentage_change, 2 ) AS percentage_change,
	FORMAT( ( SELECT total_expense FROM TotalAcc WHERE `year` = 2023 ), 2 ) AS total_expense_2023 
FROM
	CombinedResultsWithTotal CRWT 
WHERE
	CRWT.`year` = 2024 
ORDER BY
	CRWT.sum_criteria_expense DESC
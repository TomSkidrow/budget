SELECT
	SUM( z.budget_12m ) AS sum_budget_12m,
	SUM( z.budget_total ) AS sum_budget_total,
	SUM( z.criteria_expenses ) AS sum_criteria_expenses,
	z.`month`,
	z.`year`,
	p.profit_code,
	p.pea_sname 
FROM
	`zbudr091` z
	JOIN `acc_group` ag ON z.`acc_code` = ag.`acc_code`
	JOIN profit p ON z.profit_code = p.profit_code 
WHERE
	p.pea_sname IS NOT NULL 
GROUP BY
	p.profit_code,
	z.`month`,
	z.`year` 
ORDER BY
	z.`year`,
	z.`month`,
	p.profit_code ASC
    
 ==============================================================   
    SELECT
    z.acc_code,
    ag.acc,
    z.budget_12m,
    z.budget_total,
    z.criteria_expenses,
    z.`month`,
    z.`year`,
    p.profit_code,
    p.pea_sname 
FROM
    `zbudr091` z
    JOIN `acc_group` ag ON z.`acc_code` = ag.`acc_code`
    JOIN profit p ON z.profit_code = p.profit_code 
WHERE
    p.pea_sname IS NOT NULL 
    AND z.`month` = MONTH(NOW()) 
    AND z.`year` = YEAR(NOW()) - 1
    AND z.active = 'Y'
ORDER BY
    z.`year`,
    z.`month`,
    p.profit_code ASC;
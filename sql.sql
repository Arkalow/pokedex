SELECT * , SUM(coeff) AS total
FROM ( 
	SELECT *, 2 as coeff
	FROM pokemon 
	WHERE UPPER(nom) LIKE "%AU%"
	UNION
	SELECT *, 100 as coeff
	FROM pokemon 
	WHERE UPPER(nom) LIKE "%BULBA%"
) as A

group by id
ORDER BY total DESC
LIMIT 100

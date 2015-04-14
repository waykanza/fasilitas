--========================== UPPERCASE FIRST LETTER 
IF OBJECT_ID (N'dbo.UCF', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.UCF; 
GO 
CREATE FUNCTION dbo.UCF (@string VARCHAR(200)) RETURNS VARCHAR(200)
AS
BEGIN
	DECLARE 
	@Index INT = 1, 
	@ResultString VARCHAR(200) = ''
	
	WHILE (@Index <LEN(@string)+1)
	BEGIN
		IF (@Index = 1) 
		BEGIN
			SET @ResultString = @ResultString + UPPER(SUBSTRING(@string, @Index, 1)) 
			SET @Index = @Index+ 1 
		END
		ELSE IF ((SUBSTRING(@string, @Index-1, 1) =' 'or SUBSTRING(@string, @Index-1, 1) ='-' or SUBSTRING(@string, @Index+1, 1) ='-') and @Index+1 <> LEN(@string))
		BEGIN
			SET @ResultString = @ResultString + UPPER(SUBSTRING(@string,@Index, 1))
			SET @Index = @Index +1 
		END
		ELSE 
		BEGIN
			SET @ResultString = @ResultString + LOWER(SUBSTRING(@string,@Index, 1))
			SET @Index = @Index +1 
		END
	END 

	IF (@@ERROR <> 0) 
	BEGIN
		SET @ResultString = @string
	END 
	
	RETURN @ResultString
END
GO 

--========================== TRIM 
IF OBJECT_ID (N'dbo.TRIM', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.TRIM; 
GO 
CREATE FUNCTION dbo.TRIM (@s VARCHAR(MAX)) RETURNS VARCHAR(MAX) 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN LTRIM(RTRIM(@s)) 
END 
GO 

--========================== PTDF (First Periode) 
IF OBJECT_ID (N'dbo.PTDF', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.PTDF; 
GO 
CREATE FUNCTION dbo.PTDF (@p VARCHAR(8)) RETURNS DATE 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN (CAST(@p + '01' AS DATE)) 
END 
GO 

--========================== PTDL (Last Periode) 
IF OBJECT_ID (N'dbo.PTDL', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.PTDL; 
GO 
CREATE FUNCTION dbo.PTDL (@p VARCHAR(8)) RETURNS DATE 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN (DATEADD(s,-1,DATEADD(MM, DATEDIFF(MM,0,CAST(@p + '01' AS DATE))+1,0))) 
END 
GO 

--========================== PTPS (Periode to string MM-YYYY)
IF OBJECT_ID (N'dbo.PTPS', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.PTPS; 
GO 
CREATE FUNCTION dbo.PTPS (@p VARCHAR(8)) RETURNS VARCHAR(9) 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN (RIGHT(@p,2) + '-' + LEFT(@p,4)) 
END 
GO 

--========================== ETN (Empty to NULL)
IF OBJECT_ID (N'dbo.ETN', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.ETN; 
GO 
CREATE FUNCTION dbo.ETN (@v VARCHAR(MAX)) RETURNS VARCHAR(MAX) 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN NULLIF(@v,'') 
END 
GO 

--========================== PPN 
IF OBJECT_ID (N'dbo.PPN', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.PPN; 
GO 
CREATE FUNCTION dbo.PPN (@p NUMERIC(30,2), @v NUMERIC(30,2)) RETURNS NUMERIC(30,2) 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN @v * @p/(100+@p) 
END 
GO 

--========================== DPP 
IF OBJECT_ID (N'dbo.DPP', N'FN') IS NOT NULL 
	DROP FUNCTION dbo.DPP; 
GO 
CREATE FUNCTION dbo.DPP (@p NUMERIC(30,2), @v NUMERIC(30,2)) RETURNS NUMERIC(30,2) 
WITH EXECUTE AS CALLER 
AS 
BEGIN 
	RETURN @v * 100/(100+@p)
END 
GO 

--========================== TESTER 
DECLARE 
@d DATE = CAST('2004/12/31' AS DATE), 
@v NUMERIC(30,2) = 12000, 
@p NUMERIC(30,2) = 20 

SELECT 
	dbo.PTDF('201312') AS PTDF 
	,dbo.PTDL('201312') AS PTDL 
	,dbo.PTPS('201312') AS PTPS 
	,dbo.TRIM('') AS TRIM 
	,dbo.ETN('') AS ETN 
	,dbo.PPN(@p, @v) AS PPN 
	,dbo.DPP(@p, @v) AS DPP 
	,dbo.UCF('asdas asdasd asd asd') AS UCF 
GO 



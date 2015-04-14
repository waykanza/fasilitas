
-- Date To Periode Int
IF OBJECT_ID (N'dbo.DTPI', N'FN') IS NOT NULL
    DROP FUNCTION dbo.DTPI;
GO
CREATE FUNCTION dbo.DTPI (@d DATE) RETURNS INT
WITH EXECUTE AS CALLER
AS
BEGIN
	DECLARE @p INT = CAST(CONVERT(VARCHAR(6),@d,112) AS INT)
	RETURN(@p)
END
GO

-- Date To Periode Varchar
IF OBJECT_ID (N'dbo.DTPV', N'FN') IS NOT NULL
    DROP FUNCTION dbo.DTPV;
GO
CREATE FUNCTION dbo.DTPV (@d DATE) RETURNS VARCHAR(6)
WITH EXECUTE AS CALLER
AS
BEGIN
	RETURN(CONVERT(VARCHAR(6),@d,112))
END
GO

-- PTDF
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

-- PTDL
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

-- PTPS
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

-- CYMD
IF OBJECT_ID (N'dbo.CYMD', N'FN') IS NOT NULL
    DROP FUNCTION dbo.CYMD;
GO
CREATE FUNCTION dbo.CYMD (@d VARCHAR(10)) RETURNS DATE
WITH EXECUTE AS CALLER
AS
BEGIN
	RETURN (CASE WHEN NULLIF(@d,'') IS NOT NULL THEN CAST(@d AS DATE) ELSE NULL END)
END
GO

-- ETN
IF OBJECT_ID (N'dbo.ETN', N'FN') IS NOT NULL
    DROP FUNCTION dbo.ETN;
GO
CREATE FUNCTION dbo.ETN (@v VARCHAR(1000)) RETURNS VARCHAR(1000)
WITH EXECUTE AS CALLER
AS
BEGIN
	RETURN NULLIF(@v,'')
END
GO

-- ETN
IF OBJECT_ID (N'dbo.ETN', N'FN') IS NOT NULL
    DROP FUNCTION dbo.ETN;
GO
CREATE FUNCTION dbo.ETN (@v VARCHAR(1000)) RETURNS VARCHAR(1000)
WITH EXECUTE AS CALLER
AS
BEGIN
	RETURN NULLIF(@v,'')
END
GO

-- TESTER
DECLARE @d DATE = CAST('2004/12/31' AS DATE)

SELECT 
	 dbo.DTPV(@d) AS DTPV
	,dbo.DTPI(@d) AS DTPI
	,dbo.PTDF('201312') AS PTDF
	,dbo.PTDL('201312') AS PTDL
	,dbo.PTPS('201312') AS PTPS
	,dbo.CYMD('20131231') AS CYMD
	,dbo.ETN('') AS ETN 
	
GO

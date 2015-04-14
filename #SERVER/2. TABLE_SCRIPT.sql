-- =========================== HELPER ============================== 
-- DATETIME TO STRING 
-- CONVERT(VARCHAR (10), FILED, 105) # DD-MM-YYYY 
-- CONVERT(VARCHAR(6), FIELD, 112) # YYYYMM 
-- CONVERT(VARCHAR(19), FILED, 120) # YYYY-MM-DD HH:MI:SS 

-- STRING TO DATETIME 
-- CONVERT(DATETIME, '$value', 105) # DD-MM-YYYY 


-- KWT_USER 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_USER') AND type in (N'U')) 
DROP TABLE KWT_USER 
GO 
CREATE TABLE KWT_USER 
( 
	ID_USER INT NOT NULL IDENTITY(1,1) PRIMARY KEY , 
	LOGIN_USER VARCHAR (100) NOT NULL UNIQUE ,
	NAMA_USER VARCHAR (100) NOT NULL , 
	PASS_USER VARCHAR (100) NOT NULL , 
	AKTIF_USER INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_USER_MODUL 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_USER_MODUL') AND type in (N'U')) 
DROP TABLE KWT_USER_MODUL 
GO 
CREATE TABLE KWT_USER_MODUL 
( 
	ID_USER INT NOT NULL , 
	ID_MODUL VARCHAR (100) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_PARAMETER 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PARAMETER') AND type in (N'U')) 
DROP TABLE KWT_PARAMETER 
GO 
CREATE TABLE KWT_PARAMETER 
( 
	-- JRP 
	JRP_PT VARCHAR (100) ,			JRP_KODE_POS VARCHAR (5) , 
	JRP_ALAMAT_1 VARCHAR (200) ,	JRP_TELP VARCHAR (100) , 
	JRP_ALAMAT_2 VARCHAR (200) ,	JRP_FAX VARCHAR (100) , 
	JRP_KOTA VARCHAR (100) ,		JRP_EMAIL VARCHAR (100) , 
	
	-- UNIT 
	UNIT_NAMA VARCHAR (100) ,		UNIT_KODE_POS VARCHAR (5) , 
	UNIT_ALAMAT_1 VARCHAR (200) ,	UNIT_TELP VARCHAR (100) , 
	UNIT_ALAMAT_2 VARCHAR (200) ,	UNIT_FAX VARCHAR (100) , 
	UNIT_KOTA VARCHAR (100) ,		UNIT_EMAIL VARCHAR (100) , 
	
	-- GLOBAL 
	---JABATAN 
	NAMA_PIMPINAN VARCHAR (100),		JBT_PIMPINAN VARCHAR (100), 
	NAMA_PAJAK VARCHAR (100),			JBT_PAJAK VARCHAR (100), 
	NAMA_ADM VARCHAR (100),	JBT_ADM VARCHAR (100), 
	
	---REG 
	COU_NP NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_NPWP VARCHAR (30) , 
	PERSEN_PPN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	REG_FP VARCHAR (15) , 
	COU_FP NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	DENDA_RUPIAH NUMERIC (25) NOT NULL DEFAULT 0 , 
	DENDA_PERSEN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	
	-- KAVLING KOSONG 
	ADM_KV NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_IVC_KV VARCHAR (30) , 
	COU_IVC_KV NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_KV VARCHAR (30) , 
	COU_KWT_KV NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- MASA MEMBANGUN 
	ADM_BG NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_IVC_BG VARCHAR (30) , 
	COU_IVC_BG NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_BG VARCHAR (30) , 
	COU_KWT_BG NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- HUNIAN 
	ADM_HN NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_HN VARCHAR (30) , 
	COU_IVC_HN NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_HN VARCHAR (30) , 
	COU_KWT_HN NUMERIC (25) NOT NULL DEFAULT 0 , 

	-- RENOVASI 
	ADM_RV NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_RV VARCHAR (30) , 
	COU_IVC_RV NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_RV VARCHAR (30) , 
	COU_KWT_RV NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- MASA MEMBANGUN (DEPOSIT) 
	ADM_LBG NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_LBG VARCHAR (30) , 
	COU_IVC_LBG NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_LBG VARCHAR (30) , 
	COU_KWT_LBG NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- RENOVASI (DEPOSIT) 
	ADM_LRV NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_LRV VARCHAR (30) , 
	COU_IVC_LRV NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_LRV VARCHAR (30) , 
	COU_KWT_LRV NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- MASA MEMBANGUN (DEPOSIT) 
	ADM_DBG NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_DBG VARCHAR (30) , 
	COU_IVC_DBG NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_DBG VARCHAR (30) , 
	COU_KWT_DBG NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	-- RENOVASI (DEPOSIT) 
	ADM_DRV NUMERIC (6) NOT NULL DEFAULT 0 , 
	REG_IVC_DRV VARCHAR (30) , 
	COU_IVC_DRV NUMERIC (25) NOT NULL DEFAULT 0 , 
	REG_KWT_DRV VARCHAR (30) , 
	COU_KWT_DRV NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_BANK 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_BANK') AND type in (N'U')) 
DROP TABLE KWT_BANK 
GO 
CREATE TABLE KWT_BANK 
( 
	KODE_BANK VARCHAR (3) PRIMARY KEY , 
	NAMA_BANK VARCHAR (40) , 
	CABANG_BANK VARCHAR (30) , 
	ALAMAT_BANK TEXT , 
	COU_BD NUMERIC (25) NOT NULL DEFAULT 0 , 
	COU_BDT NUMERIC (25) NOT NULL DEFAULT 0 , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_SEKTOR 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_SEKTOR') AND type in (N'U')) 
DROP TABLE KWT_SEKTOR 
GO 
CREATE TABLE KWT_SEKTOR 
( 
	KODE_SEKTOR VARCHAR (50) PRIMARY KEY , 
	NAMA_SEKTOR VARCHAR (40) , 
	KODE_PEL VARCHAR (2) , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_CLUSTER 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_CLUSTER') AND type in (N'U')) 
DROP TABLE KWT_CLUSTER 
GO 
CREATE TABLE KWT_CLUSTER 
( 
	KODE_CLUSTER VARCHAR (50) PRIMARY KEY , 
	KODE_SEKTOR VARCHAR (50) , 
	NAMA_CLUSTER VARCHAR (40) , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_SK_AIR 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_SK_AIR') AND type in (N'U')) 
DROP TABLE KWT_SK_AIR 
GO 
CREATE TABLE KWT_SK_AIR 
( 
	KODE_SK VARCHAR (3) PRIMARY KEY , 
	NO_SK VARCHAR (30) , 
	TGL_SK DATE , 
	TGL_BERLAKU DATE , 
	PEMBUAT VARCHAR (30) , 
	STATUS_SK INT NOT NULL DEFAULT 0 , -- (0 = TDK. AKTIF) (1 = AKTIF) 
	KET TEXT , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_TIPE_AIR 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_TIPE_AIR') AND type in (N'U')) 
DROP TABLE KWT_TIPE_AIR 
GO 
CREATE TABLE KWT_TIPE_AIR 
( 
	KODE_TIPE VARCHAR (2) PRIMARY KEY , 
	NAMA_TIPE VARCHAR (30) , 
	PERSEN_ASUMSI_1 NUMERIC (6, 2) NOT NULL DEFAULT 0 , 
	PERSEN_ASUMSI_2 NUMERIC (6, 2) NOT NULL DEFAULT 0 , 
	PERSEN_ASUMSI_3 NUMERIC (6, 2) NOT NULL DEFAULT 0 , 
	PERSEN_ASUMSI_4 NUMERIC (6, 2) NOT NULL DEFAULT 0 , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_TARIF_AIR 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_TARIF_AIR') AND type in (N'U')) 
DROP TABLE KWT_TARIF_AIR 
GO 
CREATE TABLE KWT_TARIF_AIR 
( 
	KEY_AIR VARCHAR (50) PRIMARY KEY , 
	KODE_SK VARCHAR (3) , 
	KODE_TIPE VARCHAR (2) , 
	ABONEMEN NUMERIC (6) NOT NULL DEFAULT 0 , 
	STAND_MIN_PAKAI NUMERIC (5) NOT NULL DEFAULT 0 , 
	BLOK1 NUMERIC (5) NOT NULL DEFAULT 0 , 
	BLOK2 NUMERIC (5) NOT NULL DEFAULT 0 , 
	BLOK3 NUMERIC (5) NOT NULL DEFAULT 0 , 
	BLOK4 NUMERIC (5) NOT NULL DEFAULT 0 , 
	TARIF1 NUMERIC (8) NOT NULL DEFAULT 0 , 
	TARIF2 NUMERIC (8) NOT NULL DEFAULT 0 , 
	TARIF3 NUMERIC (8) NOT NULL DEFAULT 0 , 
	TARIF4 NUMERIC (8) NOT NULL DEFAULT 0 , 
	KET TEXT , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_SK_IPL 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_SK_IPL') AND type in (N'U')) 
DROP TABLE KWT_SK_IPL 
GO 
CREATE TABLE KWT_SK_IPL 
( 
	KODE_SK VARCHAR (3) PRIMARY KEY , 
	NO_SK VARCHAR (30) , 
	TGL_SK DATE , 
	TGL_BERLAKU DATE , 
	PEMBUAT VARCHAR (30) , 
	STATUS_SK INT, -- (0 = TDK. AKTIF) (1 = AKTIF) 
	KET TEXT , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_TIPE_IPL 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_TIPE_IPL') AND type in (N'U')) 
DROP TABLE KWT_TIPE_IPL 
GO 
CREATE TABLE KWT_TIPE_IPL 
( 
	KODE_TIPE VARCHAR (9) PRIMARY KEY , 
	NAMA_TIPE VARCHAR (100) , 
	STATUS_BLOK INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_TARIF_IPL 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_TARIF_IPL') AND type in (N'U')) 
DROP TABLE KWT_TARIF_IPL 
GO 
CREATE TABLE KWT_TARIF_IPL 
( 
	KEY_IPL VARCHAR (50) PRIMARY KEY , 
	KODE_SK VARCHAR (3) , 
	KODE_TIPE VARCHAR (9) , 
	TIPE_TARIF_IPL INT NOT NULL DEFAULT 0 ,		-- TIPE_TARIF (0 = TETAP) (1 = PER METER) 
	TARIF_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	NILAI_DEPOSIT NUMERIC (15) NOT NULL DEFAULT 0 , 
	KET TEXT , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_ZMB 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_ZMB') AND type in (N'U')) 
DROP TABLE KWT_ZMB 
GO 
CREATE TABLE KWT_ZMB 
( 
	KODE_ZONA VARCHAR (50) PRIMARY KEY , 
	NAMA_ZONA VARCHAR (30) , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_DISKON_KHUSUS 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_DISKON_KHUSUS') AND type in (N'U')) 
DROP TABLE KWT_DISKON_KHUSUS 
GO 
CREATE TABLE KWT_DISKON_KHUSUS 
( 
	ID_DISKON INT NOT NULL IDENTITY(1,1) PRIMARY KEY ,	-- Penambahan (Untuk Proses Edit) 
	KODE_BLOK VARCHAR (100) , 
	PERIODE_IPL_AWAL VARCHAR (6) , 
	PERIODE_IPL_AKHIR VARCHAR (6) , 
	DISKON_AIR_NILAI NUMERIC (12, 2) NOT NULL DEFAULT 0 , 
	DISKON_IPL_NILAI NUMERIC (12, 2) NOT NULL DEFAULT 0 , 
	DISKON_AIR_PERSEN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	DISKON_IPL_PERSEN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	KET TEXT , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_PELANGGAN_LOOKUP 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PELANGGAN_LOOKUP') AND type in (N'U')) 
DROP TABLE KWT_PELANGGAN_LOOKUP 
GO 
CREATE TABLE KWT_PELANGGAN_LOOKUP 
( 
	NO_KTP			VARCHAR (100) PRIMARY KEY , 
	NAMA_PELANGGAN	VARCHAR (200) , 
	NPWP			VARCHAR (100) , 
	ALAMAT			TEXT , 
	NO_TELEPON		VARCHAR (100) , 
	NO_HP			VARCHAR (100) , 
	KODE_BANK		VARCHAR (3) , 
	NO_REKENING		VARCHAR (40) , 
	
	SM_NAMA_PELANGGAN VARCHAR (200) , 
	SM_NO_KTP VARCHAR (100) , 
	SM_NPWP VARCHAR (100) , 
	SM_NO_HP VARCHAR (100) , 
	SM_NO_TELEPON VARCHAR (100) , 
	SM_ALAMAT TEXT 
) 
GO 

-- KWT_PELANGGAN 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PELANGGAN') AND type in (N'U')) 
DROP TABLE KWT_PELANGGAN 
GO 
CREATE TABLE KWT_PELANGGAN 
( 
	NO_PELANGGAN VARCHAR (100) PRIMARY KEY , 
	TGL_PPJB DATE , 
	INFO_TAGIHAN INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	
	KODE_SEKTOR VARCHAR (50) , 
	KODE_CLUSTER VARCHAR (50) , 
	KODE_BLOK VARCHAR (100) NOT NULL UNIQUE , 
	LUAS_KAVLING NUMERIC (17, 2) DEFAULT 0 , 
	LUAS_BANGUNAN NUMERIC (17, 2) DEFAULT 0 , 
	STATUS_BLOK INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
	
	NAMA_PELANGGAN VARCHAR (200) , 
	NO_KTP VARCHAR (100) , 
	NPWP VARCHAR (100) , 
	NO_HP VARCHAR (100) , 
	NO_TELEPON VARCHAR (100) , 
	ALAMAT TEXT , 
	
	AKTIF_SM INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	SM_NAMA_PELANGGAN VARCHAR (200) , 
	SM_NO_KTP VARCHAR (100) , 
	SM_NPWP VARCHAR (100) , 
	SM_NO_HP VARCHAR (100) , 
	SM_NO_TELEPON VARCHAR (100) , 
	SM_ALAMAT TEXT , 
	
	-- PEMBAYARAN 
	AKTIF_AD INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	KODE_BANK VARCHAR (3) , 
	NO_REKENING VARCHAR (50) , 
	
	GOLONGAN INT NOT NULL DEFAULT 0 , -- (0 = STANDAR) (1 = BISNIS) 
	TIPE_DENDA INT NOT NULL DEFAULT 0 , -- (0 = RUPIAH) (1 = PERSEN) 
	
	-- AIR 
	AKTIF_AIR INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	KODE_ZONA VARCHAR (50) , 
	TIPE_AIR VARCHAR (2) , 
	KEY_AIR VARCHAR (50) , 
	
	TGL_PEMUTUSAN DATE , 
	PETUGAS VARCHAR (100) , 
	PERIODE_PUTUS VARCHAR (6) , 
	
	-- IPL 
	AKTIF_IPL INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	TIPE_IPL VARCHAR (9) , 
	KEY_IPL VARCHAR (50) , 
	
	KET TEXT , 
	
	STATUS_JOIN INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	JOIN_TO VARCHAR (30) , 
	USER_JOIN VARCHAR (100) , 
	JOIN_DATE DATETIME2 (0) , 
	
	STATUS_SPLIT INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	SPLIT_FROM VARCHAR (30) , 
	USER_SPLIT VARCHAR (100) , 
	SPLIT_DATE DATETIME2 (0) , 
	
	DISABLED INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_PELANGGAN_IMP 
--=============================== 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PELANGGAN_IMP') AND type in (N'U')) 
DROP TABLE KWT_PELANGGAN_IMP 
GO 
SELECT * INTO KWT_PELANGGAN_IMP FROM KWT_PELANGGAN WHERE 1=0 
GO 

ALTER TABLE KWT_PELANGGAN_IMP ADD PRIMARY KEY (NO_PELANGGAN) 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_INFO_TAGIHAN DEFAULT 0 FOR INFO_TAGIHAN 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT UQ_KODE_BLOK UNIQUE (KODE_BLOK)
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_LUAS_KAVLING DEFAULT 0 FOR LUAS_KAVLING 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_LUAS_BANGUNAN DEFAULT 0 FOR LUAS_BANGUNAN 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_STATUS_BLOK DEFAULT 0 FOR STATUS_BLOK 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_AKTIF_SM DEFAULT 0 FOR AKTIF_SM 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_AKTIF_AD DEFAULT 0 FOR AKTIF_AD 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_GOLONGAN DEFAULT 0 FOR GOLONGAN 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_TIPE_DENDA DEFAULT 0 FOR TIPE_DENDA 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_AKTIF_AIR DEFAULT 0 FOR AKTIF_AIR 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_AKTIF_IPL DEFAULT 0 FOR AKTIF_IPL 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_STATUS_JOIN DEFAULT 0 FOR STATUS_JOIN 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_STATUS_SPLIT DEFAULT 0 FOR STATUS_SPLIT 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_DISABLED DEFAULT 0 FOR DISABLED 
ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_CREATED_DATE DEFAULT GETDATE() FOR CREATED_DATE 

ALTER TABLE KWT_PELANGGAN_IMP ADD CONSTRAINT DF_STATUS_PROSES DEFAULT 0 FOR STATUS_PROSES 
GO 


-- KWT_PEMUTUSAN_AIR 
--=============================== 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PEMUTUSAN_AIR') AND type in (N'U')) 
DROP TABLE KWT_PEMUTUSAN_AIR 
GO 
CREATE TABLE KWT_PEMUTUSAN_AIR 
( 
	NO_PELANGGAN VARCHAR (100) , 
	PERIODE_PUTUS VARCHAR (6) , 
	TGL_PEMUTUSAN DATE , 
	PETUGAS VARCHAR (100) , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 
--=============================== MASA MEMBANGUN =============================== 
-- KWT_PERIODE_DEPOSIT 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PERIODE_DEPOSIT') AND type in (N'U')) 
DROP TABLE KWT_PERIODE_DEPOSIT 
GO 
CREATE TABLE KWT_PERIODE_DEPOSIT 
( 
	ID_DEPOSIT VARCHAR (100) PRIMARY KEY ,			-- TRX#PERIODE_IPL_AWAL#KODE_BLOK 
	TRX INT NOT NULL , 
		-- 7 = MASA MEMBANGUN (DEPOSIT) 
		-- 8 = RENOVASI (DEPOSIT) 
		
	KODE_BLOK VARCHAR (100) , 
	
	PERIODE_IPL_AWAL VARCHAR (6) , 
	PERIODE_IPL_AKHIR VARCHAR (6) , 
	JUMLAH_PERIODE_IPL INT NOT NULL DEFAULT 1 , 
	
	TIPE_DEPOSIT INT NOT NULL DEFAULT 0 , -- (0 = TDK. MENGGUNAKAN DEPOSIT [NOL]) (1 = DARI DEPOSIT TARIF IPL) 
	
	NILAI_DEPOSIT NUMERIC (15) NOT NULL DEFAULT 0 , 
	NILAI_LAIN_LAIN NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	KET_DEPOSIT TEXT , 
	KET_LAIN_LAIN TEXT , 
	
	STATUS_PROSES INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() , 
	
	--TEMP
	ID_TEMP INT 
) 
GO 

--============================================== PEMBAYARAN ========================================== 
-- KWT_PEMBAYARAN_AI 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PEMBAYARAN_AI') AND type in (N'U')) 
DROP TABLE KWT_PEMBAYARAN_AI 
GO 
CREATE TABLE KWT_PEMBAYARAN_AI ( 
	ID_PEMBAYARAN VARCHAR (200) PRIMARY KEY ,	-- TRX#PERIODE_TAG#NO_PELANGGAN 
	TRX INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
		
		-- 5 = MASA MEMBANGUN (LAIN-LAIN)
		-- 6 = RENOVASI (LAIN-LAIN)
		
		-- 7 = MASA MEMBANGUN (DEPOSIT)
		-- 8 = RENOVASI (DEPOSIT)
		
	NO_INVOICE VARCHAR (150) , 
	TGL_JATUH_TEMPO DATE , 
	KET_IVC TEXT , 
	
	-- PERIODE 
	PERIODE_TAG VARCHAR (6) , 
	PERIODE_AIR VARCHAR (6) , 
	PERIODE_IPL_AWAL VARCHAR (6) , 
	PERIODE_IPL_AKHIR VARCHAR (6) , 
	JUMLAH_PERIODE_IPL INT, 
	
	NO_PELANGGAN VARCHAR (100) , 
	
	KODE_SEKTOR VARCHAR (50) , 
	KODE_CLUSTER VARCHAR (50) , 
	KODE_BLOK VARCHAR (100) , 
	STATUS_BLOK INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
	KODE_ZONA VARCHAR (50) , 
	
	AKTIF_AIR INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	AKTIF_IPL INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	
	KEY_AIR VARCHAR (50) , 
	KEY_IPL VARCHAR (50) , 
	
	GOLONGAN INT NOT NULL DEFAULT 0 , -- (0 = STANDAR) (1 = BISNIS) 
	TIPE_DENDA INT NOT NULL DEFAULT 0 , -- (0 = RUPIAH) (1 = PERSEN) 
	
	-- PERHITUNGAN AIR 
	STAND_LALU NUMERIC (15) NOT NULL DEFAULT 0 , 
	STAND_ANGKAT NUMERIC (15) NOT NULL DEFAULT 0 , 
	STAND_AKHIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	BLOK1 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK2 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK3 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK4 NUMERIC (10) NOT NULL DEFAULT 0 , 
	STAND_MIN_PAKAI NUMERIC (10) NOT NULL DEFAULT 0 , 
	TARIF1 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF2 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF3 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF4 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF_MIN_PAKAI NUMERIC (10) NOT NULL DEFAULT 0 , 
	
	-- PERHITUNGAN IPL 
	LUAS_KAVLING NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	JUMLAH_AIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	ABONEMEN NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	DENDA NUMERIC (15) NOT NULL DEFAULT 0 , 
	ADM NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_BAYAR NUMERIC (20) DEFAULT 0 , 
	
	DISKON_AIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	TGL_DISKON_AIR DATETIME2 (0) , 
	USER_DISKON_AIR VARCHAR (100) , 
	KET_DISKON_AIR TEXT , 
	
	DISKON_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	TGL_DISKON_IPL DATETIME2 (0) , 
	USER_DISKON_IPL VARCHAR (100) , 
	KET_DISKON_IPL TEXT , 
	
	PERSEN_PPN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	NILAI_PPN NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	STATUS_BAYAR INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	NO_KWITANSI VARCHAR (100) , 
	BAYAR_VIA VARCHAR (50) , 
	CARA_BAYAR INT NOT NULL DEFAULT 0 , 
		-- (0 = BELUM) 
		-- (1 = TUNAI) 
		-- (2 = K. DEBIT) 
		-- (3 = K. KREDIT) 
		-- (4 = T. BANK) 
	TGL_BAYAR_BANK DATETIME2 (0) , 
	TGL_BAYAR_SYS DATETIME2 (0) , 
	USER_BAYAR VARCHAR (100) , 
	KET_BAYAR TEXT , 
	
	STATUS_CETAK_KWT INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	USER_CETAK_KWT VARCHAR (100) , 
	TGL_CETAK_KWT DATETIME2 (0) ,
	
	STATUS_CETAK_IVC INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	TGL_CETAK_IVC DATETIME2 (0) , 
	USER_CETAK_IVC VARCHAR (100) ,
	
	STATUS_BATAL INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	USER_BATAL VARCHAR (100) , 
	TGL_BATAL DATETIME2 (0) , 
	KET_BATAL TEXT , 
	
	STATUS_POST_PB INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	STATUS_POST_BD INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	
	NO_FP VARCHAR (100) , 
	TGL_FP DATE , 
	TGL_POST_FP DATE , 
	STATUS_CETAK_FP INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	TGL_CETAK_FP DATETIME2 (0) , 
	USER_CETAK_FP VARCHAR (100) ,
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() , 
	
	--TEMP
	ID_TEMP INT 
) 
GO 

-- KWT_PEMBAYARAN_AI_TEMP
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_PEMBAYARAN_AI_TEMP') AND type in (N'U')) 
DROP TABLE KWT_PEMBAYARAN_AI_TEMP 
GO 
CREATE TABLE KWT_PEMBAYARAN_AI_TEMP ( 
	ID_PEMBAYARAN VARCHAR (200) PRIMARY KEY ,	-- TRX#PERIODE_TAG#NO_PELANGGAN 
	TRX INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
		
		-- 5 = MASA MEMBANGUN (LAIN-LAIN)
		-- 6 = RENOVASI (LAIN-LAIN)
		
		-- 7 = MASA MEMBANGUN (DEPOSIT)
		-- 8 = RENOVASI (DEPOSIT)
		
	NO_INVOICE VARCHAR (150) , 
	TGL_JATUH_TEMPO DATE , 
	KET_IVC TEXT , 
	
	-- PERIODE 
	PERIODE_TAG VARCHAR (6) , 
	PERIODE_AIR VARCHAR (6) , 
	PERIODE_IPL_AWAL VARCHAR (6) , 
	PERIODE_IPL_AKHIR VARCHAR (6) , 
	JUMLAH_PERIODE_IPL INT, 
	
	NO_PELANGGAN VARCHAR (100) , 
	
	KODE_SEKTOR VARCHAR (50) , 
	KODE_CLUSTER VARCHAR (50) , 
	KODE_BLOK VARCHAR (100) , 
	STATUS_BLOK INT NOT NULL , 
		-- 1 = KAVLING KOSONG 
		-- 2 = MASA MEMBANGUN 
		-- 3 = HUNIA 
		-- 4 = RENOVASI 
		
	KODE_ZONA VARCHAR (50) , 
	
	AKTIF_AIR INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	AKTIF_IPL INT NOT NULL DEFAULT 0 , -- (0 = TIDAK) (1 = YA) 
	
	KEY_AIR VARCHAR (50) , 
	KEY_IPL VARCHAR (50) , 
	
	GOLONGAN INT NOT NULL DEFAULT 0 , -- (0 = STANDAR) (1 = BISNIS) 
	TIPE_DENDA INT NOT NULL DEFAULT 0 , -- (0 = RUPIAH) (1 = PERSEN) 
	
	-- PERHITUNGAN AIR 
	STAND_LALU NUMERIC (15) NOT NULL DEFAULT 0 , 
	STAND_ANGKAT NUMERIC (15) NOT NULL DEFAULT 0 , 
	STAND_AKHIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	BLOK1 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK2 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK3 NUMERIC (10) NOT NULL DEFAULT 0 , 
	BLOK4 NUMERIC (10) NOT NULL DEFAULT 0 , 
	STAND_MIN_PAKAI NUMERIC (10) NOT NULL DEFAULT 0 , 
	TARIF1 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF2 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF3 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF4 NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF_MIN_PAKAI NUMERIC (10) NOT NULL DEFAULT 0 , 
	
	-- PERHITUNGAN IPL 
	LUAS_KAVLING NUMERIC (17, 2) NOT NULL DEFAULT 0 , 
	TARIF_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	JUMLAH_AIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	ABONEMEN NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	DENDA NUMERIC (15) NOT NULL DEFAULT 0 , 
	ADM NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_BAYAR NUMERIC (20) DEFAULT 0 , 
	
	DISKON_AIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	TGL_DISKON_AIR DATETIME2 (0) , 
	USER_DISKON_AIR VARCHAR (100) , 
	KET_DISKON_AIR TEXT , 
	
	DISKON_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	TGL_DISKON_IPL DATETIME2 (0) , 
	USER_DISKON_IPL VARCHAR (100) , 
	KET_DISKON_IPL TEXT , 
	
	PERSEN_PPN NUMERIC (5, 2) NOT NULL DEFAULT 0 , 
	NILAI_PPN NUMERIC (15) NOT NULL DEFAULT 0 , 
	
	STATUS_BAYAR INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	NO_KWITANSI VARCHAR (100) , 
	BAYAR_VIA VARCHAR (50) , 
	CARA_BAYAR INT NOT NULL DEFAULT 0 , 
		-- (0 = BELUM) 
		-- (1 = TUNAI) 
		-- (2 = K. DEBIT) 
		-- (3 = K. KREDIT) 
		-- (4 = T. BANK) 
	TGL_BAYAR_BANK DATETIME2 (0) , 
	TGL_BAYAR_SYS DATETIME2 (0) , 
	USER_BAYAR VARCHAR (100) , 
	KET_BAYAR TEXT , 
	
	STATUS_CETAK_KWT INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	USER_CETAK_KWT VARCHAR (100) , 
	TGL_CETAK_KWT DATETIME2 (0) ,
	
	STATUS_CETAK_IVC INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	TGL_CETAK_IVC DATETIME2 (0) , 
	USER_CETAK_IVC VARCHAR (100) ,
	
	STATUS_BATAL INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	USER_BATAL VARCHAR (100) , 
	TGL_BATAL DATETIME2 (0) , 
	KET_BATAL TEXT , 
	
	STATUS_POST_PB INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	STATUS_POST_BD INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	
	NO_FP VARCHAR (100) , 
	TGL_FP DATE , 
	TGL_POST_FP DATE , 
	STATUS_CETAK_FP INT NOT NULL DEFAULT 0 , -- (0 = BELUM) (1 = SUDAH) 
	TGL_CETAK_FP DATETIME2 (0) , 
	USER_CETAK_FP VARCHAR (100) ,
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() , 
	
	--TEMP
	ID_TEMP INT 
) 
GO 

-- KWT_POST_PEMBAYARAN 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_POST_PEMBAYARAN') AND type in (N'U')) 
DROP TABLE KWT_POST_PEMBAYARAN 
GO 
CREATE TABLE KWT_POST_PEMBAYARAN 
( 
	USER_POST VARCHAR (100) , 
	TGL_POST DATE , 
	
	JUMLAH_AIR NUMERIC (15) NOT NULL DEFAULT 0 , 
	ABONEMEN NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_IPL NUMERIC (15) NOT NULL DEFAULT 0 , 
	DENDA NUMERIC (15) NOT NULL DEFAULT 0 , 
	ADM NUMERIC (15) NOT NULL DEFAULT 0 , 
	JUMLAH_BAYAR NUMERIC (20) DEFAULT 0 , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_POST_BD 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_POST_BD') AND type in (N'U')) 
DROP TABLE KWT_POST_BD 
GO 
CREATE TABLE KWT_POST_BD 
( 
	USER_BD VARCHAR (100) , 
	BANK_BD VARCHAR (3) , 
	NO_BD VARCHAR (100) , 
	NO_BDT VARCHAR (100) , 
	JUMLAH_BD NUMERIC (25) NOT NULL DEFAULT 0 , 
	JUMLAH_BDT NUMERIC (25) NOT NULL DEFAULT 0 , 
	TGL_BD DATE , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 

-- KWT_POST_FP 
IF EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'KWT_POST_FP') AND type in (N'U')) 
DROP TABLE KWT_POST_FP 
GO 
CREATE TABLE KWT_POST_FP 
( 
	USER_POST VARCHAR (100) , TGL_POST DATE , 
	USER_BATAL VARCHAR (100) , TGL_BATAL DATE , 
	
	USER_MODIFIED VARCHAR (100) , 
	MODIFIED_DATE DATETIME2 (0) , 
	USER_CREATED VARCHAR (100) , 
	CREATED_DATE DATETIME2 (0) DEFAULT GETDATE() 
) 
GO 



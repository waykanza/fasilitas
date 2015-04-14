<?php 
$query = "
DECLARE @persen_ppn NUMERIC(5,2) = (SELECT TOP 1 ISNULL(PERSEN_PPN,0) FROM KWT_PARAMETER)

INSERT INTO KWT_PEMBAYARAN_AI_TEMP
(
	ID_PEMBAYARAN,
	TRX,
	
	PERIODE_TAG,
	PERIODE_AIR,
	PERIODE_IPL_AWAL,
	PERIODE_IPL_AKHIR,
	JUMLAH_PERIODE_IPL,
	TGL_JATUH_TEMPO,
	
	NO_PELANGGAN,
	
	KODE_SEKTOR,
	KODE_CLUSTER,
	KODE_BLOK,
	STATUS_BLOK,
	KODE_ZONA,
	
	GOLONGAN,
	TIPE_DENDA,
	
	AKTIF_AIR,
	AKTIF_IPL,
	
	KEY_AIR,
	KEY_IPL,
	
	STAND_AKHIR,
	STAND_LALU,
	
	LUAS_KAVLING,
	TARIF_IPL,
	JUMLAH_IPL,
	
	DISKON_IPL,
	TGL_DISKON_IPL,
	USER_DISKON_IPL,
	
	PERSEN_PPN, 
	
	USER_CREATED,
	KET_IVC
)
SELECT 
	(CAST(p.STATUS_BLOK AS VARCHAR(1)) + '#$periode_tag#' + p.NO_PELANGGAN) AS ID_PEMBAYARAN,
	p.STATUS_BLOK AS TRX,
	
	'$periode_tag' AS PERIODE_TAG,
	'$periode_air' AS PERIODE_AIR,
	'$periode_air' AS PERIODE_IPL_AWAL,
	'$periode_air' AS PERIODE_IPL_AKHIR,
	1 AS JUMLAH_PERIODE_IPL,
	'$tgl_jatuh_tempo_air_ipl',
	
	p.NO_PELANGGAN,
	
	p.KODE_SEKTOR,
	p.KODE_CLUSTER,
	p.KODE_BLOK,
	p.STATUS_BLOK,
	(CASE WHEN p.AKTIF_AIR = 1 THEN p.KODE_ZONA END) AS KODE_ZONA,
	
	p.GOLONGAN,
	p.TIPE_DENDA,
	
	p.AKTIF_AIR, 
	p.AKTIF_IPL, 
	
	p.KEY_AIR,
	p.KEY_IPL,
	
	0 AS STAND_AKHIR,
	(CASE WHEN p.AKTIF_AIR = 1 THEN ISNULL(b.STAND_AKHIR, 0) ELSE 0 END) AS STAND_LALU,
	
	p.LUAS_KAVLING AS LUAS_KAVLING,
	ISNULL(i.TARIF_IPL,0) AS TARIF_IPL,
	(
		CASE WHEN p.AKTIF_IPL = 1
		THEN
			CASE WHEN i.TIPE_TARIF_IPL = 1 
			THEN (p.LUAS_KAVLING * ISNULL(i.TARIF_IPL,0)) 
			ELSE ISNULL(i.TARIF_IPL,0) END
		ELSE 0 END
	) AS JUMLAH_IPL,
	
	$diskon_ipl AS DISKON_IPL,
	$tgl_diskon_ipl AS TGL_DISKON_IPL,
	$user_diskon_ipl AS USER_DISKON_IPL,
	
	@persen_ppn AS PERSEN_PPN, 
	
	'$sess_id_user' AS USER_CREATED,
	'$ket_ivc_kv_hn' AS KET_IVC 
FROM 
	KWT_PELANGGAN p 
	LEFT JOIN KWT_PEMBAYARAN_AI b ON p.NO_PELANGGAN = b.NO_PELANGGAN AND 
		$where_trx_air_ipl AND 
		b.PERIODE_AIR = '$periode_air_prev' AND 
		b.AKTIF_AIR = 1 
		
	LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
WHERE
	p.DISABLED = 0 AND 
	p.STATUS_BLOK = $trx_hn 
	
	$where_single_blok
	
ORDER BY p.KODE_BLOK ASC
";

ex_false($conn->Execute($query), "Error Insert HN Air IPL, Hubungi Developer/MSI !!");


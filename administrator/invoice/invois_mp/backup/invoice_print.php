<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
die_login();
die_mod('U1');
$conn = conn();
die_conn($conn);

$terbilang = new Terbilang;

$obj = $conn->Execute("SELECT DENDA_RUPIAH, DENDA_PERSEN FROM KWT_PARAMETER");

$denda_rupiah	= $obj->fields['DENDA_RUPIAH'];
$denda_persen	= $obj->fields['DENDA_PERSEN'];

$cb_data	= (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();
$trx		= (isset($_REQUEST['trx'])) ? $_REQUEST['trx'] : '';

$in_id_pembayaran = array();
foreach ($cb_data as $x) { $in_id_pembayaran[] = base64_decode($x); }
$in_id_pembayaran = implode("' ,'", $in_id_pembayaran);

$query = "
SELECT 
	b.ID_PEMBAYARAN, 
	b.TRX, 
	b.NO_INVOICE, 
	(CASE WHEN p.AKTIF_SM = 1 THEN p.SM_NAMA_PELANGGAN ELSE p.NAMA_PELANGGAN END) AS NAMA_PELANGGAN, 
	(CASE WHEN p.AKTIF_SM = 1 THEN p.SM_ALAMAT ELSE p.ALAMAT END) AS ALAMAT, 
	s.NAMA_SEKTOR, 
	c.NAMA_CLUSTER,
	b.KODE_BLOK, 
	b.NO_PELANGGAN, 
	CONVERT(VARCHAR(10), b.TGL_JATUH_TEMPO, 120) AS TGL_JATUH_TEMPO, 
	CONVERT(VARCHAR(10), b.CREATED_DATE, 120) AS TGL_IVC, 
	
	b.PERIODE_TAG,
	b.PERIODE_AIR,
	b.PERIODE_IPL_AWAL,
	b.PERIODE_IPL_AKHIR,
	
	b.STAND_LALU,
	(b.STAND_AKHIR + STAND_ANGKAT) AS STAND_AKHIR,
	
	b.JUMLAH_AIR,
	b.ABONEMEN,
	b.JUMLAH_IPL,
	b.DISKON_IPL,
	b.DISKON_AIR,
	b.DENDA,
	(
		b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA - b.DISKON_AIR - b.DISKON_IPL
	) AS JUMLAH_BAYAR, 
	(
		SELECT 
			CAST(ISNULL(SUM(JUMLAH_AIR), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(ABONEMEN), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(JUMLAH_IPL), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(DISKON_AIR), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(DISKON_IPL), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(DENDA), 0) AS VARCHAR) + '|' + 
			CAST(ISNULL(SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_AIR - DISKON_IPL), 0) AS VARCHAR)
		FROM KWT_PEMBAYARAN_AI 
		WHERE 
			$where_trx_air_ipl AND 
			STATUS_BAYAR = 0 AND 
			NO_PELANGGAN = b.NO_PELANGGAN AND 
			CAST(PERIODE_TAG AS INT) < CAST(b.PERIODE_TAG AS INT) 
	) AS BAYAR_PREV,
	
	b.AKTIF_IPL, 
	b.AKTIF_AIR, 
	
	b.GOLONGAN,
	b.TIPE_DENDA,
	
	b.PERSEN_PPN
FROM 
	KWT_PEMBAYARAN_AI b 
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN 
	LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR 
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER 
	LEFT JOIN KWT_USER uc ON b.USER_CETAK_KWT = uc.ID_USER 
WHERE 
	$where_trx_air_ipl AND 
	b.STATUS_BAYAR = 0 AND
	p.INFO_TAGIHAN = 1 AND 
	b.ID_PEMBAYARAN IN ('$in_id_pembayaran') AND 
	b.TRX = $trx
	
ORDER BY b.KODE_BLOK
";
	
$obj = $conn->Execute($query);

if ($trx == $trx_hn) { 
	include('HN_top.php');
}
elseif ($trx == $trx_kv || $trx == $trx_bg || $trx == $trx_rv) { 
	include('KVMBDPLL_top.php');
}

while( ! $obj->EOF)
{
	$id_pembayaran	= $obj->fields['ID_PEMBAYARAN'];
	$trx			= $obj->fields['TRX'];
	$no_invoice 	= $obj->fields['NO_INVOICE'];
	
	$nama_pelanggan	= $obj->fields['NAMA_PELANGGAN'];
	$alamat			= $obj->fields['ALAMAT'];
	
	$nama_sektor	= $obj->fields['NAMA_SEKTOR']; 
	$nama_cluster	= $obj->fields['NAMA_CLUSTER'];
	$kode_blok		= $obj->fields['KODE_BLOK']; 
	$no_pelanggan	= $obj->fields['NO_PELANGGAN']; 
	$tgl_jatuh_tempo = $obj->fields['TGL_JATUH_TEMPO'];
	$tgl_ivc		= $obj->fields['TGL_IVC'];
	
	$periode_tag	= $obj->fields['PERIODE_TAG'];
	$periode_air	= $obj->fields['PERIODE_AIR'];
	$periode_ipl_awal	= $obj->fields['PERIODE_IPL_AWAL'];
	$periode_ipl_akhir	= $obj->fields['PERIODE_IPL_AKHIR'];
	
	$stand_lalu		= $obj->fields['STAND_LALU'];
	$stand_akhir	= $obj->fields['STAND_AKHIR'];
	
	$jumlah_air		= $obj->fields['JUMLAH_AIR'];
	$abonemen		= $obj->fields['ABONEMEN'];
	$jumlah_ipl		= $obj->fields['JUMLAH_IPL'];
	$diskon_air		= $obj->fields['DISKON_AIR'];
	$diskon_ipl		= $obj->fields['DISKON_IPL'];
	$denda			= $obj->fields['DENDA'];
	$jumlah_bayar	= $obj->fields['JUMLAH_BAYAR']; 
	
	$bayar_prev			= explode('|', $obj->fields['BAYAR_PREV']);
	$prev_jumlah_air	= $bayar_prev[0];
	$prev_abonemen		= $bayar_prev[1];
	$prev_jumlah_ipl	= $bayar_prev[2];
	$prev_diskon_air	= $bayar_prev[3];
	$prev_diskon_ipl	= $bayar_prev[4];
	$prev_denda			= $bayar_prev[5];
	$prev_jumlah_bayar	= $bayar_prev[6];
	
	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	$aktif_air			= $obj->fields['AKTIF_AIR'];
	
	$golongan			= $obj->fields['GOLONGAN'];
	$tipe_denda			= $obj->fields['TIPE_DENDA'];
	
	if ($tipe_denda == '1') {
		$tipe_denda = $denda_persen;
	} else {
		$tipe_denda = $denda_rupiah;
	}
	
	$persen_ppn			= $obj->fields['PERSEN_PPN'];
	

	if ($trx == $trx_kv) { 
		include('KV.php');
	}
	elseif ($trx == $trx_bg) { 
		include('BG.php');
	}
	elseif ($trx == $trx_hn) { 
		if ($golongan == '1') { 
			include('HN_bisnis.php'); 
		} else { 
			include('HN_standar.php'); 
		}
	}
	elseif ($trx == $trx_rv) { 
		include('RV.php');
	}
	
	$obj->movenext();
}

if ($trx == $trx_hn) { 
	include('HN_bottom.php');
}
elseif ($trx == $trx_kv || $trx == $trx_bg || $trx == $trx_rv) { 
	include('KVMBDPLL_bottom.php');
}

$conn->Execute("
UPDATE KWT_PEMBAYARAN_AI 
SET 
	STATUS_CETAK_IVC = 1, 
	TGL_CETAK_IVC = GETDATE(), 
	USER_CETAK_IVC = '$sess_id_user', 
					
	USER_MODIFIED = '$sess_id_user', 
	MODIFIED_DATE = GETDATE() 
WHERE ID_PEMBAYARAN IN ('$in_id_pembayaran')");

close($conn); 

?>


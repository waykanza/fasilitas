<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
die_login();
die_mod('PA1');
$conn = conn();
die_conn($conn);

$terbilang = new Terbilang;

$idp = (isset($_REQUEST['idp'])) ? clean($_REQUEST['idp']) : '';
$tipe = (isset($_REQUEST['tipe'])) ? clean($_REQUEST['tipe']) : '';

$idp = explode('||', $idp);
$in_id_pembayaran = array();

foreach ($idp as $x) { $in_id_pembayaran[] = base64_decode($x); }

$in_id_pembayaran = implode("' ,'", $in_id_pembayaran);

$query = "
SELECT
	b.ID_PEMBAYARAN,
	b.TRX,
	b.NO_PELANGGAN,
	p.NAMA_PELANGGAN,
	b.PERIODE_TAG,
	b.PERIODE_AIR,
	b.PERIODE_IPL_AWAL,
	b.PERIODE_IPL_AKHIR,
	b.KODE_BLOK,
	b.KODE_SEKTOR,
	
	b.AKTIF_AIR,
	b.AKTIF_IPL,
	
	b.KEY_AIR,
	b.KEY_IPL,
	
	b.STAND_LALU,
	b.STAND_ANGKAT,
	b.STAND_AKHIR,
	(b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) AS PEMAKAIAN,
	
	b.LUAS_KAVLING,
	b.TARIF_IPL,
	
	t.BLOK1 AS L_BLOK1,
	t.BLOK2 AS L_BLOK2,
	t.BLOK3 AS L_BLOK3,
	t.BLOK4 AS L_BLOK4,
	
	b.BLOK1,
	b.BLOK2,
	b.BLOK3,
	b.BLOK4,
	b.STAND_MIN_PAKAI,
	
	b.TARIF1,
	b.TARIF2,
	b.TARIF3,
	b.TARIF4,
	b.TARIF_MIN_PAKAI,
	
	b.JUMLAH_AIR,
	b.ABONEMEN,
	b.JUMLAH_IPL,
	b.DENDA,
	b.ADM,
	(b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADM) AS TOTAL, 
	(b.DISKON_AIR + b.DISKON_IPL) AS DISKON, 
	b.JUMLAH_BAYAR,
	b.PERSEN_PPN,
	
	b.STATUS_BAYAR,
	uk.NAMA_USER AS USER_BAYAR, 
	b.CARA_BAYAR,
	b.BAYAR_VIA,
	b.NO_KWITANSI,
	CONVERT(VARCHAR(10),b.TGL_BAYAR_BANK,105) AS TGL_BAYAR_BANK,
	CONVERT(VARCHAR(10),b.TGL_BAYAR_SYS,105) AS TGL_BAYAR_SYS,
	b.KET_BAYAR,
	
	b.STATUS_CETAK_KWT
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN 
	LEFT JOIN KWT_TARIF_AIR t ON b.KEY_AIR = t.KEY_AIR 
	LEFT JOIN KWT_USER uk ON b.USER_BAYAR = uk.ID_USER 
WHERE 
	$where_trx_air_ipl AND 
	ID_PEMBAYARAN IN ('$in_id_pembayaran')
";
	
$obj = $conn->Execute($query);

$trx = $obj->fields['TRX'];

if ($trx == $trx_hn) {
	include('kwt_HN_top.php');
} else {
	include('kwt_KVBGRV_top.php');
}

while( ! $obj->EOF)
{
	$id_pembayaran		= $obj->fields['ID_PEMBAYARAN'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode_tag		= $obj->fields['PERIODE_TAG'];
	$periode_air		= $obj->fields['PERIODE_AIR'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	
	$aktif_air			= $obj->fields['AKTIF_AIR'];
	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	
	$key_air			= $obj->fields['KEY_AIR'];
	$key_ipl			= $obj->fields['KEY_IPL'];

	$stand_lalu			= $obj->fields['STAND_LALU'];
	$stand_angkat		= $obj->fields['STAND_ANGKAT'];
	$stand_akhir		= $obj->fields['STAND_AKHIR'];
	$pemakaian			= $obj->fields['PEMAKAIAN'];
	
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$tarif_ipl			= $obj->fields['TARIF_IPL'];

	$blok1				= $obj->fields['BLOK1'];
	$blok2				= $obj->fields['BLOK2'];
	$blok3				= $obj->fields['BLOK3'];
	$blok4				= $obj->fields['BLOK4'];
	
	if ($aktif_air == '1') {
		$l_blok1 = '0 - ' . $obj->fields['L_BLOK1'];
		$l_blok2 = $obj->fields['L_BLOK1'] + 1 . ' - ' . $obj->fields['L_BLOK2'];
		$l_blok3 = $obj->fields['L_BLOK2'] + 1 . ' - ' . $obj->fields['L_BLOK3'];
		$l_blok4 = $obj->fields['L_BLOK4'];
	} else {
		$l_blok1 = '0 - 0';
		$l_blok2 = '0 - 0';
		$l_blok3 = '0 - 0';
		$l_blok4 = '0';
	}
	
	$stand_min_pakai	= $obj->fields['STAND_MIN_PAKAI'];
	
	$tarif1				= $obj->fields['TARIF1'];
	$tarif2				= $obj->fields['TARIF2'];
	$tarif3				= $obj->fields['TARIF3'];
	$tarif4				= $obj->fields['TARIF4'];
	$tarif_min_pakai	= $obj->fields['TARIF_MIN_PAKAI'];

	$jumlah_air			= $obj->fields['JUMLAH_AIR'];
	$abonemen			= $obj->fields['ABONEMEN'];
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$adm				= $obj->fields['ADM'];
	$total				= $jumlah_air + $abonemen + $jumlah_ipl + $denda + $adm;
	$diskon				= $obj->fields['DISKON'];
	$jumlah_bayar		= $obj->fields['JUMLAH_BAYAR'];
	
	$persen_ppn			= $obj->fields['PERSEN_PPN'];

	$status_bayar 		= $obj->fields['STATUS_BAYAR'];
	$user_bayar			= $obj->fields['USER_BAYAR'];
	$cara_bayar			= $obj->fields['CARA_BAYAR'];
	$bayar_via			= $obj->fields['BAYAR_VIA'];
	$no_kwitansi		= $obj->fields['NO_KWITANSI'];
	$tgl_bayar_bank		= $obj->fields['TGL_BAYAR_BANK'];
	$tgl_bayar_sys		= $obj->fields['TGL_BAYAR_SYS'];
	$ket_bayar			= $obj->fields['KET_BAYAR'];
	
	$status_cetak_kwt	= $obj->fields['STATUS_CETAK_KWT'];

	if ($status_bayar != '1')
	{
		if ($tipe == '1') {
			close($conn);
			echo '<script type="text/javascript">alert("Tagihan belum dibayar!");window.close();</script>';
			exit;
		}
		
		$obj->movenext();
		echo "Tagihan belum dibayar [$kode_blok] [$periode_tag] [$no_pelanggan]";
	}
	/*elseif ($status_cetak_kwt == '1')
	{
		$obj->movenext();
		echo "Tagihan sudah dibayar dan dicetak! [$periode_tag] [$no_pelanggan]";
	}*/
	else
	{
		$conn->Execute("
		UPDATE KWT_PEMBAYARAN_AI 
		SET STATUS_CETAK_KWT = 1,
			USER_CETAK_KWT = '$sess_id_user', 
			TGL_CETAK_KWT = GETDATE(), 
					
			USER_MODIFIED = '$sess_id_user', 
			MODIFIED_DATE = GETDATE() 
		WHERE ID_PEMBAYARAN = '$id_pembayaran'");
	}
	
	if ($trx == $trx_kv) {
		include('kwt_KV.php');
	} elseif ($trx == $trx_bg) {
		include('kwt_BG.php');
	} elseif ($trx == $trx_hn) {
		include('kwt_HN.php');
	} elseif ($trx == $trx_rv) {
		include('kwt_RV.php');
	}

	$obj->movenext();
}

if ($trx == $trx_hn) {
	include('kwt_HN_bottom.php');
} else {
	include('kwt_KVBGRV_bottom.php');
}

close($conn);



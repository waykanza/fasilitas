<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
die_login();
die_mod('PL1');
$conn = conn();
die_conn($conn);

$terbilang = new Terbilang;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Cetak - Iuran Pemeliharaan Lingkungan</title>
<style type="text/css">
@media print {
	@page {
		size: 8.5in 4in portrait;
	}
	.newpage {page-break-before:always;}
}
	
table {
	border-collapse: collapse;
}
table tr td {
	font-family: "New Century Schoolbook", Times, serif;
	font-size: 12px;
	vertical-align: top;
}
.top-enabled {
	border-top:1px solid #000;
}
</style>
</head>

<body onload="window.print()">
<div id="wrap">

<?php
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
	b.PERIODE_IPL_AWAL,
	b.PERIODE_IPL_AKHIR,
	b.KODE_BLOK,
	b.KODE_SEKTOR,
	
	b.KEY_IPL,
	
	b.LUAS_KAVLING,
	b.TARIF_IPL,
	
	b.JUMLAH_IPL,
	b.DENDA,
	b.ADM,
	(JUMLAH_IPL + DENDA + ADM) AS TOTAL, 
	(b.DISKON_IPL) AS DISKON, 
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
	LEFT JOIN KWT_USER uk ON b.USER_BAYAR = uk.ID_USER 
WHERE 
	$where_trx_lain_lain AND 
	ID_PEMBAYARAN IN ('$in_id_pembayaran')
";
	
$obj = $conn->Execute($query);

while( ! $obj->EOF)
{
	$id_pembayaran		= $obj->fields['ID_PEMBAYARAN'];
	$trx				= $obj->fields['TRX'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode_tag		= $obj->fields['PERIODE_TAG'];
	$periode_ipl_awal	= $obj->fields['PERIODE_IPL_AWAL'];
	$periode_ipl_akhir	= $obj->fields['PERIODE_IPL_AKHIR'];
	
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	
	$key_ipl			= $obj->fields['KEY_IPL'];

	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$tarif_ipl			= $obj->fields['TARIF_IPL'];
	
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$adm				= $obj->fields['ADM'];
	$total				= $jumlah_ipl + $denda + $adm;
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
	
	$text_st = '';
	
	if ($trx == $trx_lbg) { $text_st = 'Masa Membangun'; }
	elseif ($trx == $trx_lrv) { $text_st = 'Renovasi'; }
	
	$status_cetak_kwt	= $obj->fields['STATUS_CETAK_KWT'];

	if ($status_bayar != '1')
	{
		if ($tipe == '1') {
			close($conn);
			echo '<script type="text/javascript">alert("Tagihan belum dibayar!");window.close();</script>';
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
			TGL_CETAK_KWT = GETDATE()
		WHERE ID_PEMBAYARAN = '$id_pembayaran'");
	}
	?>
	
	
	<div>
	<table width="800">
	<tbody>
	<tr>
	<td width="55%">
	<table width="100%">
	<tbody>
	<tr>
	<td>
	<table width="100%">
	<tbody>
	<tr>
	<td width="20%"></td>
	<td width="5%"></td>
	<td width="35%"></td>
	<td width="15%"></td>
	<td width="5%"></td>
	<td width="20%"></td>
	</tr>
	<tr>
	<td colspan="6">&nbsp;</td>
	</tr>
	<tr>
	<td colspan="6">&nbsp;</td>
	</tr>
	<tr>
	<td colspan="6">&nbsp;</td>
	</tr>
	<tr>
	<td>No. Bukti</td>
	<td align="center">:</td>
	<td><span><?php echo $no_kwitansi; ?></span></td>
	<td>Bulan</td>
	<td align="center">:</td>
	<td><span><?php echo ucfirst(fm_periode($periode_tag)); ?></span></td>
	</tr>
	<tr>
	<td>No. Pelanggan</td>
	<td align="center">:</td>
	<td><span><?php echo fm_nopel($no_pelanggan); ?></span></td>
	<td>Awal</td>
	<td align="center">:</td>
	<td><span><?php echo fm_periode_first($periode_tag); ?></span></td>
	</tr>
	<tr>
	<td>N a m a</td>
	<td align="center">:</td>
	<td><span><?php echo $nama_pelanggan; ?></span></td>
	<td>Akhir</td>
	<td align="center">:</td>
	<td><span><?php echo fm_periode_last($periode_tag); ?></span></td>
	</tr>
	<tr>
	<td>Blok / Sektor</td>
	<td align="center">:</td>
	<td><span><?php echo $kode_blok; ?></span>&nbsp; / &nbsp;<span><?php echo $kode_sektor; ?></span></td>
	<td>Bayar via</td>
	<td align="center">:</td>
	<td><span><?php echo $bayar_via; ?></span></td>
	</tr>
	<tr>
	<td>Luas / Tarif</td>
	<td align="center">:</td>
	<td><span><?php echo to_money($luas_kavling); ?></span>&nbsp; / &nbsp;<span><?php echo to_money($tarif_ipl); ?></span> (M<sup>2</sup>)</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>Tgl. Bayar</td>
	<td align="center">:</td>
	<td><span><?php echo $tgl_bayar_bank; ?></span></td>
	<td>Tgl. Kwit</td>
	<td><div align="center">:</div></td>
	<td><span><?php echo $tgl_bayar_sys; ?></span></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>
	<table width="100%">
	<tbody>
	<tr>
	<td width="45%"></td>
	<td width="5%"></td>
	<td width="10%"></td>
	<td width="30%"></td>
	<td width="10%"></td>
	</tr>
	<tr>
	<td>IPL&nbsp;<span>Biaya Lain Lain <?php echo $text_st; ?></span></td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($jumlah_ipl); ?></span></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>Denda</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($denda); ?></span></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>Total</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($total); ?></span></td>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>Discount</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($diskon); ?></span></td>
	<td>&nbsp;</td>
	</tr>
	<tr height="25" valign="bottom">
	<td><b>TOTAL BAYAR</b></td>
	<td align="center">:</td>
	<td class="top-enabled"><b>Rp.</b></td>
	<td class="top-enabled" align="right"><b><span><?php echo to_money($jumlah_bayar); ?></span></b></td>
	<td>&nbsp;</td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td>
	<table width="100%">
	<tbody>
	<tr>
	<td width="25%"></td>
	<td width="5%"></td>
	<td width="70%"></td>
	</tr>
	<tr valign="top" height="30">
	<td>Terbilang</td>
	<td align="center">:</td>
	<td><span><?php echo strtoupper($terbilang->eja($jumlah_bayar)); ?></span></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td>
	<table width="100%">
	<tbody><tr valign="top">
	<td width="25%">Keterangan</td>
	<td width="5%" align="center">:</td>
	<td width="70%"><span><?php echo $ket_bayar; ?></span></td>
	</tr>
	<tr>
	<td>Kasir</td>
	<td align="center">:</td>
	<td><span><?php echo $user_bayar; ?></span></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	</tbody>
	</table></td>
	<td width="3%">&nbsp;</td>
	<td width="42%">
	<table width="100%" align="right">
	<tbody>
	<tr>
	<td><table width="100%" align="right">
	<tbody>
	<tr>
	<td width="40%"></td>
	<td width="5%"></td>
	<td width="55%"></td>
	</tr>
	<tr>
	<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
	<td colspan="3" align="right"><table width="100%" align="right">
	<tbody>
	<tr>
	<td width="45%">&nbsp;</td>
	<td align="left" width="20%">No. Dok</td>
	<td width="10%" align="center">:</td>
	<td align="left">082/F/KEU/JRP/06</td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td align="left">Rev.</td>
	<td align="center">:</td>
	<td align="left">0</td>
	</tr>
	<tr>
	<td colspan="3">&nbsp;</td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td>No. Bukti</td>
	<td>:</td>
	<td><span><?php echo $no_kwitansi; ?></span></td>
	</tr>
	<tr>
	<td>No. Pelanggan</td>
	<td>:</td>
	<td><span><?php echo fm_nopel($no_pelanggan); ?></span></td>
	</tr>
	<tr>
	<td>N a m a</td>
	<td>:</td>
	<td><span><?php echo $nama_pelanggan; ?></span></td>
	</tr>
	<tr>
	<td>Blok / Sektor</td>
	<td>:</td>
	<td><span><?php echo $kode_blok; ?></span>&nbsp; / &nbsp;<span><?php echo $kode_sektor; ?></span></td>
	</tr>
	<tr>
	<td>Luas / Tarif</td>
	<td>:</td>
	<td><span><?php echo to_money($luas_kavling); ?></span>&nbsp; / &nbsp;<span><?php echo to_money($tarif_ipl); ?></span> (M<sup>2</sup>)</td>
	</tr>
	<tr>
	<td>Tgl. Bayar</td>
	<td>:</td>
	<td><span><?php echo $tgl_bayar_bank; ?></span></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td><table width="100%">
	<tbody>
	<tr>
	<td width="10%"></td>
	<td width="5%"></td>
	<td width="35%"></td>
	<td width="10%"></td>
	<td width="5%"></td>
	<td width="35%"></td>
	</tr>
	<tr>
	<td>Bulan</td>
	<td>:</td>
	<td><span><?php echo ucfirst(fm_periode($periode_tag)); ?></span></td>
	<td>Via</td>
	<td>:</td>
	<td><span><?php echo $bayar_via; ?></span></td>
	</tr>
	<tr>
	<td>Awal</td>
	<td>:</td>
	<td><span><?php echo fm_periode_first($periode_tag); ?></span></td>
	<td>Akhir</td>
	<td>:</td>
	<td><span><?php echo fm_periode_last($periode_tag); ?></span></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	<tr>
	<td><table width="100%">
	<tbody>
	<tr>
	<td width="45%"></td>
	<td width="5%"></td>
	<td width="10%"></td>
	<td width="30%"></td>
	</tr>
	<tr>
	<td>IPL&nbsp;<span>Biaya Lain Lain <?php echo $text_st; ?></span></td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($jumlah_ipl); ?></span></td>
	</tr>
	<tr>
	<td>Denda</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($denda); ?></span></td>
	</tr>
	<tr>
	<td>Total</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($total); ?></span></td>
	</tr>
	<tr>
	<td>Discount</td>
	<td align="center">:</td>
	<td>Rp.</td>
	<td align="right"><span><?php echo to_money($diskon); ?></span></td>
	</tr>
	<tr height="25" valign="bottom">
	<td><b>TOTAL BAYAR</b></td>
	<td align="center">:</td>
	<td class="top-enabled"><b>Rp.</b></td>
	<td class="top-enabled" align="right"><b><span><?php echo to_money($jumlah_bayar); ?></span></b></td>
	</tr>
	<tr height="25" valign="bottom">
	<td>(Include ppn <?php echo to_money($persen_ppn); ?> %)</td>
	<td align="center">&nbsp;</td>
	<td class="top-enabled">&nbsp;</td>
	<td class="top-enabled" align="right"><b></b></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	</tbody>
	</table></td>
	</tr>
	</tbody>
	</table>
	</div>

	<div class="newpage"></div>
	
	<?php
	$obj->movenext();
}
?>

</div>
</body>
</html>

<?php close($conn); ?>

<?php
require_once('../../../../config/config.php');
require_once('../../../../config/terbilang.php');
$conn = conn();
$terbilang = new Terbilang;
$id_pembayaran = (isset($_REQUEST['id_pembayaran'])) ? base64_decode(clean($_REQUEST['id_pembayaran'])) : '';

$query = "

	SELECT *
	FROM PELANGGAN_PKL a left join
	KWT_LOKASI_PKL b on a.KODE_LOKASI = b.KODE_LOKASI
	WHERE ID_PEMBAYARAN='$id_pembayaran'

	";
	$obj = $conn->Execute($query);
	$query = "
	SELECT *
	FROM KWT_PARAMETER
	";
	$param = $conn->Execute($query);
	$nomor_kwitansi = $param->fields['KWITANSI_PKL'];
	$nama_ttd = $param->fields['NAMA_PAJAK'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];	
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];	
	
	$lokasi				= $obj->fields['NAMA_LOKASI'];
	
	$awal				= date("d M Y", strtotime($obj->fields['PERIODE_AWAL']));
	$akhir				= date("d M Y", strtotime($obj->fields['PERIODE_AKHIR']));
	
	$status_bayar			= $obj->fields['STATUS_BAYAR'];
	$tanggal_bayar			= date("d-m-Y", strtotime($obj->fields['TANGGAL_BAYAR']));
	$no_kwi = explode('-', $tanggal_bayar);
	$total_bayar = to_number($obj->fields['TOTAL_BAYAR']);
	$jenis_bayar			= jenis_bayar($obj->fields['JENIS_BAYAR']);
	$no_rekening			= $obj->fields['NO_REKENING'];
	$no_kwitansi			= $obj->fields['ID_PEMBAYARAN'];
	$keterangan				= $obj->fields['KETERANGAN'];
	$kasir = $_SESSION['ID_USER'];
	
	if ($status_bayar != '2')
	{
		close($conn);
		echo '<script type="text/javascript">alert("Data belum dibayar!");window.close();</script>';
	}

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style type="text/css">
	@media print {
		@page {
			size: 8.5in 4in Landscape;
			margin: 0;
		}
		.newpage {
			page-break-before: always;
		}
	}
	
	table {
		border-collapse: collapse;
	}
	table tr td {
		/* font-family: "New Century Schoolbook", Times, serif; */
		font-size: 12px;
	}
	.line-sum {
		border:none;
		border-top:1px solid #000;
		margin:0;
		padding:0 0 2px 0;
	}
	
	/*.wrap {
		position: relative;
		width: 800px;
	}*/
	
	.left {
		float: left;
		width: 438px;
		padding:0 1px 0 1px;
		margin: 56px 0 0 0;
	}
	
	.mid {
		float: left;
		width: 24px;
	}
	
	#right {
		float: left;
		width: 334px;
		padding: 0 1px 0 1px;
		margin: 56px 0 0 0;
	}
	#terbilang{
		width: 800px;
		height: 200px;
	}
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.va-top { vertical-align:top; }
</style>
</head>

<body onload="window.print()">
	<div class="clear"></div>
<div id='no'>

	<a><?php echo $no_kwitansi;?><?php echo $nomor_kwitansi;?>/<?php echo $terbilang->romawi($no_kwi[0]);?>/<?php echo $no_kwi[1];?></a>
</div>
<div id = 'terima'>
	<a><?php echo $nama_pelanggan;?></a>
</div>
<div id = 'terbilang'>
	<a><?php echo $terbilang->eja($total_bayar); ?></a>
</div>
<div id = 'untuk'>
	<a>Untuk Sewa Lokasi Pedagang Kaki Lima Periode <?php echo $awal ;?> s/d <?php echo $akhir;?> di Lokasi <?php echo $lokasi;?> </a>
</div>
<div id = 'tanggal'>
	<a><?php
	$now = new DateTime();
	echo $now->format('d M Y');

	?></a>
</div>
<div id = 'jumlah'>
	<a><?php $total_bayar;?></a>
</div>

	
	<div class="clear"></div>
<div id="nama_ttd">
	<a><?php echo $nama_ttd;?></a>
</div>
	
</body>
</html>

<?php close($conn); ?>

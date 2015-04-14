<?php
require_once('../../../../config/config.php');
require_once('../../../../config/terbilang.php');
$conn = conn();

$terbilang = new Terbilang;
$id_pembayaran = (isset($_REQUEST['id_pembayaran'])) ? base64_decode(clean($_REQUEST['id_pembayaran'])) : '';

$query = "
	SELECT
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.PERIODE,
		b.KODE_BLOK,
		b.KODE_SEKTOR,
		b.KEY_AIR,
		b.KEY_IPL,
		
		b.STAND_LALU,
		b.STAND_ANGKAT,
		b.STAND_AKHIR,
		((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
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
		b.ADMINISTRASI,
		b.JUMLAH_BAYAR,
		b.PERSEN_PPN,
		
		(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS DISKON,
		
		b.STATUS_BAYAR,
		b.JENIS_BAYAR,
		b.BAYAR_MELALUI,
		CONVERT(VARCHAR(10),b.TGL_BAYAR,105) AS TGL_BAYAR,
		b.NO_KWITANSI,
		b.KETERANGAN_BAYAR,
		b.KASIR,
		b.STATUS_CETAK_KWT
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE 
		$where_trx_air_ipl AND  
		ID_PEMBAYARAN = '$id_pembayaran'";
	
	$obj = $conn->Execute($query);
	
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode			= $obj->fields['PERIODE'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	$key_air			= $obj->fields['KEY_AIR'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	
	$stand_lalu			= $obj->fields['STAND_LALU'];
	$stand_angkat		= $obj->fields['STAND_ANGKAT'];
	$stand_akhir		= $obj->fields['STAND_AKHIR'];
	$pemakaian			= $obj->fields['PEMAKAIAN'];
	
	$blok1				= $obj->fields['BLOK1'];
	$blok2				= $obj->fields['BLOK2'];
	$blok3				= $obj->fields['BLOK3'];
	$blok4				= $obj->fields['BLOK4'];
	
	$tarif1				= $obj->fields['TARIF1'];
	$tarif2				= $obj->fields['TARIF2'];
	$tarif3				= $obj->fields['TARIF3'];
	$tarif4				= $obj->fields['TARIF4'];
	
	$stand_min_pakai	= $obj->fields['STAND_MIN_PAKAI'];
	$tarif_min_pakai	= $obj->fields['TARIF_MIN_PAKAI'];
	
	$jumlah_air			= $obj->fields['JUMLAH_AIR'];
	$abonemen			= $obj->fields['ABONEMEN'];
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$administrasi		= $obj->fields['ADMINISTRASI'];
	
	$total				= $jumlah_air + $abonemen + $jumlah_ipl + $denda + $administrasi;
	$diskon				= $obj->fields['DISKON'];
	
	$jumlah_bayar		= $obj->fields['JUMLAH_BAYAR'];
	$persen_ppn			= $obj->fields['PERSEN_PPN'];
	
	$status_bayar 		= $obj->fields['STATUS_BAYAR'];
	$jenis_bayar		= $obj->fields['JENIS_BAYAR'];
	$bayar_melalui		= $obj->fields['BAYAR_MELALUI'];
	$tgl_bayar			= $obj->fields['TGL_BAYAR'];
	$no_kwitansi		= $obj->fields['NO_KWITANSI'];
	$keterangan_bayar	= $obj->fields['KETERANGAN_BAYAR'];
	$kasir				= $obj->fields['KASIR'];
	$status_cetak_kwt	= $obj->fields['STATUS_CETAK_KWT'];
	
	if ($status_bayar != '2')
	{
		close($conn);
		echo '<script type="text/javascript">alert("Data belum dibayar!");window.close();</script>';
	}
	elseif ($status_cetak_kwt == '1')
	{
		#close($conn);
		#echo '<script type="text/javascript">alert("Data sudah dibayar dan dicetak!");window.close();</script>';
	}
	else
	{
		$query = "UPDATE KWT_PEMBAYARAN_AI SET STATUS_CETAK_KWT = '1' WHERE ID_PEMBAYARAN = '$id_pembayaran'";
		$conn->Execute($query);
	}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style type="text/css">
	@media print {
		@page {
			size: 8.5in 4in portrait;
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
	
	.wrap {
		position: relative;
		width: 800px;
	}
	
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
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.va-top { vertical-align:top; }
</style>
</head>

<body onload="window.print()">
<div class="wrap">
	<div class="clear"></div>
	
	<div class="left">
		<table>
			<tr>
				<td style="width:90px;">No. Bukti</td>
				<td style="width:10px;">:</td>
				<td style="width:153px;"><?php echo $no_kwitansi; ?></td>
				
				<td style="width:80px;">Bulan</td>
				<td style="width:10px;">:</td>
				<td style="width:105px;"><?php echo ucfirst(fm_periode($periode)); ?></td>
			</tr>
			<tr>
				<td>No. Pelanggan</td>
				<td>:</td>
				<td><?php echo no_pelanggan($no_pelanggan); ?></td>
				
				<td>Stand Meter</td>
				<td>:</td>
				<td><?php echo to_money($stand_akhir) . ' - ' . to_money($stand_lalu); ?></td>
			</tr>
			<tr>
				<td class="va-top">N a m a</td>
				<td class="va-top">:</td>
				<td class="va-top"><?php echo $nama_pelanggan; ?></td>
				
				<td class="va-top">Ganti Meter</td>
				<td class="va-top">:</td>
				<td class="va-top"><?php echo to_money($stand_angkat); ?></td>
			</tr>
			<tr>
				<td>Blok / Sektor</td>
				<td>:</td>
				<td><?php echo $kode_blok . ' / ' . $kode_sektor; ?></td>
				
				<td>Pemakaian</td>
				<td>:</td>
				<td><?php echo to_money($pemakaian); ?></td>
			</tr>
			<tr>
				<td>Tgl. Byr. / Via</td>
				<td>:</td>
				<td><?php echo $tgl_bayar . ' / '. $bayar_melalui; ?></td>
				
				<td>Golongan</td>
				<td>:</td>
				<td><?php echo $key_air . ' / ' . $key_ipl; ?></td>
			</tr>
		</table>

		<div style="height:5px;"></div>

		<table>
			<tr>
				<td style="width:100px;">&nbsp;&nbsp;0 - 10 m&sup3;</td>
				<td style="width:10px;">Rp.</td>
				<td style="width:90px;" class="text-right"><?php echo to_money($blok1 * $tarif1); ?></td>
				
				<td rowspan="8" style="width:15px;"></td>
				
				<td style="width:110px;">Air Bersih</td>
				<td style="width:10px;">Rp.</td>
				<td style="width:98px;" class="text-right"><?php echo to_money($jumlah_air); ?></td>
			</tr>
			<tr>
				<td>11 - 20 m&sup3;</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($blok2 * $tarif2); ?></td>
				<td>IPL</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
			</tr>
			<tr>
				<td>21 - 40 m&sup3;</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($blok3 * $tarif3); ?></td>
				<td>Abonemen</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($abonemen); ?></td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;> 40 m&sup3;</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($blok4 * $tarif4); ?></td>
				<td>Denda</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($denda); ?></td>
			</tr>
			<tr>
				<td>Pakai Minimal</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($stand_min_pakai * $tarif_min_pakai); ?></td>
				<td>Administrasi</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($administrasi); ?></td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td><b>TOTAL</b></td><td><b>Rp.</b></td>
				<td class="text-right"><b><?php echo to_money($total); ?></b></td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td>Diskon</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($diskon); ?></td>
			</tr>
			<tr>
				<td colspan="3"></td>
				<td><b>TOTAL BAYAR</b></td><td><b>Rp.</b></td>
				<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
			</tr>
		</table>

		<div style="height:5px;"></div>

		<table>
			<tr>
				<td class="va-top" style="width:60px;"><i>Terbilang</i></td>
				<td class="va-top" style="width:10px;"><i>:</i></td>
				<td><i><?php echo ucfirst($terbilang->eja($jumlah_bayar)); ?> rupiah.</i></td>
			</tr>
			<tr>
				<td class="va-top"><i>Keterangan</i></td>
				<td class="va-top"><i>:</i></td>
				<td><i><?php echo $keterangan_bayar; ?></i></td>
			</tr>
			<tr>
				<td><i>Kasir</i></td>
				<td><i>:</i></td>
				<td><?php echo $kasir; ?> / - / COPY KWITANSI</td>
			</tr>
			<tr>
				<td colspan="3">*) <i>Termasuk PPN <?php echo to_money($persen_ppn); ?>%</i></td>
			</tr>
		</table>
	</div>

	<div class="mid">&nbsp;</div>

	<div class="right">
		<table>
			<tr>
				<td style="width:90px;">No. Bukti</td>
				<td style="width:10px;">:</td>
				<td style="width:234px;"><?php echo $no_kwitansi; ?></td>
			</tr>
			<tr>
				<td>No. Pelanggan</td>
				<td>:</td>
				<td><?php echo no_pelanggan($no_pelanggan); ?></td>
			</tr>
			<tr>
				<td class="va-top">N a m a</td>
				<td class="va-top">:</td>
				<td class="va-top"><?php echo $nama_pelanggan; ?></td>
			</tr>
			<tr>
				<td>Blok / Sektor</td>
				<td>:</td>
				<td><?php echo $kode_blok . ' / ' . $kode_sektor; ?></td>
			</tr>
			<tr>
				<td>Tgl. Bayar</td>
				<td>:</td>
				<td><?php echo $tgl_bayar; ?></td>
			</tr>
		</table>
		
		<div style="height:5px;"></div>
		
		<table>
			<tr>
				<td style="width:70px;">Bulan</td>
				<td style="width:10px;">:</td>
				<td style="width:87px;"><?php echo ucfirst(fm_periode($periode)); ?></td>
				
				<td style="width:50px;">Golongan</td>
				<td style="width:10px;">:</td>
				<td style="width:107px;"><?php echo $key_air . ' / ' . $key_ipl; ?></td>
			</tr>
			<tr>
				<td>Pemakaian</td>
				<td>:</td>
				<td><?php echo $pemakaian; ?></td>
				
				<td>Via</td>
				<td>:</td>
				<td><?php echo $bayar_melalui; ?></td>
			</tr>
			<tr>
				<td>Ganti Meter</td>
				<td>:</td>
				<td><?php echo to_money($stand_angkat); ?></td>
			</tr>
		</table>

		<div style="height:5px;"></div>

		<table>
			<tr>
				<td style="width:150px;">Air Bersih</td>
				<td style="width:10px;">Rp.</td>
				<td style="width:174px;" class="text-right"><?php echo to_money($jumlah_air); ?></td>
			</tr>
			<tr>
				<td>IPL</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
			</tr>
			<tr>
				<td>Abonemen</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($abonemen); ?></td>
			</tr>
			<tr>
				<td>Denda</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($denda); ?></td>
			</tr>
			<tr>
				<td>Administrasi</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($administrasi); ?></td>
			</tr>
			<tr>
				<td><b>TOTAL</b></td><td><b>Rp.</b></td>
				<td class="text-right"><b><?php echo to_money($total); ?></b></td>
			</tr>
			<tr>
				<td>Diskon</td><td>Rp.</td>
				<td class="text-right"><?php echo to_money($diskon); ?></td>
			</tr>
			<tr>
				<td><b>TOTAL BAYAR</b></td><td><b>Rp.</b></td>
				<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
			</tr>
		</table>
		
		<table>
			<tr>
				<td style="width:62px;"><i>Kasir</i></td>
				<td style="width:12px;"><i>:</i></td>
				<td style="width:260px;"><?php echo $kasir; ?> / - / COPY KWITANSI</td>
			</tr>
			<tr>
				<td colspan="3">*) <i>Termasuk PPN <?php echo to_money($persen_ppn); ?>%</i></td>
			</tr>
		</table>
	</div>
	
	<div class="clear"></div>
</div>

</body>
</html>

<?php close($conn); ?>

<?php
require_once('../../../../config/config.php');
require_once('../../../../config/terbilang.php');
$conn = conn();

$terbilang = new Terbilang;
$id_pembayaran = (isset($_REQUEST['id_pembayaran'])) ? base64_decode(clean($_REQUEST['id_pembayaran'])) : '';

$query = "

	SELECT *
			FROM PELANGGAN_MP a	
			LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
			LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE
			LEFT JOIN KWT_BANK e ON a.KODE_BANK = e.KODE_BANK
	WHERE ID_PEMBAYARAN='$id_pembayaran'


	";
	
	$obj = $conn->Execute($query);
	
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];	
	
	$no_ktp				= $obj->fields['NO_KTP'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$npwp				= $obj->fields['NPWP'];
	$alamat				= $obj->fields['ALAMAT'];
	$no_tlp				= $obj->fields['NO_TELEPON'];
	$no_hp				= $obj->fields['NO_HP'];
	
	$pembayaran = $obj->fields['PEMBAYARAN'];
	if ($obj->fields['KODE_MP'] == 'A') {
			$mp = 'BILLBOARD / SIGN BOARD / PLAY SIGN';
			$tahun = ' / Tahun';
			$satuan ='Per ' .$pembayaran. ' Bulan';
			if ($pembayaran % 12 == 0){
				$xx = $pembayaran / 12;
				$satuan ='Per '.$xx.' Tahun';
				if ($pembayaran / 12 == 1){
					$satuan =' Tahunan';
				}
			}
		} else if ($obj->fields['KODE_MP'] == 'B') {
			$mp = 'NEON BOX / NEON SIGN';
			$tahun = ' / Tahun';
			$satuan ='Per ' .$pembayaran. ' Bulan';
			if ($pembayaran % 12 == 0){
				$xx = $pembayaran / 12;
				$satuan ='Per '.$xx.' Tahun';
				if ($pembayaran / 12 == 1){
					$satuan =' Tahunan';
				}
			}
		} else if ($obj->fields['KODE_MP'] == 'C') {
			$mp = 'SPANDUK / UMBUL-UMBUL / STANDING DISPLAY';
			$tahun = ' / Minggu';	
			$satuan ='Per ' .$pembayaran. ' Minggu';
			if ($pembayaran % 4 == 0){
				$bulan = $pembayaran / 4;
				$satuan ='Per ' .$bulan. ' Bulan';
				if ($pembayaran / 4 == 1){
					$satuan =' Bulanan';
				}
			} else if ($pembayaran == 1){
					$satuan =' Mingguan';
			}
		} else {
			$mp = 'BANNER / BALIHO';
			$tahun = ' / Hari';
			$satuan ='Per ' .$pembayaran. ' Hari';
			if ($pembayaran % 7 == 0){
				$minggu = $pembayaran / 7;
				$satuan ='Per ' .$minggu. ' Minggu';
				if ($pembayaran / 7 == 1){
					$satuan =' Mingguan';
				}
			}else if ($pembayaran == 1){
					$satuan =' Harian';
			}
		}
	
	$kategori			= $obj->fields['NAMA_TIPE'];
	$lokasi				= $obj->fields['NAMA_LOKASI'];
	
	$awal				= date("d M Y", strtotime($obj->fields['PERIODE_AWAL']));
	$akhir				= date("d M Y", strtotime($obj->fields['PERIODE_AKHIR']));
	
	$tarif				= to_money($obj->fields['TARIF']);
	$tarif2				= to_money($obj->fields['TARIF2']);
	$nilai_tambah		= to_money($obj->fields['NILAI_TAMBAH']);
	$persen_nilai_tambah		= to_decimal($obj->fields['PERSEN_NILAI_TAMBAH']);
	$nilai_kurang		= to_money($obj->fields['NILAI_KURANG']);
	$persen_nilai_kurang		= to_decimal($obj->fields['PERSEN_NILAI_KURANG']);
	$nilai_ppn			= to_money($obj->fields['NILAI_PPN']);
	$persen_ppn			= to_decimal($obj->fields['PERSEN_PPN']);
	$total				= to_money($obj->fields['TOTAL']);
	$total_bayar		= to_money($obj->fields['TOTAL_BAYAR']);
	$total_bayar2		= $obj->fields['TOTAL_BAYAR'];
	
	$status_bayar			= $obj->fields['STATUS_BAYAR'];
	$tanggal_bayar			= date("d-m-Y", strtotime($obj->fields['TANGGAL_BAYAR']));
	$jenis_bayar			= jenis_bayar($obj->fields['JENIS_BAYAR']);
	$kode_bank				= $obj->fields['NAMA_BANK'].' ('.$obj->fields['KODE_BANK'].')';
	$no_rekening			= $obj->fields['NO_REKENING'];
	$no_kwitansi			= $obj->fields['NO_KWITANSI'];
	$keterangan				= $obj->fields['KETERANGAN'];
	$kasir = $_SESSION['ID_USER'];
	
	if ($status_bayar != '2')
	{
		close($conn);
		echo '<script type="text/javascript">alert("Data belum dibayar!");window.close();</script>';
	}
	/*elseif ($status_cetak_kwt == '1')
	{
		#close($conn);
		#echo '<script type="text/javascript">alert("Data sudah dibayar dan dicetak!");window.close();</script>';
	}
	else
	{
		$query = "UPDATE KWT_PEMBAYARAN_AI SET STATUS_CETAK_KWT = '1' WHERE ID_PEMBAYARAN = '$id_pembayaran'";
		$conn->Execute($query);
	}*/
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
	
	<table class="smf t-popup wauto f-left" style="margin-right:35px">
	
	<tr><td>TANGGAL BAYAR</td><td>:</td><td><?php echo $tanggal_bayar; ?></td></tr>
	<tr><td>NO. KWITANSI</td><td>:</td><td><?php echo $no_kwitansi; ?></td></tr>
	<tr><td>JENIS BAYAR</td><td>:</td><td><?php echo $jenis_bayar; ?></td></tr>
	<tr><td>KODE BANK</td><td>:</td><td><?php echo $kode_bank; ?></td></tr>
	<tr><td>NO. REKENING</td><td>:</td><td><?php echo $no_rekening; ?></td></tr>
	
	
	<tr><td>NO. KTP</td><td>:</td><td><?php echo $no_ktp; ?></td></tr>
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<tr><td>NPWP</td><td>:</td><td><?php echo $npwp; ?></td></tr>
	<tr><td>ALAMAT</td><td>:</td><td><?php echo $alamat; ?></td></tr>
	<tr><td>NO. TELEPON</td><td>:</td><td><?php echo $no_tlp; ?></td></tr>
	<tr><td>NO. HP</td><td>:</td><td><?php echo $no_hp; ?></td></tr>
	
	<tr><td>MEDIA PROMOSI</td><td>:</td><td><?php echo $mp; ?></td></tr>	
	<tr><td>KATEGORI</td><td>:</td><td><?php echo $kategori; ?></td></tr>	
	<tr><td>LOKASI</td><td>:</td><td><?php echo $lokasi;?></td></tr>
	<tr><td>TARIF</td><td>:</td><td><?php echo 'Rp. '.$tarif.$tahun;?></td></tr>
	
	<tr><td>PERIODE</td><td>:</td><td><?php echo $awal; ; echo ' s/d '; echo $akhir; ?></td></tr>
	<tr><td>CARA PEMBAYARAN</td><td>:</td><td><?php echo $satuan;?></td></tr>
</table>


<table class="smf t-popup wauto" style="margin-right:35px">
	<tr><td>TARIF</td><td>=</td><td>Rp.</td><td size="50" align="right"><?php echo $tarif2; ?></td><td>(<?php echo $satuan; ?>)</td></tr>
	<tr><td>BIAYA STRATEGIS</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_tambah; ?></td><td>(<?php echo $persen_nilai_tambah; ?>%)</td></tr>
	<tr><td>DISCOUNT</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_kurang; ?></td><td>(<?php echo $persen_nilai_kurang; ?>%)</td></tr>
	<tr><td></td><td colspan="4"><hr></td></tr>
	<tr><td>TOTAL</td><td>=</td><td>Rp.</td><td align="right"><?php echo $total; ?></td></tr>
	<tr><td>PPN</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_ppn; ?></td><td>(<?php echo $persen_ppn; ?>%)</td></tr>
	<tr><td></td><td colspan="4"><hr></td></tr>
	<tr><td><b>TOTAL BAYAR</td><td><b>=</td><td><b>Rp.</td><td align="right"><b><?php echo $total_bayar; ?></td></tr>
	
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
<tr>
	<td colspan="5">
		<br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja($total_bayar2)); ?> rupiah.</b></i>
	</td>
</tr>
</table>
	
	<div class="clear"></div>
</div>

</body>
</html>

<?php close($conn); ?>

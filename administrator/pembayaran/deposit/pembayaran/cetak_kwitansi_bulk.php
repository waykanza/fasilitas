<?php
require_once('../../../../config/config.php');
require_once('../../../../config/terbilang.php');
$conn = conn();

$terbilang = new Terbilang;

$idp = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$idp = explode('||', $idp);
$list_id = array();

foreach ($idp as $x) { $list_id[] = base64_decode($x); }

$list_id = implode("' ,'", $list_id);

$query = "
SELECT
	b.ID_PEMBAYARAN,
	b.NO_PELANGGAN,
	p.NAMA_PELANGGAN,
	b.PERIODE,
	b.PERIODE_AWAL,
	b.PERIODE_AKHIR,
	b.KODE_BLOK,
	b.KODE_SEKTOR,
	
	b.JUMLAH_IPL,
	b.DENDA,
	b.ADMINISTRASI,
	b.JUMLAH_BAYAR,
	b.PERSEN_PPN,
	
	b.DISKON_RUPIAH_IPL AS DISKON,
	
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
	$where_trx_deposit AND 
	ID_PEMBAYARAN IN ('$list_id')";
	
$obj = $conn->Execute($query);

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
	
	.right {
		float: left;
		width: 334px;
		padding: 0 1px 0 1px;
		margin: 26px 0 0 0;
	}
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.va-top { vertical-align:top; }
</style>
</head>

<body onload="window.print()">

<?php
while( ! $obj->EOF)
{
	$id_pembayaran		= $obj->fields['ID_PEMBAYARAN'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode			= $obj->fields['PERIODE'];
	$periode_awal		= $obj->fields['PERIODE_AWAL'];
	$periode_akhir		= $obj->fields['PERIODE_AKHIR'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];

	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$administrasi		= $obj->fields['ADMINISTRASI'];

	$total				= $jumlah_ipl + $denda + $administrasi;
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
		$obj->movenext();
		echo "Tagihan belum dibayar [$periode] [$no_pelanggan]";
	}
	elseif ($status_cetak_kwt == '1')
	{
		#$obj->movenext();
		#echo "Tagihan sudah dibayar dan dicetak! [$periode] [$no_pelanggan]";
	}
	else
	{
		$conn->Execute("UPDATE KWT_PEMBAYARAN_AI SET STATUS_CETAK_KWT = '1' WHERE ID_PEMBAYARAN = '$id_pembayaran'");
	}
	?>
	
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
					
					<td>Awal</td>
					<td>:</td>
					<td><?php echo fm_date_first($periode_awal, 'd-m-Y'); ?></td>
				</tr>
				<tr>
					<td class="va-top">N a m a</td>
					<td class="va-top">:</td>
					<td class="va-top"><?php echo $nama_pelanggan; ?></td>
					
					<td class="va-top">Akhir</td>
					<td class="va-top">:</td>
					<td class="va-top"><?php echo fm_date_last($periode_akhir, 't-m-Y'); ?></td>
				</tr>
				<tr>
					<td>Blok / Sektor</td>
					<td>:</td>
					<td><?php echo $kode_blok . ' / ' . $kode_sektor; ?></td>
				</tr>
				<tr>
					<td>Luas / Tarif</td>
					<td>:</td>
					<td><?php echo ' 0 / Rp. ' . to_money($jumlah_ipl); ?></td>
					
					<td>Bayar Via</td>
					<td>:</td>
					<td><?php echo $bayar_melalui; ?></td>
				</tr>
				<tr>
					<td>Tgl. Bayar</td>
					<td>:</td>
					<td><?php echo $tgl_bayar; ?></td>
					
					<td>Tgl. Kwit</td>
					<td>:</td>
					<td><?php echo date('d-m-Y'); ?></td>
				</tr>
			</table>

			<div style="height:5px;"></div>

			<table>
				<tr>
					<td style="width:250px;">IPL Deposit</td>
					<td style="width:20px;">:</td>
					<td style="width:10px;">Rp.</td>
					<td style="width:158px;" class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
				</tr>
				<tr>
					<td>Denda</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($denda); ?></td>
				</tr>
				<tr>
					<td>Total</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($total); ?></td>
				</tr>
				<tr>
					<td>Diskon</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($diskon); ?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td colspan="2"><b><hr class="line-sum"></b></td>
				</tr>
				<tr>
					<td><b>TOTAL BAYAR</b></td>
					<td><b>:</b></td>
					<td><b>Rp.</b></td>
					<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
				</tr>
			</table>

			<div style="height:5px;"></div>

			<table>
				<tr>
					<td class="va-top" style="width:60px;">Terbilang</td>
					<td class="va-top" style="width:10px;">:</td>
					<td><?php echo strtoupper($terbilang->eja($jumlah_bayar)); ?> RUPIAH.</td>
				</tr>
				<tr>
					<td class="va-top">Keterangan</td>
					<td class="va-top">:</td>
					<td class="va-top"><?php echo $keterangan_bayar; ?></td>
				</tr>
				<tr>
					<td>Kasir</td>
					<td>:</td>
					<td><?php echo $kasir; ?></td>
				</tr>
			</table>
		</div>
		
		<div class="mid">&nbsp;</div>

		<div class="right">
			<table>
				<tr> 
					<td style="width:45%"></td>
					<td style="width:20%">No. Dok</td>
					<td style="width:5%">:</td>
					<td style="width:30%">082/F/KEU/JRP/06</td>
				</tr>
				<tr> 
					<td></td>
					<td>Rev.</td>
					<td>:</td>
					<td>0</td>
				</tr>
				<tr> 
					<td colspan="3">&nbsp;</td>
				</tr>
			</table>
			
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
					<td>Luas / Tarif</td>
					<td>:</td>
					<td><?php echo ' 0 / Rp. ' . to_money($jumlah_ipl); ?></td>
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
					<td style="width:50px;">Bulan</td>
					<td style="width:10px;">:</td>
					<td style="width:107px;"><?php echo ucfirst(fm_periode($periode)); ?></td>
					
					<td style="width:50px;">Via</td>
					<td style="width:10px;">:</td>
					<td style="width:127px;"><?php echo $bayar_melalui; ?></td>
				</tr>
				<tr>
					<td>Awal</td>
					<td>:</td>
					<td><?php echo fm_date_first($periode_awal, 'd-m-Y'); ?></td>
					
					<td>Akhir</td>
					<td>:</td>
					<td><?php echo fm_date_last($periode_awal, 't-m-Y'); ?></td>
				</tr>
			</table>

			<div style="height:5px;"></div>

			<table>
				<tr>
					<td style="width:150px;">IPL Deposit</td>
					<td style="width:10px;">:</td>
					<td style="width:10px;">Rp.</td>
					<td style="width:164px;text-align:right;"><?php echo to_money($jumlah_ipl); ?></td>
				</tr>
				<tr>
					<td>Denda</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($denda); ?></td>
				</tr>
				<tr>
					<td>Total</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($total); ?></td>
				</tr>
				<tr>
					<td>Diskon</td>
					<td>:</td>
					<td>Rp.</td>
					<td class="text-right"><?php echo to_money($diskon); ?></td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td colspan="2"><b><hr class="line-sum"></b></td>
				</tr>
				<tr>
					<td><b>TOTAL BAYAR</b></td>
					<td><b>:</b></td>
					<td><b>Rp.</b></td>
					<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
				</tr>
				<tr>
					<td colspan="4">(Include ppn <?php echo to_money($persen_ppn); ?> %)</td>
				</tr>
			</table>
		</div>
		<div class="clear"></div>
	</div>

	<div class="newpage"></div>
	
	<?php
	$obj->movenext();
}
?>
</body>
</html>

<?php close($conn); ?>

<?php
require_once('../../../../config/config.php');
$conn = conn();

$no_pelanggan = (isset($_REQUEST['id'])) ? clean(base64_decode($_REQUEST['id'])) : '';
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
	
	thead {
		background: #EEEEEE;
		color: #666666;
	}
	table {
		border-collapse: collapse;
	}
	table tr th {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
		padding: 2px;
	}
	table tr td {
		font-family: Arial, Helvetica, sans-serif;
		font-size: 12px;
	}
	tfoot {
		background: #DDDDDD;
		color: #333333;
		font-weight:bold;
	}
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.va-top { vertical-align:top; }
</style>
</head>
<body onload="window.print()">

<?php
$query = "
SELECT 
	p.KODE_BLOK,
	p.NAMA_PELANGGAN
FROM 
	KWT_PELANGGAN p
WHERE
	p.NO_PELANGGAN = '$no_pelanggan'
";

$obj = $conn->Execute($query);
	
$kode_blok		= $obj->fields['KODE_BLOK'];
$nama_pelanggan = $obj->fields['NAMA_PELANGGAN'];
?>

<table width="100%">
<tr>
	<td width="100">KODE BLOK</td>
	<td width="5">:</td>
	<td width="300"><?php echo $kode_blok; ?></td>
</tr>
<tr>
	<td>NAMA</td>
	<td>:</td>
	<td><?php echo $nama_pelanggan; ?></td>
	<td class="text-right">TGL. CETAK. <?php echo date('d-m-Y'); ?> PKL. <?php echo date('H:i'); ?></td>
</tr>
</table>

<table border="1">
<thead>
<tr>
	<th width="5%">NO.</th>
	<th width="10%">PERIODE</th>
	<th width="10%">AKHIR</th>
	<th width="10%">LALU</th>
	<th width="5%">PAKAI</th>
	<th width="10%">AIR</th>
	<th width="10%">ABONEMEN</th>
	<th width="10%">IPL</th>
	<th width="5%">DENDA</th>
	<th width="5%">ADMINISTRASI</th>
	<th width="10%">DISKON</th>
	<th width="10%">JUMLAH</th>
</tr>
</thead>

<?php
	$query = "
	DECLARE 
	@adm_kv INT,
	@adm_bg INT,
	@adm_hn INT,
	@adm_rv INT
	
	SELECT TOP 1 
	@adm_kv = ISNULL(ADMINISTRASI_KV, 0) ,
	@adm_bg = ISNULL(ADMINISTRASI_BG, 0) ,
	@adm_hn = ISNULL(ADMINISTRASI_HN, 0) ,
	@adm_rv = ISNULL(ADMINISTRASI_RV, 0)
	FROM KWT_PARAMETER
	
	SELECT 
		dbo.PTPS(b.PERIODE) AS PERIODE,
		(b.STAND_AKHIR + b.STAND_ANGKAT + b.STAND_MIN_PAKAI) AS STAND_AKHIR,
		b.STAND_LALU,
		(b.STAND_AKHIR + b.STAND_ANGKAT + b.STAND_MIN_PAKAI - b.STAND_LALU) AS PAKAI,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.JUMLAH_IPL,
		b.DENDA,
		(
			CASE TRX
				WHEN '1' THEN @adm_kv
				WHEN '2' THEN @adm_bg 
				WHEN '4' THEN @adm_hn
				WHEN '5' THEN @adm_rv
			END 
		) AS ADMINISTRASI,
		(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS DISKON,
		(
			(
				b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA +  
				CASE TRX
					WHEN '1' THEN @adm_kv
					WHEN '2' THEN @adm_bg 
					WHEN '4' THEN @adm_hn
					WHEN '5' THEN @adm_rv
				END
			) - (b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL)
		) AS JUMLAH
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND  
		b.STATUS_BAYAR IS NULL AND
		b.NO_PELANGGAN = '$no_pelanggan'
	ORDER BY PERIODE ASC
	";
	$obj = $conn->Execute($query);

	$i = 1;
	$sum_jumlah_air = 0;
	$sum_abonemen = 0;
	$sum_jumlah_ipl = 0;
	$sum_denda = 0;
	$sum_administrasi = 0;
	$sum_diskon = 0;
	$sum_jumlah = 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_AKHIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_LALU']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PAKAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH']); ?></td>
		</tr>
		<?php
		
		$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen		+= $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_administrasi	+= $obj->fields['ADMINISTRASI'];
		$sum_diskon			+= $obj->fields['DISKON'];
		$sum_jumlah			+= $obj->fields['JUMLAH'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="5" class="text-right"><b>TOTAL .......</b></td>
		<td class="text-right"><b><?php echo to_money($sum_jumlah_air); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_abonemen); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_jumlah_ipl); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_denda); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_administrasi); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_diskon); ?></b></td>
		<td class="text-right"><b><?php echo to_money($sum_jumlah); ?></b></td>
	</tr>
	</tfoot>
</table>

</body>
</html>
<?php close($conn); ?>
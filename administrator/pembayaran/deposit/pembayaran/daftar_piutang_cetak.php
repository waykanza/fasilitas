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

<table width="100%" border="1">
<thead>
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">STATUS</th>
	<th colspan="3">PERIODE</th>
	<th rowspan="2">DEPOSIT</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">ADMINISTRASI</th>
	<th rowspan="2">DISKON</th>
	<th rowspan="2">JUMLAH</th>
</tr>
<tr>
	<th>AWAL</th>
	<th>AKHIR</th>
	<th>JML.</th>
</tr>
</thead>

<?php
$query = "
DECLARE 
@adm_db INT,
@adm_dr INT

SELECT TOP 1 
@adm_db = ISNULL(ADMINISTRASI_DB, 0) ,
@adm_dr = ISNULL(ADMINISTRASI_DR, 0)
FROM KWT_PARAMETER

SELECT 
	b.STATUS_BLOK,
	dbo.PTPS(b.PERIODE_AWAL) AS PERIODE_AWAL,
	dbo.PTPS(b.PERIODE_AKHIR) AS PERIODE_AKHIR,
	b.JUMLAH_PERIODE,
	ISNULL(b.JUMLAH_IPL, 0) AS JUMLAH_IPL,
	ISNULL(b.DENDA, 0) AS DENDA,
	(
		CASE TRX
			WHEN '3' THEN @adm_db
			WHEN '6' THEN @adm_dr 
		END
	) AS ADMINISTRASI,
	ISNULL(b.DISKON_RUPIAH_IPL, 0) AS DISKON,
	(
		(
			ISNULL(b.JUMLAH_IPL, 0) + ISNULL(b.DENDA, 0) + 
			(
				CASE TRX
					WHEN '3' THEN @adm_db
					WHEN '6' THEN @adm_dr 
				END
			)
		) - ISNULL(b.DISKON_RUPIAH_IPL, 0)
	) AS JUMLAH
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_deposit AND 
	b.STATUS_BAYAR IS NULL AND
	b.NO_PELANGGAN = '$no_pelanggan'
ORDER BY PERIODE ASC
";
$obj = $conn->Execute($query);

$i = 1;
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
		<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
		<td class="text-center"><?php echo $obj->fields['PERIODE_AWAL']; ?></td>
		<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR']; ?></td>
		<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH']); ?></td>
	</tr>
	<?php
	
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
	<td colspan="5" class="text-right">TOTAL .......</td>
	<td class="text-right"><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td class="text-right"><?php echo to_money($sum_denda); ?></td>
	<td class="text-right"><?php echo to_money($sum_administrasi); ?></td>
	<td class="text-right"><?php echo to_money($sum_diskon); ?></td>
	<td class="text-right"><?php echo to_money($sum_jumlah); ?></td>
</tr>
</tfoot>
</table>

</body>
</html>
<?php close($conn); ?>
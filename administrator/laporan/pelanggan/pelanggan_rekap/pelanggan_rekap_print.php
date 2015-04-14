<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rekap Pelanggan';

if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$desc_top[] = 'Periode : ' . fm_periode($periode);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="6" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="6" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="6" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="6" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="6" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="4" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>

<tr>
	<th rowspan="2">KODE</th>
	<th rowspan="2">NAMA SEKTOR / CLUSTER</th>
	<th colspan="4">JUMLAH PELANGGAN</th>
</tr>
<tr>
	<th>AIR</th>
	<th>IPL</th>
	<th>AIR & IPL</th>
	<th>TOTAL</th>
</tr>
';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $desc_top[0]; ?></title>
<style type="text/css">
@media print {
	@page {
		size:34.6cm 27.90cm;
	}
	.newpage {page-break-before:always;}
}

.newpage {margin-top:25px;}

table {
	font-family:Arial, Helvetica, sans-serif;
	width:100%;
	border-spacing:0;
	border-collapse:collapse;
}
table tr {
	font-size:11px;
	padding:2px;
}
table td {
	padding:2px;
}
table th.nb,
table td.nb {
	border:none !important;
}
table.data th {
	border:1px solid #000000;
}
table.data td {
	border-right:1px solid #000000;
	border-left:1px solid #000000;
}
tfoot tr {
	font-weight:bold;
	text-align:right;
	border:1px solid #000000;
}
.break { 
	word-wrap:break-word; 
}
.nowrap {
	white-space:nowrap;
}
.text-left {
	text-align:left;
}
.text-center {
	text-align:center;
}
.text-right {
	text-align:right;
}
</style>
</head>
<body>

<table class="data">

<?php
echo $set_jrp;

$query = "
SELECT
	(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR) AS NAMA_SEKTOR,
	(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER) AS NAMA_CLUSTER,
	q.KODE_SEKTOR AS KODE_SEKTOR,
	q.KODE_CLUSTER AS KODE_CLUSTER,
	SUM(q.AIR) AS AIR,
	SUM(q.IPL) AS IPL,
	SUM(q.AIR_IPL) AS AIR_IPL
FROM
(
	SELECT 
		x.KODE_SEKTOR AS KODE_SEKTOR,
		x.KODE_CLUSTER AS KODE_CLUSTER,
		NULL AS AIR,
		NULL AS IPL,
		NULL AS AIR_IPL
	FROM KWT_CLUSTER x
	GROUP BY x.KODE_SEKTOR, x.KODE_CLUSTER
	
	UNION ALL
	
	SELECT 
		a.KODE_SEKTOR AS KODE_SEKTOR,
		a.KODE_CLUSTER AS KODE_CLUSTER,
		COUNT(a.NO_PELANGGAN) AS AIR,
		NULL AS IPL,
		NULL AS AIR_IPL
	FROM 
		KWT_PEMBAYARAN_AI a
	WHERE
		$where_trx_air_ipl AND 
		a.PERIODE = '$periode' AND 
		a.AKTIF_AIR = '1' AND a.AKTIF_IPL IS NULL 
		$query_search
	GROUP BY a.KODE_SEKTOR, a.KODE_CLUSTER
	
	UNION ALL
	
	SELECT 
		b.KODE_SEKTOR AS KODE_SEKTOR,
		b.KODE_CLUSTER AS KODE_CLUSTER,
		NULL AS AIR,
		COUNT(b.NO_PELANGGAN) AS IPL,
		NULL AS AIR_IPL
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode' AND 
		b.AKTIF_AIR IS NULL AND b.AKTIF_IPL = '1' 
		$query_search
	GROUP BY b.KODE_SEKTOR, b.KODE_CLUSTER
	
	UNION ALL
	
	SELECT 
		c.KODE_SEKTOR AS KODE_SEKTOR,
		c.KODE_CLUSTER AS KODE_CLUSTER,
		NULL AS AIR,
		NULL AS IPL,
		COUNT(c.NO_PELANGGAN) AS AIR_IPL
	FROM 
		KWT_PEMBAYARAN_AI c
	WHERE
		$where_trx_air_ipl AND 
		c.PERIODE = '$periode' AND 
		c.AKTIF_AIR = '1' AND c.AKTIF_IPL = '1' 
		$query_search
	GROUP BY c.KODE_SEKTOR, c.KODE_CLUSTER
) q
GROUP BY q.KODE_SEKTOR, q.KODE_CLUSTER
";
$obj = $conn->Execute($query);

$sum_air		= 0;
$sum_ipl		= 0;
$sum_air_ipl	= 0;
$sum_total		= 0;

$grp_air		= 0;
$grp_ipl		= 0;
$grp_air_ipl	= 0;
$grp_total		= 0;

$kode_sektor = '';
while( ! $obj->EOF)
{
	if ($kode_sektor == '')
	{
		?>
		<tr>
			<td><b><?php echo $obj->fields['KODE_SEKTOR']; ?></b></td>
			<td colspan="5"><b><?php echo $obj->fields['NAMA_SEKTOR']; ?></b></td>
		</tr>
		<?php
	}
	elseif ($kode_sektor != $obj->fields['KODE_SEKTOR'])
	{
		?>
		<tr>
			<td colspan="2" class="text-right"><b>SUB TOTAL .........</b></td>
			<td class="text-right"><b><?php echo to_money($grp_air); ?></b></td>
			<td class="text-right"><b><?php echo to_money($grp_ipl); ?></b></td>
			<td class="text-right"><b><?php echo to_money($grp_air_ipl); ?></b></td>
			<td class="text-right"><b><?php echo to_money($grp_total); ?></b></td>
		</tr>
		<tr>
			<td><b><?php echo $obj->fields['KODE_SEKTOR']; ?></b></td>
			<td colspan="5"><b><?php echo $obj->fields['NAMA_SEKTOR']; ?></b></td>
		</tr>
		<?php
		
		$grp_air		= 0;
		$grp_ipl		= 0;
		$grp_air_ipl	= 0;
		$grp_total		= 0;
	}

	$kode_sektor = $obj->fields['KODE_SEKTOR'];
	$total = $obj->fields['AIR'] + $obj->fields['IPL'] + $obj->fields['AIR_IPL'];

	?>
	<tr> 
		<td class="text-center"><?php echo $obj->fields['KODE_CLUSTER']; ?></td>
		<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['AIR']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['AIR_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($total); ?></td>
	</tr>
	<?php

	$grp_air		+= $obj->fields['AIR'];
	$grp_ipl		+= $obj->fields['IPL'];
	$grp_air_ipl	+= $obj->fields['AIR_IPL'];
	$grp_total		+= $total;

	$sum_air		+= $obj->fields['AIR'];
	$sum_ipl		+= $obj->fields['IPL'];
	$sum_air_ipl	+= $obj->fields['AIR_IPL'];
	$sum_total		+= $total;

	$obj->movenext();
}
?>

<tr>
	<td colspan="2" class="text-right"><b>SUB TOTAL .........</b></td>
	<td class="text-right"><b><?php echo to_money($grp_air); ?></b></td>
	<td class="text-right"><b><?php echo to_money($grp_ipl); ?></b></td>
	<td class="text-right"><b><?php echo to_money($grp_air_ipl); ?></b></td>
	<td class="text-right"><b><?php echo to_money($grp_total); ?></b></td>
</tr>

<tfoot>
<tr>
	<td colspan="2"><b>GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_air); ?></td>
	<td><?php echo to_money($sum_ipl); ?></td>
	<td><?php echo to_money($sum_air_ipl); ?></td>
	<td><?php echo to_money($sum_total); ?></td>
</tr>
</tfoot>
</table>

</body>
</html>
<?php
close($conn);
exit;
?>
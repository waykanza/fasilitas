<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL6');
$conn = conn();
die_conn($conn);

$query_search = '';

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$banyak_tangihan	= (isset($_REQUEST['banyak_tangihan'])) ? to_number($_REQUEST['banyak_tangihan']) : '1';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rekap Piutang BIAYA LAIN-LAIN';

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$field_group = 'KODE_CLUSTER';
	$query_nama = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
else
{
	$th_sek_clu = 'SEKTOR';
	
	$field_group = 'KODE_SEKTOR';
	$query_nama = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
	$query_lokasi = 'KWT_SEKTOR';
}
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="10" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="10" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="10" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="10" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="10" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="8" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman : 1 dari 1</td>
</tr>

<tr>
	<th>NO.</th>
	<th>' . $th_sek_clu . '</th>
	<th>BANYAK<br>TAGIHAN</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>TAGIHAN</th>
</tr>
';

$filename = "LAPORAN_REKAP_PIUTANG";

header("Content-type: application/msexcel");
header("Content-Disposition: attachment; filename=$filename.xls");
header("Pragma: no-cache");
header("Expires: 0");
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
	vertical-align:top;
}
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
.break { word-wrap:break-word; }
.nowrap { white-space:nowrap; }
.va-top { vertical-align:top; }
.va-bottom { vertical-align:bottom; }
.text-left { text-align:left; }
.text-center { text-align:center; }
.text-right { text-align:right; }
</style>
</head>
<body>

<table class="data">

<?php
echo $set_jrp;

$query = "
SELECT
	$query_nama AS NAMA_LOKASI,
	SUM(q.BANYAK_TAGIHAN) AS BANYAK_TAGIHAN,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADM) AS ADM,
	SUM(q.DISKON_IPL) AS DISKON_IPL,
	SUM(q.TAGIHAN) AS JUMLAH_TAGIHAN
FROM 
(
	SELECT 
		$field_group,
		0 AS BANYAK_TAGIHAN,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADM,
		0 AS DISKON_IPL,
		0 AS JUMLAH_TAGIHAN
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		b.$field_group AS $field_group,
		COUNT(b.NO_PELANGGAN) AS BANYAK_TAGIHAN,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADM) AS ADM,
		SUM(b.DISKON_IPL) AS DISKON_IPL,
		SUM(b.JUMLAH_IPL + b.DENDA + b.ADM - b.DISKON_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_lain_lain AND 
		b.STATUS_BAYAR = 0 AND
		(SELECT COUNT(NO_PELANGGAN) FROM KWT_PEMBAYARAN_AI WHERE $where_trx_lain_lain AND NO_PELANGGAN = b.NO_PELANGGAN AND STATUS_BAYAR = 0) >= $banyak_tangihan
		$query_search
	GROUP BY b.$field_group
) q
GROUP BY q.$field_group
ORDER BY q.$field_group ASC
";
$obj = $conn->Execute($query);

$i = 1;

$sum_banyak_tangihan	= 0;
$sum_jumlah_ipl		= 0;
$sum_denda			= 0;
$sum_adm	= 0;
$sum_diskon_ipl = 0;
$sum_jumlah_tagihan = 0;

while( ! $obj->EOF)
{		
	?>
	<tr> 
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['BANYAK_TAGIHAN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
	</tr>
	<?php
	
	$sum_banyak_tangihan	+= $obj->fields['BANYAK_TAGIHAN'];
	$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
	$sum_denda			+= $obj->fields['DENDA'];
	$sum_adm			+= $obj->fields['ADM'];
	$sum_diskon_ipl		+= $obj->fields['DISKON_IPL'];
	$sum_jumlah_tagihan += $obj->fields['JUMLAH_TAGIHAN'];
	
	$i++;
	$obj->movenext();
}
?>
<tfoot>
<tr>
	<td colspan="2">GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_banyak_tangihan); ?></td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_jumlah_tagihan); ?></td>
</tr>
</tfoot>

</table>
</body>
</html>
<?php
close($conn);
exit;
?>
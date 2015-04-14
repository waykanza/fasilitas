<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rekap Rencana Penerimaan SAVE DEPOSIT';

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$field_group = 'KODE_CLUSTER';
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search = " AND b.KODE_SEKTOR = '$kode_sektor' ";
	$query_nama = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	
	$desc_top[] = 'Sektor : ' . get_nama('sektor', $kode_sektor);
}
else
{	
	$th_sek_clu = 'SEKTOR';
	
	$field_group = 'KODE_SEKTOR';
	$query_lokasi = 'KWT_SEKTOR';
	$query_nama = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
}

$desc_top[] = 'Periode : ' . fm_periode($periode);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="7" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="7" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="7" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="7" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="7" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="5" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right">Halaman : 1 dari 1</td>
</tr>

<tr>
	<th>NO.</th>
	<th>' . $th_sek_clu . '</th>
	<th>DEPOSIT</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>TOTAL TAGIHAN</th>
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
	$query_nama AS NAMA_LOKASI,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADMINISTRASI) AS ADMINISTRASI,
	SUM(q.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
	SUM(q.TAGIHAN) AS TAGIHAN
FROM 
(
	SELECT 
		$field_group,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADMINISTRASI,
		0 AS DISKON_RUPIAH_IPL,
		0 AS TAGIHAN
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		$field_group AS field_group,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADMINISTRASI) AS ADMINISTRASI,
		SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		SUM(b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_IPL) AS TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_deposit AND 
		b.PERIODE = '$periode'
		$query_search
	GROUP BY b.$field_group
) q
GROUP BY q.$field_group
ORDER BY q.$field_group ASC
";

$obj = $conn->Execute($query);

$i = 1;

$sum_jumlah_ipl			= 0;
$sum_denda				= 0;
$sum_administrasi		= 0;
$sum_diskon_rupiah_ipl	= 0;
$sum_tagihan			= 0;

while( ! $obj->EOF)
{		
	?>
	<tr> 
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['TAGIHAN']); ?></td>
	</tr>
	<?php
	
	$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
	$sum_denda				+= $obj->fields['DENDA'];
	$sum_administrasi		+= $obj->fields['ADMINISTRASI'];
	$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
	$sum_tagihan			+= $obj->fields['TAGIHAN'];
	
	$i++;
	$obj->movenext();
}
?>
<tfoot>
<tr>
	<td colspan="2">TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_administrasi); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
	<td><?php echo to_money($sum_tagihan); ?></td>
</tr>
</tfoot>
</table>

</body>
</html>
<?php
close($conn);
exit;
?>
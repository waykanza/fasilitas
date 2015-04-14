<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$trx = (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Daftar Pelanggan';

if ($trx != '')
{
	$query_search .= "AND p.STATUS_BLOK = '$trx' ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$query = "
SELECT 
	COUNT(p.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PELANGGAN p
WHERE 
	p.DISABLED IS NULL
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="16" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="16" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="16" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="16" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="16" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="13" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="3" class="nb text-right va-bottom">Halaman 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">BLOK</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th colspan="4">PELANGGAN</th>
	<th colspan="2">LUAS (M&sup2;)</th>
	<th colspan="3">AIR</th>
	<th colspan="3">IPL</th>
</tr>
<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>NO. TELEPON</th>
	<th>NO. HANDPHONE</th>
	<th>KAVL.</th>
	<th>BANG.</th>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>PERIODE<br>TERAKHIR</th>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>PERIODE<br>TERAKHIR</th>
</tr>
';

$p = $page_num;
function th_print() {
	Global $p, $set_jrp, $set_th;
	echo $set_jrp . $p . $set_th;
	$p++;
}

$filename = "LAPORAN_DAFTAR_PELANGGAN";

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
echo th_print();

if ($total_data > 0)
{
	$query = "
	SELECT 
		p.KODE_BLOK,
		s.NAMA_SEKTOR,
		c.NAMA_CLUSTER,
		p.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		p.NO_TELEPON,
		p.NO_HP,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.AKTIF_AIR,
		p.KEY_AIR,
		p.AKTIF_IPL,
		p.KEY_IPL,
		dbo.PTPS(
			(SELECT MAX(PERIODE) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE $where_trx_air_ipl AND NO_PELANGGAN = p.NO_PELANGGAN)
		) AS PERIODE_AKHIR_AIR,
		dbo.PTPS(
			(SELECT MAX(PERIODE_AKHIR) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE $where_trx_air_ipl AND NO_PELANGGAN = p.NO_PELANGGAN)
		) AS PERIODE_AKHIR_IPL
	FROM 
		KWT_PELANGGAN p 
		LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE 
		p.DISABLED IS NULL
		$query_search
	ORDER BY p.KODE_SEKTOR, p.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1;
	$gr_kode_cluster = '';
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		
		?>
		<tr>
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NO_TELEPON']; ?></td>
			<td><?php echo $obj->fields['NO_HP']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_AIR']); ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR_AIR']; ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_IPL']); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR_IPL']; ?></td>
		</tr>
		<?php
		$i++;
		
		$obj->movenext();
	}
}
?>
</table>
</body>
</html>
<?php
close($conn);
exit;
?>
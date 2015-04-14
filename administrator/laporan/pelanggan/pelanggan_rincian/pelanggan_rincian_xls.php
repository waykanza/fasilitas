<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Pelanggan';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	$desc_top[] = 'Sektor : ' . get_nama('sektor', $kode_sektor);
}
if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}
if ($aktif_air != '')
{
	$query_search .= " AND b.AKTIF_AIR = '1' ";
	$desc_top[] = 'Aktif Air ';
}
if ($aktif_ipl != '')
{
	$query_search .= " AND b.AKTIF_IPL = '1' ";
	$desc_top[] = 'Aktif IPL ';
}

$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
WHERE
	$where_trx_air_ipl AND 
	b.PERIODE = '$periode'
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

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
	<td align="right" colspan="2" class="nb text-right va-bottom">Halaman 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">BLOK/NO.</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th colspan="2">LUAS</th>
	<th rowspan="2">NO. TELEPON</th>
</tr>
<tr>
	<th>KAVL.</th>
	<th>BANG.</th>
</tr>
';

$p = 1;
function th_print() {
	Global $p, $set_jrp, $set_th;
	echo $set_jrp . $p . $set_th;
	$p++;
}

$filename = "LAPORAN_RINCIAN_PELANGGAN";

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
		b.KODE_CLUSTER,
		c.NAMA_CLUSTER,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.NO_TELEPON
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode'
		$query_search
	ORDER BY b.KODE_SEKTOR, b.KODE_BLOK, b.NO_PELANGGAN ASC
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	$gr_kode_cluster = '';
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		if ($gr_kode_cluster != $obj->fields['KODE_CLUSTER'])
		{
			?>
			<tr>
				<td class="text-center"><b><?php echo $obj->fields['KODE_CLUSTER']; ?></b></td>
				<td colspan="17"><b><?php echo $obj->fields['NAMA_CLUSTER']; ?></b></td>
			</tr>
			<?php
			$gr_kode_cluster = $obj->fields['KODE_CLUSTER'];
			$i = 1;
		}
		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td><?php echo $obj->fields['NO_TELEPON']; ?></td>
		</tr>
		<?php
		
		if ($i % $per_page === 0)
		{
			echo '<tr><td class="nb"><div class="newpage"></div></td></tr>';
			th_print();
		}
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
<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$tgl_start		= (isset($_REQUEST['tgl_start'])) ? clean($_REQUEST['tgl_start']) : '';
$tgl_end		= (isset($_REQUEST['tgl_end'])) ? clean($_REQUEST['tgl_end']) : '';

$tmp = explode('-',$tgl_start);
$query_tgl_start = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
$tmp = explode('-',$tgl_end);
$query_tgl_end = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Penutusan Air';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	$desc_top[] = 'Sektor : ' . get_nama('sektor', $kode_sektor);
}
if ($kode_cluster != '')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
	$desc_top[] = 'Cluster : ' . get_nama('cluster', $kode_cluster);
}
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
	p.TGL_PEMUTUSAN > CAST(('$query_tgl_start') AS DATE) AND
	p.TGL_PEMUTUSAN < CAST(('$query_tgl_end') AS DATE) 
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="8" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="8" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="8" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="8" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="8" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="6" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" align="right" class="nb text-right va-bottom">Halaman 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">BLOK/NO.</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th colspan="2">PEMUTUSAN</th>
</tr>
<tr>
	<th>TANGGAL</th>
	<th>NAMA PETUGAS</th>
</tr>';

$p = 1;
function th_print() {
	Global $p, $set_jrp, $set_th;
	echo $set_jrp . $p . $set_th;
	$p++;
}
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
		CONVERT(VARCHAR(10),p.TGL_PEMUTUSAN,105) AS TGL_PEMUTUSAN,
		p.PETUGAS
	FROM 
		KWT_PELANGGAN p
		LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		p.TGL_PEMUTUSAN > CAST(('$query_tgl_start') AS DATE) AND
		p.TGL_PEMUTUSAN < CAST(('$query_tgl_end') AS DATE) 
		$query_search
	ORDER BY p.TGL_PEMUTUSAN ASC
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	
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
			<td class="text-center"><?php echo $obj->fields['TGL_PEMUTUSAN']; ?></td>
			<td><?php echo $obj->fields['PETUGAS']; ?></td>
		</tr>
		<?php
		
		if ($total_rows == $i)
		{
			?>
			
			<?php
		}
		elseif ($i % $per_page === 0)
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
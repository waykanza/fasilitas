<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$tgl_posting	= (isset($_REQUEST['tgl_posting'])) ? clean($_REQUEST['tgl_posting']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Daftar Faktur Pajak';

if ($trx != '')
{
	$query_search .= "AND p.STATUS_BLOK = '$trx' ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	$where_trx_air_ipl AND 
	CONVERT(VARCHAR(10),b.TGL_POST_FP,105) = '$tgl_posting'
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);

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
	<td colspan="2" align="right" class="nb text-right va-bottom">Halaman : 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>NPWP</th>
	<th>NO. SERI FAKTUR</th>
	<th>TANGGAL</th>
	<th>NILAI DPP</th>
	<th>NILAI PPN</th>
	<th>BLOK NO</th>
	<th>NO. KWITANSI</th>
	<th>NILAI KWITANSI</th>
</tr>
';

$p = $page_num;
function th_print() {
	Global $p, $set_jrp, $set_th;
	echo $set_jrp . $p . $set_th;
	$p++;
}

$filename = "LAPORAN_DAFTAR_FAKTUR_PAJAK_$tgl_posting";

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
echo th_print();

if ($total_data > 0)
{
	$query = "
	SELECT 
		p.NAMA_PELANGGAN,
		p.NPWP,
		b.NO_FAKTUR_PAJAK,
		CONVERT(VARCHAR(10),b.TGL_FAKTUR_PAJAK,105) AS TGL_FAKTUR_PAJAK,
		((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (100 / (100 + b.PERSEN_PPN))) AS NILAI_DPP,
		b.NILAI_PPN,
		b.KODE_BLOK,
		b.NO_KWITANSI,
		b.JUMLAH_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_air_ipl AND 
		CONVERT(VARCHAR(10),b.TGL_POST_FP,105) = '$tgl_posting'
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1 + $page_start;
	$sum_nilai_dpp = 0;
	$sum_nilai_ppn = 0;
	$sum_nilai_kwitansi = 0;
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NPWP']; ?></td>
			<td><?php echo $obj->fields['NO_FAKTUR_PAJAK']; ?></td>
			<td><?php echo $obj->fields['TGL_FAKTUR_PAJAK']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_DPP']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-right"><?php echo $obj->fields['NO_KWITANSI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
		</tr>
		<?php
		
		$sum_nilai_dpp		+= $obj->fields['NILAI_DPP'];
		$sum_nilai_ppn		+= $obj->fields['NILAI_PPN'];
		$sum_nilai_kwitansi	+= $obj->fields['JUMLAH_BAYAR'];
		
		if ($total_rows == ($i - $page_start))
		{
			
		}
		elseif ($i % $per_page === 0)
		{
			echo '<tr><td class="nb"><div class="newpage"></div></td></tr>';
			th_print();
		}
		
		$i++;
		$obj->movenext();
	}
	
	?>
	<tfoot>
	<tr>
		<td colspan="5">GRAND TOTAL .........</td>
		<td><?php echo to_money($sum_nilai_dpp); ?></td>
		<td><?php echo to_money($sum_nilai_ppn); ?></td>
		<td colspan="2"></td>
		<td><?php echo to_money($sum_nilai_kwitansi); ?></td>
	</tr>
	</tfoot>
	<?php
}
?>
</table>
</body>
</html>
<?php
close($conn);
exit;
?>
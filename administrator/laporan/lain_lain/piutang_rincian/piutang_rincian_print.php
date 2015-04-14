<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL5');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$banyak_tangihan	= (isset($_REQUEST['banyak_tangihan'])) ? to_number($_REQUEST['banyak_tangihan']) : '1';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Piutang BIAYA LAIN-LAIN';

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

$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) OVER () AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 0
	$query_search
GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
HAVING COUNT(b.NO_PELANGGAN) >= $banyak_tangihan
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="11" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="11" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="11" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="11" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="11" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="9" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman : 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th>NO.</th>
	<th>BLOK / NO.</th>
	<th>NAMA PELANGGAN</th>
	<th>BANYAK<br>TAGIHAN</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>TAGIHAN</th>
</tr>
';

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
<body onload="window.print()">

<table class="data">

<?php
echo th_print();

if ($total_data > 0)
{
	$query = "
	SELECT 
		b.KODE_BLOK,
		(SELECT NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS NAMA_PELANGGAN,
		b.NO_PELANGGAN,
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
		b.STATUS_BAYAR = 0
		$query_search
	GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
	HAVING COUNT(b.NO_PELANGGAN) >= $banyak_tangihan
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	
	$sum_banyak_tangihan	= 0;
	$sum_jumlah_ipl		= 0;
	$sum_denda			= 0;
	$sum_adm	= 0;
	$sum_diskon_ipl = 0;
	$sum_jumlah_tagihan = 0;
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
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
		
		if ($total_rows == $i)
		{
			?>
			<tfoot>
			<tr>
				<td colspan="3">GRAND TOTAL .........</td>
				<td><?php echo to_money($sum_banyak_tangihan); ?></td>
				<td><?php echo to_money($sum_jumlah_ipl); ?></td>
				<td><?php echo to_money($sum_adm); ?></td>
				<td><?php echo to_money($sum_diskon_ipl); ?></td>
				<td><?php echo to_money($sum_denda); ?></td>
				<td><?php echo to_money($sum_jumlah_tagihan); ?></td>
			</tr>
			</tfoot>
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
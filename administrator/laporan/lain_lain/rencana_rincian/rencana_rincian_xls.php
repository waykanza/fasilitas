<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL1');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page		= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$periode_tag		= (isset($_REQUEST['periode_tag'])) ? to_periode($_REQUEST['periode_tag']) :'';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) :'';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) :'';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Rencana Penerimaan BIAYA LAIN-LAIN';

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
	$query_search .= " AND TRX = $trx ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$desc_top[] = 'Periode : ' . fm_periode($periode_tag);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="13" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="13" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="13" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="13" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="13" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="11" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman : 1 dari 1</td>
</tr>

<tr>
	<th>NO.</th>
	<th>NO. TAGIHAN</th>
	<th>BLOK / NO.</th>
	<th>NO. PELANGGAN</th>
	<th>NAMA PELANGGAN</th>
	<th>LUAS (M<sup>2</sup>)</th>
	<th>TARIF</th>
	<th>PERIODE TAG.</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL TAGIHAN</th>
</tr>
';

$filename = "LAPORAN_RINCIAN_RENCANAN_PENERIMAAN_$periode_tag";

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
		b.NO_INVOICE,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		b.JUMLAH_PERIODE_IPL,
		b.JUMLAH_IPL,
		b.DENDA,
		b.ADM,
		b.DISKON_IPL,
		(b.JUMLAH_IPL + b.DENDA + b.ADM - b.DISKON_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_lain_lain AND 
		b.PERIODE_TAG = '$periode_tag'
		$query_search
	ORDER BY b.KODE_BLOK ASC
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_adm				= 0;
	$sum_diskon_ipl	= 0;
	$sum_jumlah_tagihan 	= 0;
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td class="nowrap"><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo fm_nopel($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'], 2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF_IPL']); ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		<?php
		
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_adm				+= $obj->fields['ADM'];
		$sum_diskon_ipl			+= $obj->fields['DISKON_IPL'];
		$sum_jumlah_tagihan 	+= $obj->fields['JUMLAH_TAGIHAN'];
		
		$i++;
		
		$obj->movenext();
	}

?>
<tfoot>
<tr>
	<td colspan="8">TOTAL .........</td>
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
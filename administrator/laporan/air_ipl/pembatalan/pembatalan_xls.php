<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LA1');
$conn = conn();
die_conn($conn);

$query_search = '';

$periode_tag	= (isset($_REQUEST['periode_tag'])) ? to_periode($_REQUEST['periode_tag']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$kode_zona		= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Rencana Penerimaan';

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
if ($kode_zona != '')
{
	$query_search .= " AND b.KODE_ZONA = '$kode_zona' ";
	$desc_top[] = 'Zona meter : ' . get_nama('zona_meter', $kode_zona);
}
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}
if ($aktif_air != '') {
	$query_search .= " AND b.AKTIF_AIR = $aktif_air ";
	$desc_top[] = ($aktif_air == '1') ? 'Aktif Air ' : 'Tidak Aktif Air ';
}
if ($aktif_ipl != '') {
	$query_search .= " AND b.AKTIF_IPL = $aktif_ipl ";
	$desc_top[] = ($aktif_ipl == '1') ? 'Aktif IPL ' : 'Tidak Aktif IPL ';
}

$desc_top[] = 'Periode : ' . fm_periode($periode_tag);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="17" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="17" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="17" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="17" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="17" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="15" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>

<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NO. TAGIHAN</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th rowspan="2">LUAS (M2)</th>
	<th rowspan="2">TARIF</th>
	<th rowspan="2">PERIODE</th>
	<th rowspan="2">IPL</th>
	<th rowspan="2">PEMAKAIAN</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">ADM</th>
	<th colspan="2">DISKON</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">TOTAL<br>TAGIHAN</th>
</tr>
<tr>
	<th>AIR</th>
	<th>IPL</th>
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
echo $set_jrp;

	$obj = $conn->Execute("
	SELECT TOP 1 
	ADM_KV, ADM_BG,
	ADM_HN, ADM_RV
	FROM KWT_PARAMETER");
	
	$adm_kv = $obj->fields['ADM_KV'];
	$adm_bg = $obj->fields['ADM_BG'];
	$adm_hn = $obj->fields['ADM_HN'];
	$adm_rv = $obj->fields['ADM_RV'];
	
	$query = "
	SELECT 
		b.NO_INVOICE,
		b.KODE_CLUSTER,
		c.NAMA_CLUSTER,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		b.JUMLAH_PERIODE_IPL,
		b.JUMLAH_IPL,
		((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.DENDA,
		(
			CASE WHEN b.STATUS_BAYAR = 1 THEN b.ADM ELSE 
			CASE TRX
				WHEN $trx_kv THEN $adm_kv 
				WHEN $trx_bg THEN $adm_bg
				WHEN $trx_hn THEN $adm_hn 
				WHEN $trx_rv THEN $adm_rv
			END END 
		) AS ADM,
		b.DISKON_AIR,
		b.DISKON_IPL,
		(
			b.JUMLAH_AIR + b.JUMLAH_IPL + b.ABONEMEN + b.DENDA + 
			CASE WHEN b.STATUS_BAYAR = 1 THEN b.ADM ELSE 
			CASE TRX
				WHEN $trx_kv THEN $adm_kv 
				WHEN $trx_bg THEN $adm_bg 
				WHEN $trx_hn THEN $adm_hn 
				WHEN $trx_rv THEN $adm_rv 
			END END 
			- b.DISKON_AIR - b.DISKON_IPL
		) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b 
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE_TAG = '$periode_tag'
		$query_search
	ORDER BY b.KODE_CLUSTER, b.KODE_BLOK ASC
	";
	
	$obj = $conn->Execute($query);
	
	$i = 1;
	
	$sum_jumlah_ipl		= 0;
	$sum_pemakaian		= 0;
	$sum_jumlah_air		= 0;
	$sum_abonemen		= 0;
	$sum_denda			= 0;
	$sum_adm			= 0;
	$sum_diskon_air		= 0;
	$sum_diskon_ipl		= 0;
	$sum_jumlah_tagihan	= 0;
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo fm_nopel($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF_IPL']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['JUMLAH_PERIODE_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PEMAKAIAN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		
		<?php
		
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_pemakaian		+= $obj->fields['PEMAKAIAN'];
		$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen		+= $obj->fields['ABONEMEN'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_adm			+= $obj->fields['ADM'];
		$sum_diskon_air		+= $obj->fields['DISKON_AIR'];
		$sum_diskon_ipl		+= $obj->fields['DISKON_IPL'];
		$sum_jumlah_tagihan	+= $obj->fields['JUMLAH_TAGIHAN'];
		
		if ($total_rows == $i)
		{
			
		}
		
		$i++;
		$obj->movenext();
	}
?>
<tfoot>
<tr>
	<td colspan="8">GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_pemakaian); ?></td>
	<td><?php echo to_money($sum_jumlah_air); ?></td>
	<td><?php echo to_money($sum_abonemen); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_air); ?></td>
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
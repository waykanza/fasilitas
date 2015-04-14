<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rekap Rencana Penerimaan';

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$query_group = 'KODE_CLUSTER';
	$query_header = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search = " AND b.KODE_SEKTOR = '$kode_sektor' ";
	
	$desc_top[] = 'Sektor : ' . get_nama('sektor', $kode_sektor);
}
else
{	
	$th_sek_clu = 'SEKTOR';
	
	$query_group = 'KODE_SEKTOR';
	$query_header = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
	$query_lokasi = 'KWT_SEKTOR';
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

$desc_top[] = 'Periode : ' . fm_periode($periode);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="12" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="12" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="12" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="12" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="12" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="10" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>

<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">' . $th_sek_clu . '</th>
	<th rowspan="2">PEMAKAIAN</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">IPL</th>
	<th rowspan="2">DENDAN</th>
	<th rowspan="2">ADMINISTRASI</th>
	<th colspan="2">DISKON</th>
	<th rowspan="2">JML. DISKON</th>
	<th rowspan="2">JML. BAYAR</th>
</tr>
<tr>
	<th>AIR</th>
	<th>IPL</th>
</tr>
';

$filename = "LAPORAN_REKAP_RENCANAN_PENERIMAAN_$periode";

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

$obj = $conn->Execute("
SELECT TOP 1 
ADMINISTRASI_KV, ADMINISTRASI_BG,
ADMINISTRASI_HN, ADMINISTRASI_RV
FROM KWT_PARAMETER");

$adm_kv = $obj->fields['ADMINISTRASI_KV'];
$adm_bg = $obj->fields['ADMINISTRASI_BG'];
$adm_hn = $obj->fields['ADMINISTRASI_HN'];
$adm_rv = $obj->fields['ADMINISTRASI_RV'];

$query = "
SELECT
	$query_header AS NAMA_LOKASI,
	SUM(q.PEMAKAIAN) AS PEMAKAIAN,
	SUM(q.JUMLAH_AIR) AS JUMLAH_AIR,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ABONEMEN) AS ABONEMEN,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADMINISTRASI) AS ADMINISTRASI,
	SUM(q.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
	SUM(q.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
	SUM(q.JUMLAH_DISKON) AS JUMLAH_DISKON,
	SUM(q.JUMLAH_TAGIHAN) AS JUMLAH_TAGIHAN
FROM 
(
	SELECT 
		$query_group,
		NULL AS PEMAKAIAN,
		NULL AS JUMLAH_AIR,
		NULL AS ABONEMEN,
		NULL AS JUMLAH_IPL,
		NULL AS DENDA,
		NULL AS ADMINISTRASI,
		NULL AS DISKON_RUPIAH_AIR,
		NULL AS DISKON_RUPIAH_IPL,
		NULL AS JUMLAH_DISKON,
		NULL AS JUMLAH_TAGIHAN
	FROM $query_lokasi

	UNION ALL

	SELECT 
		$query_group AS query_group,
		SUM((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
		SUM(b.JUMLAH_AIR) AS JUMLAH_AIR,
		SUM(b.ABONEMEN) AS ABONEMEN,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(
			CASE WHEN b.STATUS_BAYAR = '2' THEN b.ADMINISTRASI ELSE 
			CASE TRX
				WHEN '1' THEN $adm_kv WHEN '2' THEN $adm_bg
				WHEN '4' THEN $adm_hn WHEN '5' THEN $adm_rv
			END END 
		) AS ADMINISTRASI,
		SUM(b.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
		SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		SUM(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS JUMLAH_DISKON,
		SUM(
			(
				b.JUMLAH_AIR + b.JUMLAH_IPL + b.ABONEMEN + b.DENDA + 
				CASE WHEN b.STATUS_BAYAR = '2' THEN b.ADMINISTRASI ELSE 
				CASE TRX
					WHEN '1' THEN $adm_kv WHEN '2' THEN $adm_bg
					WHEN '4' THEN $adm_hn WHEN '5' THEN $adm_rv
				END END 
			) - (b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL)
		) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode'
		$query_search
	GROUP BY b.$query_group
) q
GROUP BY q.$query_group
ORDER BY q.$query_group ASC
";

$obj = $conn->Execute($query);

$i = 1;

$sum_pemakaian			= 0;
$sum_jumlah_air			= 0;
$sum_abonemen			= 0;
$sum_jumlah_ipl			= 0;
$sum_jumlah_denda		= 0;
$sum_jumlah_administrasi = 0;
$sum_diskon_rupiah_air	= 0;
$sum_diskon_rupiah_ipl 	= 0;
$sum_jumlah_diskon		= 0;
$sum_jumlah_tagihan		= 0;

while( ! $obj->EOF)
{		
	?>
	<tr> 
		<td align="center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td align="right"><?php echo to_money($obj->fields['PEMAKAIAN']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['DISKON_RUPIAH_AIR']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['JUMLAH_DISKON']); ?></td>
		<td align="right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
	</tr>
	<?php
	
	$sum_pemakaian			+= $obj->fields['PEMAKAIAN'];
	$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
	$sum_abonemen			+= $obj->fields['ABONEMEN'];
	$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
	$sum_jumlah_denda		+= $obj->fields['DENDA'];
	$sum_jumlah_administrasi += $obj->fields['ADMINISTRASI'];
	$sum_diskon_rupiah_air	+= $obj->fields['DISKON_RUPIAH_AIR'];
	$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
	$sum_jumlah_diskon		+= $obj->fields['JUMLAH_DISKON'];
	$sum_jumlah_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
	
	$i++;
	$obj->movenext();
}
?>
<tfoot>
<tr>
	<td colspan="2">GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_pemakaian); ?></td>
	<td><?php echo to_money($sum_jumlah_air); ?></td>
	<td><?php echo to_money($sum_abonemen); ?></td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_jumlah_denda); ?></td>
	<td><?php echo to_money($sum_jumlah_administrasi); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_air); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
	<td><?php echo to_money($sum_jumlah_diskon); ?></td>
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
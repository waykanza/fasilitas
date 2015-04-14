<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page		= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$kode_zona		= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
$jdl			= (isset($_REQUEST['jdl'])) ? clean($_REQUEST['jdl']) : '';

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

$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	$where_trx_air_ipl AND 
	b.PERIODE = '$periode'
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="18" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="18" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="18" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="18" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="18" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="16" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman 
';

$set_th = '
	dari ' . $total_page . '</td>
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
	<th rowspan="2">DENDA</th>
	<th rowspan="2">ADM</th>
	<th colspan="3">DISKON</th>
	<th rowspan="2">TOTAL<br>TAGIHAN</th>
</tr>
<tr>
	<th>AIR</th>
	<th>IPL</th>
	<th>JML.</th>
</tr>
';

$p = 1;
function th_print() {
	Global $p, $set_jrp, $set_th;
	echo $set_jrp . $p . $set_th;
	$p++;
}

$filename = "LAPORAN_RINCIAN_RENCANAN_PENERIMAAN_$periode";

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
		b.NO_INVOICE,
		b.KODE_CLUSTER,
		c.NAMA_CLUSTER,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		b.JUMLAH_PERIODE,
		b.JUMLAH_IPL,
		((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.DENDA,
		(
			CASE WHEN b.STATUS_BAYAR = '2' THEN b.ADMINISTRASI ELSE 
			CASE TRX
				WHEN '1' THEN $adm_kv WHEN '2' THEN $adm_bg
				WHEN '4' THEN $adm_hn WHEN '5' THEN $adm_rv
			END END 
		) AS ADMINISTRASI,
		b.DISKON_RUPIAH_AIR,
		b.DISKON_RUPIAH_IPL,
		(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS JUMLAH_DISKON,
		(
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
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode'
		$query_search
	ORDER BY b.KODE_CLUSTER, b.KODE_BLOK ASC
	";
	
	$obj = $conn->Execute($query);
	
	$i = 1;
	
	$sum_jumlah_ipl			= 0;
	$sum_pemakaian			= 0;
	$sum_jumlah_air			= 0;
	$sum_abonemen			= 0;
	$sum_denda				= 0;
	$sum_administrasi		= 0;
	$sum_diskon_rupiah_air	= 0;
	$sum_diskon_rupiah_ipl	= 0;
	$sum_jumlah_diskon		= 0;
	$sum_jumlah_tagihan		= 0;
	
	if ($jdl == '1')
	{
		$grp_kode_cluster	= '';
		$grp_jumlah_ipl			= 0;
		$grp_pemakaian			= 0;
		$grp_jumlah_air			= 0;
		$grp_abonemen			= 0;
		$grp_denda				= 0;
		$grp_administrasi		= 0;
		$grp_diskon_rupiah_air	= 0;
		$grp_diskon_rupiah_ipl	= 0;
		$grp_jumlah_diskon		= 0;
		$grp_jumlah_tagihan		= 0;
	}
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		if ($jdl == '1')
		{
			if ($grp_kode_cluster == '')
			{
				?>
				<tr>
					<td class="text-center"><b><?php echo $obj->fields['KODE_CLUSTER']; ?></b></td>
					<td colspan="17"><b><?php echo $obj->fields['NAMA_CLUSTER']; ?></b></td>
				</tr>
				<?php
			}
			
			if ($grp_kode_cluster != '' AND $grp_kode_cluster != $obj->fields['KODE_CLUSTER'])
			{
				?>
				<tr>
					<td colspan="8" class="text-right"><b>SUB TOTAL .........</b></td>
					<td class="text-right"><b><?php echo to_money($grp_pemakaian); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_air); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_abonemen); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_ipl); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_denda); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_administrasi); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_diskon_rupiah_air); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_diskon_rupiah_ipl); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_diskon); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_tagihan); ?></b></td>
				</tr>
				<tr>
					<td class="text-center"><b><?php echo $obj->fields['KODE_CLUSTER']; ?></b></td>
					<td colspan="17"><b><?php echo $obj->fields['NAMA_CLUSTER']; ?></b></td>
				</tr>
				<?php
				
				if ($grp_kode_cluster != $obj->fields['KODE_CLUSTER']) {
					$i = 1;
				}
				
				$grp_jumlah_ipl			= 0;
				$grp_pemakaian			= 0;
				$grp_jumlah_air			= 0;
				$grp_abonemen			= 0;
				$grp_denda				= 0;
				$grp_administrasi		= 0;
				$grp_diskon_rupiah_air	= 0;
				$grp_diskon_rupiah_ipl	= 0;
				$grp_jumlah_diskon		= 0;
				$grp_jumlah_tagihan		= 0;
			}
			
			$grp_kode_cluster = $obj->fields['KODE_CLUSTER'];
		}
		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF_IPL']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['JUMLAH_PERIODE']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PEMAKAIAN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_DISKON']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		
		<?php
		if ($jdl == '1')
		{
			$grp_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
			$grp_pemakaian			+= $obj->fields['PEMAKAIAN'];
			$grp_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
			$grp_abonemen			+= $obj->fields['ABONEMEN'];
			$grp_denda				+= $obj->fields['DENDA'];
			$grp_administrasi		+= $obj->fields['ADMINISTRASI'];
			$grp_diskon_rupiah_air	+= $obj->fields['DISKON_RUPIAH_AIR'];
			$grp_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
			$grp_jumlah_diskon		+= $obj->fields['JUMLAH_DISKON'];
			$grp_jumlah_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
		}
		
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_pemakaian			+= $obj->fields['PEMAKAIAN'];
		$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen			+= $obj->fields['ABONEMEN'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_administrasi		+= $obj->fields['ADMINISTRASI'];
		$sum_diskon_rupiah_air	+= $obj->fields['DISKON_RUPIAH_AIR'];
		$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
		$sum_jumlah_diskon		+= $obj->fields['JUMLAH_DISKON'];
		$sum_jumlah_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
		
		if ($total_rows == $i)
		{
			if ($jdl == '1')
			{
				?>
				<tr>
					<td colspan="8" class="text-right"><b>SUB TOTAL .........</b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_ipl); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_pemakaian); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_air); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_abonemen); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_denda); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_administrasi); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_diskon_rupiah_air); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_diskon_rupiah_ipl); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_diskon); ?></b></td>
					<td class="text-right"><b><?php echo to_money($grp_jumlah_tagihan); ?></b></td>
				</tr>
				<?php
			}
			?>
			
			<tfoot>
			<tr>
				<td colspan="8">GRAND TOTAL .........</td>
				<td><?php echo to_money($sum_jumlah_ipl); ?></td>
				<td><?php echo to_money($sum_pemakaian); ?></td>
				<td><?php echo to_money($sum_jumlah_air); ?></td>
				<td><?php echo to_money($sum_abonemen); ?></td>
				<td><?php echo to_money($sum_denda); ?></td>
				<td><?php echo to_money($sum_administrasi); ?></td>
				<td><?php echo to_money($sum_diskon_rupiah_air); ?></td>
				<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
				<td><?php echo to_money($sum_jumlah_diskon); ?></td>
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
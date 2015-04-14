<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page		= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;

$jenis_laporan	= (isset($_REQUEST['jenis_laporan'])) ? clean($_REQUEST['jenis_laporan']) : '';
$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$jenis_bayar	= (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
$bayar_melalui	= (isset($_REQUEST['bayar_melalui'])) ? clean($_REQUEST['bayar_melalui']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';

$field_tgl_trx = " b.TGL_BAYAR ";
$nama_tgl_trx = 'Bayar';
$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rincian Penerimaan';

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
if ($kasir != '')
{
	$query_search .= " AND b.KASIR = '$kasir' ";
	$desc_bottom[] = 'Kasir : ' . get_nama('user', $kasir);
}
if ($periode != '')
{
	$query_search .= " AND b.PERIODE = '$periode' ";
	$desc_top[] = 'Periode : ' . fm_periode($periode);
}
if ($jenis_bayar != '')
{
	$query_search .= " AND b.JENIS_BAYAR = '$jenis_bayar' ";
	$desc_bottom[] = 'Jenis Bayar : ' . jenis_bayar($jenis_bayar);
	if ($jenis_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($jenis_tgl_trx == 'TGL_TERIMA_BANK') { $nama_tgl_trx = 'Terima Bank'; }
		if ($bayar_melalui != '')
		{
			$query_search .= " AND b.BAYAR_MELALUI = '$bayar_melalui' ";
			$desc_bottom[] = get_nama('bank', $bayar_melalui);
		}
	}
}

if ($tgl_trx != '')
{
	if ($jenis_laporan == 'HARIAN') {
		$query_search .= " AND CONVERT(VARCHAR(10), $field_tgl_trx, 105) = '$tgl_trx' ";
		$desc_top[] = 'Tanggal ' . $nama_tgl_trx . ' : ' . fm_date($tgl_trx);
	} else {
		$query_search .= " AND RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx' ";
		$desc_top[] = 'Bulan ' . $nama_tgl_trx . ' : ' . fm_periode(to_periode($tgl_trx));
	}
}

$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_air_ipl AND 
	b.STATUS_BAYAR = '2' 
	$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="21" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="21" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="21" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="21" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="21" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="19" class="nb">
		' . implode(' | ', $desc_top) . '<br>' . implode(' | ', $desc_bottom) . '
	</td>
	<td colspan="2" align="right" class="nb text-right va-bottom">Halaman 
';

$set_th = '
	dari ' . $total_page . '</td>
</tr>

<tr>
	<th>NO.</th>
	<th>NO. TAGIHAN</th>
	<th>BLOK / NO.</th>
	<th>SEKTOR</th>
	<th>CLUSTER</th>
	<th>NAMA PELANGGAN</th>
	<th>NO. KWITANSI</th>
	<th>PERIODE</th>
	<th>JML.<br>PERIODE</th>
	<th>TGL.<br>BAYAR</th>
	<th>AIR</th>
	<th>IPL</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>PPN</th>
	<th>TOTAL<br>EXC. PPN</th>
	<th>TOTAL<br>BAYAR</th>
	<th>KASIR</th>
	<th>JENIS<br>BAYAR</th>
	<th>KET. BAYAR</th>
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
		b.NO_INVOICE,
		b.KODE_BLOK,
		s.NAMA_SEKTOR,
		c.NAMA_CLUSTER,
		p.NAMA_PELANGGAN,
		b.NO_KWITANSI,
		dbo.PTPS(b.PERIODE) AS PERIODE,
		b.JUMLAH_PERIODE,
		CONVERT(VARCHAR(10),b.TGL_BAYAR,105) AS TGL_BAYAR,
		
		b.JUMLAH_AIR,
		b.JUMLAH_IPL,
		b.DENDA,
		b.ADMINISTRASI,
		b.DISKON_RUPIAH_IPL,
		
		CASE WHEN b.NILAI_PPN = 0 
			THEN ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100))
			ELSE b.NILAI_PPN
		END AS NILAI_PPN,
		
		CASE WHEN b.NILAI_PPN = 0 
			THEN (b.JUMLAH_BAYAR - ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100)))
			ELSE (b.JUMLAH_BAYAR - b.NILAI_PPN)
		END AS EXC_PPN,
		
		b.JUMLAH_BAYAR,
		b.KASIR,
		b.JENIS_BAYAR,
		b.BAYAR_MELALUI,
		b.KETERANGAN_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		b.STATUS_BAYAR = '2' 
		$query_search
	ORDER BY $field_tgl_trx, b.KODE_BLOK ASC
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	
	$sum_jumlah_air			= 0;
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_administrasi		= 0;
	$sum_diskon_rupiah_ipl	= 0;
	$sum_nilai_ppn			= 0;
	$sum_exc_ppn			= 0;
	$sum_jumlah_bayar		= 0;
	
	$total_rows = $obj->RecordCount();
	
	while( ! $obj->EOF)
	{		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td class="nowrap"><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NO_KWITANSI']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['TGL_BAYAR']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['EXC_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
			<td><?php echo $obj->fields['KASIR']; ?></td>
			<td class="nowrap"><?php echo jenis_bayar($obj->fields['JENIS_BAYAR'], $obj->fields['BAYAR_MELALUI']); ?></td>
			<td><?php echo $obj->fields['KETERANGAN_BAYAR']; ?></td>
		</tr>
		<?php
		
		$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_administrasi		+= $obj->fields['ADMINISTRASI'];
		$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
		$sum_nilai_ppn			+= $obj->fields['NILAI_PPN'];
		$sum_exc_ppn			+= $obj->fields['EXC_PPN'];
		$sum_jumlah_bayar		+= $obj->fields['JUMLAH_BAYAR'];
		
		if ($total_rows == $i)
		{
			?>
			<tfoot>
			<tr>
				<td colspan="10">TOTAL .........</td>
				<td><?php echo to_money($sum_jumlah_air); ?></td>
				<td><?php echo to_money($sum_jumlah_ipl); ?></td>
				<td><?php echo to_money($sum_denda); ?></td>
				<td><?php echo to_money($sum_administrasi); ?></td>
				<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
				<td><?php echo to_money($sum_nilai_ppn); ?></td>
				<td><?php echo to_money($sum_exc_ppn); ?></td>
				<td><?php echo to_money($sum_jumlah_bayar); ?></td>
				<td colspan="3"></td>
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
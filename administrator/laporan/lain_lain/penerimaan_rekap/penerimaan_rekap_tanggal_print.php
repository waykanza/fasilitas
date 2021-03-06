<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL4');
$conn = conn();
die_conn($conn);

$query_search = '';

$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$cara_bayar	= (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
$bayar_via	= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$user_bayar			= (isset($_REQUEST['user_bayar'])) ? clean($_REQUEST['user_bayar']) : '';

$field_tgl_trx = " b.TGL_BAYAR_BANK ";
$nama_tgl_trx = 'Bayar';
$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Rekap Tanggal Penerimaan BIAYA LAIN-LAIN';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	$desc_top[] = 'Sektor : ' . get_nama('sektor', $kode_sektor);
}
if ($user_bayar != '')
{
	$query_search .= " AND b.USER_BAYAR = '$user_bayar' ";
	$desc_bottom[] = 'Kasir : ' . get_nama('user', $user_bayar);
}
if ($cara_bayar != '')
{
	$query_search .= " AND b.CARA_BAYAR = $cara_bayar ";
	$desc_bottom[] = 'Cara Bayar : ' . cara_bayar($cara_bayar);
	if ($cara_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($jenis_tgl_trx == 'TGL_BAYAR_SYS') { $nama_tgl_trx = 'Terima Bank'; }
		if ($bayar_via != '')
		{
			$query_search .= " AND b.BAYAR_VIA = '$bayar_via' ";
			$desc_bottom[] = get_nama('bank', $bayar_via);
		}
	}
}

$desc_top[] = 'Bulan ' . $nama_tgl_trx . ' : ' . fm_periode(to_periode($tgl_trx));

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="8" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="8" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="8" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="8" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="8" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="6" class="nb">
		' . implode(' | ', $desc_top) . '<br>' . implode(' | ', $desc_bottom) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman : 1 dari 1</td>
</tr>

<tr>
	<th width="150">TANGGAL</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>BAYAR</th>
</tr>
';
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

<?php
$max_tgl = (int) date('t', strtotime(to_periode($tgl_trx) . '01'));

$x = array();
for ($x = 1; $x <= $max_tgl; $x++)
{
	$xjumlah_ipl[$x] = 0;
	$xdenda[$x] = 0;
	$xadm[$x] = 0;
	$xdiskon_ipl[$x] = 0;
	$xjumlah_bayar[$x] = 0;
}

$query = "
SELECT 
	CONVERT(VARCHAR(2), $field_tgl_trx, 105) AS TANGGAL,
	
	SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(b.DENDA) AS DENDA,
	SUM(b.ADM) AS ADM,
	SUM(b.DISKON_IPL) AS DISKON_IPL,
	SUM(b.JUMLAH_BAYAR) AS JUMLAH_BAYAR
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 1 AND
	RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx'
	$query_search
GROUP BY CONVERT(VARCHAR(2), $field_tgl_trx, 105)
";

$obj = $conn->Execute($query);
$sum_jumlah_ipl = 0;
$sum_denda = 0;
$sum_adm = 0;
$sum_diskon_ipl = 0;
$sum_jumlah_bayar = 0;

while( ! $obj->EOF)
{
	$i = (int) $obj->fields['TANGGAL'];
	
	$xjumlah_ipl[$i]		= $obj->fields['JUMLAH_IPL'];
	$xdenda[$i]				= $obj->fields['DENDA'];
	$xadm[$i]		= $obj->fields['ADM'];
	$xdiskon_ipl[$i]	= $obj->fields['DISKON_IPL'];
	$xjumlah_bayar[$i]		= $obj->fields['JUMLAH_BAYAR'];
	
	$sum_jumlah_ipl			+= $xjumlah_ipl[$i];
	$sum_denda				+= $xdenda[$i];
	$sum_adm		+= $xadm[$i];
	$sum_diskon_ipl	+= $xdiskon_ipl[$i];
	$sum_jumlah_bayar		+= $xjumlah_bayar[$i];
	
	$obj->movenext();
}

$fm_periode = fm_periode(to_periode($tgl_trx), '%b %Y');

echo '<table class="data">' . $set_jrp;

foreach ($xjumlah_bayar AS $k => $v)
{
	?>
	<tr> 
		<td class="text-center"><?php echo $k.' '.$fm_periode; ?></td>
		<td class="text-right"><?php echo to_money($xjumlah_ipl[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xadm[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdiskon_ipl[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdenda[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xjumlah_bayar[$k]); ?></td>
	</tr>
	<?php
}
?>
<tfoot>
<tr>
	<td>GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_jumlah_bayar); ?></td>
</tr>
</tfoot>
</table>

</body>
</html>

<?php
close($conn);
exit;
?>
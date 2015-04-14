<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LB2');
$conn = conn();
die_conn($conn);

$query_search = '';

$tgl_bd		= (isset($_REQUEST['tgl_bd'])) ? clean($_REQUEST['tgl_bd']) : '';
$bank_bd	= (isset($_REQUEST['bank_bd'])) ? clean($_REQUEST['bank_bd']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan BD';

if ($tgl_bd != '') {
	$desc_top[] = 'Bulan : ' . fm_periode(to_periode($tgl_bd));
}
if ($bank_bd != '') {
	$query_search .= " AND BANK_BD = '$bank_bd' ";
	$desc_top[] = get_nama('bank', $bank_bd);
}

$list_bank_bd = array();
$obj = $conn->Execute("
SELECT DISTINCT(BANK_BD) AS BANK_BD
FROM KWT_POST_BD 
WHERE 
	RIGHT(CONVERT(VARCHAR(10), TGL_BD, 105), 7) = '$tgl_bd' 
	$query_search
ORDER BY BANK_BD");
while( ! $obj->EOF)
{
	$list_bank_bd[] = $obj->fields['BANK_BD'];
	$obj->movenext(); 
}

$list_tgl = array();
$obj = $conn->Execute("
SELECT 
	BANK_BD, 
	NO_BD, 
	NO_BDT, 
	JUMLAH_BD, 
	JUMLAH_BDT, 
	LEFT(CONVERT(VARCHAR(10), TGL_BD, 105), 2) AS TGL_BD
FROM KWT_POST_BD 
WHERE 
	RIGHT(CONVERT(VARCHAR(10), TGL_BD, 105), 7) = '$tgl_bd' 
	$query_search
ORDER BY BANK_BD");
while( ! $obj->EOF)
{
	$tgl = $obj->fields['TGL_BD'];
	$bank = $obj->fields['BANK_BD'];
	
	$list_tgl[$tgl][$bank]['NO_BD'] = $obj->fields['NO_BD'];
	$list_tgl[$tgl][$bank]['JUMLAH_BD'] = $obj->fields['JUMLAH_BD'];
	$list_tgl[$tgl][$bank]['NO_BDT'] = $obj->fields['NO_BDT'];
	$list_tgl[$tgl][$bank]['JUMLAH_BDT'] = $obj->fields['JUMLAH_BDT'];
	
	$obj->movenext(); 
}

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="5" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="5" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="5" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="5" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="5" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="3" class="nb">
		' . implode(' | ', $desc_top) . '<br>' . implode(' | ', $desc_bottom) . '
	</td>
	<td colspan="2" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>
';

$set_jrp = $set_jrp . '<tr>';
$set_jrp = $set_jrp . '<th rowspan="2" width="150">TANGGAL</th>';

foreach($list_bank_bd as $q) { 
	$sum_bd[$q] = 0;
	$sum_bdt[$q] = 0;
	$set_jrp = $set_jrp . '<th colspan="4">'.$q.'</th>';
} 
$set_jrp = $set_jrp . '<th rowspan="2">TOTAL</th></tr><tr>';
	foreach($list_bank_bd as $q) { 
	$set_jrp = $set_jrp . '<th>BD</th><th>JUMLAH</th><th>BDT</th><th>JUMLAH</th>';
} 
$set_jrp = $set_jrp . '</tr>';

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
<body onload="window.print()">

<table class="data">

<?php
echo $set_jrp;

$sum_day = array();
$max_tgl = (int) date('t', strtotime(to_periode($tgl_bd) . '01'));
for ($x = 1; $x <= $max_tgl; $x++) 
{
	$sum_day[$x] = 0;
	
	foreach($list_tgl as $a => $b) 
	{ 
		if ($a == $x) 
		{
			echo '<tr>';
			echo "<td class='text-center'>$x</td>";
			foreach ($list_tgl[$a] as $c => $d) 
			{ 
				?>
				<td><?php echo $d['NO_BD']; ?></td>
				<td class="text-right"><?php echo to_money($d['JUMLAH_BD']); ?></td>
				<td><?php echo $d['NO_BDT']; ?></td>
				<td class="text-right"><?php echo to_money($d['JUMLAH_BDT']); ?></td>
				<?php
				
				$sum_bd[$c] += $d['JUMLAH_BD'];
				$sum_bdt[$c] += $d['JUMLAH_BDT'];
				
				$sum_day[$x] += $d['JUMLAH_BD'] + $d['JUMLAH_BDT'];
			} 
			echo "<td class='text-right'>" . to_money($sum_day[$x]) . "</td>";
			echo '</tr>';
		}
	} 
}
?>
<tfoot>
<tr>
	<td>TOTAL .........</td>
	<?php 
	$sum_all_day = 0;
	foreach($list_bank_bd as $q) 
	{ 
		?>
		<td></td>
		<td class="text-right"><?php echo to_money($sum_bd[$q]); ?></td>
		<td></td>
		<td class="text-right"><?php echo to_money($sum_bdt[$q]); ?></td>
		<?php
		
		$sum_all_day += $sum_bd[$q] + $sum_bdt[$q];
	} 
	
	echo "<td>" . to_money($sum_all_day) . "</td>";
	?>
</tr>
</tfoot>

</table>
</body>
</html>
<?php
close($conn);
exit;
?>
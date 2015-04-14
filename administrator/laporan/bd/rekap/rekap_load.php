<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LB2');
$conn = conn();
die_conn($conn);

$query_search = '';

$tgl_bd		= (isset($_REQUEST['tgl_bd'])) ? clean($_REQUEST['tgl_bd']) : '';
$bank_bd	= (isset($_REQUEST['bank_bd'])) ? clean($_REQUEST['bank_bd']) : '';

if ($bank_bd != '') {
	$query_search .= " AND BANK_BD = '$bank_bd' ";
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

?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<input type="button" id="excel" value=" Excel (Alt+X) ">
		<input type="button" id="print" value=" Print (Alt+P) ">
	</td>
</tr>
</table>

<table class="t-data t-nowrap wm100">
<tr>
	<th rowspan="2" width="150">TANGGAL</th>
	<?php 
	foreach($list_bank_bd as $q) 
	{ 
		$sum_bd[$q] = 0;
		$sum_bdt[$q] = 0;
		?>
		<th colspan="4"><?php echo $q; ?></th>
		<?php
	} 
	?>
	<th rowspan="2">TOTAL</th>
</tr>
<tr>
	<?php 
	foreach($list_bank_bd as $q) 
	{ 
		?>
		<th>BD</th>
		<th>JUMLAH</th>
		<th>BDT</th>
		<th>JUMLAH</th>
		<?php
	} 
	?>
</tr>
<?php

$sum_day = array();
$max_tgl = (int) date('t', strtotime(to_periode($tgl_bd) . '01'));
for ($x = 1; $x <= $max_tgl; $x++) 
{
	$sum_day[$x] = 0;
	
	foreach ($list_tgl as $a => $b) 
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

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$kode_tipe = (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';

# Pagination
$query = "
SELECT 
	COUNT(t.KEY_AIR) AS TOTAL
FROM 
	KWT_TARIF_AIR t
	JOIN KWT_SK_AIR s ON t.KODE_SK = s.KODE_SK AND s.STATUS_SK = '1'
WHERE
	t.KODE_TIPE = '$kode_tipe'
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table id="pagging-1" class="t-control t-popup">
<tr>
	<td class="text-right">
		<input type="button" id="prev_page" value=" < (Alt+Left) ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" (Alt+Right) > ">
	</td>
</tr>
</table>

<table class="t-data">
<tr>
	<th rowspan="2">KODE</th>
	<th colspan="4">BLOK</th>
	<th colspan="4">TARIF</th>
	<th rowspan="2">ABONEMEN</th>
	<th colspan="2">DENDA (%)</th>
	<th rowspan="2">KETERANGAN</th>
</tr>
<tr>
	<th>1</th>
	<th>2</th>
	<th>3</th>
	<th>4</th>
	<th>1</th>
	<th>2</th>
	<th>3</th>
	<th>4</th>
	<th>STANDAR</th>
	<th>BISNIS</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT *
	FROM 
		KWT_TARIF_AIR t
		JOIN KWT_SK_AIR s ON t.KODE_SK = s.KODE_SK AND s.STATUS_SK = '1'
	WHERE
		t.KODE_TIPE = '$kode_tipe'
	ORDER BY t.KEY_AIR DESC
	";
	
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KEY_AIR'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="nowrap"><?php echo $id; ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['BLOK1']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['BLOK2']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['BLOK3']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['BLOK4']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF1'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF2'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF3'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF4'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA_STANDAR_AIR'], 2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA_BISNIS_AIR'], 2); ?></td>
			<td><?php echo $obj->fields['KETERANGAN']; ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control t-popup"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	
	$('#total-data').html('<?php echo $total_data; ?>');
	$('#per_page').val('<?php echo $per_page; ?>');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
});
</script>
<?php
close($conn);
exit;
?>
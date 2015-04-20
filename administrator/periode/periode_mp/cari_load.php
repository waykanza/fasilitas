<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1		= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1	= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$search2	= (isset($_REQUEST['search2'])) ? clean($_REQUEST['search2']): '';

if ($search1 != '' || $search2 != '')
{
	$query_search = ' WHERE ';

	if ($search1 != '')
	{
		$query_search .= " $field1 LIKE '%$search1%' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(c.NO_PELANGGAN) AS TOTAL
FROM 
	FSL_PELANGGAN c
$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table class="t-popup">
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
	<th>NO. VA.</th>
	<th>NAMA</th>
	<th>NO. TELEPON</th>
	<th>ALAMAT</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		c.NO_PELANGGAN, 
		c.NAMA_PELANGGAN,
		c.KODE_BLOK,
		ISNULL(c.NO_HP, c.NO_TELEPON) AS TELEPON,
		c.ALAMAT,
		c.KETERANGAN
	FROM 
		FSL_PELANGGAN c
	$query_search
	ORDER BY c.NO_PELANGGAN ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	while( ! $obj->EOF)
	{
		$id = $obj->fields['NO_PELANGGAN'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['TELEPON']; ?></td>
			<td><?php echo $obj->fields['ALAMAT']; ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table class="t-popup">
<tr>
	<td class="td-action">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

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
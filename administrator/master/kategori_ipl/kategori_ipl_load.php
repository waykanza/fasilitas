<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1		= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1	= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$status_blok = (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';
$golongan	= (isset($_REQUEST['golongan'])) ? clean($_REQUEST['golongan']) : '';

if ($search1 != '' || $status_blok != '' || $golongan != '')
{
	$query_search .= " WHERE ";
	$and = '';
	if ($search1 != '')
	{
		$query_search .= " $field1 LIKE '%$search1%' ";
		$and = ' AND ';
	}
	if ($status_blok != '')
	{
		$query_search .= " $and STATUS_BLOK = '$status_blok' ";
		$and = ' AND ';
	}
	if ($golongan != '')
	{
		$query_search .= " $and GOLONGAN = '$golongan' ";
		$and = ' AND ';
	}
}
	
# Pagination
$query = "
SELECT 
	COUNT(KODE_TIPE) AS TOTAL
FROM 
	KWT_TIPE_IPL
$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table id="pagging-1" class="t-control w70">
<tr>
	<td>
		<input type="button" id="tambah" value=" Tambah (Alt+N) ">
		<input type="button" id="hapus" value=" Hapus (Alt+D) ">
	</td>
	
	<td class="text-right">
		<input type="button" id="prev_page" value=" < (Alt+Left) ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" (Alt+Right) > ">
	</td>
</tr>
</table>

<table class="t-data w70">
<tr>
	<th><input type="checkbox" id="cb_all"></th>
	<th>KODE</th>
	<th>STATUS BLOK</th>
	<th>KATEGORI</th>
	<th>GOLONGAN</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT *
	FROM KWT_TIPE_IPL
	$query_search
	ORDER BY KODE_TIPE ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_TIPE'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo $id; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td class="text-center"><?php echo golongan($obj->fields['GOLONGAN']); ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control w70"></table>

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
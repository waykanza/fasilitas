<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$opr1 = (isset($_REQUEST['opr1'])) ? clean_op($_REQUEST['opr1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$golongan	= (isset($_REQUEST['golongan'])) ? clean($_REQUEST['golongan']) : '';

if ($search1 != '' || $golongan != '')
{
	$query_search = ' WHERE ';
	$and = '';
	
	if ($search1 != '')
	{
		if ($field1 == 'KODE_TIPE' || $field1 == 'NAMA_TIPE') {
			$query_search .= " $field1 LIKE '%$search1%' ";
		} else {
			$query_search .= " $field1 $opr1 ".to_decimal($search1);
		}
		
		$and = ' AND ';
	}
	
	if ($golongan != '')
	{
		$query_search .= " $and GOLONGAN = '$golongan' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(KODE_TIPE) AS TOTAL
FROM 
	KWT_TIPE_AIR
$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table id="pagging-1" class="t-control">
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

<table class="t-data">
<tr>
	<th rowspan="2" class="w5"><input type="checkbox" id="cb_all"></th>
	<th rowspan="2" class="w5">KODE</th>
	<th rowspan="2" class="w25">KATEGORI</th>
	<th rowspan="2" class="w10">GOLONGAN</th>
	<th colspan="4" class="w50">PERSENTASE (UNTUK LAPORAN PEMAKAIAN AIR)</th>
</tr>
<tr>
	<th class="w10">BLOK 1</th>
	<th class="w10">BLOK 2</th>
	<th class="w10">BLOK 3</th>
	<th class="w10">BLOK 4</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		KODE_TIPE,
		NAMA_TIPE, 
		GOLONGAN,
		PERSEN_ASUMSI_1,
		PERSEN_ASUMSI_2,
		PERSEN_ASUMSI_3,
		PERSEN_ASUMSI_4
	FROM 
		KWT_TIPE_AIR
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
			<td class="text-center"><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td class="text-center"><?php echo golongan($obj->fields['GOLONGAN']); ?></td>
			<td class="text-right"><?php echo $obj->fields['PERSEN_ASUMSI_1']; ?></td>
			<td class="text-right"><?php echo $obj->fields['PERSEN_ASUMSI_2']; ?></td>
			<td class="text-right"><?php echo $obj->fields['PERSEN_ASUMSI_3']; ?></td>
			<td class="text-right"><?php echo $obj->fields['PERSEN_ASUMSI_4']; ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control"></table>

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
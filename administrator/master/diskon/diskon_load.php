<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$opr1 = (isset($_REQUEST['opr1'])) ? clean_op($_REQUEST['opr1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$field2		= (isset($_REQUEST['field2'])) ? clean($_REQUEST['field2']) : '';
$search2	= (isset($_REQUEST['search2'])) ? to_periode($_REQUEST['search2']) : '';

if ($search1 != '' || $search2 != '')
{
	$query_search = " WHERE ";
	$and = '';
	if ($search1 != '')
	{
		if ($field1 == 'KODE_BLOK' || $field1 == 'KETERANGAN') {
			$query_search .= " $field1 LIKE '%$search1%' ";
		} else {
			$query_search .= " $field1 $opr1 ".to_decimal($search1);
		}
		$and = 'AND';
	}
	if ($search2 != '')
	{
		$query_search .= " $and $field2 = '$search2' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(KODE_BLOK) AS TOTAL
FROM 
	KWT_DISKON_KHUSUS
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
	<th rowspan="2" class="w15">KODE BLOK</th>
	<th colspan="2" class="w20">PERIODE</th>
	<th colspan="2" class="w20">DISKON (Rp.)</th>
	<th colspan="2" class="w10">DISKON (%)</th>
	<th rowspan="2" class="w35">KETERANGAN</th>
</tr>
<tr>
	<th class="w10">AWAL</th>
	<th class="w10">AKHIR</th>
	<th class="w10">AIR</th>
	<th class="w10">IPL</th>
	<th class="w5">AIR</th>
	<th class="w5">IPL</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		ID_DISKON,
		KODE_BLOK,
		dbo.PTPS(PERIODE_AWAL) AS PERIODE_AWAL,
		dbo.PTPS(PERIODE_AKHIR) AS PERIODE_AKHIR,
		DISKON_AIR_NILAI,
		DISKON_IPL_NILAI,
		DISKON_AIR_PERSEN,
		DISKON_IPL_PERSEN,
		KETERANGAN
	FROM 
		KWT_DISKON_KHUSUS
	$query_search
	ORDER BY PERIODE_AWAL DESC
	
	";
	
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['ID_DISKON'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AWAL']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR_NILAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL_NILAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR_PERSEN'], 2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL_PERSEN'], 2); ?></td>
			<td><?php echo $obj->fields['KETERANGAN']; ?></td>
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
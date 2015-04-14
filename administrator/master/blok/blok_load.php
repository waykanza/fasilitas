<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$field1			= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$opr1			= (isset($_REQUEST['opr1'])) ? clean_op($_REQUEST['opr1']) : '';
$search1		= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$status_blok	= (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';

if ($kode_sektor != '' || $kode_cluster != '' || $search1 != '' || $status_blok != '')
{
	$query_search .= " WHERE ";
	$and = '';
	if ($kode_sektor != '')
	{
		$query_search .= " b.KODE_SEKTOR = '$kode_sektor' ";
		$and = ' AND ';
	}
	if ($kode_cluster != '')
	{
		$query_search .= " $and b.KODE_CLUSTER = '$kode_cluster' ";
		$and = ' AND ';
	}
	if ($search1 != '')
	{
		if ($field1 == 'KODE_BLOK') {
			$query_search .= " $and b.$field1 LIKE '%$search1%' ";
		} else {
			$query_search .= " $and b.$field1 $opr1 ".to_decimal($search1);
		}
	}
	if ($status_blok != '')
	{
		$query_search .= " $and b.STATUS_BLOK = '$status_blok' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(b.KODE_BLOK) AS TOTAL
FROM 
	KWT_BLOK b
	LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
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
	<th rowspan="2" class="w25">SEKTOR</th>
	<th rowspan="2" class="w25">CLUSTER</th>
	<th colspan="2" class="w20">LUAS (m&sup2;)</th>
	<th rowspan="2" class="w10">STATUS</th>
</tr>
<tr>
	<th>KAVLING</th>
	<th>BANGUNAN</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.KODE_BLOK, 
		s.NAMA_SEKTOR, 
		c.NAMA_CLUSTER,
		b.LUAS_KAVLING,
		b.LUAS_BANGUNAN,
		b.STATUS_BLOK
	FROM 
		KWT_BLOK b
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	$query_search
	ORDER BY s.NAMA_SEKTOR, c.NAMA_CLUSTER, b.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'], 2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'], 2); ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
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
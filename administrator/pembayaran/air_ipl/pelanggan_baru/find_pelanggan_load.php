<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';

if ($search1 != '')
{
	$query_search .= " AND p.$field1 LIKE '%$search1%' ";
}
	
# Pagination
$query = "
SELECT 
	COUNT(p.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PELANGGAN p
WHERE
	p.DISABLED IS NULL AND 
	p.STATUS_BLOK IN ('1', '2', '4', '5') AND 
	p.NO_PELANGGAN NOT IN (SELECT NO_PELANGGAN FROM KWT_PEMBAYARAN_AI WHERE $where_trx_air_ipl AND PERIODE = '$periode')
	$query_search
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

<table class="t-data wm100">
<tr>
	<th colspan="2">PELANGGAN</th>
	<th colspan="8">BLOK</th>
	<th rowspan="3">KETERANGAN</th>
</tr>
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NAMA</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">STATUS</th>
	<th colspan="2">LUAS (M&sup2;)</th>
	<th colspan="2">AIR</th>
	<th colspan="2">IPL</th>
</tr>
<tr>
	<th>KAVL.</th>
	<th>BANG.</th>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>AKTIF</th>
	<th>GOL.</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		p.NAMA_PELANGGAN,
		p.NO_PELANGGAN,
		p.KODE_BLOK,
		p.STATUS_BLOK,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.AKTIF_AIR,
		p.KEY_AIR,
		p.AKTIF_IPL,
		p.KEY_IPL,
		p.KETERANGAN
	FROM 
		KWT_PELANGGAN p
	WHERE
		DISABLED IS NULL AND 
		STATUS_BLOK IN ('1', '2', '4', '5') AND 
		NO_PELANGGAN NOT IN (SELECT NO_PELANGGAN FROM KWT_PEMBAYARAN_AI WHERE $where_trx_air_ipl AND PERIODE = '$periode')
		$query_search
	ORDER BY p.NO_PELANGGAN DESC
	";
	
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	while( ! $obj->EOF)
	{
		$id = $obj->fields['NO_PELANGGAN'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td><?php echo no_pelanggan($id); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_AIR']); ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_IPL']); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
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
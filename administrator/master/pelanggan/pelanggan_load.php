<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$kode_sektor = (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster = (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$status_blok = (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';

$aktif_air = (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl = (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

if ($search1 != '' || $kode_sektor != '' || $kode_cluster !='' || $status_blok != '' || $aktif_air != '' || $aktif_ipl != '')
{
	$query_search = ' WHERE ';
	$and = '';
	if ($search1 != '')
	{
		$query_search .= " $and p.$field1 LIKE '%$search1%' ";
		$and = 'AND';
	}
	if ($kode_sektor != '')
	{
		$query_search .= " $and p.KODE_SEKTOR = '$kode_sektor' ";
		$and = 'AND';
	}
	if ($kode_cluster != '')
	{
		$query_search .= " $and p.KODE_CLUSTER = '$kode_cluster' ";
		$and = 'AND';
	}
	if ($status_blok != '')
	{
		$query_search .= " $and p.STATUS_BLOK = '$status_blok' ";
		$and = 'AND';
	}
	if ($aktif_air != '')
	{
		$query_search .= " $and p.AKTIF_AIR = '1' ";
		$and = 'AND';
	}
	if ($aktif_ipl != '')
	{
		$query_search .= " $and p.AKTIF_IPL = '1' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PELANGGAN p
$query_search
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table class="t-control" id="pagging-1">
<tr>
	<td>
		<input type="button" id="join" value=" Penggabungan Blok ">
		<input type="button" id="split" value=" Pemisahan Blok ">
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

<table class="t-data wm100">
<tr>
	<th rowspan="3">NO.</th>
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
	$query_search
	ORDER BY NO_PELANGGAN DESC
	";
	
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = $obj->fields['NO_PELANGGAN'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
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
		$i++;
		$obj->movenext();
	}
}
?>
</table>

<table class="t-control" id="pagging-2"></table>

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
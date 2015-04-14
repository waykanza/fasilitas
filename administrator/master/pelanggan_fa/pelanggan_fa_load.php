<?php
require_once('../../../config/config.php');
die_login();
die_mod('M15');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$field1			= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1		= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$status_blok	= (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

$and = '';
if ($search1 != '')
{
	$query_search .= " p.$field1 LIKE '%$search1%' ";
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
	$query_search .= " $and p.STATUS_BLOK = $status_blok ";
	$and = 'AND';
}
if ($aktif_air != '') {
	$query_search .= " $and p.AKTIF_AIR = $aktif_air ";
	$and = 'AND';
}
if ($aktif_ipl != '') {
	$query_search .= " $and p.AKTIF_IPL = $aktif_ipl ";
	$and = 'AND';
}

if ($query_search != '') {
	$query_search = ' WHERE ' . $query_search;
}

# Pagination
$query = "
SELECT 
	(SELECT COUNT(NO_PELANGGAN) FROM KWT_PELANGGAN p $query_search ) AS TOTAL,
	(SELECT COUNT(NO_PELANGGAN) FROM KWT_PELANGGAN p) AS TOTAL_ALL,
	(SELECT COUNT(NO_PELANGGAN) FROM KWT_PELANGGAN p WHERE DISABLED = 0) AS TOTAL_AKTIF,
	(SELECT COUNT(NO_PELANGGAN) FROM KWT_PELANGGAN p WHERE DISABLED = 1) AS TOTAL_TIDAK_AKTIF
";
$td = $conn->Execute($query);

$total_data_all = $td->fields['TOTAL_ALL'];
$total_data_aktif = $td->fields['TOTAL_AKTIF'];
$total_data_tidak_aktif = $td->fields['TOTAL_TIDAK_AKTIF'];

$total_data = $td->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table class="t-control" id="pagging-1">
<tr>
	<td>
		<input type="button" id="tambah" value=" Tambah (Alt+N) ">
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

<table class="t-data t-nowrap wm100">
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
		p.KET,
		p.DISABLED
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
		<tr <?php echo ($obj->fields['DISABLED'] == '1') ? 'style="background:#7A7A7A;"' : ''; ?> class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo fm_nopel($id); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['AKTIF_AIR']); ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['AKTIF_IPL']); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td><?php echo $obj->fields['KET']; ?></td>
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
	
	$('#total-data_all').html('<?php echo $total_data_all; ?>');
	$('#total-data_aktif').html('<?php echo $total_data_aktif; ?>');
	$('#total-data_tidak_aktif').html('<?php echo $total_data_tidak_aktif; ?>');
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
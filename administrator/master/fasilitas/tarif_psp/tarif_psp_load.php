<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';

$kode_tipe = (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$kode_fungsi = (isset($_REQUEST['kode_fungsi'])) ? clean($_REQUEST['kode_fungsi']) : '';
$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$opr1 = (isset($_REQUEST['opr1'])) ? clean_op($_REQUEST['opr1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

if ($kode_tipe != '' || $kode_fungsi != '' || $search1 != '')
{
	if ($kode_tipe != '')
	{
		$query_search .= " AND l.KODE_TIPE = '$kode_tipe' ";
	}
	if ($kode_fungsi != '')
	{
		$query_search .= " AND l.KODE_FUNGSI = '$kode_fungsi' ";
	}
	if ($search1 != '')
	{
		if ($field1 == 'TARIF') {
			$query_search .= " AND l.$field1 $opr1 ".to_decimal($search1);
		} else {
			$query_search .= " AND l.$field1 LIKE '%$search1%' ";
		}
	}
}

$query = "
SELECT 
	KODE_SK,
	NO_SK, 
	CONVERT(VARCHAR(11),TGL_SK,106) AS TGL_SK,
	CONVERT(VARCHAR(11),TGL_BERLAKU,106) AS TGL_BERLAKU,
	PEMBUAT,
	KETERANGAN
FROM 
	KWT_SK_PSP
WHERE
	KODE_SK = '$kode_sk'
";
$obj = $conn->query($query);
	
?>
<table class="t-desc w50">
<tr>
	<td width="115">KODE</td><td width="1">:</td>
	<td><?php echo $obj->fields['KODE_SK']; ?></td>
</tr>
<tr>
	<td>NO. SK SEWA</td><td>:</td>
	<td><?php echo $obj->fields['NO_SK']; ?></td>
</tr>
<tr>
	<td>TANGGAL SK</td><td>:</td>
	<td><?php echo $obj->fields['TGL_SK']; ?></td>
</tr>
<tr>
	<td>TANGGAL BERLAKU</td><td>:</td>
	<td><?php echo $obj->fields['TGL_BERLAKU']; ?></td>
</tr>
<tr>
	<td>PEMBUAT</td><td>:</td>
	<td><?php echo $obj->fields['PEMBUAT']; ?></td>
</tr>
<tr>
	<td>KETERANGAN</td><td>:</td>
	<td><?php echo $obj->fields['KETERANGAN']; ?></td>
</tr>
</table>

<?php
/* Pagination */
$query = "
SELECT 
	COUNT(l.KEY_PSP) AS TOTAL
FROM 
	KWT_TARIF_PSP l
	LEFT JOIN KWT_FUNGSI_PSP f ON l.KODE_FUNGSI = f.KODE_FUNGSI
WHERE
	l.KODE_SK = '$kode_sk'
	$query_search
";
$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
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

<table class="t-data" id="t-detail">
<tr>
	<th class="w5"><input type="checkbox" id="cb_all"></th>
	<th class="w7">KEY#</th>
	<th class="w5">KATEGORI</th>
	<th class="w20">FUNGSI</th>
	<th class="w7">BIAYA / m&sup2;</th>
	<th class="w56">LOKASI</th>
</tr>

<?php
if ($kode_sk != '' AND $total_data > 0)
{
	$query = "
	SELECT l.*, f.NAMA_FUNGSI
	FROM 
		KWT_TARIF_PSP l
		LEFT JOIN KWT_FUNGSI_PSP f ON l.KODE_FUNGSI = f.KODE_FUNGSI
	WHERE
		l.KODE_SK = '$kode_sk'
		$query_search
	ORDER BY f.NAMA_FUNGSI, l.KODE_TIPE
	";
	
	$obj = $conn->selectlimit($query, $per_page, $page_start);
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KEY_PSP'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo $id; ?></td>
			<td class="text-center"><?php echo $obj->fields['KODE_TIPE']; ?></td>
			<td><?php echo $obj->fields['NAMA_FUNGSI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF']); ?></td>
			<td><?php echo $obj->fields['LOKASI']; ?></td>
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
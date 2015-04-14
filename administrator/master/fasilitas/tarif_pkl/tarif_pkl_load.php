<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$kode_lokasi = (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';

$field1 = (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$opr1 = (isset($_REQUEST['opr1'])) ? clean_op($_REQUEST['opr1']) : '';
$search1 = (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

if ($search1 != '')
{
	if ($field1 == 'KEY_PKL') {
		$query_search .= " AND l.$field1 LIKE '%$search1%' ";
	} else {
		$query_search .= " AND l.$field1 $opr1 ".to_decimal($search1);
	}
}

$query = "
SELECT 
	s.KODE_SK,
	s.NO_SK, 
	CONVERT(VARCHAR(11),s.TGL_SK,106) AS TGL_SK,
	CONVERT(VARCHAR(11),s.TGL_BERLAKU,106) AS TGL_BERLAKU,
	s.PEMBUAT,
	s.KETERANGAN,
	l.KODE_LOKASI,
	l.NAMA_LOKASI,
	l.DETAIL_LOKASI
FROM 
	KWT_LOKASI_PKL l
	LEFT JOIN KWT_SK_SEWA s ON l.KODE_SK = s.KODE_SK
WHERE
	l.KODE_LOKASI = '$kode_lokasi'
";
$obj = $conn->query($query);
	
?>
<table class="t-desc w50">
<tr>
	<td colspan="4" class="text-center"> --------------------------------------------- SK --------------------------------------------- </td>
</tr>
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
<tr>
	<td colspan="4" class="text-center"> ----------------------------------------- LOKASI ----------------------------------------- </td>
</tr>
<tr>
	<td>KODE</td><td>:</td>
	<td><?php echo $obj->fields['KODE_LOKASI']; ?></td>
</tr>
<tr>
	<td>NAMA</td><td>:</td>
	<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
</tr>
<tr>
	<td>DETAIL</td><td>:</td>
	<td><?php echo $obj->fields['DETAIL_LOKASI']; ?></td>
</tr>
</table>

<?php
/* Pagination */
$query = "
SELECT 
	COUNT(l.KEY_PKL) AS TOTAL
FROM 
	KWT_TARIF_PKL l
	LEFT JOIN KWT_TIPE_PKL t ON l.KODE_TIPE = t.KODE_TIPE
WHERE
	KODE_LOKASI = '$kode_lokasi'
	$query_search
";
$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
?>

<table id="pagging-1" class="t-control w55">
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

<table class="t-data w55" id="t-detail">
<tr>
	<th class="w5"><input type="checkbox" id="cb_all"></th>
	<th class="w15">KEY#</th>
	<th class="w45">KATEGORI</th>
	<th class="w20">UANG PANGKAL</th>
	<th class="w15">TARIF</th>
</tr>

<?php
if ($kode_lokasi != '' AND $total_data > 0)
{
	$query = "
	SELECT l.*, t.NAMA_TIPE
	FROM 
		KWT_TARIF_PKL l
		LEFT JOIN KWT_TIPE_PKL t ON l.KODE_TIPE = t.KODE_TIPE
	WHERE
		KODE_LOKASI = '$kode_lokasi'
		$query_search
	ORDER BY NAMA_TIPE ASC
	";
	
	$obj = $conn->selectlimit($query, $per_page, $page_start);
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KEY_PKL'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td class="text-center"><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['UANG_PANGKAL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF']) . satuan($obj->fields['SATUAN']); ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control w55"></table>

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
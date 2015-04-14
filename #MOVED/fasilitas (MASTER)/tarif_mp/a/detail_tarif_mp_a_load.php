<?php
require_once('../../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$key_mp = (isset($_REQUEST['key_mp'])) ? clean($_REQUEST['key_mp']) : '';

$query = "
SELECT 
	s.KODE_SK,
	s.NO_SK, 
	CONVERT(VARCHAR(11),s.TGL_SK,106) AS TGL_SK,
	CONVERT(VARCHAR(11),s.TGL_BERLAKU,106) AS TGL_BERLAKU,
	s.PEMBUAT,
	s.KETERANGAN,
	m.KEY_MP,
	t.NAMA_TIPE,
	m.UKURAN_1,
	m.UKURAN_2
FROM 
	KWT_TARIF_MP m
	LEFT JOIN KWT_SK_SEWA s ON m.KODE_SK = s.KODE_SK
	LEFT JOIN KWT_TIPE_MP t ON m.KODE_TIPE = t.KODE_TIPE
WHERE
	m.KEY_MP = '$key_mp'
";
$obj = $conn->query($query);
	
?>
<table class="t-desc w50">
<tr>
	<td colspan="3" class="text-center">--------------------------------------------- SK ---------------------------------------------</td>
</tr>
<tr>
	<td width="115">KODE</td><td width="1">:</td>
	<td><?php echo $obj->fields['KODE_SK']; ?></td>
</tr>
<tr>
	<td>NO SK SEWA</td><td>:</td>
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
	<td colspan="3" class="text-center">---------------------------------- KATEGORI TARIF ----------------------------------</td>
</tr>
<tr>
	<td>KODE</td><td>:</td>
	<td><?php echo $obj->fields['KEY_MP']; ?></td>
</tr>
<tr>
	<td>KATEGORI</td><td>:</td>
	<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
</tr>
<tr>
	<td>UKURAN</td><td>:</td>
	<td><?php echo to_money($obj->fields['UKURAN_1'],2).' - '.to_money($obj->fields['UKURAN_2'],2); ?> m&sup2;</td>
</tr>
</table>

<?php
/* Pagination */
$query = "
SELECT 
	COUNT(KEY_MPD) AS TOTAL
FROM 
	KWT_TARIF_MPD t
	LEFT JOIN KWT_LOKASI_MP l ON t.KODE_LOKASI = l.KODE_LOKASI
WHERE
	t.KEY_MP = '$key_mp'
";
$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
?>

<table id="pagging-1" class="t-control w60">
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

<table class="t-data w60" id="t-detail">
<tr>
	<th class="w5"><input type="checkbox" id="cb_all"></th>
	<th class="w15">KEY#</th>
	<th class="w50">LOKASI</th>
	<th class="w30">TARIF</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		t.KEY_MPD,
		l.NAMA_LOKASI,
		t.TARIF
	FROM 
		KWT_TARIF_MPD t
		LEFT JOIN KWT_LOKASI_MP l ON t.KODE_LOKASI = l.KODE_LOKASI
	WHERE
		t.KEY_MP = '$key_mp'
	";
	
	$obj = $conn->selectlimit($query, $per_page, $page_start);
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KEY_MPD'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF']); ?></td>
		</tr>
		<?php
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control w60"></table>

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
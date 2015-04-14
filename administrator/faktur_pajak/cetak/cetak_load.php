<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$tipe_cari		= (isset($_REQUEST['tipe_cari'])) ? clean($_REQUEST['tipe_cari']) : '';
$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';


if ($tipe_cari == '2')
{
	if ($kode_cluster != '')
	{
		$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
	}
	else
	{
		$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	}
}
else
{
	$query_search .= " AND b.KODE_BLOK LIKE '%$kode_blok%' ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.ID_PEMBAYARAN) AS TOTAL 
FROM 
	KWT_PEMBAYARAN_AI b 
WHERE 
	b.PERIODE = '$periode' AND 
	b.NO_FAKTUR_PAJAK IS NOT NULL 
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
		<input type="button" id="print" value=" Cetak (Alt+P) ">
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
	<th><input type="checkbox" id="cb_all"></th>
	<th>NO.</th>
	<th>KODE BLOK</th>
	<th>NO. PELANGGAN</th>
	<th>NAMA PELANGGAN</th>
	<th>NO. FAKTUR PAJAK</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.ID_PEMBAYARAN, 
		b.KODE_BLOK, 
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.NO_FAKTUR_PAJAK
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		b.PERIODE = '$periode' AND
		b.NO_FAKTUR_PAJAK IS NOT NULL
		$query_search
	ORDER BY b.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr id="<?php echo $id; ?>"> 
			<td width="30" class="text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NO_FAKTUR_PAJAK']; ?></td>
		</tr>
		<?php
		$i++;
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
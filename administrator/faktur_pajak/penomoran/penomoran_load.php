<?php
require_once('../../../config/config.php');
$conn = conn();

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$periode	= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$pnofp		= '';

# CHECK DATA
$query = "
SELECT 
	COUNT(b.ID_PEMBAYARAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	b.PERIODE = '$periode' AND 
	b.STATUS_BAYAR = '2' AND
	b.TGL_POST_FP IS NULL
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table id="pagging-1" class="t-control">
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

<table class="t-data">
<tr>
	<th class="w5">NO.</th>
	<th class="w15">KODE BLOK</th>
	<th class="w30">NAMA PELANGGAN</th>
	<th class="w20">NO. FAKTUR</th>
	<th class="w15">TANGGAL FAKTUR</th>
	<th class="w15">PPN</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.KODE_BLOK, 
		p.NAMA_PELANGGAN,
		b.NO_FAKTUR_PAJAK,
		CONVERT(VARCHAR(10), b.TGL_FAKTUR_PAJAK, 105) AS TGL_FAKTUR_PAJAK,
		b.NILAI_PPN
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		b.PERIODE = '$periode' AND 
		b.STATUS_BAYAR = '2' AND
		b.TGL_POST_FP IS NULL
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1;
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NO_FAKTUR_PAJAK']; ?></td>
			<td class="text-center"><?php echo $obj->fields['TGL_FAKTUR_PAJAK']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
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
	
	<?php
	if ($total_data < 1) {
		echo "alert('Data pembayaran tidak tidemukan.');";
		echo "$('#proses').hide();";
	} else {
		echo "$('#proses').show();";
	}
	?>
});
</script>

<?php
close($conn);
exit;
?>
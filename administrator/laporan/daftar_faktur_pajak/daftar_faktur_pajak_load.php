<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$tgl_posting	= (isset($_REQUEST['tgl_posting'])) ? clean($_REQUEST['tgl_posting']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	$where_trx_air_ipl AND 
	CONVERT(VARCHAR(10),b.TGL_POST_FP,105) = '$tgl_posting'
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
		<input type="button" id="excel" value=" Excel (Alt+X) ">
		<input type="button" id="print" value=" Print (Alt+P) ">
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

<table class="t-data t-nowrap">
<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>NPWP</th>
	<th>NO. SERI FAKTUR</th>
	<th>TANGGAL</th>
	<th>NILAI DPP</th>
	<th>NILAI PPN</th>
	<th>BLOK NO</th>
	<th>NO. KWITANSI</th>
	<th>NILAI KWITANSI</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		p.NAMA_PELANGGAN,
		p.NPWP,
		b.NO_FAKTUR_PAJAK,
		CONVERT(VARCHAR(10),b.TGL_FAKTUR_PAJAK,105) AS TGL_FAKTUR_PAJAK,
		(
			(b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (100 / (100 + b.PERSEN_PPN))
		) AS NILAI_DPP,
		b.NILAI_PPN,
		b.KODE_BLOK,
		b.NO_KWITANSI,
		b.JUMLAH_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_air_ipl AND 
		CONVERT(VARCHAR(10),b.TGL_POST_FP,105) = '$tgl_posting'
		$query_search
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	$sum_nilai_dpp = 0;
	$sum_nilai_ppn = 0;
	$sum_nilai_kwitansi = 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NPWP']; ?></td>
			<td><?php echo $obj->fields['NO_FAKTUR_PAJAK']; ?></td>
			<td><?php echo $obj->fields['TGL_FAKTUR_PAJAK']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_DPP']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-right"><?php echo $obj->fields['NO_KWITANSI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
		</tr>
		<?php
		
		$sum_nilai_dpp		+= $obj->fields['NILAI_DPP'];
		$sum_nilai_ppn		+= $obj->fields['NILAI_PPN'];
		$sum_nilai_kwitansi	+= $obj->fields['JUMLAH_BAYAR'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="5">GRAND TOTAL .........</td>
		<td><?php echo to_money($sum_nilai_dpp); ?></td>
		<td><?php echo to_money($sum_nilai_ppn); ?></td>
		<td colspan="2"></td>
		<td><?php echo to_money($sum_nilai_kwitansi); ?></td>
	</tr>
	</tfoot>
	<?php
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
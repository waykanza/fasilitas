<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($kode_cluster != '')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
}
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
	$where_trx_deposit AND 
	b.PERIODE = '$periode'
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

<table class="t-data">
<tr>
	<th>NO.</th>
	<th>NO. TAGIHAN</th>
	<th>BLOK / NO.</th>
	<th>NO. PELANGGAN</th>
	<th>NAMA PELANGGAN</th>
	<th>LUAS (M<sup>2</sup>)</th>
	<th>TARIF</th>
	<th>PERIODE</th>
	<th>DEPOSIT</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>TOTAL<br>TAGIHAN</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.NO_INVOICE,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		b.JUMLAH_PERIODE,
		b.JUMLAH_IPL,
		b.DENDA,
		b.ADMINISTRASI,
		b.DISKON_RUPIAH_IPL,
		(b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_IPL) AS TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_deposit AND 
		b.PERIODE = '$periode'
		$query_search
	ORDER BY b.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_administrasi		= 0;
	$sum_diskon_rupiah_ipl	= 0;
	$sum_tagihan			= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td class="nowrap"><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'], 2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF_IPL']); ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TAGIHAN']); ?></td>
		</tr>
		<?php
		
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_administrasi		+= $obj->fields['ADMINISTRASI'];
		$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
		$sum_tagihan			+= $obj->fields['TAGIHAN'];
		
		$i++;
		$obj->movenext();
	}
	
	?>
	<tfoot>
	<tr>
		<td colspan="8">TOTAL .........</td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_administrasi); ?></td>
		<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
		<td><?php echo to_money($sum_tagihan); ?></td>
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
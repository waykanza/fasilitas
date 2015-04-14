<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$trx				= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$field1				= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1			= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$kode_sektor		= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster		= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';

if ($search1 != '' || $kode_sektor != '' || $kode_cluster != '')
{
	if ($search1 != '')
	{
		$query_search .= " AND $field1 LIKE '%$search1%' ";
	}
	if ($kode_sektor != '')
	{
		$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	}
	if ($kode_cluster != '')
	{
		$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
	}
}

# Pagination
$query = "
SELECT 
	COUNT(b.ID_PEMBAYARAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	b.TRX = '$trx' AND 
	b.STATUS_BAYAR IS NULL 
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

<table class="t-data">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2" width="30"><input type="checkbox" id="cb_all"></th>
	<th rowspan="2">ALASAN</th>
	<th rowspan="2">NO. TAGIHAN</th>
	<th colspan="4">PELANGGAN</th>
	<th colspan="2">GOL.</th>
	<th rowspan="2">TOTAL<br>TAGIHAN</th>
</tr>
<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>STATUS</th>
	<th>KODE BLOK</th>
	<th>AIR</th>
	<th>IPL</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.ID_PEMBAYARAN, 
		b.NO_INVOICE, 
		b.KODE_BLOK, 
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.KEY_AIR,
		b.KEY_IPL,
		b.STATUS_BAYAR,
		b.STATUS_CETAK_KWT,
		b.JENIS_BAYAR,
		(
			(ISNULL(JUMLAH_AIR,0) + ISNULL(ABONEMEN,0) + ISNULL(JUMLAH_IPL,0) + ISNULL(DENDA,0)) - 
			(ISNULL(DISKON_RUPIAH_AIR,0) + ISNULL(DISKON_RUPIAH_IPL,0))
		) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		b.TRX = '$trx' AND 
		b.STATUS_BAYAR IS NULL  
		$query_search
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><input type="checkbox" name="cb_data[<?php echo $i; ?>]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td class="text-center"><textarea name="cb_ket[<?php echo $i; ?>]" class="w90 x" rows="1"></textarea></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
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
	$('#per_page').inputmask('integer', { repeat: '3' });
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
	
});
</script>

<?php
close($conn);
exit;
?>
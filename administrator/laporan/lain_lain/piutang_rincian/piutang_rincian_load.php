<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL5');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$banyak_tangihan	= (isset($_REQUEST['banyak_tangihan'])) ? to_number($_REQUEST['banyak_tangihan']) : '1';

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
	$query_search .= " AND TRX = $trx ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) OVER () AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 0
	$query_search
GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
HAVING COUNT(b.NO_PELANGGAN) >= $banyak_tangihan
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

<table class="t-data t-nowrap wm100">
<tr>
	<th>NO.</th>
	<th>BLOK / NO.</th>
	<th>NAMA PELANGGAN</th>
	<th>BANYAK<br>TAGIHAN</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>TAGIHAN</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.KODE_BLOK,
		(SELECT NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE NO_PELANGGAN = b.NO_PELANGGAN) AS NAMA_PELANGGAN,
		b.NO_PELANGGAN,
		COUNT(b.NO_PELANGGAN) AS BANYAK_TAGIHAN,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADM) AS ADM,
		SUM(b.DISKON_IPL) AS DISKON_IPL,
		
		SUM(b.JUMLAH_IPL + b.DENDA + b.ADM - b.DISKON_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_lain_lain AND 
		b.STATUS_BAYAR = 0
		$query_search
	GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
	HAVING COUNT(b.NO_PELANGGAN) >= $banyak_tangihan
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
	$sum_banyak_tangihan	= 0;
	$sum_jumlah_ipl		= 0;
	$sum_denda			= 0;
	$sum_adm	= 0;
	$sum_diskon_ipl = 0;
	$sum_jumlah_tagihan = 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['BANYAK_TAGIHAN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		<?php
		
		$sum_banyak_tangihan	+= $obj->fields['BANYAK_TAGIHAN'];
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_adm			+= $obj->fields['ADM'];
		$sum_diskon_ipl		+= $obj->fields['DISKON_IPL'];
		$sum_jumlah_tagihan += $obj->fields['JUMLAH_TAGIHAN'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="3">GRAND TOTAL .........</td>
		<td><?php echo to_money($sum_banyak_tangihan); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_diskon_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_jumlah_tagihan); ?></td>
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
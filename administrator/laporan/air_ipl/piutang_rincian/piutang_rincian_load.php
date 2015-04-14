<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
$jumlah_piutang	= (isset($_REQUEST['jumlah_piutang'])) ? to_number($_REQUEST['jumlah_piutang']) : '1';

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
if ($aktif_air != '')
{
	$query_search .= " AND b.AKTIF_AIR = '1' ";
}
if ($aktif_ipl != '')
{
	$query_search .= " AND b.AKTIF_IPL = '1' ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) OVER () AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_air_ipl AND 
	b.STATUS_BAYAR IS NULL
	$query_search
GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
HAVING COUNT(b.NO_PELANGGAN) >= $jumlah_piutang
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
	<th>BLOK / NO.</th>
	<th>NAMA<br>PELANGGAN</th>
	<th>BANYAK<br>TAGIHAN</th>
	<th>AIR</th>
	<th>ABONEMEN</th>
	<th>IPL</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON AIR</th>
	<th>DISKON IPL</th>
	<th>PPN</th>
	<th>TOTAL<br>EXC. PPN</th>
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
		COUNT(b.NO_PELANGGAN) AS JUMLAH_PIUTANG,
		SUM(b.JUMLAH_AIR) AS JUMLAH_AIR,
		SUM(b.ABONEMEN) AS ABONEMEN,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADMINISTRASI) AS ADMINISTRASI,
		SUM(b.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
		SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		
		SUM((b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) * (b.PERSEN_PPN / 100)) AS NILAI_PPN,
		
		SUM(
			(b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) - 
			((b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) * (b.PERSEN_PPN / 100))
		) AS EXC_PPN,
		
		SUM(b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) AS TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.STATUS_BAYAR IS NULL
		$query_search
	GROUP BY b.NO_PELANGGAN, b.KODE_BLOK
	HAVING COUNT(b.NO_PELANGGAN) >= $jumlah_piutang
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
	$sum_jumlah_piutang	= 0;
	$sum_jumlah_air		= 0;
	$sum_jumlah_abonemen = 0;
	$sum_jumlah_ipl		= 0;
	$sum_denda			= 0;
	$sum_administrasi	= 0;
	$sum_diskon_rupiah_air = 0;
	$sum_diskon_rupiah_ipl = 0;
	$sum_nilai_ppn		= 0;
	$sum_exc_ppn		= 0;
	$sum_tagihan		= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_PIUTANG']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['EXC_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TAGIHAN']); ?></td>
		</tr>
		<?php
		
		$sum_jumlah_piutang	+= $obj->fields['JUMLAH_PIUTANG'];
		$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
		$sum_jumlah_abonemen += $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_administrasi	+= $obj->fields['ADMINISTRASI'];
		$sum_diskon_rupiah_air += $obj->fields['DISKON_RUPIAH_AIR'];
		$sum_diskon_rupiah_ipl += $obj->fields['DISKON_RUPIAH_IPL'];
		$sum_nilai_ppn		+= $obj->fields['NILAI_PPN'];
		$sum_exc_ppn		+= $obj->fields['EXC_PPN'];
		$sum_tagihan		+= $obj->fields['TAGIHAN'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="3">GRAND TOTAL .........</td>
		<td><?php echo to_money($sum_jumlah_piutang); ?></td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_jumlah_abonemen); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_administrasi); ?></td>
		<td><?php echo to_money($sum_diskon_rupiah_air); ?></td>
		<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
		<td><?php echo to_money($sum_nilai_ppn); ?></td>
		<td><?php echo to_money($sum_exc_ppn); ?></td>
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
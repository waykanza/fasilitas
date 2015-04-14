<?php
require_once('../../../config/config.php');
die_login();
die_mod('PD1');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$tipe_load		= (isset($_REQUEST['tipe_load'])) ? clean($_REQUEST['tipe_load']) : '';
$search_load	= (isset($_REQUEST['search_load'])) ? clean($_REQUEST['search_load']) : '';

$field1			= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1		= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$status_bayar	= (isset($_REQUEST['status_bayar'])) ? clean($_REQUEST['status_bayar']) : '';
$status_cetak_kwt = (isset($_REQUEST['status_cetak_kwt'])) ? clean($_REQUEST['status_cetak_kwt']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

if ($tipe_load == 'load_periode') {
	$query_search .= " b.PERIODE_TAG = '" . to_periode($search_load) . "' ";
} else {
	$query_search .= " b.KODE_BLOK = '$search_load' ";
}

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
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
}
if ($status_bayar != '')
{
	$query_search .= " AND b.STATUS_BAYAR = $status_bayar ";
}
if ($status_cetak_kwt != '')
{
	$query_search .= " AND b.STATUS_CETAK_KWT = $status_cetak_kwt ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.ID_PEMBAYARAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	$where_trx_deposit AND 
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
	<td class="text-right">
		<input type="button" id="prev_page" value=" < (Alt+Left) ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" (Alt+Right) > ">
	</td>
</tr>
</table>

<table class="t-nowrap t-data t-nowrap wm100">
<tr>
	<th rowspan="3">NO.</th>
	<th rowspan="3">NO. TAGIHAN</th>
	<th rowspan="3">PERIODE</th>
	<th colspan="2">PELANGGAN</th>
	<th colspan="8">BLOK</th>
	<th rowspan="3">TOTAL<br>TAGIHAN</th>
	<th colspan="2">STATUS</th>
	<th rowspan="3">CARA BAYAR</th>
</tr>
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NAMA</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">STATUS</th>
	<th colspan="2">LUAS (M&sup2;)</th>
	<th colspan="4">IPL</th>
	<th rowspan="2">BAYAR</th>
	<th rowspan="2">CETAK</th>
</tr>
<tr>
	<th>KAVL.</th>
	<th>BANG.</th>
	<th>GOL.</th>
	<th>AWAL</th>
	<th>AKHIR</th>
	<th>JML.</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 		
		b.ID_PEMBAYARAN, 
		b.NO_INVOICE, 
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		dbo.PTPS(b.PERIODE_IPL_AWAL) AS PERIODE_IPL_AWAL,
		dbo.PTPS(b.PERIODE_IPL_AKHIR) AS PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
		
		p.NAMA_PELANGGAN,
		b.NO_PELANGGAN,
		b.KODE_BLOK,
		b.STATUS_BLOK,
		b.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		b.KEY_IPL,
		b.STATUS_BAYAR,
		b.STATUS_CETAK_KWT,
		b.CARA_BAYAR,
		(JUMLAH_IPL + DENDA - DISKON_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_deposit AND
		$query_search 
	ORDER BY b.PERIODE_TAG DESC, b.PERIODE_IPL_AWAL DESC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td><?php echo fm_nopel($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AWAL']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AKHIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['STATUS_BAYAR']); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['STATUS_CETAK_KWT']); ?></td>
			<td class="text-center"><?php echo cara_bayar($obj->fields['CARA_BAYAR']); ?></td>
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
	$('#per_page').inputmask('integer');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
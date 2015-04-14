<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL3');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$jenis_laporan	= (isset($_REQUEST['jenis_laporan'])) ? clean($_REQUEST['jenis_laporan']) : '';
$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$cara_bayar	= (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
$bayar_via	= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$user_bayar			= (isset($_REQUEST['user_bayar'])) ? clean($_REQUEST['user_bayar']) : '';

$field_tgl_trx = " b.TGL_BAYAR_BANK ";

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($kode_cluster != '')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
}
if ($user_bayar != '')
{
	$query_search .= " AND b.USER_BAYAR = '$user_bayar' ";
}
if ($cara_bayar != '')
{
	$query_search .= " AND b.CARA_BAYAR = $cara_bayar ";
	if ($cara_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($bayar_via != '')
		{
			$query_search .= " AND b.BAYAR_VIA = '$bayar_via' ";
		}
	}
}

if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
}

if ($jenis_laporan == 'HARIAN') {
	$query_jenis_laporan = " CONVERT(VARCHAR(10), $field_tgl_trx, 105) = '$tgl_trx' ";
} else {
	$query_jenis_laporan = " RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx' ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 1 AND
	$query_jenis_laporan
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

<table class="t-data t-nowrap wm100">
<tr>
	<th>NO.</th>
	<th>NO. TAGIHAN</th>
	<th>BLOK / NO.</th>
	<th>NAMA<br>PELANGGAN</th>
	<th>NO. KWITANSI</th>
	<th>PERIODE TAG.</th>
	<th>TGL. BAYAR</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>BAYAR</th>
	<th>USER_BAYAR</th>
	<th>JENIS<br>BAYAR</th>
	<th>KET. BAYAR</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.NO_INVOICE,
		b.KODE_BLOK,
		p.NAMA_PELANGGAN,
		b.NO_KWITANSI,
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		CONVERT(VARCHAR(10),b.TGL_BAYAR_BANK,105) AS TGL_BAYAR_BANK,
		
		b.JUMLAH_IPL,
		b.DENDA,
		b.ADM,
		b.DISKON_IPL,
		b.JUMLAH_BAYAR,
		b.USER_BAYAR,
		b.CARA_BAYAR,
		b.BAYAR_VIA,
		b.KET_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_lain_lain AND 
		b.STATUS_BAYAR = 1 AND
		$query_jenis_laporan
		$query_search
	ORDER BY $field_tgl_trx, b.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_adm				= 0;
	$sum_diskon_ipl	= 0;
	$sum_jumlah_bayar		= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td class="nowrap"><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo $obj->fields['NO_KWITANSI']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['TGL_BAYAR_BANK']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
			<td><?php echo $obj->fields['USER_BAYAR']; ?></td>
			<td class="nowrap"><?php echo cara_bayar($obj->fields['CARA_BAYAR'], $obj->fields['BAYAR_VIA']); ?></td>
			<td><?php echo $obj->fields['KET_BAYAR']; ?></td>
		</tr>
		<?php
		
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_adm				+= $obj->fields['ADM'];
		$sum_diskon_ipl			+= $obj->fields['DISKON_IPL'];
		$sum_jumlah_bayar		+= $obj->fields['JUMLAH_BAYAR'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="7">TOTAL .........</td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_diskon_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_jumlah_bayar); ?></td>
		<td colspan="3"></td>
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
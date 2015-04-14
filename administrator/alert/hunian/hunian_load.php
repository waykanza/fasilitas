<?php
require_once('../../../config/config.php');
die_login();
die_mod('U2');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$field1			= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1		= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

if ($search1 != '')
{
	$query_search .= " AND b.$field1 LIKE '%$search1%' ";
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
	$query_search .= " AND b.TRX = $trx ";
}

# Pagination
$query = "
SELECT 
	TOP 1 COUNT(b.ID_PEMBAYARAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b 
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN 
	LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR 
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER 
WHERE 
	$where_trx_deposit AND 
	b.STATUS_BAYAR = 0 AND 
	p.INFO_TAGIHAN = 1 AND 
	
	CAST(DATEDIFF(MONTH, dbo.PTDF(b.PERIODE_TAG), GETDATE()) AS INT) = 0
	
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
	<th rowspan="2">NO.</th>
	<th rowspan="2"><input type="checkbox" id="cb_all"></th>
	<th colspan="2">PELANGGAN</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th rowspan="2">PERIODE</th>
	<th rowspan="2">IPL DEPOSIT</th>
	<th rowspan="2">DISKON</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">TOTAL TAGIHAN</th>
	<th colspan="3">INVOICE</th>
</tr>
<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>CETAK</th>
	<th>TGL.</th>
	<th>USER</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.ID_PEMBAYARAN, 
		b.NO_PELANGGAN, 
		(CASE WHEN p.AKTIF_SM = 1 THEN p.SM_NAMA_PELANGGAN ELSE p.NAMA_PELANGGAN END) AS NAMA_PELANGGAN, 
		b.KODE_BLOK, 
		s.NAMA_SEKTOR, 
		c.NAMA_CLUSTER, 
		
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		
		b.JUMLAH_IPL,
		b.DISKON_IPL,
		b.DENDA,
		(b.JUMLAH_IPL + b.DENDA - b.DISKON_IPL) AS TOTAL_TAGIHAN, 
		
		STATUS_CETAK_KWT, 
		CONVERT(VARCHAR(10),b.TGL_CETAK_KWT,105) AS TGL_CETAK_KWT, 
		uc.NAMA_USER AS USER_CETAK_KWT
	FROM 
		KWT_PEMBAYARAN_AI b 
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN 
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR 
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER 
		LEFT JOIN KWT_USER uc ON b.USER_CETAK_KWT = uc.ID_USER 
	WHERE 
		$where_trx_deposit AND 
		b.STATUS_BAYAR = 0 AND 
		p.INFO_TAGIHAN = 1 AND 
		
		CAST(DATEDIFF(MONTH, dbo.PTDF(b.PERIODE_TAG), GETDATE()) AS INT) = 0 
		
		$query_search
		
	ORDER BY b.KODE_BLOK 
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
	
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td width="30" class="text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td><?php echo fm_nopel($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TOTAL_TAGIHAN']); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['STATUS_CETAK_KWT']); ?></td>
			<td class="text-center"><?php echo $obj->fields['TGL_CETAK_KWT']; ?></td>
			<td><?php echo $obj->fields['USER_CETAK_KWT']; ?></td>
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
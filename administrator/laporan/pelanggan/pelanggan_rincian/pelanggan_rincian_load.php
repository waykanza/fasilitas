<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
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
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
WHERE
	$where_trx_air_ipl AND 
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
	<th rowspan="2">NO.</th>
	<th rowspan="2">BLOK/NO.</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th colspan="2">LUAS</th>
	<th rowspan="2">NO. TELEPON</th>
</tr>
<tr>
	<th>KAVL.</th>
	<th>BANG.</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		b.KODE_CLUSTER,
		c.NAMA_CLUSTER,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.NO_TELEPON
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode'
		$query_search
	ORDER BY b.KODE_SEKTOR, b.KODE_BLOK, b.NO_PELANGGAN ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1;
	$gr_kode_cluster = '';
	while( ! $obj->EOF)
	{
		if ($gr_kode_cluster != $obj->fields['KODE_CLUSTER'])
		{
			?>
			<tr>
				<td class="text-center"><b><?php echo $obj->fields['KODE_CLUSTER']; ?></b></td>
				<td colspan="17"><b><?php echo $obj->fields['NAMA_CLUSTER']; ?></b></td>
			</tr>
			<?php
			$gr_kode_cluster = $obj->fields['KODE_CLUSTER'];
			$i = 1;
		}
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td><?php echo $obj->fields['NO_TELEPON']; ?></td>
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
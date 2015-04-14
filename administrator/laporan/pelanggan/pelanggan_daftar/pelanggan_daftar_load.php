<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$status_blok = (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';

if ($status_blok != '')
{
	$query_search .= "AND p.STATUS_BLOK = '$status_blok' ";
}

# Pagination
$query = "
SELECT 
	COUNT(p.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PELANGGAN p
WHERE 
	p.DISABLED IS NULL
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
	<th rowspan="2">BLOK</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th colspan="4">PELANGGAN</th>
	<th colspan="2">LUAS (M&sup2;)</th>
	<th colspan="3">AIR</th>
	<th colspan="3">IPL</th>
</tr>
<tr>
	<th>NO.</th>
	<th>NAMA</th>
	<th>NO. TELEPON</th>
	<th>NO. HANDPHONE</th>
	<th>KAVL.</th>
	<th>BANG.</th>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>PERIODE<br>TERAKHIR</th>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>PERIODE<br>TERAKHIR</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		p.KODE_BLOK,
		s.NAMA_SEKTOR,
		c.NAMA_CLUSTER,
		p.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		p.NO_TELEPON,
		p.NO_HP,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.AKTIF_AIR,
		p.KEY_AIR,
		p.AKTIF_IPL,
		p.KEY_IPL,
		dbo.PTPS(
			(SELECT MAX(PERIODE) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE $where_trx_air_ipl AND NO_PELANGGAN = p.NO_PELANGGAN)
		) AS PERIODE_AKHIR_AIR,
		dbo.PTPS(
			(SELECT MAX(PERIODE_AKHIR) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE $where_trx_air_ipl AND NO_PELANGGAN = p.NO_PELANGGAN)
		) AS PERIODE_AKHIR_IPL
	FROM 
		KWT_PELANGGAN p 
		LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE 
		p.DISABLED IS NULL
		$query_search
	ORDER BY p.KODE_SEKTOR, p.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	$kode_cluster = '';
	while( ! $obj->EOF)
	{
		?>
		<tr>
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td><?php echo no_pelanggan($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NO_TELEPON']; ?></td>
			<td><?php echo $obj->fields['NO_HP']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_AIR']); ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR_AIR']; ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['AKTIF_IPL']); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR_IPL']; ?></td>
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
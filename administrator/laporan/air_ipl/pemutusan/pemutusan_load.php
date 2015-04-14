<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$tgl_start		= (isset($_REQUEST['tgl_start'])) ? clean($_REQUEST['tgl_start']) : '';
$tgl_end		= (isset($_REQUEST['tgl_end'])) ? clean($_REQUEST['tgl_end']) : '';

$tmp = explode('-',$tgl_start);
$query_tgl_start = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];
$tmp = explode('-',$tgl_end);
$query_tgl_end = $tmp[2].'-'.$tmp[1].'-'.$tmp[0];

if ($kode_sektor != '')
{
	$query_search .= " AND p.KODE_SEKTOR = '$kode_sektor' ";
}
if ($kode_cluster != '')
{
	$query_search .= " AND p.KODE_CLUSTER = '$kode_cluster' ";
}
if ($trx != '')
{
	$query_search .= "AND p.STATUS_BLOK = '$trx' ";
}

# Pagination
$query = "
SELECT 
	COUNT(p.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PELANGGAN p
WHERE
	p.TGL_PEMUTUSAN > CAST(('$query_tgl_start') AS DATE) AND
	p.TGL_PEMUTUSAN < CAST(('$query_tgl_end') AS DATE) 
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
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th colspan="2">PEMUTUSAN</th>
</tr>
<tr>
	<th>TANGGAL</th>
	<th>NAMA PETUGAS</th>
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
		CONVERT(VARCHAR(10),p.TGL_PEMUTUSAN,105) AS TGL_PEMUTUSAN,
		p.PETUGAS
	FROM 
		KWT_PELANGGAN p
		LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		p.TGL_PEMUTUSAN > CAST(('$query_tgl_start') AS DATE) AND
		p.TGL_PEMUTUSAN < CAST(('$query_tgl_end') AS DATE) 
		$query_search
	ORDER BY p.TGL_PEMUTUSAN ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
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
			<td class="text-center"><?php echo $obj->fields['TGL_PEMUTUSAN']; ?></td>
			<td><?php echo $obj->fields['PETUGAS']; ?></td>
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
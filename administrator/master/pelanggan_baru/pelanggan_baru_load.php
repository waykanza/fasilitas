<?php
require_once('../../../config/config.php');
die_login();
die_mod('M14');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$created_date = (isset($_REQUEST['created_date'])) ? to_periode($_REQUEST['created_date']) : '';

?>

<table class="t-data t-nowrap wm100">
<tr>
	<th rowspan="3">NO.</th>
	<th rowspan="3">STATUS<br>PROSES</th>
	<th colspan="2">PELANGGAN</th>
	<th colspan="6">BLOK</th>
	<th rowspan="3">KETERANGAN</th>
</tr>
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NAMA</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">STATUS</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th colspan="2">LUAS (M&sup2;)</th>
</tr>
<tr>
	<th>KAVL.</th>
	<th>BANG.</th>
</tr>

<?php
	$query = "
	SELECT 
		p.STATUS_PROSES,
		p.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		p.KODE_BLOK,
		s.NAMA_SEKTOR,
		c.NAMA_CLUSTER,
		p.STATUS_BLOK,
		p.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		p.KET
	FROM 
		KWT_PELANGGAN_IMP p
		LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		LEFT(CONVERT(VARCHAR, p.CREATED_DATE, 112),6) = '$created_date'
	ORDER BY p.STATUS_PROSES ASC, p.KODE_BLOK ASC
	";
	
	$obj = $conn->Execute($query);
	
	$i = 1;
	while( ! $obj->EOF)
	{
		$id = $obj->fields['KODE_BLOK'];
		$status_proses = $obj->fields['STATUS_PROSES'];
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo status_check($status_proses); ?></td>
			<td>
				<?php 
				if ($status_proses == '1') {
					echo fm_nopel($obj->fields['NO_PELANGGAN']);
				} else {
					echo '-';
				}
				?>
			</td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $id; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_BANGUNAN'],2); ?></td>
			<td><?php echo $obj->fields['KET']; ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
?>
</table>

<script type="text/javascript">
jQuery(function($) {
	t_strip('.t-data');
});
</script>
<?php
close($conn);
exit;
?>
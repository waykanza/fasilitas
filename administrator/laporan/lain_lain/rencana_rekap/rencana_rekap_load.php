<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL2');
$conn = conn();
die_conn($conn);

$query_search = '';

$periode_tag	= (isset($_REQUEST['periode_tag'])) ? to_periode($_REQUEST['periode_tag']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$field_group = 'KODE_CLUSTER';
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
	$query_nama = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
}
else
{
	$th_sek_clu = 'SEKTOR';
	
	$field_group = 'KODE_SEKTOR';
	$query_lokasi = 'KWT_SEKTOR';
	$query_nama = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
}

if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
}
?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<input type="button" id="excel" value=" Excel (Alt+X) ">
		<input type="button" id="print" value=" Print (Alt+P) ">
	</td>
</tr>
</table>

<table class="t-data t-nowrap wm100">
<tr>
	<th>NO.</th>
	<th><?php echo $th_sek_clu; ?></th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL TAGIHAN</th>
</tr>

<?php
$query = "
SELECT
	$query_nama AS NAMA_LOKASI,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADM) AS ADM,
	SUM(q.DISKON_IPL) AS DISKON_IPL,
	SUM(q.JUMLAH_TAGIHAN) AS JUMLAH_TAGIHAN
FROM 
(
	SELECT 
		$field_group,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADM,
		0 AS DISKON_IPL,
		0 AS JUMLAH_TAGIHAN
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		$field_group AS field_group,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADM) AS ADM,
		SUM(b.DISKON_IPL) AS DISKON_IPL,
		SUM(b.JUMLAH_IPL + b.DENDA + b.ADM - b.DISKON_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_lain_lain AND 
		b.PERIODE_TAG = '$periode_tag'
		$query_search
	GROUP BY b.$field_group
) q
GROUP BY q.$field_group
ORDER BY q.$field_group ASC
";

$obj = $conn->Execute($query);

$i = 1;

$sum_jumlah_ipl			= 0;
$sum_denda				= 0;
$sum_adm				= 0;
$sum_diskon_ipl	= 0;
$sum_jumlah_tagihan 	= 0;

while( ! $obj->EOF)
{
	?>
	<tr> 
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
	</tr>
	<?php
	
	$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
	$sum_denda				+= $obj->fields['DENDA'];
	$sum_adm				+= $obj->fields['ADM'];
	$sum_diskon_ipl			+= $obj->fields['DISKON_IPL'];
	$sum_jumlah_tagihan 	+= $obj->fields['JUMLAH_TAGIHAN'];
	
	$i++;
	$obj->movenext();
}
	
?>
<tfoot>
<tr>
	<td colspan="2">TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_jumlah_tagihan); ?></td>
</tr>
</tfoot>
</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
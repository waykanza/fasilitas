<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL4');
$conn = conn();
die_conn($conn);

$query_search = '';

$jenis_laporan	= (isset($_REQUEST['jenis_laporan'])) ? clean($_REQUEST['jenis_laporan']) : '';
$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$cara_bayar		= (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
$bayar_via		= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$user_bayar		= (isset($_REQUEST['user_bayar'])) ? clean($_REQUEST['user_bayar']) : '';

$field_tgl_trx = " b.TGL_BAYAR_BANK ";

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$field_group = 'KODE_CLUSTER';
	$query_nama = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
else
{
	$th_sek_clu = 'SEKTOR';
	
	$field_group = 'KODE_SEKTOR';
	$query_nama = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
	$query_lokasi = 'KWT_SEKTOR';
}
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
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

if ($jenis_laporan == 'HARIAN') {
	$query_jenis_laporan = " CONVERT(VARCHAR(10), $field_tgl_trx, 105) = '$tgl_trx' ";
} else {
	$query_jenis_laporan = " RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx' ";
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
	<th>REC.</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
	<th>TOTAL<br>BAYAR</th>
</tr>

<?php
$query = "
SELECT
	$query_nama AS NAMA_LOKASI,
	SUM(q.REC) AS REC,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADM) AS ADM,
	SUM(q.DISKON_IPL) AS DISKON_IPL,
	SUM(q.JUMLAH_BAYAR) AS JUMLAH_BAYAR
FROM 
(
	SELECT 
		$field_group,
		0 AS REC,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADM,
		0 AS DISKON_IPL,
		0 AS JUMLAH_BAYAR
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		b.$field_group AS $field_group,
		COUNT(b.$field_group) AS REC,
		
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADM) AS ADM,
		SUM(b.DISKON_IPL) AS DISKON_IPL,
		SUM(b.JUMLAH_BAYAR) AS JUMLAH_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_lain_lain AND 
		b.STATUS_BAYAR = 1 AND
		$query_jenis_laporan
		$query_search
	GROUP BY b.$field_group
) q
GROUP BY q.$field_group
ORDER BY q.$field_group ASC
";

$obj = $conn->Execute($query);

$i = 1;
	
$sum_rec				= 0;
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
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['REC']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
	</tr>
	<?php
	
	$sum_rec				+= $obj->fields['REC'];
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
	<td colspan="2">TOTAL .........</td>
	<td><?php echo to_money($sum_rec); ?></td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_jumlah_bayar); ?></td>
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
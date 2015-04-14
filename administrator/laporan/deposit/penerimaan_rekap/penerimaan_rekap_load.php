<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$jenis_laporan	= (isset($_REQUEST['jenis_laporan'])) ? clean($_REQUEST['jenis_laporan']) : '';
$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$jenis_bayar	= (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
$bayar_melalui	= (isset($_REQUEST['bayar_melalui'])) ? clean($_REQUEST['bayar_melalui']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';

$field_tgl_trx = " b.TGL_BAYAR ";

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$field_group = 'KODE_CLUSTER';
	$query_nama = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search = " AND b.KODE_SEKTOR = '$kode_sektor' ";
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
	$query_search .= "AND TRX = '$trx' ";
}
if ($kasir != '')
{
	$query_search .= " AND b.KASIR = '$kasir' ";
}
if ($jenis_bayar != '')
{
	$query_search .= " AND b.JENIS_BAYAR = '$jenis_bayar' ";
	if ($jenis_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($bayar_melalui != '')
		{
			$query_search .= " AND b.BAYAR_MELALUI = '$bayar_melalui' ";
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

<table class="t-data">
<tr>
	<th>NO.</th>
	<th><?php echo $th_sek_clu; ?></th>
	<th>REC.</th>
	<th>DEPOSIT</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>PPN</th>
	<th>TOTAL<br>EXC. PPN</th>
	<th>TOTAL<br>BAYAR</th>
</tr>

<?php
$query = "
SELECT
	$query_nama AS NAMA_LOKASI,
	SUM(q.REC) AS REC,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADMINISTRASI) AS ADMINISTRASI,
	SUM(q.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
	SUM(q.NILAI_PPN) AS NILAI_PPN,
	SUM(q.EXC_PPN) AS EXC_PPN,
	SUM(q.JUMLAH_BAYAR) AS JUMLAH_BAYAR
FROM 
(
	SELECT 
		$field_group,
		0 AS REC,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADMINISTRASI,
		0 AS DISKON_RUPIAH_IPL,
		0 AS NILAI_PPN,
		0 AS EXC_PPN,
		0 AS JUMLAH_BAYAR
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		b.$field_group AS $field_group,
		COUNT(b.$field_group) AS REC,
		
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADMINISTRASI) AS ADMINISTRASI,
		SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		
		SUM(CASE WHEN b.NILAI_PPN = 0 
				THEN ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100))
				ELSE b.NILAI_PPN
			END) AS NILAI_PPN,
			
		SUM(CASE WHEN b.NILAI_PPN = 0 
				THEN (b.JUMLAH_BAYAR - ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100)))
				ELSE (b.JUMLAH_BAYAR - b.NILAI_PPN)
			END) AS EXC_PPN,
			
		SUM(b.JUMLAH_BAYAR) AS JUMLAH_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_deposit AND 
		b.STATUS_BAYAR = '2' AND
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
$sum_administrasi		= 0;
$sum_diskon_rupiah_ipl	= 0;
$sum_nilai_ppn			= 0;
$sum_exc_ppn			= 0;
$sum_jumlah_bayar		= 0;

while( ! $obj->EOF)
{
	?>
	<tr> 
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['REC']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['EXC_PPN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
	</tr>
	<?php
	
	$sum_rec				+= $obj->fields['REC'];
	$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
	$sum_denda				+= $obj->fields['DENDA'];
	$sum_administrasi		+= $obj->fields['ADMINISTRASI'];
	$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
	$sum_nilai_ppn			+= $obj->fields['NILAI_PPN'];
	$sum_exc_ppn			+= $obj->fields['EXC_PPN'];
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
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_administrasi); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
	<td><?php echo to_money($sum_nilai_ppn); ?></td>
	<td><?php echo to_money($sum_exc_ppn); ?></td>
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
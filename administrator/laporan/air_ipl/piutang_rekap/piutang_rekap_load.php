<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
$jumlah_piutang	= (isset($_REQUEST['jumlah_piutang'])) ? to_number($_REQUEST['jumlah_piutang']) : '1';

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
if ($aktif_air != '')
{
	$query_search .= " AND b.AKTIF_AIR = '1' ";
}
if ($aktif_ipl != '')
{
	$query_search .= " AND b.AKTIF_IPL = '1' ";
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
	<th>AIR</th>
	<th>ABONEMEN</th>
	<th>IPL</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON AIR</th>
	<th>DISKON IPL</th>
	<th>PPN</th>
	<th>TOTAL<br>EXC. PPN</th>
	<th>TOTAL<br>TAGIHAN</th>
</tr>

<?php
$query = "	
SELECT
	$query_nama AS NAMA_LOKASI,
	SUM(q.JUMLAH_PIUTANG) AS JUMLAH_PIUTANG,
	SUM(q.JUMLAH_IPL) AS JUMLAH_AIR,
	SUM(q.ABONEMEN) AS ABONEMEN,
	SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(q.DENDA) AS DENDA,
	SUM(q.ADMINISTRASI) AS ADMINISTRASI,
	SUM(q.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
	SUM(q.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
	SUM(q.NILAI_PPN) AS NILAI_PPN,
	SUM(q.EXC_PPN) AS EXC_PPN,
	SUM(q.JUMLAH_TAGIHAN) AS JUMLAH_TAGIHAN
FROM 
(
	SELECT 
		$field_group,
		0 AS JUMLAH_PIUTANG,
		0 AS JUMLAH_AIR,
		0 AS ABONEMEN,
		0 AS JUMLAH_IPL,
		0 AS DENDA,
		0 AS ADMINISTRASI,
		0 AS DISKON_RUPIAH_AIR,
		0 AS DISKON_RUPIAH_IPL,
		0 AS NILAI_PPN,
		0 AS EXC_PPN,
		0 AS JUMLAH_TAGIHAN
	FROM $query_lokasi
	
	UNION ALL
	
	SELECT 
		b.$field_group AS $field_group,
		COUNT(b.NO_PELANGGAN) AS JUMLAH_PIUTANG,
		SUM(b.JUMLAH_AIR) AS JUMLAH_AIR,
		SUM(b.ABONEMEN) AS ABONEMEN,
		SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(b.DENDA) AS DENDA,
		SUM(b.ADMINISTRASI) AS ADMINISTRASI,
		SUM(b.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
		SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		
		SUM((b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) * (b.PERSEN_PPN / 100)) AS NILAI_PPN,
		
		SUM(
			(b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) - 
			((b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) * (b.PERSEN_PPN / 100))
		) AS EXC_PPN,
		
		SUM(b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_AIR - b.DISKON_RUPIAH_IPL) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.STATUS_BAYAR IS NULL AND 
		(SELECT COUNT(NO_PELANGGAN) FROM KWT_PEMBAYARAN_AI WHERE $where_trx_air_ipl AND NO_PELANGGAN = b.NO_PELANGGAN AND STATUS_BAYAR IS NULL) >= $jumlah_piutang
		$query_search
	GROUP BY b.$field_group
) q
GROUP BY q.$field_group
ORDER BY q.$field_group ASC
";
$obj = $conn->Execute($query);

$i = 1;

$sum_jumlah_piutang		= 0;
$sum_jumlah_air			= 0;
$sum_abonemen			= 0;
$sum_jumlah_ipl			= 0;
$sum_denda				= 0;
$sum_administrasi		= 0;
$sum_diskon_rupiah_air	= 0;
$sum_diskon_rupiah_ipl	= 0;
$sum_nilai_ppn			= 0;
$sum_exc_ppn			= 0;
$sum_tagihan			= 0;

while( ! $obj->EOF)
{
	?>
	<tr> 
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_PIUTANG']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_AIR']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['EXC_PPN']); ?></td>
		<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
	</tr>
	<?php
	
	$sum_jumlah_piutang	+= $obj->fields['JUMLAH_PIUTANG'];
	$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
	$sum_abonemen		+= $obj->fields['ABONEMEN'];
	$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
	$sum_denda			+= $obj->fields['DENDA'];
	$sum_administrasi	+= $obj->fields['ADMINISTRASI'];
	$sum_diskon_rupiah_air += $obj->fields['DISKON_RUPIAH_AIR'];
	$sum_diskon_rupiah_ipl += $obj->fields['DISKON_RUPIAH_IPL'];
	$sum_nilai_ppn		+= $obj->fields['NILAI_PPN'];
	$sum_exc_ppn		+= $obj->fields['EXC_PPN'];
	$sum_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
	
	$i++;
	$obj->movenext();
}
?>
<tfoot>
<tr>
	<td colspan="2">GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_piutang); ?></td>
	<td><?php echo to_money($sum_jumlah_air); ?></td>
	<td><?php echo to_money($sum_abonemen); ?></td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_administrasi); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_air); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
	<td><?php echo to_money($sum_nilai_ppn); ?></td>
	<td><?php echo to_money($sum_exc_ppn); ?></td>
	<td><?php echo to_money($sum_tagihan); ?></td>
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
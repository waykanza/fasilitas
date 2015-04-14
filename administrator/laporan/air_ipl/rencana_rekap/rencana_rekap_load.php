<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page		= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num		= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

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

if ($kode_sektor != '')
{
	$th_sek_clu = 'CLUSTER';
	
	$query_group = 'KODE_CLUSTER';
	$query_header = "(SELECT ISNULL(NAMA_CLUSTER, q.KODE_CLUSTER) FROM KWT_CLUSTER WHERE KODE_CLUSTER = q.KODE_CLUSTER)";
	$query_lokasi = "KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor'";
	$query_search = " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
else
{
	$th_sek_clu = 'SEKTOR';
	
	$query_group = 'KODE_SEKTOR';
	$query_header = "(SELECT ISNULL(NAMA_SEKTOR, q.KODE_SEKTOR) FROM KWT_SEKTOR WHERE KODE_SEKTOR = q.KODE_SEKTOR)";
	$query_lokasi = 'KWT_SEKTOR';
}

# Pagination
$query = "
SELECT
	COUNT(q.$query_group) OVER () AS TOTAL
FROM 
(
	SELECT $query_group FROM $query_lokasi
	
	UNION ALL
	
	SELECT b.$query_group AS $query_group
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.PERIODE = '$periode'
		$query_search
	GROUP BY b.$query_group
) q
GROUP BY q.$query_group
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
	<th rowspan="2"><?php echo $th_sek_clu; ?></th>
	<th rowspan="2">PEMAKAIAN</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">IPL</th>
	<th rowspan="2">DENDAN</th>
	<th rowspan="2">ADMINISTRASI</th>
	<th colspan="2">DISKON</th>
	<th rowspan="2">JML. DISKON</th>
	<th rowspan="2">JML. BAYAR</th>
</tr>
<tr>
	<th>AIR</th>
	<th>IPL</th>
</tr>

<?php
if ($total_data > 0)
{
	$obj = $conn->Execute("
	SELECT TOP 1 
	ADMINISTRASI_KV, ADMINISTRASI_BG,
	ADMINISTRASI_HN, ADMINISTRASI_RV
	FROM KWT_PARAMETER");
	
	$adm_kv = $obj->fields['ADMINISTRASI_KV'];
	$adm_bg = $obj->fields['ADMINISTRASI_BG'];
	$adm_hn = $obj->fields['ADMINISTRASI_HN'];
	$adm_rv = $obj->fields['ADMINISTRASI_RV'];
	
	$query = "
	SELECT
		$query_header AS NAMA_LOKASI,
		SUM(q.PEMAKAIAN) AS PEMAKAIAN,
		SUM(q.JUMLAH_AIR) AS JUMLAH_AIR,
		SUM(q.DENDA) AS DENDA,
		SUM(q.ABONEMEN) AS ABONEMEN,
		SUM(q.JUMLAH_IPL) AS JUMLAH_IPL,
		SUM(q.DENDA) AS DENDA,
		SUM(q.ADMINISTRASI) AS ADMINISTRASI,
		SUM(q.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
		SUM(q.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
		SUM(q.JUMLAH_DISKON) AS JUMLAH_DISKON,
		SUM(q.JUMLAH_TAGIHAN) AS JUMLAH_TAGIHAN
	FROM 
	(
		SELECT 
			$query_group,
			NULL AS PEMAKAIAN,
			NULL AS JUMLAH_AIR,
			NULL AS ABONEMEN,
			NULL AS JUMLAH_IPL,
			NULL AS DENDA,
			NULL AS ADMINISTRASI,
			NULL AS DISKON_RUPIAH_AIR,
			NULL AS DISKON_RUPIAH_IPL,
			NULL AS JUMLAH_DISKON,
			NULL AS JUMLAH_TAGIHAN
		FROM $query_lokasi

		UNION ALL

		SELECT 
			$query_group AS query_group,
			SUM((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
			SUM(b.JUMLAH_AIR) AS JUMLAH_AIR,
			SUM(b.ABONEMEN) AS ABONEMEN,
			SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
			SUM(b.DENDA) AS DENDA,
			SUM(
				CASE WHEN b.STATUS_BAYAR = '2' THEN b.ADMINISTRASI ELSE 
				CASE TRX
					WHEN '1' THEN $adm_kv WHEN '2' THEN $adm_bg
					WHEN '4' THEN $adm_hn WHEN '5' THEN $adm_rv
				END END 
			) AS ADMINISTRASI,
			SUM(b.DISKON_RUPIAH_AIR) AS DISKON_RUPIAH_AIR,
			SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
			SUM(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS JUMLAH_DISKON,
			SUM(
				(
					b.JUMLAH_AIR + b.JUMLAH_IPL + b.ABONEMEN + b.DENDA + 
					CASE WHEN b.STATUS_BAYAR = '2' THEN b.ADMINISTRASI ELSE 
					CASE TRX
						WHEN '1' THEN $adm_kv WHEN '2' THEN $adm_bg
						WHEN '4' THEN $adm_hn WHEN '5' THEN $adm_rv
					END END 
				) - (b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL)
			) AS JUMLAH_TAGIHAN
		FROM 
			KWT_PEMBAYARAN_AI b
		WHERE
			$where_trx_air_ipl AND 
			b.PERIODE = '$periode'
			$query_search
		GROUP BY b.$query_group
	) q
	GROUP BY q.$query_group
	ORDER BY q.$query_group ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	$sum_pemakaian			= 0;
	$sum_jumlah_air			= 0;
	$sum_abonemen			= 0;
	$sum_jumlah_ipl			= 0;
	$sum_jumlah_denda		= 0;
	$sum_jumlah_administrasi = 0;
	$sum_diskon_rupiah_air	= 0;
	$sum_diskon_rupiah_ipl 	= 0;
	$sum_jumlah_diskon		= 0;
	$sum_jumlah_tagihan		= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PEMAKAIAN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_RUPIAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_DISKON']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		<?php
		
		$sum_pemakaian			+= $obj->fields['PEMAKAIAN'];
		$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen			+= $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_jumlah_denda		+= $obj->fields['DENDA'];
		$sum_jumlah_administrasi += $obj->fields['ADMINISTRASI'];
		$sum_diskon_rupiah_air	+= $obj->fields['DISKON_RUPIAH_AIR'];
		$sum_diskon_rupiah_ipl	+= $obj->fields['DISKON_RUPIAH_IPL'];
		$sum_jumlah_diskon		+= $obj->fields['JUMLAH_DISKON'];
		$sum_jumlah_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
		
		$i++;
		$obj->movenext();
	}
	
	?>
	<tfoot>
	<tr>
		<td colspan="2">GRAND TOTAL .........</td>
		<td><?php echo to_money($sum_pemakaian); ?></td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_abonemen); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_jumlah_denda); ?></td>
		<td><?php echo to_money($sum_jumlah_administrasi); ?></td>
		<td><?php echo to_money($sum_diskon_rupiah_air); ?></td>
		<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
		<td><?php echo to_money($sum_jumlah_diskon); ?></td>
		<td><?php echo to_money($sum_jumlah_tagihan); ?></td>
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
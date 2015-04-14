<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LA1');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page		= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num		= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$periode_tag	= (isset($_REQUEST['periode_tag'])) ? to_periode($_REQUEST['periode_tag']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$kode_zona		= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($kode_cluster != '')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
}
if ($kode_zona != '')
{
	$query_search .= " AND b.KODE_ZONA = '$kode_zona' ";
}
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
}
if ($aktif_air != '') {
	$query_search .= " AND b.AKTIF_AIR = $aktif_air ";
}
if ($aktif_ipl != '') {
	$query_search .= " AND b.AKTIF_IPL = $aktif_ipl ";
}

# Pagination
$query = "
SELECT 
	COUNT(b.NO_PELANGGAN) AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	$where_trx_air_ipl AND 
	(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) < 1 AND
	b.PERIODE_TAG = '$periode_tag'
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

<table class="t-data t-nowrap wm100">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NO. TAGIHAN</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th rowspan="2">LUAS (M2)</th>
	<th rowspan="2">TARIF</th>
	<th rowspan="2">PERIODE</th>
	<th rowspan="2">IPL</th>
	<th rowspan="2">PEMAKAIAN</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">ADM</th>
	<th colspan="2">DISKON</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">TOTAL<br>TAGIHAN</th>
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
	ADM_KV, ADM_BG,
	ADM_HN, ADM_RV
	FROM KWT_PARAMETER");
	
	$adm_kv = $obj->fields['ADM_KV'];
	$adm_bg = $obj->fields['ADM_BG'];
	$adm_hn = $obj->fields['ADM_HN'];
	$adm_rv = $obj->fields['ADM_RV'];
	
	$query = "
	SELECT 
		b.NO_INVOICE,
		b.KODE_CLUSTER,
		c.NAMA_CLUSTER,
		b.KODE_BLOK,
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		b.JUMLAH_PERIODE_IPL,
		b.JUMLAH_IPL,
		((b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) + b.STAND_MIN_PAKAI) AS PEMAKAIAN,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.DENDA,
		(
			CASE WHEN b.STATUS_BAYAR = 1 THEN b.ADM ELSE 
			CASE TRX
				WHEN $trx_kv THEN $adm_kv 
				WHEN $trx_bg THEN $adm_bg 
				WHEN $trx_hn THEN $adm_hn 
				WHEN $trx_rv THEN $adm_rv 
			END END 
		) AS ADM,
		b.DISKON_AIR,
		b.DISKON_IPL,
		(
			b.JUMLAH_AIR + b.JUMLAH_IPL + b.ABONEMEN + b.DENDA + 
			CASE WHEN b.STATUS_BAYAR = 1 THEN b.ADM ELSE 
			CASE TRX
				WHEN $trx_kv THEN $adm_kv 
				WHEN $trx_bg THEN $adm_bg 
				WHEN $trx_hn THEN $adm_hn 
				WHEN $trx_rv THEN $adm_rv 
			END END 
			- b.DISKON_AIR - b.DISKON_IPL
		) AS JUMLAH_TAGIHAN
	FROM 
		KWT_PEMBAYARAN_AI b 
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE
		$where_trx_air_ipl AND 
		(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) < 1 AND
		b.PERIODE_TAG = '$periode_tag'
		$query_search
	ORDER BY b.KODE_CLUSTER, b.KODE_BLOK ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	$sum_jumlah_ipl			= 0;
	$sum_pemakaian			= 0;
	$sum_jumlah_air			= 0;
	$sum_abonemen			= 0;
	$sum_denda				= 0;
	$sum_adm				= 0;
	$sum_diskon_air			= 0;
	$sum_diskon_ipl			= 0;
	$sum_jumlah_tagihan		= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo fm_nopel($obj->fields['NO_PELANGGAN']); ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['LUAS_KAVLING'],2); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF_IPL']); ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['JUMLAH_PERIODE_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PEMAKAIAN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
		</tr>
		<?php 
		
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_pemakaian			+= $obj->fields['PEMAKAIAN'];
		$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen			+= $obj->fields['ABONEMEN'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_adm				+= $obj->fields['ADM'];
		$sum_diskon_air			+= $obj->fields['DISKON_AIR'];
		$sum_diskon_ipl			+= $obj->fields['DISKON_IPL'];
		$sum_jumlah_tagihan		+= $obj->fields['JUMLAH_TAGIHAN'];
		
		$i++;
		$obj->movenext();
	}
	
	?>
	<tfoot>
	<tr>
		<td colspan="8">TOTAL .........</td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_pemakaian); ?></td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_abonemen); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_diskon_air); ?></td>
		<td><?php echo to_money($sum_diskon_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
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
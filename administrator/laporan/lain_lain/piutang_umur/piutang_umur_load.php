<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL7');
$conn = conn();
die_conn($conn);

$tahun_tag			= (isset($_REQUEST['tahun_tag'])) ? clean($_REQUEST['tahun_tag']) : date('Y');
$jenis_tgl_bayar	= (isset($_REQUEST['jenis_tgl_bayar'])) ? clean($_REQUEST['jenis_tgl_bayar']) : '';
$trx				= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

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

<?php
$query_search = " $where_trx_lain_lain ";

if ($trx != '') {
	$query_search .= " AND TRX = $trx ";
} 

for ($bulan = 1; $bulan <= 12; $bulan++)
{
	if ($bulan < 10) {
		$bulan_tag = '0' . $bulan;
	} else {
		$bulan_tag = $bulan;
	}
	
	$periode_tag = $tahun_tag . $bulan_tag;
	$periode_tag_1 = periode_mod('-1', $periode_tag);
	$periode_tag_2 = periode_mod('-2', $periode_tag);
	$periode_tag_3 = periode_mod('-3', $periode_tag);
	
	$query = "
	DECLARE 
	@periode_tag VARCHAR(6) = '$periode_tag', 
	@periode_tag_1 VARCHAR(6) = '$periode_tag_1', 
	@periode_tag_2 VARCHAR(6) = '$periode_tag_2', 
	@periode_tag_3 VARCHAR(6) = '$periode_tag_3' 
	
	SELECT 
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR 
					(
						(
							STATUS_BAYAR = 1 
							AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) >= @periode_tag 
						)
					)
				)
				AND CAST(PERIODE_TAG AS INT) < CAST(@periode_tag AS INT) 
		) AS SALDO_PIUTANG,

		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND STATUS_BAYAR = 1 
				AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) = @periode_tag
				
				AND CAST(PERIODE_TAG AS INT) < CAST(@periode_tag AS INT) 
		) AS REALISASI_PIUTANG,
		
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND PERIODE_TAG = @periode_tag 
		) AS INVOICE,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND STATUS_BAYAR = 1 
				AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) = @periode_tag
				AND PERIODE_TAG = @periode_tag 
		) AS REALISASI_INVOICE,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR
					(
						STATUS_BAYAR = 1 
						AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) > @periode_tag 
					)
				)
				AND PERIODE_TAG = @periode_tag 
		) AS PIUTANG_0,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR
					(
						STATUS_BAYAR = 1 
						AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) > @periode_tag
					)
				)
				AND PERIODE_TAG = @periode_tag_1 
		) AS PIUTANG_1,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR
					(
						STATUS_BAYAR = 1 
						AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) > @periode_tag 
					)
				)
				AND PERIODE_TAG = @periode_tag_2 
		) AS PIUTANG_2,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR
					(
						STATUS_BAYAR = 1 
						AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) > @periode_tag 
					)
				)
				AND PERIODE_TAG = @periode_tag_3 
		) AS PIUTANG_3,
		
		(
			SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + ADM + DENDA - DISKON_AIR - DISKON_IPL) 
			FROM KWT_PEMBAYARAN_AI 
			WHERE 
				$query_search 
				AND 
				(
					STATUS_BAYAR = 0 
					OR
					(
						STATUS_BAYAR = 1 
						AND CONVERT(VARCHAR(6), $jenis_tgl_bayar, 112) > @periode_tag 
					)
				)
				AND CAST(PERIODE_TAG AS INT) < CAST(@periode_tag_3 AS INT) 
		) AS PIUTANG_3_PLUS
	";
	
	$obj = $conn->Execute($query);
	
	$SALDO_PIUTANG = $obj->fields['SALDO_PIUTANG'];
	$INVOICE = $obj->fields['INVOICE'];
	
	$REALISASI_PIUTANG = $obj->fields['REALISASI_PIUTANG'];
	$REALISASI_INVOICE = $obj->fields['REALISASI_INVOICE'];
	
	$SISA_PIUTANG = ($SALDO_PIUTANG - $REALISASI_PIUTANG) + ($INVOICE - $REALISASI_INVOICE);
	
	$PIUTANG_0 = $obj->fields['PIUTANG_0'];
	$PIUTANG_1 = $obj->fields['PIUTANG_1'];
	$PIUTANG_2 = $obj->fields['PIUTANG_2'];
	$PIUTANG_3 = $obj->fields['PIUTANG_3'];
	$PIUTANG_3_PLUS = $obj->fields['PIUTANG_3_PLUS'];
	
	?>
	<tr>
		<th rowspan="3"><?php echo fm_periode($periode_tag, '%b %Y'); ?></th>
		<th rowspan="2">SALDO PIUTANG<br><?php echo fm_periode($periode_tag_1, '%b %Y'); ?></th>
		<th rowspan="2">INVOICE</th>
		<th colspan="2">REALISASI</th>
		<th rowspan="2">SISA PIUTANG</th>
		<th colspan="5">UMUR PIUTANG</th>
	</tr>
	<tr>
		<th>PIUTANG</th>
		<th>INVOICE</th>
		<th>PIUTANG 0 BLN</th>
		<th>PIUTANG 1 BLN</th>
		<th>PIUTANG 2 BLN</th>
		<th>PIUTANG 3 BLN</th>
		<th>PIUTANG >3 BLN</th>
	</tr>
	<tr>
		<td class="text-right"><?php echo to_money($SALDO_PIUTANG); ?></td>
		<td class="text-right"><?php echo to_money($INVOICE); ?></td>
		<td class="text-right"><?php echo to_money($REALISASI_PIUTANG); ?></td>
		<td class="text-right"><?php echo to_money($REALISASI_INVOICE); ?></td>
		<td class="text-right"><?php echo to_money($SISA_PIUTANG); ?></td>
		<td class="text-right"><?php echo to_money($PIUTANG_0); ?></td>
		<td class="text-right"><?php echo to_money($PIUTANG_1); ?></td>
		<td class="text-right"><?php echo to_money($PIUTANG_2); ?></td>
		<td class="text-right"><?php echo to_money($PIUTANG_3); ?></td>
		<td class="text-right"><?php echo to_money($PIUTANG_3_PLUS); ?></td>
	</tr>
	
	<tr>
		<td colspan="11" style="background:#FFF;padding-bottom:25px;"></td>
	</tr>
	<?php
}
?>
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
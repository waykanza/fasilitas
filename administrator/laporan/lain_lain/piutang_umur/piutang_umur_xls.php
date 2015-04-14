<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL7');
$conn = conn();
die_conn($conn);


$tahun_tag	= (isset($_REQUEST['tahun_tag'])) ? clean($_REQUEST['tahun_tag']) : date('Y');
$jenis_tgl_bayar	= (isset($_REQUEST['jenis_tgl_bayar'])) ? clean($_REQUEST['jenis_tgl_bayar']) : '';
$trx		= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Umur Piutang Save Deposit';

$query_search = " $where_trx_lain_lain ";
if ($trx != '') {
	$query_search .= " AND TRX = $trx ";
	$desc_top[] = 'Status : ' . status_blok($trx);
}

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<table class="data">
<tr><td colspan="11" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="11" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="11" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="11" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="11" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="9" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" align="right" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>
';

$filename = "LAPORAN_UMUR_PIUTANG_$tahun_tag";

header("Content-type: application/msexcel");
header("Content-Disposition: attachment; filename=$filename.xls");
header("Pragma: no-cache");
header("Expires: 0");

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $desc_top[0]; ?></title>
<style type="text/css">
@media print {
	@page {
		size:34.6cm 27.90cm;
	}
	.newpage {page-break-before:always;}
}

.newpage {margin-top:25px;}

table {
	font-family:Arial, Helvetica, sans-serif;
	width:100%;
	border-spacing:0;
	border-collapse:collapse;
}
table tr {
	font-size:11px;
	padding:2px;
}
table td {
	padding:2px;
	vertical-align:top;
}
table th.nb,
table td.nb {
	border:none !important;
}
table.data th {
	border:1px solid #000000;
}
table.data td {
	border-right:1px solid #000000;
	border-left:1px solid #000000;
}
tfoot tr {
	font-weight:bold;
	text-align:right;
	border:1px solid #000000;
}
.break { word-wrap:break-word; }
.nowrap { white-space:nowrap; }
.va-top { vertical-align:top; }
.va-bottom { vertical-align:bottom; }
.text-left { text-align:left; }
.text-center { text-align:center; }
.text-right { text-align:right; }
</style>
</head>
<body>

<?php
echo $set_jrp;

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
	
	$SISA_PIUTANG = ($SALDO_PIUTANG + $INVOICE - $REALISASI_PIUTANG - $REALISASI_INVOICE);
	
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
	<?php
}
?>
</table>

</body>
</html>
<?php
close($conn);
exit;
?>
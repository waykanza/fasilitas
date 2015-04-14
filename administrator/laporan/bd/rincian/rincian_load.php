<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LB1');
$conn = conn();
die_conn($conn);

$query_search = '';

$per_page	= (isset($_REQUEST['per_page'])) ? max(1, $_REQUEST['per_page']) : 20;
$page_num	= (isset($_REQUEST['page_num'])) ? max(1, $_REQUEST['page_num']) : 1;

$jenis_tgl_bd	= (isset($_REQUEST['jenis_tgl_bd'])) ? clean($_REQUEST['jenis_tgl_bd']) : '';
$tgl_bd			= (isset($_REQUEST['tgl_bd'])) ? clean($_REQUEST['tgl_bd']) : '';
$no_kwitansi	= (isset($_REQUEST['no_kwitansi'])) ? clean($_REQUEST['no_kwitansi']) : '';
$bank_bd		= (isset($_REQUEST['bank_bd'])) ? clean($_REQUEST['bank_bd']) : '';

$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';

if ($tgl_bd != '') {
	if ($jenis_tgl_bd == 'HARIAN') {
		$query_search .= " AND CONVERT(VARCHAR(10), b.TGL_BAYAR_SYS, 105) = '$tgl_bd' ";
	} else {
		$query_search .= " AND RIGHT(CONVERT(VARCHAR(10), b.TGL_BAYAR_SYS, 105), 7) = '$tgl_bd' ";
	}
}
if ($no_kwitansi != '') {
	$query_search .= " AND b.NO_KWITANSI LIKE '%$no_kwitansi%' ";
}
if ($trx != '') {
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
	LEFT JOIN KWT_POST_BD d ON 
		CONVERT(VARCHAR(10), b.TGL_BAYAR_SYS, 105) = CONVERT(VARCHAR(10), d.TGL_BD, 105) AND 
		b.BAYAR_VIA = d.BANK_BD
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	
	LEFT JOIN KWT_USER uk ON b.USER_BAYAR = uk.ID_USER 
WHERE
	b.STATUS_BAYAR = 1 AND 
	b.BAYAR_VIA = '$bank_bd' AND 
	b.STATUS_POST_BD = 1 
	
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
	<th colspan="2">TANGGAL BAYAR</th>
	<th rowspan="2">NO. BD/BDT</th>
	<th rowspan="2">NO. TAGIHAN</th>
	<th rowspan="2">BLOK / NO.</th>
	<th rowspan="2">SEKTOR</th>
	<th rowspan="2">CLUSTER</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th rowspan="2">NO. KWITANSI</th>
	<th rowspan="2">PERIODE</th>
	<th rowspan="2">JUMLAH<br>PERIODE</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">IPL</th>
	<th colspan="2">DISKON</th>
	<th rowspan="2">ADM</th>
	<th rowspan="2">PPN</th>
	<th rowspan="2">TOTAL<br>EXC. PPN</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">TOTAL<br>BAYAR</th>
	<th rowspan="2">JENIS<br>BAYAR</th>
	<th rowspan="2">VALIDASI</th>
	<th rowspan="2">KET. BAYAR</th>
</tr>
<tr>
	<th>SYS</th>
	<th>BANK</th>
	<th>AIR</th>
	<th>IPL</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT 
		CASE 
			WHEN b.AKTIF_AIR = 1 THEN d.NO_BD 
			WHEN (b.AKTIF_IPL = 1 AND b.AKTIF_AIR = 0) THEN d.NO_BDT
		END NO_BDBDT,
		
		b.NO_INVOICE,
		b.KODE_BLOK,
		s.NAMA_SEKTOR,
		c.NAMA_CLUSTER,
		p.NAMA_PELANGGAN,
		b.NO_KWITANSI,
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		b.JUMLAH_PERIODE_IPL,
		CONVERT(VARCHAR(10),b.TGL_BAYAR_SYS,105) AS TGL_BAYAR_SYS,
		CONVERT(VARCHAR(10),b.TGL_BAYAR_BANK,105) AS TGL_BAYAR_BANK,
		
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.JUMLAH_IPL,
		b.DENDA,
		b.ADM,
		b.DISKON_AIR,
		b.DISKON_IPL,
		
		CASE WHEN b.NILAI_PPN = 0 
			THEN dbo.PPN(b.PERSEN_PPN, (b.JUMLAH_BAYAR - b.ADM - b.DENDA)) 
			ELSE b.NILAI_PPN
		END AS NILAI_PPN,
		
		CASE WHEN b.NILAI_PPN = 0 
			THEN (b.ADM + b.DENDA + dbo.DPP(b.PERSEN_PPN, (b.JUMLAH_BAYAR - b.ADM - b.DENDA)))
			ELSE (b.JUMLAH_BAYAR - b.NILAI_PPN)
		END AS EXC_PPN,
		
		b.JUMLAH_BAYAR,
		uk.NAMA_USER AS USER_BAYAR,
		b.CARA_BAYAR,
		b.BAYAR_VIA,
		b.KET_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_POST_BD d ON 
			CONVERT(VARCHAR(10), b.TGL_BAYAR_SYS, 105) = CONVERT(VARCHAR(10), d.TGL_BD, 105) AND 
			b.BAYAR_VIA = d.BANK_BD
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
		
		LEFT JOIN KWT_USER uk ON b.USER_BAYAR = uk.ID_USER 
	WHERE
		b.STATUS_BAYAR = 1 AND 
		b.BAYAR_VIA = '$bank_bd' AND 
		b.STATUS_POST_BD = 1 
		
		$query_search
		
	ORDER BY NO_BDBDT ASC
	";
	$obj = $conn->SelectLimit($query, $per_page, $page_start);

	$i = 1 + $page_start;
	
	$sum_jumlah_air			= 0;
	$sum_abonemen			= 0;
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_adm				= 0;
	$sum_diskon_air			= 0;
	$sum_diskon_ipl			= 0;
	$sum_nilai_ppn			= 0;
	$sum_exc_ppn			= 0;
	$sum_jumlah_bayar		= 0;
	
	while( ! $obj->EOF)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo $obj->fields['TGL_BAYAR_SYS']; ?></td>
			<td class="text-center"><?php echo $obj->fields['TGL_BAYAR_BANK']; ?></td>
			<td><?php echo $obj->fields['NO_BDBDT']; ?></td>
			<td><?php echo $obj->fields['NO_INVOICE']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_SEKTOR']; ?></td>
			<td><?php echo $obj->fields['NAMA_CLUSTER']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-right"><?php echo $obj->fields['NO_KWITANSI']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['EXC_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
			<td><?php echo cara_bayar($obj->fields['CARA_BAYAR'], $obj->fields['BAYAR_VIA']); ?></td>
			<td><?php echo $obj->fields['USER_BAYAR']; ?></td>
			<td><?php echo $obj->fields['KET_BAYAR']; ?></td>
		</tr>
		<?php
		
		$sum_jumlah_air			+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen			+= $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl			+= $obj->fields['JUMLAH_IPL'];
		$sum_denda				+= $obj->fields['DENDA'];
		$sum_adm				+= $obj->fields['ADM'];
		$sum_diskon_air			+= $obj->fields['DISKON_AIR'];
		$sum_diskon_ipl			+= $obj->fields['DISKON_IPL'];
		$sum_nilai_ppn			+= $obj->fields['NILAI_PPN'];
		$sum_exc_ppn			+= $obj->fields['EXC_PPN'];
		$sum_jumlah_bayar		+= $obj->fields['JUMLAH_BAYAR'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="12">TOTAL .........</td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_abonemen); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_diskon_air); ?></td>
		<td><?php echo to_money($sum_diskon_ipl); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_nilai_ppn); ?></td>
		<td><?php echo to_money($sum_exc_ppn); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_jumlah_bayar); ?></td>
		<td colspan="3"></td>
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
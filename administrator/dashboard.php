<!-- ========================= MASA MEMBANGUN / RENOVASI ========================= -->
<div class="w48 f-right">
<table class="t-data">
<tr>
	<th colspan="3">BLOK MASA MEMBANGUN</th>
</tr>
<tr>
	<th>NO.</th>
	<th>BLOK / NO.</th>
	<th>PERIODE AKHIR</th>
</tr>

<?php
$periode_tag_now = date('Ym');
$query = "
SELECT 
	p.KODE_BLOK, 
	MAX(CAST(PERIODE_IPL_AKHIR AS INT)) AS PERIODE_IPL_AKHIR 
FROM 
	KWT_PELANGGAN p 
	JOIN KWT_PERIODE_DEPOSIT d ON 
		p.KODE_BLOK = d.KODE_BLOK AND 
		TRX = $trx_dbg AND 
		STATUS_PROSES = 1
WHERE 
	p.STATUS_BLOK = $trx_bg AND 
	(
		DATEDIFF(MONTH, GETDATE(), dbo.PTDF(d.PERIODE_IPL_AKHIR)) <= 2 OR 
		CAST(d.PERIODE_IPL_AKHIR AS INT) < $periode_tag_now 
	)
GROUP BY p.KODE_BLOK
";

$obj = $conn->Execute($query);
$i = 1;
while( ! $obj->EOF)
{
	
	?>
	<tr>
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
		<td class="text-center"><?php echo fm_periode($obj->fields['PERIODE_IPL_AKHIR']); ?></td>
	</tr>
	<?php
	$i++;
	$obj->movenext();
}
?>
<tr>
	<th colspan="3">BLOK RENOVASI</th>
</tr>
<tr>
	<th>NO.</th>
	<th>BLOK / NO.</th>
	<th>PERIODE AKHIR</th>
</tr>
<?php
$query = "
SELECT 
	p.KODE_BLOK, 
	MAX(CAST(PERIODE_IPL_AKHIR AS INT)) AS PERIODE_IPL_AKHIR 
FROM 
	KWT_PELANGGAN p 
	JOIN KWT_PERIODE_DEPOSIT d ON 
		p.KODE_BLOK = d.KODE_BLOK AND 
		TRX = $trx_drv AND 
		STATUS_PROSES = 1
WHERE 
	p.STATUS_BLOK = $trx_rv AND 
	(
		DATEDIFF(MONTH, GETDATE(), dbo.PTDF(d.PERIODE_IPL_AKHIR)) <= 2 OR 
		CAST(d.PERIODE_IPL_AKHIR AS INT) < $periode_tag_now 
	)
GROUP BY p.KODE_BLOK
";

$obj = $conn->Execute($query);
$i = 1;
while( ! $obj->EOF)
{
	
	?>
	<tr>
		<td class="text-center"><?php echo $i; ?></td>
		<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
		<td class="text-center"><?php echo fm_periode($obj->fields['PERIODE_IPL_AKHIR']); ?></td>
	</tr>
	<?php
	$i++;
	$obj->movenext();
}
?>
</table>
</div>


<!-- ========================= TRASAKSI ========================= -->
<?php 
$periode_tag = (isset($_REQUEST['periode_tag'])) ? clean($_REQUEST['periode_tag']) : date('Ym'); 
$query = "
SELECT 
	(SELECT COUNT(ID_PEMBAYARAN) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag') AS TAG, 
	
	(SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_AIR - DISKON_IPL) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag') AS TAG_TOT, 
	
	(SELECT COUNT(ID_PEMBAYARAN) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag' AND STATUS_BAYAR = 0) AS IVC, 
	
	(SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_AIR - DISKON_IPL) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag' AND STATUS_BAYAR = 0) AS IVC_TOT, 
	
	(SELECT COUNT(ID_PEMBAYARAN) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag' AND STATUS_BAYAR = 1) AS REA, 
	
	(SELECT SUM(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_AIR - DISKON_IPL) FROM KWT_PEMBAYARAN_AI 
	WHERE PERIODE_TAG = '$periode_tag' AND STATUS_BAYAR = 1) AS REA_TOT
";
$obj = $conn->Execute($query);
?>

<div class="w48 f-left">
<table class="t-data">
<tr>
	<th colspan="3">
		PERIODE <?php echo fm_periode($periode_tag); ?>
	</th>
</tr>
<tr>
	<th width="170" class="text-left">BANYAK TAGIHAN</th>
	<td class="text-right"><?php echo to_money($obj->fields['TAG']); ?></td>
</tr>
<tr>
	<th class="text-left">SUDAH DIBAYAR</th>
	<td class="text-right"><?php echo to_money($obj->fields['REA']); ?></td>
</tr>
<tr>
	<th class="text-left">BELUM DIBAYAR</th>
	<td class="text-right"><?php echo to_money($obj->fields['IVC']); ?></td>
</tr>
<tr>
	<th class="text-left">TOTAL TAGIHAN</th>
	<td class="text-right"><?php echo to_money($obj->fields['TAG_TOT']); ?></td>
</tr>
<tr>
	<th class="text-left">TOTAL SUDAH DIBAYAR</th>
	<td class="text-right"><?php echo to_money($obj->fields['REA_TOT']); ?></td>
</tr>
<tr>
	<th class="text-left">TOTAL BELUM DIBAYAR</th>
	<td class="text-right"><?php echo to_money($obj->fields['IVC_TOT']); ?></td>
</tr>

</table>
</div>

<script type="text/javascript">
jQuery(function($) {
	t_strip('.t-data');
});
</script>
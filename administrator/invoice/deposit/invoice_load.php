<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$tipe_cari		= (isset($_REQUEST['tipe_cari'])) ? clean($_REQUEST['tipe_cari']) : '';
$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';

if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
}

if ($tipe_cari == '2')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
}
else
{
	$query_search .= " AND b.KODE_BLOK LIKE '%$kode_blok%' ";
}

# Pagination
$query = "
DECLARE
@now DATE = DATEADD(MONTH, -1, GETDATE())

SELECT 
	COUNT(b.KODE_BLOK) OVER () AS TOTAL
FROM 
	KWT_PEMBAYARAN_AI b 
	LEFT JOIN KWT_PELANGGAN p ON b.KODE_BLOK = p.KODE_BLOK
WHERE 
	p.INFO_TAGIHAN = '1' AND 
	b.STATUS_BAYAR IS NULL AND
	(CAST(DATEDIFF(MONTH, dbo.PTDF(b.PERIODE), @now) AS INT)) >= 2
	$query_search
GROUP BY b.KODE_BLOK
ORDER BY b.KODE_BLOK
";
$total_data = $conn->Execute($query)->fields['TOTAL'];
# End Pagination
?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<input type="button" id="print" value=" Cetak (Alt+P) ">
	</td>
</tr>
</table>

<table class="t-data">
<tr>
	<th rowspan="2" class="w5"><input type="checkbox" id="cb_all"></th>
	<th rowspan="2" class="w5">NO.</th>
	<th rowspan="2" class="w20">KODE BLOK</th>
	<th rowspan="2" class="w35">NAMA PELANGGAN</th>
	<th rowspan="2" class="w5">JML.<br>PERIODE</th>
	<th colspan="2" class="w15">PERIODE</th>
	<th rowspan="2" class="w15">JUMLAH BAYAR</th>
</tr>
<tr>
	<th class="w7">AWAL</th>
	<th class="w7">AKHIR</th>
</tr>


<?php
if ($total_data > 0)
{
	$query = "
	DECLARE 
	@adm_db INT,
	@adm_dr INT,
	@now DATE = DATEADD(MONTH, -1, GETDATE())

	SELECT TOP 1 
	@adm_db = ISNULL(ADMINISTRASI_DB, 0) ,
	@adm_dr = ISNULL(ADMINISTRASI_DR, 0)
	FROM KWT_PARAMETER

	SELECT 
		b.KODE_BLOK,
		
		(SELECT NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE KODE_BLOK = b.KODE_BLOK) AS NAMA_PELANGGAN,
		SUM(b.JUMLAH_PERIODE) AS JUMLAH_PERIODE,
	
		MIN(b.PERIODE_AWAL) AS PERIODE_AWAL,
		MAX(b.PERIODE_AKHIR) AS PERIODE_AKHIR,

		SUM(
			b.JUMLAH_IPL + b.DENDA - b.DISKON_RUPIAH_IPL + 
			CASE TRX
				WHEN '3' THEN @adm_db
				WHEN '6' THEN @adm_dr 
			END
		) AS JUMLAH_BAYAR
	FROM 
		KWT_PEMBAYARAN_AI b 
		LEFT JOIN KWT_PELANGGAN p ON b.KODE_BLOK = p.KODE_BLOK
	WHERE 
		$where_trx_deposit AND 
		p.INFO_TAGIHAN = '1' AND 
		b.STATUS_BAYAR IS NULL AND
		(CAST(DATEDIFF(MONTH, dbo.PTDF(b.PERIODE), @now) AS INT)) >= 2
		$query_search
	GROUP BY b.KODE_BLOK
	ORDER BY b.KODE_BLOK
	";
	$obj = $conn->Execute($query);
	
	$i = 1;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['KODE_BLOK']);
		?>
		<tr id="<?php echo $id; ?>"> 
			<td width="30" class="text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AWAL']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	
	$('#total-data').html('<?php echo $total_data; ?>');
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
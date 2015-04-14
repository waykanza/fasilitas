<?php
require_once('../../../../config/config.php');
$conn = conn();

$no_pelanggan = (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
$from_master = (isset($_REQUEST['from_master'])) ? clean($_REQUEST['from_master']) : '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	/* -- SHORTCUT -- */
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#print').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'air_ipl/pembayaran/daftar_piutang_cetak.php?id=<?php echo base64_encode($no_pelanggan); ?>';
		open_print(url, '2');
		
		return false;
	});
	
	$('#close').on('click', function(e) {
		parent.window.focus();
		parent.window.popup.close();
	});
	
	$('#bayar').on('click', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length,
			jenis_bayar	= $('#jenis_bayar').val();
			
		if (checked < 1) {
			alert('Pilih data yang akan dibayar.');
			return false;
		} else if (jenis_bayar == '') {
			alert('Pilih jenis pembayaran.');
			return false;
		}
		
		var url		= base_pembayaran + 'air_ipl/pembayaran/pembayaran_proses_bulk.php',
			data	= $('#form').serialize(),
			cek		= false;
		
		$.post(url, data, function(data) {
			
			alert(data.msg);
			
			if (data.error == false)
			{
				var url = base_pembayaran + 'air_ipl/pembayaran/cetak_kwitansi_bulk.php?id=' + data.list_id;
				open_print(url, '2');
				location.reload();
			}
			
		}, 'json');
		
		return false;
	});
	
	t_strip('.t-data');
	
});
</script>
</head>
<body class="popup">

<form name="form" id="form" method="post">

<table class="t-data">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">STATUS</th>
	<th colspan="3">PERIODE</th>
	<th colspan="3">STAND METER</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">ABONEMEN</th>
	<th rowspan="2">IPL</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">ADMINISTRASI</th>
	<th rowspan="2">DISKON</th>
	<th rowspan="2">JUMLAH</th>
	<?php if ($from_master == '') { ?>
	<th rowspan="2"><input type="checkbox" id="cb_all"></th>
	<?php } ?>
</tr>
<tr>
	<th>AWAL</th>
	<th>AKHIR</th>
	<th>JML.</th>
	<th>AKHIR</th>
	<th>LALU</th>
	<th>PAKAI</th>
</tr>

<?php
	$query = "
	DECLARE 
	@adm_kv INT,
	@adm_bg INT,
	@adm_hn INT,
	@adm_rv INT
	
	SELECT TOP 1 
	@adm_kv = ISNULL(ADMINISTRASI_KV, 0) ,
	@adm_bg = ISNULL(ADMINISTRASI_BG, 0) ,
	@adm_hn = ISNULL(ADMINISTRASI_HN, 0) ,
	@adm_rv = ISNULL(ADMINISTRASI_RV, 0)
	FROM KWT_PARAMETER
	
	SELECT 
		b.ID_PEMBAYARAN,
		b.STATUS_BLOK,
		dbo.PTPS(b.PERIODE) AS PERIODE,
		dbo.PTPS(b.PERIODE_AKHIR) AS PERIODE_AKHIR,
		b.JUMLAH_PERIODE,
		(b.STAND_AKHIR + b.STAND_ANGKAT + b.STAND_MIN_PAKAI) AS STAND_AKHIR,
		b.STAND_LALU,
		(b.STAND_AKHIR + b.STAND_ANGKAT + b.STAND_MIN_PAKAI - b.STAND_LALU) AS PAKAI,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.JUMLAH_IPL,
		b.DENDA,
		(
			CASE TRX
				WHEN '1' THEN @adm_kv
				WHEN '2' THEN @adm_bg 
				WHEN '4' THEN @adm_hn
				WHEN '5' THEN @adm_rv
			END
		) AS ADMINISTRASI,
		(b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL) AS DISKON,
		(
			(
				b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA +  
				CASE TRX
					WHEN '1' THEN @adm_kv
					WHEN '2' THEN @adm_bg 
					WHEN '4' THEN @adm_hn
					WHEN '5' THEN @adm_rv
				END
			) - (b.DISKON_RUPIAH_AIR + b.DISKON_RUPIAH_IPL)
		) AS JUMLAH
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND  
		b.STATUS_BAYAR IS NULL AND
		b.NO_PELANGGAN = '$no_pelanggan'
	ORDER BY PERIODE ASC
	";
	$obj = $conn->Execute($query);

	$i = 1;
	$sum_jumlah_air = 0;
	$sum_abonemen = 0;
	$sum_jumlah_ipl = 0;
	$sum_denda = 0;
	$sum_administrasi = 0;
	$sum_diskon = 0;
	$sum_jumlah = 0;
	
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_AKHIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_AKHIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_LALU']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PAKAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH']); ?></td>
			<?php if ($from_master == '') { ?>
			<td class="text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>
			<?php } ?>
		</tr>
		<?php
		
		$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen		+= $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_administrasi	+= $obj->fields['ADMINISTRASI'];
		$sum_diskon			+= $obj->fields['DISKON'];
		$sum_jumlah			+= $obj->fields['JUMLAH'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="8">TOTAL .......</td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_abonemen); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_administrasi); ?></td>
		<td><?php echo to_money($sum_diskon); ?></td>
		<td><?php echo to_money($sum_jumlah); ?></td>
		<?php if ($from_master == '') { ?>
		<td></td>
		<?php } ?>
	</tr>
	</tfoot>
</table>

<table class="t-popup">
<tr>
	<td class="td-action">
		<input type="button" id="print" value=" Cetak Daftar Piutang (Alt+P) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
	
	<?php if ($from_master == '') { ?>
	<td class="text-right">
		<select name="jenis_bayar" id="jenis_bayar">
			<option value=""> -- JENIS BAYAR -- </option>
			<option value="2"> K. DEBET </option>
			<option value="3"> K. KREDIT </option>
		</select>
		
		<input type="button" id="bayar" value=" Cetak Kwitansi ">
	</td> <?php } ?>
</tr>
</table>

</form>
</body>
</html>
<?php close($conn); ?>
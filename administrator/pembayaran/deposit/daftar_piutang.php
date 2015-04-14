<?php
require_once('../../../config/config.php');
die_login();
$conn = conn();
die_conn($conn);


$no_pelanggan = (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
$from_master = (isset($_REQUEST['from_master'])) ? clean($_REQUEST['from_master']) : '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	/* -- SHORTCUT -- */
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#print').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'deposit/daftar_piutang_cetak.php?id=<?php echo base64_encode($no_pelanggan); ?>';
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
			cara_bayar	= $('#cara_bayar').val();
			
		if (checked < 1) {
			alert('Pilih data yang akan dibayar.');
			return false;
		} else if (cara_bayar == '') {
			alert('Pilih jenis pembayaran.');
			return false;
		}
		
		var url		= base_pembayaran + 'deposit/pembayaran_proses_bulk.php',
			data	= $('#form').serialize(),
			cek		= false;
		
		$.post(url, data, function(data) {
			
			alert(data.msg);
			
			if (data.error == false) {
				var url = base_pembayaran + 'deposit/cetak_kwitansi.php?idp=' + data.list_idp;
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
	<th rowspan="2">DEPOSIT</th>
	<th rowspan="2">DENDA</th>
	<th rowspan="2">ADM</th>
	<th rowspan="2">DISKON</th>
	<th rowspan="2">JUMLAH</th>
	<?php if ($from_master == '') { ?>
	<th rowspan="2">KET.</th>
	<th rowspan="2"><input type="checkbox" id="cb_all"></th>
	<?php } ?>
</tr>
<tr>
	<th>AWAL</th>
	<th>AKHIR</th>
	<th>JML.</th>
</tr>

<?php
	$query = "
	DECLARE 
	@adm_dbg INT,
	@adm_drv INT
	
	SELECT TOP 1 
	@adm_dbg = ADM_DBG ,
	@adm_drv = ADM_DRV
	FROM KWT_PARAMETER
	
	SELECT 
		b.ID_PEMBAYARAN,
		b.STATUS_BLOK,
		dbo.PTPS(b.PERIODE_IPL_AWAL) AS PERIODE_IPL_AWAL,
		dbo.PTPS(b.PERIODE_IPL_AKHIR) AS PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
		b.JUMLAH_IPL,
		b.DENDA,
		b.DISKON_IPL AS DISKON,
		(
			CASE TRX
				WHEN $trx_dbg THEN @adm_dbg
				WHEN $trx_drv THEN @adm_drv 
			END
		) AS ADM,
		(
			(
				b.JUMLAH_IPL + b.DENDA + 
				(
					CASE TRX
						WHEN $trx_dbg THEN @adm_dbg
						WHEN $trx_drv THEN @adm_drv 
					END
				)
			) - b.DISKON_IPL
		) AS JUMLAH
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_deposit AND 
		b.STATUS_BAYAR = 0 AND
		b.NO_PELANGGAN = '$no_pelanggan'
	ORDER BY CAST(PERIODE_TAG AS INT) ASC
	";
	$obj = $conn->Execute($query);

	$i = 1;
	$sum_jumlah_ipl = 0;
	$sum_denda = 0;
	$sum_adm = 0;
	$sum_diskon = 0;
	$sum_jumlah = 0;
	
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_IPL_AWAL']; ?></td>
			<td class="text-center"><?php echo $obj->fields['PERIODE_IPL_AKHIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH']); ?></td>
			<?php if ($from_master == '') { ?>
			<td class="text-center"><textarea name="cb_ket_bayar[<?php echo $i; ?>]" rows="1"></textarea></td>
			<td class="text-center"><input type="checkbox" name="cb_data[<?php echo $i; ?>]" class="cb_data" value="<?php echo $id; ?>"></td>
			<?php } ?>
		</tr>
		<?php
		
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_adm			+= $obj->fields['ADM'];
		$sum_diskon			+= $obj->fields['DISKON'];
		$sum_jumlah			+= $obj->fields['JUMLAH'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="5">TOTAL .......</td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_diskon); ?></td>
		<td><?php echo to_money($sum_jumlah); ?></td>
		<?php if ($from_master == '') { ?>
		<td colspan="2"></td>
		<?php } ?>
	</tr>
	</tfoot>
</table>

<table class="t-popup">
<tr>
	<td class="td-action va-top">
		<input type="button" id="print" value=" Cetak Daftar Piutang (Alt+P) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
	
	<?php if ($from_master == '') { ?>
	<td class="text-right">
		<br>
		<table class="wauto f-right">
		<tr>
			<td width="120">CARA BAYAR</td><td width="10">:</td>
			<td width="190">
				<select name="cara_bayar" id="cara_bayar">
					<option value=""> -- CARA BAYAR -- </option>
					<option value="2"> K. DEBET </option>
					<option value="3"> K. KREDIT </option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>PEMBAYARAN VIA</td><td>:</td>
			<td>
				<select name="bayar_via" id="bayar_via">
					<option value=""> -- KODE BANK -- </option>
					<?php
					$obj = $conn->Execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
					while( ! $obj->EOF)
					{
						$ov = $obj->fields['KODE_BANK'];
						$on = $obj->fields['NAMA_BANK'];
						echo "<option value='$ov'> $on ($ov) </option>";
						$obj->movenext();
					}
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>TGL. BAYAR (BANK)</td><td>:</td>
			<td><input type="text" name="tgl_bayar_bank" id="tgl_bayar_bank" class="dd-mm-yyyy" size="13" value=""></td>
		</tr> 
		
		<tr>
			<td colspan="3"><input type="button" id="bayar" value=" Cetak Kwitansi "></td>
		</tr>
		</table>
	</td> 
	<?php } ?>
</tr>
</table>

</form>
</body>
</html>
<?php close($conn); ?>
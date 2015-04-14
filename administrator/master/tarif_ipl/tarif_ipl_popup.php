<?php
require_once('tarif_ipl_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#tarif_ipl').inputmask('numeric', { repeat: '8' });
	$('#nilai_deposit').inputmask('numeric', { repeat: '15' });
	$('#denda_standar_ipl').inputmask('percent');
	$('#denda_bisnis_ipl').inputmask('percent');
	$('#key_ipl').inputmask('varchar', { repeat: '13' });
	
	$('#kode_tipe').on('change', function(e) {
		e.preventDefault();
		var key_ipl = $('#kode_sk').val() + '-' + $(this).val();
		$('#key_ipl').val(key_ipl);
	});
	
	$('#status_blok').on('change', function(e) {
		e.preventDefault();
		var status_blok = $(this).val();
		$('#kode_tipe').load(base_master + 'opt_kode_tipe_ipl.php?status_blok=' + status_blok);
		
		var key_ipl = $('#kode_sk').val() + '-';
		$('#key_ipl').val(key_ipl);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'tarif_ipl/tarif_ipl_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				if (data.act == 'Simpan') {
					$('#reset').click();
				} else if (data.act == 'Ubah') {
					parent.loadData();
				}
			}
		}, 'json');
		
		return false;
	});
});
</script>
</head>
<body class="popup">

<div id="msg"><?php echo $msg; ?></div>

<form name="form" id="form" method="post">
<table class="t-popup wauto">
<tr>
	<td width="150">KODE</td>
	<td><input readonly="readonly" type="text" name="key_ipl" id="key_ipl" size="15" class="text-center" value="<?php echo $key_ipl; ?>"></td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="status_blok" id="status_blok" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="1" <?php echo is_checked('1', $status_blok); ?>> KAVLING KOSONG </option>
			<option value="2" <?php echo is_checked('2', $status_blok); ?>> MASA MEMBANGUN </option>
			<option value="4" <?php echo is_checked('4', $status_blok); ?>> HUNIAN </option>
			<option value="5" <?php echo is_checked('5', $status_blok); ?>> RENOVASI </option>
		</select>
	</td>
</tr>

<tr>
	<td>KATEGORI</td>
	<td>
		<select name="kode_tipe" id="kode_tipe">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_IPL WHERE STATUS_BLOK = '$status_blok' ORDER BY NAMA_TIPE ASC");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				$on = $obj->fields['NAMA_TIPE'];
				echo "<option value='$ov' ".is_selected($ov, $kode_tipe)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>

<tr>
	<td>TARIF</td>
	<td><input type="text" name="tarif_ipl" id="tarif_ipl" size="11" value="<?php echo $tarif_ipl; ?>"></td>
</tr>

<tr>
	<td>DENDA STANDAR</td>
	<td><input type="text" name="denda_standar_ipl" id="denda_standar_ipl" size="5" value="<?php echo $denda_standar_ipl; ?>"> %</td>
</tr>

<tr>
	<td>DENDA BISNIS</td>
	<td><input type="text" name="denda_bisnis_ipl" id="denda_bisnis_ipl" size="5" value="<?php echo $denda_bisnis_ipl; ?>"> %</td>
</tr>

<tr>
	<td>DEPOSIT</td>
	<td><input type="text" name="nilai_deposit" id="nilai_deposit" size="15" value="<?php echo $nilai_deposit; ?>"></td>
</tr>

<tr>
	<td class="va-top">KETERANGAN</td>
	<td><textarea name="keterangan" id="keterangan" rows="3" cols="45"><?php echo $keterangan; ?></textarea></td>
</tr>

<tr>
	<td></td>
	<td class="td-action">
		<input type="submit" id="save" value=" <?php echo $act; ?> (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="kode_sk" id="kode_sk" value="<?php echo $kode_sk; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
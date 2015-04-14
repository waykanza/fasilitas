<?php
require_once('periode_renovasi_proses.php');
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
	
	$('#jumlah_periode').inputmask('integer', { repeat: '2' });
	$('#nilai_deposit').inputmask('numeric', { repeat: '15' });

	$('#periode_awal').Zebra_DatePicker({
		format: 'm-Y'
	});
	
	$('#opt_deposit').on('change', function(e) {
		e.preventDefault();
		$('#nilai_deposit').val($(this).val());
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.location.reload();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_periode + 'periode_renovasi/periode_renovasi_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(result) {
			
			alert(result.msg);
			if (result.error == false)
			{
				parent.location.reload();
			}
			
		}, 'json');
		
		return false;
	});
});
</script>
</head>
<body class="popup">


<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="140">PERIODE MULAI</td>
	<td><input type="text" name="periode_awal" id="periode_awal" size="10" value="<?php echo $periode_awal; ?>"></td>
</tr>

<tr>
	<td>JML. PERIODE</td>
	<td><input type="text" name="jumlah_periode" id="jumlah_periode" size="5" class="text-center" value="<?php echo $jumlah_periode; ?>"></td>
</tr>

<tr>
	<td>DEPOSIT</td>
	<td>
		<input readonly="readonly" type="text" name="nilai_deposit" id="nilai_deposit" size="20" value="<?php echo $nilai_deposit; ?>">
		<select id="opt_deposit" name="opt_deposit">
			<option value=""></option>
			<option value="<?php echo $master_nilai_deposit; ?>" <?php if ($nilai_deposit > 0) { echo 'selected="selected"'; } ?>> DENGAN DEPOSIT </option>
			<option value="0" <?php echo is_selected('0', $nilai_deposit); ?>> TANPA DEPOSIT </option>
		</select>
	</td>
</tr>

<tr>
	<td class="va-top">KETERANGAN</td>
	<td><textarea name="keterangan" id="keterangan" rows="3" cols="44"><?php echo $keterangan; ?></textarea></td>
</tr>

<tr>
	<td></td>
	<td class="td-action">
		<input type="submit" id="save" value=" <?php echo $act; ?> ">
		<input type="reset" id="reset" value=" Reset ">
		<input type="button" id="close" value=" Tutup "></td>
	</td>
</tr>
</table>

<input type="hidden" name="idd" id="idd" value="<?php echo $idd; ?>">
<input type="hidden" name="kode_blok" id="kode_blok" value="<?php echo $kode_blok; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
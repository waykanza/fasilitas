<?php
require_once('lokasi_ksp_proses.php');
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
$(function() {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#tipe_lokasi').on('change', function(e) {
		e.preventDefault();
		var kode_lokasi = $('#kode_sk').val() + '-' + $(this).val();
		$('#kode_lokasi').val(kode_lokasi);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master_fa + 'lokasi_ksp/lokasi_ksp_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			if (data.error == true)
			{
				alert(data.msg);
			}
			else
			{
				if (data.act == 'Simpan')
				{
					alert(data.msg);
					$('#reset').click();
				}
				else if (data.act == 'Ubah')
				{
					alert(data.msg);
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
	<td width="120">KODE</td>
	<td><input readonly="readonly" type="text" name="kode_lokasi" id="kode_lokasi" size="6" value="<?php echo $kode_lokasi; ?>"></td>
</tr>
<tr>
	<td>TIPE LOKASI</td>
	<td>
		<select name="tipe_lokasi" id="tipe_lokasi">
			<option value=""> -- TIPE LOKASI -- </option>
			<option value="ID" <?php echo is_selected('ID', $tipe_lokasi); ?>> INDOOR </option>
			<option value="OD" <?php echo is_selected('OD', $tipe_lokasi); ?>> OUTDOOR </option>
		</select>
	</td>
</tr>
<tr>
	<td>DETAIL LOKASI</td>
	<td><textarea name="detail_lokasi" id="detail_lokasi" cols="44" rows="3"><?php echo $detail_lokasi; ?></textarea></td>
</tr>
<tr>
	<td></td>
	<td class="td-proses">
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
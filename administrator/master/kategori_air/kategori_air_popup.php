<?php
require_once('kategori_air_proses.php');
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
	
	$('#kode_tipe').inputmask('varchar', { repeat: '2' });
	$('#nama_tipe').inputmask('varchar', { repeat: '30' });
	$('#persen_asumsi_1, #persen_asumsi_2, #persen_asumsi_3, #persen_asumsi_4').inputmask('percent100');
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'kategori_air/kategori_air_proses.php',
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


<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="150">KODE</td>
	<td><input type="text" name="kode_tipe" id="kode_tipe" size="3" value="<?php echo $kode_tipe; ?>"></td>
</tr>

<tr>
	<td>KATEGORI AIR</td>
	<td><input type="text" name="nama_tipe" id="nama_tipe" size="30" value="<?php echo $nama_tipe; ?>"></td>
</tr>

<tr>
	<td>GOLONGAN</td>
	<td>
		<select name="golongan" id="golongan">
			<option value=""> -- GOLONGAN -- </option>
			<option value="0" <?php echo is_selected('0', $golongan); ?>> STANDAR </option>
			<option value="1" <?php echo is_selected('1', $golongan); ?>> BISNIS </option>
		</select>
	</td>
</tr>

<tr>
	<td>BLOK</td>
	<td>
		<table style="width:320px;">
		<tr><th>1</th><th>2</th><th>3</th><th>4</th></tr>
		<tr>
			<th><input type="text" name="persen_asumsi_1" id="persen_asumsi_1" size="7" value="<?php echo $persen_asumsi_1; ?>"></th>
			<th><input type="text" name="persen_asumsi_2" id="persen_asumsi_2" size="7" value="<?php echo $persen_asumsi_2; ?>"></th>
			<th><input type="text" name="persen_asumsi_3" id="persen_asumsi_3" size="7" value="<?php echo $persen_asumsi_3; ?>"></th>
			<th><input type="text" name="persen_asumsi_4" id="persen_asumsi_4" size="7" value="<?php echo $persen_asumsi_4; ?>"></th>
		</tr>
		</table>
	</td>
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

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
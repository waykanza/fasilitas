<?php
require_once('pelanggan_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#no_va').inputmask('varchar', { repeat: '15' });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'fasilitas/pelanggan/pelanggan_proses.php',
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
	<td width="100">NO. VA</td>
	<td><input type="text" name="no_va" id="no_va" size="10" value="<?php echo $no_va; ?>"></td>
</tr>
<tr>
	<td width="100">NAMA PELANGGAN</td>
	<td><input type="text" name="nama" id="nama" size="50" value="<?php echo $nama; ?>"></td>
</tr>
<tr>
	<td width="100">KODE BLOK</td>
	<td><input type="text" name="kode_blok" id="kode_blok" size="50" value="<?php echo $kode_blok; ?>"></td>
</tr>
<tr>
	<td width="100">NO. TELEPON</td>
	<td><input type="text" name="no_telp" id="no_telp" size="50" value="<?php echo $no_telp; ?>"></td>
</tr>
<tr>
	<td width="100">NO. HP</td>
	<td><input type="text" name="no_hp" id="no_hp" size="50" value="<?php echo $no_hp; ?>"></td>
</tr>
<tr>
	<td width="100">ALAMAT</td>
	<td><input type="text" name="alamat" id="alamat" size="50" value="<?php echo $alamat; ?>"></td>
</tr>
<tr>
	<td width="100">KETERANGAN</td>
	<td><input type="text" name="keterangan" id="keterangan" size="50" value="<?php echo $keterangan; ?>"></td>
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
<?php close($conn); ?>
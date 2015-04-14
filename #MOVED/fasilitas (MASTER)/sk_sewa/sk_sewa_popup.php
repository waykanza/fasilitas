<?php
require_once('sk_sewa_proses.php');
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
	
	$('#tgl_sk').Zebra_DatePicker({
		format: 'd-m-Y',
		pair: $('#tgl_berlaku')
	});
	
	$('#tgl_berlaku').Zebra_DatePicker({
		format: 'd-m-Y'
	});
	
	$('#kode_sk').inputmask('varchar', { repeat: '3' });
	$('#no_sk').inputmask('varchar', { repeat: '50' });
	$('#pembuat').inputmask('varchar', { repeat: '50' });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master_fa + 'sk_sewa/sk_sewa_proses.php',
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

<div id="msg"></div>

<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="100">KODE</td>
	<td><input type="text" name="kode_sk" id="kode_sk" size="3" value="<?php echo $kode_sk; ?>"></td>
</tr>
<tr>
	<td>NO SK</td>
	<td><input type="text" name="no_sk" id="no_sk" size="45" value="<?php echo $no_sk; ?>"></td>
</tr>
<tr>
	<td>TANGGAL SK</td>
	<td><input type="text" name="tgl_sk" id="tgl_sk" size="13" value="<?php echo $tgl_sk; ?>"></td>
</tr>
<tr>
	<td>TANGGAL BERLAKU</td>
	<td><input type="text" name="tgl_berlaku" id="tgl_berlaku" size="13" value="<?php echo $tgl_berlaku; ?>"></td>
</tr>
<tr>
	<td>PEMBUAT</td>
	<td><input type="text" name="pembuat" id="pembuat" size="45" value="<?php echo $pembuat; ?>"></td>
</tr>
<tr>
	<td>KETERANGAN</td>
	<td><textarea name="keterangan" id="keterangan" rows="3" cols="45"><?php echo $keterangan; ?></textarea></td>
</tr>
<tr>
	<td>STATUS</td>
	<td>
		<input type="radio" name="status_sk" id="ssa" value="1" <?php echo is_checked('1', $status_sk); ?>> <label for="ssa">AKTIF</label>
		<input type="radio" name="status_sk" id="ssta" value="0" <?php echo is_checked('0', $status_sk); ?>> <label for="ssta">TIKDAK AKTIF</label>
	</td>
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

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
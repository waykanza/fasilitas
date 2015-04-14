<?php
require_once('diskon_proses.php');
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
	
	$('#periode_awal').Zebra_DatePicker({
		format: 'm-Y',
		pair: $('#periode_akhir')
	});
	
	$('#periode_akhir').Zebra_DatePicker({
		format: 'm-Y'
	});
	
	$('#kode_blok').inputmask('varchar', { repeat: '15' });
	$('#diskon_air_nilai, #diskon_ipl_nilai').inputmask('numericDecimal', { 
		radixPoint: '*', 
		integerDigits: 10, 
		fractionalDigits: 0, 
	});
	$('#diskon_air_persen, #diskon_ipl_persen').inputmask('percent100');
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'diskon/diskon_proses.php',
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
	<td width="120">KODE BLOK</td>
	<td><input type="text" name="kode_blok" id="kode_blok" size="20" value="<?php echo $kode_blok; ?>"></td>
</tr>
<tr>
	<td>PERIODE AWAL</td>
	<td><input type="text" name="periode_awal" id="periode_awal" size="10" value="<?php echo $periode_awal; ?>"></td>
</tr>
<tr>
	<td>PERIODE AKHIR</td>
	<td><input type="text" name="periode_akhir" id="periode_akhir" size="10" value="<?php echo $periode_akhir; ?>"></td>
</tr>
<tr>
	<td>DISKON AIR (Rp.)</td>
	<td><input type="text" name="diskon_air_nilai" id="diskon_air_nilai" size="20" value="<?php echo $diskon_air_nilai; ?>"></td>
</tr>
<tr>
	<td>DISKON IPL (Rp.)</td>
	<td><input type="text" name="diskon_ipl_nilai" id="diskon_ipl_nilai" size="20" value="<?php echo $diskon_ipl_nilai; ?>"></td>
</tr>
<tr>
	<td>DISKON AIR</td>
	<td><input type="text" name="diskon_air_persen" id="diskon_air_persen" size="7" value="<?php echo $diskon_air_persen; ?>"> %</td>
</tr>
<tr>
	<td>DISKON IPL</td>
	<td><input type="text" name="diskon_ipl_persen" id="diskon_ipl_persen" size="7" value="<?php echo $diskon_ipl_persen; ?>"> %</td>
</tr>
<tr>
	<td>KETERANGAN</td>
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

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
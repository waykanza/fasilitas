<?php
require_once('../../../../config/config.php');

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';

$conn = conn();
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
	
	key('alt+c', function(e) { e.preventDefault(); $('#find_pelanggan').trigger('click'); });
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#stand_akhir, #stand_lalu').inputmask('numeric', { repeat: '10' });
	$('#pemakaian').inputmask('numeric', { repeat: '10', allowMinus: true });
	
	$('#stand_akhir, #stand_lalu').on('change', function(e) {
		e.preventDefault();
		
		var stand_akhir	= $('#stand_akhir').val(),
			stand_lalu	= $('#stand_lalu').val();

		stand_akhir = stand_akhir.replace(/[^0-9.]/g, '');
		stand_lalu = stand_lalu.replace(/[^0-9.]/g, '');
		
		stand_akhir = (stand_akhir == '') ? 0 : parseFloat(stand_akhir);
		stand_lalu = (stand_lalu == '') ? 0 : parseFloat(stand_lalu);
		
		var pemakaian = (stand_akhir - stand_lalu);
		
		$('#pemakaian').val(pemakaian);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		parent.window.focus();
		parent.window.popup.close();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_pembayaran + 'air_ipl/pelanggan_baru/pelanggan_baru_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				$('#reset').trigger('click');
				$('#td-nama_pelanggan').empty();
				$('#td-nama_sektor').empty();
				$('#td-nama_cluster').empty();
				$('#td-kode_blok').empty();
				$('#td-key_air').empty();
				$('#td-key_ipl').empty();
			}
		}, 'json');
		
		return false;
	});
	
	$('#find_pelanggan').on('click', function(e) {
		e.preventDefault();
		var periode = $('#periode').val(),
			url = base_pembayaran + 'air_ipl/pelanggan_baru/find_pelanggan.php?periode=' + periode;
		if (periode == '')
		{
			return false;
		}
		
		setPopup('Cari Pelanggan', url, winWidth-100, 500);
		
		return false;
	});
	
});
</script>
</head>
<body class="popup">

<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="150">PERIODE</td><td width="7">:</td>
	<td><?php echo fm_periode($periode); ?></td>
</tr>
<tr>
	<td>NO. PELANGGAN</td><td>:</td>
	<td>
		<input readonly="readonly" type="text" name="no_pelanggan" id="no_pelanggan" value="">
		<input type="button" id="find_pelanggan" value=" Cari (Alt+C) ">
	</td>
</tr>
<tr>
	<td>NAMA PELANGGAN</td><td>:</td>
	<td id="td-nama_pelanggan"></td>
</tr>
<tr>
	<td>SEKTOR</td><td>:</td>
	<td id="td-nama_sektor"></td>
</tr>
<tr>
	<td>CLUSTER</td><td>:</td>
	<td id="td-nama_cluster"></td>
</tr>
<tr>
	<td>KODE BLOK</td><td>:</td>
	<td id="td-kode_blok"></td>
</tr>
<tr>
	<td>KODE TARIF AIR</td><td>:</td>
	<td id="td-key_air"></td>
</tr>
<tr>
	<td>KODE TARIF IPL</td><td>:</td>
	<td id="td-key_ipl"></td>
</tr>
</table>

<br><hr><br>

<table class="t-popup wauto">
<tr>
	<td width="100">STAND AKHIR</td><td width="7">:</td>
	<td><input type="text" name="stand_akhir" id="stand_akhir" size="10" value=""></td>
</tr>
<tr>
	<td>STAND LALU</td><td>:</td>
	<td><input type="text" name="stand_lalu" id="stand_lalu" size="10" value=""></td>
</tr>
<tr>
	<td>PEMAKAIAN</td><td>:</td>
	<td><input readonly="readonly" type="text" id="pemakaian" size="10" value=""></td>
</tr>
</table>

<table class="t-popup">
<tr>
	<td></td>
	<td colspan="5" class="td-action">
		<input type="button" id="save" value=" Simpan (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="periode" id="periode" value="<?php echo $periode; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
<?php
require_once('../../../../config/config.php');
$conn = conn();

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
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
	
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('click', '#next_page', function(e) {
		e.preventDefault();
		var total_page = parseInt($('#total_page').val()),
			page_num = parseInt($('.page_num').val()) + 1;
		if (page_num <= total_page)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	$(document).on('click', '#prev_page', function(e) {
		e.preventDefault();
		var page_num = parseInt($('.page_num').val()) - 1;
		if (page_num > 0)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	$(document).on('click', 'tr.onclick', function(e) {
		e.preventDefault();
		var tr_no_pelanggan = $(this).attr('id'),
			url = base_pembayaran + 'air_ipl/pelanggan_baru/get_data_pelanggan.php';
		
		$.post(url, {no_pelanggan: tr_no_pelanggan}, function(data) {
			
			parent.jQuery('#no_pelanggan').val(data.no_pelanggan);
			parent.jQuery('#trx').val(data.status_blok);
			parent.jQuery('#td-nama_pelanggan').html(data.nama_pelanggan);
			parent.jQuery('#td-nama_sektor').html(data.nama_sektor);
			parent.jQuery('#td-nama_cluster').html(data.nama_cluster);
			parent.jQuery('#td-kode_blok').html(data.kode_blok);
			parent.jQuery('#td-key_air').html(data.key_air);
			parent.jQuery('#td-key_ipl').html(data.key_ipl);
			
			$('#close').trigger('click');
			
		}, 'json');
		
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	$('#close').on('click', function(e) {
		parent.window.focus();
		parent.window.popup.close();
	});
	
	loadData();
});

function loadData()
{
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_pembayaran + 'air_ipl/pelanggan_baru/find_pelanggan_load.php', data);
	
	return false;
}
</script>
</head>
<body class="popup">


<form name="form" id="form" method="post">
<table class="t-popup wauto">
<tr>
	<td width="100">PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KODE_BLOK"> KODE BLOK </option>
			<option value="NO_PELANGGAN"> NO. PELANGGAN </option>
			<option value="NAMA_PELANGGAN"> NAMA PELANGGAN </option>
			<option value="KEY_AIR"> KEY AIR </option>
			<option value="KEY_IPL"> KEY IPL </option>
			<option value="KETERANGAN"> KETERANGAN </option>
		</select>
		<input type="text" name="search1" id="search1" size="30" class="apply" value="">
	</td>
</tr>
<tr>
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class="apply text-center" value="20">
		<input type="button" id="apply" value=" Apply (Enter) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>

<tr>
	<td>TOTAL DATA</td>
	<td id="total-data"></td>
</tr>
</table>

<div id="t-detail"></div>
<input type="hidden" name="periode" id="periode" value="<?php echo $periode; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
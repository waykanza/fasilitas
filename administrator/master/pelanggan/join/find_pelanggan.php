<?php
require_once('../../../../config/config.php');
$conn = conn();

$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

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
		var id = $('#id').val(),
			kode_blok = $(this).data('kode-blok'),
			luas_kavling = $(this).data('luas-kavling'),
			luas_bangunan = $(this).data('luas-bangunan');
		
		parent.jQuery('input[name="ref_kode_blok['+id+']"]').val(kode_blok);
		parent.jQuery('input[name="ref_luas_kavling['+id+']"]').val(luas_kavling);
		parent.jQuery('input[name="ref_luas_bangunan['+id+']"]').val(luas_bangunan);
		parent.cacl('lk');
		parent.cacl('lb');
		parent.window.focus();
		parent.window.popup.close();
		
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
	jQuery('#t-detail').load(base_master + 'pelanggan/split/find_pelanggan_load.php', data);
	
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
<input type="hidden" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
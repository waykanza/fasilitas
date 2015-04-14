<?php
require_once('../../../../config/config.php');
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
/* lookup */

jQuery(function($) {
	
	$('#ref_luas_kavling, #ref_luas_bangunan, .lk, .lb, #total_lk, #total_lb').inputmask('numericDecimal', { integerDigits: '7' });
	
	$('#tipe_air').on('change', function(e) {
		e.preventDefault();
		$('#key_air').val('');
		return false;
	});
	
	$('#form').delegate('.lk', 'change', function(e) {
		e.preventDefault();
		cacl('lk');
		return false;
	});
	$('#form').delegate('.lb', 'change', function(e) {
		e.preventDefault();
		cacl('lb');
		return false;
	});
	
	/* ACTION */
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		cacl('lk');
		cacl('lb');
		var ref_luas_kavling = to_decimal($('#ref_luas_kavling').val()),
			ref_luas_bangunan = to_decimal($('#ref_luas_bangunan').val()),
			total_lk = to_decimal($('#total_lk').val()),
			total_lb = to_decimal($('#total_lb').val());
		
		if (total_lk != ref_luas_kavling) {
			alert('Total luas kavling tidak sama dengan ref. luas kavling.');
			return false;
		} else if (total_lb != ref_luas_bangunan) {
			alert('Total luas bangunan tidak sama dengan ref. luas bangunan.');
			return false;
		}
		
		var url		= base_master + 'pelanggan/split/split_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				parent.loadData();
			}
		}, 'json');
		
		return false;
	});
});

function cacl(luas) {
	var sum_l = 0;
	
	jQuery('.' + luas).each(function() {
		var l = to_decimal(jQuery(this).val());
		sum_l += l;
	});
	jQuery('#total_' + luas).val(sum_l);
}
	
function get_blok()
{
	var url = base_master + 'pelanggan/split/find_pelanggan.php';
	setPopup('Kode Blok', url, winWidth-100, winHeight-100);
	
	return false;
}

function add_blok()
{
	var max = Number(jQuery('#max').val());
	id = max + 1;
	jQuery('#max').val(id);
	
	jQuery('' + 
	'<tr id="tr-ref-'+id+'">' +
		'<td></td>' +
		'<td>:</td>' +
		'<td><span class="ref_kode_blok"></span>&nbsp;<input type="text" name="kode_blok['+id+']" size="3" class="text-center" value=""></td> ' +
		'<td class="text-center"><input type="text" name="luas_kavling['+id+']" class="lk" size="10" value="0"></td> ' +
		'<td class="text-center"><input type="text" name="luas_bangunan['+id+']" class="lb" size="10" value="0"></td> ' +
		'<td class="text-center"><input type="button" value=" X " onclick="del_blok(\''+id+'\')"></td> ' +
	'</tr>' +
	'').insertAfter('#tr-ref');
	
	jQuery('.lk, .lb').inputmask('numericDecimal', { integerDigits: '7' });
	
	jQuery('.ref_kode_blok').html(jQuery('#ref_kode_blok').val());
	
	cacl('lk');
	cacl('lb');
	
	return false;
}

function del_blok(id)
{
	var max = Number(jQuery('#max').val());
	jQuery('#max').val(max - 1);
	
	jQuery('#tr-ref-' + id).remove();
	
	cacl('lk');
	cacl('lb');
	
	return false;
}
</script>
</head>
<body class="popup">

<form name="form" id="form" method="post">
<table class="t-popup wauto">
<tr>
	<td width="150">REF. KODE BLOK</td>
	<td width="10">:</td>
	<td colspan="6"> 
		<input readonly="readonly" type="text" name="ref_kode_blok" id="ref_kode_blok" size="30">
		<input type="button" value=" ... " onclick="get_blok()">
	</td>
</tr>
<tr>
	<td>REF. LUAS KAVLING</td>
	<td>:</td>
	<td colspan="6"> 
		<input readonly="readonly" type="text" id="ref_luas_kavling" size="10">
	</td>
</tr>
<tr>
	<td>REF. LUAS BANGUNAN</td>
	<td>:</td>
	<td colspan="6"> 
		<input readonly="readonly" type="text" id="ref_luas_bangunan" size="10">
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
</tr>

<tr>
	<td></td>
	<td></td>
	<td class="text-center"><b>KODE BLOK</b></td>
	<td class="text-center"><b>LUAS KAVL.</b></td>
	<td class="text-center"><b>LUAS BANG.</b></td>
</tr>

<tr>
	<td>KODE BLOK (BARU)</td>
	<td>:</td>
	<td><span class="ref_kode_blok"></span>&nbsp;<input type="text" name="kode_blok[1]" size="3" class="text-center" value=""></td>
	<td class="text-center"><input type="text" name="luas_kavling[1]" class="lk" size="10" value="0"></td>
	<td class="text-center"><input type="text" name="luas_bangunan[1]" class="lb" size="10" value="0"></td>
</tr>
<tr id="tr-ref">
	<td></td>
	<td>:</td>
	<td><span class="ref_kode_blok"></span>&nbsp;<input type="text" name="kode_blok[2]" size="3" class="text-center" value=""></td>
	<td class="text-center"><input type="text" name="luas_kavling[2]" class="lk" size="10" value="0"></td>
	<td class="text-center"><input type="text" name="luas_bangunan[2]" class="lb" size="10" value="0"></td>
	<td class="text-center"><input type="button" value=" + " onclick="add_blok()"></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td></td>
	<td class="text-center"><input readonly="readonly" type="text" id="total_lk" size="10"></td>
	<td class="text-center"><input readonly="readonly" type="text" id="total_lb" size="10"></td>
	<td></td>
</tr>
</table>

<input type="hidden" id="max" value="2">

<table class="t-popup">
<tr>
	<td class="td-action">
		<input type="submit" id="save" value=" Simpan ">
		<input type="reset" id="reset" value=" Reset ">
		<input type="button" id="close" value=" Tutup "></td>
	</td>
</tr>
</table>
</form>

</body>
</html>
<?php close($conn); ?>
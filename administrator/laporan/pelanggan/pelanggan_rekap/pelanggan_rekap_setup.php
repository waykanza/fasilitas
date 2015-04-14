<div class="title-page">LAPORAN REKAP PELANGGAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="130">PERIODE</td>
	<td><input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value=""></td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="1"> KAVLING KOSONG </option>
			<option value="2"> MASA MEMBANGUN </option>
			<option value="4"> HUNIAN </option>
			<option value="5"> RENOVASI </option>
		</select>
	</td>
<tr>

<tr>
	<td></td>
	<td><input type="button" id="apply" value=" Apply (Enter) "></td>
</tr>

</table>

<script type="text/javascript">
jQuery(function($) {
	
	/* -- FILTER -- */
	
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
	
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		if (jQuery('#periode').val() == '') 
		{
			alert('Masukkan periode!');
			jQuery('#periode').focus();
			return false;
		}
		
		location.href = base_laporan + 'pelanggan/pelanggan_rekap/pelanggan_rekap_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#periode').val() == '') 
		{
			alert('Masukkan periode!');
			jQuery('#periode').focus();
			return false;
		}
		
		var url = base_laporan + 'pelanggan/pelanggan_rekap/pelanggan_rekap_print.php?' + $('#form').serialize();
		open_print(url)
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
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+x', function(e) { e.preventDefault(); $('#excel').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#periode').val() == '') 
	{
		alert('Masukkan periode!');
		jQuery('#periode').focus();
		return false;
	}
	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'pelanggan/pelanggan_rekap/pelanggan_rekap_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
<div class="title-page">LAPORAN DAFTAR PELANGGAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="130">STATUS BLOK</td>
	<td>
		<select name="status_blok" id="status_blok" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="1"> KAVLING KOSONG </option>
			<option value="2"> MASA MEMBANGUN </option>
			<option value="4"> HUNIAN </option>
			<option value="5"> RENOVASI </option>
		</select>
	</td>
<tr>

<tr>
	<td>JUMLAH BARIS</td>
	<td colspan="2">
		<input type="text" name="per_page" size="3" id="per_page" class="apply text-center" value="20">
		<input type="button" id="apply" value=" Apply (Enter) ">
	</td>
</tr>

<tr>
	<td>TOTAL DATA</td>
	<td id="total-data"></td>
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
		location.href = base_laporan + 'pelanggan/pelanggan_daftar/pelanggan_daftar_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		
		var url = base_laporan + 'pelanggan/pelanggan_daftar/pelanggan_daftar_print.php?' + $('#form').serialize();
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
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'pelanggan/pelanggan_daftar/pelanggan_daftar_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
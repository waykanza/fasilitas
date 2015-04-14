<div class="title-page">PEMBAYARAN MEDIA PROMOSI</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">PENCARIAN</td>
	<td>
		<select name="field1" id="field1">
			<option value="NAMA_PELANGGAN"> NAMA </option>
		</select>
		<input type="text" name="search1" id="search1" class="apply" value="">
	</td>
</tr>

<tr>
	<td>STATUS BAYAR</td>
	<td>
		<input type="radio" name="status_bayar" id="sba" checked="checked" value=""> <label for="sba">SEMUA</label>
		<input type="radio" name="status_bayar" id="sbs" value="1"> <label for="sbs">SUDAH</label>
		<input type="radio" name="status_bayar" id="sbb" value="2"> <label for="sbb">BELUM</label>
	</td>
</tr>

<tr>
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class=" apply text-center" value="20">
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
	
	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		
		showPopup(id);
		return false;
	});
	
		
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	$('#nama_pelanggan').inputmask('varchar', { repeat: '40' });
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_pembayaran_fasilitas + 'mp/mp_load.php', data);
	
	return false;
}

function showPopup(id)
{
	var url = base_pembayaran_fasilitas + 'mp/pembayaran_mp_popup.php?id=' + id;
	setPopup('Pembayaran Media Promosi', url, 800, 600);
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
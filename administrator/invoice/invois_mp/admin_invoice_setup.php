
<div class="title-page">CETAK INVOICE MEDIA PROMOSI</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">PENCARIAN</td>
	<td>
		<select name="field" id="field">
			<option value="NAMA_PELANGGAN"> NAMA </option>
			<option value="NO_PELANGGAN"> NO VIRTUAL ACCOUNT </option>
			<option value="KODE_BLOK"> KODE BLOK </option>
		</select>
		<input type="text" name="search" id="search" class="apply" value="">
	</td>
</tr>
<tr>
	<td>PERIODE</td>
	<td><input type="text" name = 'periode' id= 'periode' class="mm-yyyy" /></td>
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
	

	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		showPopup(id);
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
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_invoice + 'invois_mp/invoice_load.php',data);
	
	return false;
}

function showPopup(id)
{
	var url = base_invoice + 'invois_mp/admin_invoice_popup.php?id=' + id;
	setPopup('Ubah Data Pelanggan', url, winWidth-100, winHeight-100);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
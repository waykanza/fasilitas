<div class="title-page">KATEGORI AIR</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="100">PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KODE_TIPE"> KODE </option>
			<option value="NAMA_TIPE"> KATEGORI AIR </option>
			<option value="PERSEN_ASUMSI_1"> ASUMSI 1 (0-10 m&sup3;)</option>
			<option value="PERSEN_ASUMSI_2"> ASUMSI 2 (11-20 m&sup3;) </option>
			<option value="PERSEN_ASUMSI_3"> ASUMSI 3 (21-40 m&sup3;) </option>
			<option value="PERSEN_ASUMSI_4"> ASUMSI 4 (> 40 m&sup3;) </option>
		</select>
		<select name="opr1" id="opr1" class="wauto" style="display:none;">
			<option value="="> = </option>
			<option value=">="> >= </option>
			<option value="<="> <= </option>
		</select>
		<input type="text" name="search1" id="search1" size="15" class="apply" value="">
	</td>
</tr>

<tr>
	<td>FILTER</td>
	<td>
		<select name="golongan" id="golongan">
			<option value=""> -- GOLONGAN -- </option>
			<option value="0"> STANDAR </option>
			<option value="1"> BISNIS </option>
		</select>
	</td>
</tr>

<tr>
	<td>JUMLAH BARIS</td>
	<td>
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
	$('#field1').on('change', function(e) {
		e.preventDefault();
		if ($(this).val() == 'KODE_TIPE' || $(this).val() == 'NAMA_TIPE')
		{
			$('#opr1').hide();
			$('#search1').inputmask('remove');
		}
		else
		{
			$('#opr1').show();
			$('#search1').inputmask('numericDecimal');
		}
	});
	
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
	
	$(document).on('click', '#tambah', function(e) {
		e.preventDefault();
		showPopup('Simpan', '');
		return false;
	});
	
	$(document).on('click', '#hapus', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length;
		if (checked < 1)
		{
			alert('Pilih data yang akan dihapus.');
		}
		else if (confirm('Record akan dihapus secara permanent!'))
		{
			deleteData();
		}
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
		showPopup('Ubah', id);
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+n', function(e) { e.preventDefault(); $('#tambah').trigger('click'); });
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	/* -- VALIDATION -- */
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_master + 'kategori_air/kategori_air_load.php', data);
	
	return false;
}

function showPopup(act, id)
{
	var url =	base_master + 'kategori_air/kategori_air_popup.php' +
				'?act=' + act +
				'&id=' + id,
		title	= (act == 'Simpan') ? 'Tambah' : act;
	
	setPopup(title + ' Kategori Air', url, 500, 350);
	
	return false;
}

function deleteData()
{
	var url		= base_master + 'kategori_air/kategori_air_proses.php',
		data	= jQuery('#form').serializeArray();
	data.push({ name: 'act', value: 'delete' });
	
	jQuery.post(url, data, function(result) {
		var list_id = result.act.join(', #');
		jQuery('#' + list_id).remove();
		alert(result.msg);
		
	}, 'json');

	return false;
}
</script>

<div id="t-detail"></div>
</form>
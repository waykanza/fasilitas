<div class="title-page">TARIF AIR</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">NO. SK AIR</td>
	<td>
		<select name="kode_sk" id="kode_sk" class="wauto">
			<option value=""> -- NO. SK AIR -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_SK, NO_SK FROM KWT_SK_AIR ORDER BY CAST(KODE_SK AS INT) DESC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SK'];
				$on = $obj->fields['NO_SK'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>

<tr>
	<td>PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KEY_AIR"> KEY# </option>
			<option value="KETERANGAN"> KETERANGAN </option>
			<option value="ABONEMEN"> ABONEMEN </option>
			<option value="STAND_MIN_PAKAI"> MIN. PAKAI </option>
			<option value="BLOK1"> BLOK 1 </option>
			<option value="BLOK2"> BLOK 2 </option>
			<option value="BLOK3"> BLOK 3 </option>
			<option value="BLOK4"> BLOK 4 </option>
			<option value="TARIF1"> TARIF 1 </option>
			<option value="TARIF2"> TARIF 2 </option>
			<option value="TARIF3"> TARIF 3 </option>
			<option value="TARIF4"> TARIF 4 </option>
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
		<select name="kode_tipe" id="kode_tipe" class="apply wauto">
			<option value=""> -- KATEGORI AIR -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_AIR ORDER BY NAMA_TIPE ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				$on = $obj->fields['NAMA_TIPE'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
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
		if ($(this).val() == 'KODE_AIR' || $(this).val() == 'KEY_AIR' || $(this).val() == 'KETERANGAN')
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
	
	$('#kode_sk').on('change', function(e) {
		e.preventDefault();
		$('#apply').trigger('click');
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
		if (confirm('Record akan dihapus secara permanent!'))
		{
			deleteData();
		}
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
	if (jQuery('#kode_sk').val() == '') { jQuery('#t-detail').html(''); return false; }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_master + 'tarif_air/tarif_air_load.php', data);
	
	return false;
}

function showPopup(act, id)
{
	var url =	base_master + 'tarif_air/tarif_air_popup.php' +
				'?act=' + act +
				'&kode_sk=' + jQuery('#kode_sk').val() + 
				'&id=' + id,
		title	= (act == 'Simpan') ? 'Tambah' : act;
	
	setPopup(title + ' Detail Tarif Air', url, 700, 500);
	
	return false;
}

function deleteData()
{
	var checked = jQuery(".cb_data:checked").length;
	if (checked < 1)
	{
		alert('Pilih data yang akan dihapus.');
		return false;
	}
	
	var url		= base_master + 'tarif_air/tarif_air_proses.php',
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
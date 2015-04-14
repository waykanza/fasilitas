<div class="title_page">TARIF KEGIATAN SHOOTING / PEMOTRETAN</div>
<div id="msg"></div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">NO SK SEWA</td>
	<td>
		<select name="kode_sk" id="kode_sk" class="wauto">
			<option value=""> -- NO SK SEWA -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_SK, NO_SK FROM KWT_SK_SEWA ORDER BY TGL_BERLAKU DESC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SK'];
				$on = $obj->fields['NO_SK'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
		<select name="kode_lokasi" id="kode_lokasi" class="wauto">
			<option value=""> -- LOKASI -- </option>
		</select>
	</td>
</tr>

<tr>
	<td>PENCARIAN</td>
	<td>
		<select name="kode_tipe" id="kode_tipe" class="apply wauto">
			<option value=""> -- KATEGORI KSP -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_KSP ORDER BY NAMA_TIPE ASC");
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
	<td></td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KEY_KSP"> #KEY </option>
			<option value="TARIF"> TARIF </option>
			<option value="SAVE_DEPOSIT"> SAVE DEPOSIT </option>
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
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class=" apply text-center" value="20">
		<input type="button" name="apply" id="apply" value=" Apply (Enter) ">
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
	$('#kode_sk').on('change', function(e) {
		e.preventDefault();
		var kode_sk = $(this).val();
		$('#t-detail').html('');
		$('#kode_lokasi').load(base_master_fa + 'tarif_ksp/opt_lokasi_ksp.php?kode_sk=' + kode_sk);
	});
	
	$('#kode_lokasi').on('change', function(e) {
		e.preventDefault();
		var kode_lokasi = $(this).val();
		if (kode_lokasi == '') {
			$('#t-detail').html('');
		} else {
			$('#apply').trigger('click');
		}
	});
	
	$('#field1').on('change', function(e) {
		e.preventDefault();
		if ($(this).val() == 'KEY_KSP')
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
	
	/* -- Shortcut -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+n', function(e) { e.preventDefault(); $('#tambah').trigger('click'); });
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	/* -- Validate -- */
	
});

function loadData()
{
	jQuery('#msg').html('');
	if (popup) { popup.close(); }
	if (jQuery('#kode_lokasi').val() == '') { jQuery('#t-detail').html(''); return false; }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_master_fa + 'tarif_ksp/tarif_ksp_load.php', data);
	
	return false;
}

function showPopup(act, id)
{
	var url =	base_master_fa + 'tarif_ksp/tarif_ksp_popup.php' +
				'?act=' + act +
				'&kode_lokasi=' + jQuery('#kode_lokasi').val() + 
				'&id=' + id,
		title	= (act == 'Simpan') ? 'Tambah' : act;
	
	setPopup(title + ' Detail Tarif Kegiatan Shooting / Pemotretan', url, 520, 370);
	
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
	
	var url		= base_master_fa + 'tarif_ksp/tarif_ksp_proses.php',
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
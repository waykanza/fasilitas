<div class="title-page">HAPUS TAGIHAN</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="120">STATUS BLOK</td>
	<td>
		<select name="trx" id="trx">
			<option value="1"> KAVLING KOSONG (IPL) </option>
			<option value="2"> MASA MEMBANGUN (IPL) </option>
			<option value="3"> MASA MEMBANGUN (SAVE DEPOSIT) </option>
			<option value="4"> HUNIAN (AIR & IPL) </option>
		</select>
	</td>
</tr>
<tr>
	<td>PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="b.KODE_BLOK"> KODE BLOK </option>
			<option value="b.NO_PELANGGAN"> NO. PELANGGAN </option>
			<option value="p.NAMA_PELANGGAN"> NAMA PELANGGAN </option>
			<option value="b.KEY_AIR"> KEY AIR </option>
			<option value="b.KEY_IPL"> KEY IPL </option>
		</select>
		
		<input type="text" name="search1" id="search1" class="apply" value="">
	</td>
</tr>

<tr>
	<td></td>
	<td>
		<select name="kode_sektor" id="kode_sektor" class="wauto">
			<option value=""> -- SEKTOR -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_SEKTOR, NAMA_SEKTOR FROM KWT_SEKTOR ORDER BY NAMA_SEKTOR ASC");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SEKTOR'];
				$on = $obj->fields['NAMA_SEKTOR'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
		
		<select name="kode_cluster" id="kode_cluster">
			<option value=""> -- CLUSTER -- </option>
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
	
	$('#kode_sektor').on('change', function(e) {
		e.preventDefault();
		$('#kode_cluster').load(base_master + 'opt_cluster.php?kode_sektor=' + $(this).val());
		return false;
	});
	
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
	
	$(document).on('focusin', '.x', function(e) {
		e.preventDefault();
		$(this).attr('rows','3');
	});
	
	$(document).on('focusout', '.x', function(e) {
		e.preventDefault();
		$(this).attr('rows','1');
	});
	
	jQuery(document).on('click', '#hapus', function(e) {
		e.preventDefault();
		var checked = jQuery(".cb_data:checked").length;
		if (checked < 1)
		{
			alert('Pilih data yang akan dihapus.');
			return false;
		}
		
		for (var i = 1; i <= 100; i++)
		{ 
			if ((jQuery('input[name="cb_data['+i+']"]:checked').length > 0) && (jQuery('textarea[name="cb_ket['+i+']"]').val() == ''))
			{
				alert('Masukkan alasan penghapusan tagihan.' + jQuery('textarea[name="cb_ket['+i+']"]').val());
				jQuery('textarea[name="cb_ket['+i+']"]').focus();
				return false;
			}
		}
		
		if (confirm('Record akan dihapus secara permanent!'))
		{
			deleteData();
		}
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_periode + 'hapus_tagihan/hapus_tagihan_load.php', data);
	
	
	
	return false;
}

function deleteData()
{
	var url		= base_periode + 'hapus_tagihan/hapus_tagihan_proses.php',
		data	= jQuery('#form').serializeArray();
	
	jQuery.post(url, data, function(result) {
		var list_id = result.list_id.join(', #');
		jQuery('#' + list_id).remove();
		alert(result.msg);
		
	}, 'json');

	return false;
}
</script>

<div id="t-detail"></div>
</form>
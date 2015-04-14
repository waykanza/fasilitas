<div class="title-page">PEMBAYARAN BIAYA LAIN-LAIN</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="120">TIPE DATA</td>
	<td>
		<select name="tipe_load" id="tipe_load" class="wauto">
			<option value="load_periode"> PERIODE TAG. </option>
			<option value="load_blok"> BLOK / NO. </option>
		</select>
		
		<input type="text" name="search_load" id="search_load" size="10" class="apply mm-yyyy" value="">
	</td>
</tr>
<tr>
	<td>PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="b.KODE_BLOK"> BLOK / NO. </option>
			<option value="b.NO_PELANGGAN"> NO. PELANGGAN </option>
			<option value="p.NAMA_PELANGGAN"> NAMA PELANGGAN </option>
			<option value="b.KEY_AIR"> KEY AIR </option>
			<option value="b.KEY_IPL"> KEY IPL </option>
		</select>
		
		<input type="text" name="search1" id="search1" class="apply" value="">
	</td>
</tr>

<tr>
	<td>SEKTOR / CLUSTER</td>
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
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="<?php echo $trx_lbg; ?>"> MASA MEMBANGUN (BIAYA LAIN-LAIN) </option>
			<option value="<?php echo $trx_lrv; ?>"> RENOVASI (BIAYA LAIN-LAIN) </option>
		</select>
	</td>
<tr>

<tr>
	<td>STATUS BAYAR</td>
	<td>
		<select name="status_bayar" id="status_bayar" class="wauto">
			<option value=""> -- STATUS BAYAR -- </option>
			<option value="0"> BELUM </option>
			<option value="1"> SUDAH </option>
		</select>
		
		<select name="status_cetak_kwt" id="status_cetak_kwt" class="wauto">
			<option value=""> -- STATUS CETAK -- </option>
			<option value="0"> BELUM </option>
			<option value="1"> SUDAH </option>
		</select>
	</td>
<tr>

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
	
	$('#tipe_load').on('change', function(e) {
		e.preventDefault();
		if ($(this).val() == 'load_periode') {
			set_mmyyyy($('#search_load'));
		} else {
			detroy_format($('#search_load'));
		}
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
	
	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		showPopup(id);
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+n', function(e) { e.preventDefault(); $('#tambah').trigger('click'); });
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{
	if (popup) { popup.close(); }
	if (jQuery('#search_load').val() == '') 
	{
		alert('Masukkan tipe data!');
		jQuery('#search_load').focus();
		return false;
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_pembayaran + 'lain_lain/pembayaran_load.php', data);
	
	return false;
}

function showPopup(id)
{
	var url =	base_pembayaran + 'lain_lain/pembayaran_popup.php?id=' + id;
	
	setPopup('Pembayaran Biaya Lain-lain', url, winWidth-70, winHeight-100);
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
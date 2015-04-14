<div class="title-page">LAPORAN RINCIAN PIUTANG<br>BIAYA LAIN-LAIN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">SEKTOR / CLUSTER</td>
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
		
		<select name="kode_cluster" id="kode_cluster" class="wauto">
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
	<td>BANYAK TAGIHAN >=</td>
	<td><input type="text" name="banyak_tangihan" id="banyak_tangihan" size="3" class="apply text-center" value="1"></td>
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
	
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		if (jQuery('#banyak_tangihan').val() == '' || jQuery('#banyak_tangihan').val() == '0') {
			alert('Masukkan jumlah piutang!');
			jQuery('#banyak_tangihan').focus();
			return false;
		}
		
		location.href = base_laporan + 'lain_lain/piutang_rincian/piutang_rincian_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#banyak_tangihan').val() == '' || jQuery('#banyak_tangihan').val() == '0') {
			alert('Masukkan jumlah piutang!');
			jQuery('#banyak_tangihan').focus();
			return false;
		}
		
		var url = base_laporan + 'lain_lain/piutang_rincian/piutang_rincian_print.php?' + $('#form').serialize();
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
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	/* -- VALIDATION -- */
	$('#banyak_tangihan').inputmask('integer');
});

function loadData()
{
	if (jQuery('#banyak_tangihan').val() == '' || jQuery('#banyak_tangihan').val() == '0') 
	{
		alert('Masukkan jumlah piutang!');
		jQuery('#banyak_tangihan').focus();
		return false;
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'lain_lain/piutang_rincian/piutang_rincian_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
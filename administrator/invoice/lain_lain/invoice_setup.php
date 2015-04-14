<div class="title-page">INVOICE BIAYA LAIN-LAIN</div>

<form name="form" id="form" method="post">
<table class="t-control">
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
		
		<select name="kode_cluster" id="kode_cluster">
			<option value=""> -- CLUSTER -- </option>
		</select>
	</td>
</tr>

<tr>
	<td>PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KODE_BLOK"> BLOK / NO. </option>
			<option value="NO_PELANGGAN"> NO. PELANGGAN </option>
		</select>
		
		<input type="text" name="search1" id="search1" class="apply" value="">
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
	});
	
	/* -- BUTTON -- */
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
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
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length;
		if (checked < 1) {
			alert('Pilih data yang akan dicetak.');
		} else {
			open_print(base_invoice + 'lain_lain/invoice_print.php?' + $('#form').serialize());
		}
		
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData() {
	if (jQuery('#trx').val() == '') {
		alert('Pilih status blok.');
		return false;
	}
	jQuery('#t-detail').load(base_invoice + 'lain_lain/invoice_load.php', jQuery('#form').serialize());
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
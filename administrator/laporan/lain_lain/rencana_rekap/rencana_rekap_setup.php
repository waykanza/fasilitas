<div class="title-page">LAPORAN REKAP RENCANA PENERIMAAN<br>BIAYA LAIN-LAIN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">PERIODE TAG.</td>
	<td><input type="text" name="periode_tag" id="periode_tag" size="10" class="apply mm-yyyy" value=""></td>
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
	<td></td>
	<td><input type="button" id="apply" value=" Apply (Enter) "></td>
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
		if (jQuery('#periode_tag').val() == '') {
			alert('Masukkan periode laporan!');
			jQuery('#periode_tag').focus();
			return false;
		}
		
		location.href = base_laporan + 'lain_lain/rencana_rekap/rencana_rekap_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#periode_tag').val() == '') {
			alert('Masukkan periode laporan!');
			jQuery('#periode_tag').focus();
			return false;
		}
		var url = base_laporan + 'lain_lain/rencana_rekap/rencana_rekap_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#periode_tag').val() == '') 
	{
		alert('Masukkan periode laporan!');
		jQuery('#periode_tag').focus();
		return false;
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'lain_lain/rencana_rekap/rencana_rekap_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
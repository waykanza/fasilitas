<div class="title-page">LAPORAN BD</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">BULAN BAYAR</td>
	<td><input type="text" name="tgl_bd" id="tgl_bd" class="apply mm-yyyy" size="9" value=""></td>
</tr>

<tr>
	<td>BANK</td>
	<td>
		<select name="bank_bd" id="bank_bd" class="wauto">
			<option value=""> -- BANK -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_BANK'];
				$on = $obj->fields['NAMA_BANK'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
		
		<input type="button" id="apply" value=" Apply (Enter) ">
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
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
		if (jQuery('#tgl_bd').val() == '') {
			alert('Masukkan waktu pembayaran!');
			return false;
		}
		
		location.href = base_laporan + 'bd/rekap/rekap_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#tgl_bd').val() == '') {
			alert('Masukkan waktu pembayaran!');
			return false;
		} 
		
		var url = base_laporan + 'bd/rekap/rekap_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#tgl_bd').val() == '') {
		alert('Masukkan waktu pembayaran!');
		return false;
	} 
	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'bd/rekap/rekap_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
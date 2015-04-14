<div class="title-page">LAPORAN REKAP UMUR PIUTANG<br>BIAYA LAIN-LAIN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">TAHUN TAG.</td>
	<td>
		<select name="jenis_tgl_bayar" id="jenis_tgl_bayar" class="wauto">
			<option value="TGL_BAYAR_SYS"> TGL BAYAR (SYS) </option>
			<option value="TGL_BAYAR_BANK"> TGL BAYAR (BANK) </option>
		</select>
		
		<input type="text" name="tahun_tag" id="tahun_tag" size="7" class="yyyy" value="">&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="apply" value=" Apply (Enter) ">
	</td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="<?php echo $trx_dbg; ?>"> MASA MEMBANGUN (DEPOSIT) </option>
			<option value="<?php echo $trx_drv; ?>"> RENOVASI (DEPOSIT) </option>
		</select>
	</td>
<tr>
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
		if (jQuery('#tahun_tag').val() == '') 
		{
			alert('Masukkan jumlah periode!');
			jQuery('#tahun_tag').focus();
			return false;
		}
		
		location.href = base_laporan + 'lain_lain/piutang_umur/piutang_umur_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#tahun_tag').val() == '') 
		{
			alert('Masukkan jumlah periode!');
			jQuery('#tahun_tag').focus();
			return false;
		}
		var url = base_laporan + 'lain_lain/piutang_umur/piutang_umur_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+x', function(e) { e.preventDefault(); $('#excel').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#tahun_tag').val() == '') 
	{
		alert('Masukkan jumlah periode!');
		jQuery('#tahun_tag').focus();
		return false;
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'lain_lain/piutang_umur/piutang_umur_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
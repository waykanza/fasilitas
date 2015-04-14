<div class="title-page">LAPORAN REKAP UMUR PIUTANG<br>AIR & IPL</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="60">PERIODE</td>
	<td>
		<input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value="">&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="button" id="apply" value=" Apply (Enter) ">
	</td>
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
		if (jQuery('#periode').val() == '') 
		{
			alert('Masukkan jumlah periode!');
			jQuery('#periode').focus();
			return false;
		}
		
		location.href = base_laporan + 'air_ipl/piutang_umur/piutang_umur_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#periode').val() == '') 
		{
			alert('Masukkan jumlah periode!');
			jQuery('#periode').focus();
			return false;
		}
		var url = base_laporan + 'air_ipl/piutang_umur/piutang_umur_print.php?' + $('#form').serialize();
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
	if (jQuery('#periode').val() == '') 
	{
		alert('Masukkan jumlah periode!');
		jQuery('#periode').focus();
		return false;
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'air_ipl/piutang_umur/piutang_umur_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
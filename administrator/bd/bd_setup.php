<div class="title-page">PROSES BD</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<tr class="text-center">
	<td>
		<br>BULAN BAYAR SYS<br>
		<input type="text" name="bln_bayar" id="bln_bayar" size="9" class="mm-yyyy" value="">
	</td>
</tr>
<tr class="text-center">
	<td>
		<br>
		<input type="button" id="proses" value=" Proses ">
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', '#proses', function(e) {
		e.preventDefault();
		var	bln_bayar = $('#bln_bayar').val();
		
		if (bln_bayar == '') {
			alert('Masukkan tanggal bayar.');
			$('#bln_bayar').focus();
			return false;
		} else if (confirm('Benar tanggal "' + bln_bayar + '" yang akan diproses !?') == true) {
			var url		= base_adm + 'bd/bd_proses.php',
				data	= $('#form').serialize();
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}
		
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	
});
</script>
</form>
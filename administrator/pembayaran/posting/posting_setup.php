<div class="title-page">POSTING PEMBAYARAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<tr class="text-center">
	<td>
		<br>TGL. BAYAR (BANK)<br>
		<input type="text" name="tgl_bayar_bank" id="tgl_bayar_bank" size="13" class="dd-mm-yyyy" value="">
	</td>
</tr>
<tr class="text-center">
	<td>
		<br>
		<input type="button" id="batal-posting" value=" Batal Posting ">
		<input type="button" id="posting" value=" Posting ">&nbsp;&nbsp;
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', '#posting', function(e) {
		e.preventDefault();
		var	tgl_bayar_bank = $('#tgl_bayar_bank').val();
		
		if (tgl_bayar_bank == '') {
			alert('Masukkan tanggal posting yang akan dibatalkan!');
			$('#tgl_bayar_bank').focus();
			return false;
		} else if (confirm('Benar tanggal "' + tgl_bayar_bank + '" yang akan di-posting !?') == true) {
			var url		= base_pembayaran + 'posting/posting_proses.php',
				data	= $('#form').serialize();
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}
		
		return false;
	});
	
	$(document).on('click', '#batal-posting', function(e) {
		e.preventDefault();
		var	tgl_bayar_bank = $('#tgl_bayar_bank').val();
		
		if (tgl_bayar_bank == '') {
			alert('Masukkan tanggal posting yang akan dibatalkan!');
			$('#tgl_bayar_bank').focus();
			return false;
		} else if (confirm('Benar tanggal "' + tgl_bayar_bank + '" yang akan dibatalkan !?') == true) {
			var url		= base_pembayaran + 'posting/posting_batal_proses.php',
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
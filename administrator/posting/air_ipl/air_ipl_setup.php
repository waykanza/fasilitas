<div class="title-page">POSTING PEMBAYARAN AIR, IPL & SAVE DEPOSIT</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<tr class="text-center">
	<td>
		<br>STATUS BLOK<br>
		<select name="trx" id="trx">
			<option value=""> -- PILIH -- </option>
			<option value="1"> KAVLING KOSONG </option>
			<option value="2"> MASA MEMBANGUN </option>
			<option value="3"> MASA MEMBANGUN (DEPOSIT) </option>
			<option value="4"> HUNIAN </option>
			<option value="5"> RENOVASI </option>
			<option value="6"> RENOVASI (DEPOSIT) </option>
		</select>
	</td>
</tr>
<tr class="text-center">
	<td>
		<br>TANGGAL BAYAR<br>
		<input type="text" name="tgl_bayar" id="tgl_bayar" size="13" class="dd-mm-yyyy" value="">
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
		var	trx = $('#trx').val(),
			tgl_bayar = $('#tgl_bayar').val();
		
		if (trx == '')  {
			alert('Pilih status blok!');
			$('#trx').focus();
			return false;
		} else if (tgl_bayar == '')  {
			alert('Masukkan tanggal posting yang akan dibatalkan!');
			$('#tgl_bayar').focus();
			return false;
		} else if (confirm('Benar tanggal "' + tgl_bayar + '" yang akan di-posting !?') == true)
		{
			var url		= base_posting + 'air_ipl/air_ipl_proses.php',
				data	= $('#form').serialize();
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}
		
		return false;
	});
	
	$(document).on('click', '#batal-posting', function(e) {
		e.preventDefault();
		var	trx = $('#trx').val(),
			tgl_bayar = $('#tgl_bayar').val();
		
		if (trx == '')  {
			alert('Pilih status blok!');
			$('#trx').focus();
			return false;
		} else if (tgl_bayar == '')  {
			alert('Masukkan tanggal posting yang akan dibatalkan!');
			$('#tgl_bayar').focus();
			return false;
		} else if (confirm('Benar tanggal "' + tgl_bayar + '" yang akan dibatalkan !?') == true) {
			var url		= base_posting + 'air_ipl/air_ipl_batal_proses.php',
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
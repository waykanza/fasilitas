<div class="title-page">PROSES HITUNG DENDA</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<!--tr class="text-center">
	<td>
		<br>TIPE TAGIHAN<br>
		<select name="tipe_tagihan" id="trx">
			<option value=""> -- PILIH -- </option>
			<option value="air_ipl"> AIR & IPL </option>
			<option value="deposit"> DEPOSIT </option>
		</select>
	</td>
</tr-->
<tr class="text-center">
	<td><br><input type="button" id="proses" value=" Proses "></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', '#proses', function(e) {
		e.preventDefault();
		/*var	trx = $('#trx').val()
		
		if (trx == '')  {
			alert('Pilih status blok!');
			$('#trx').focus();
			return false;
		} else {
			var url		= base_periode + 'hitung_denda/hitung_denda_proses.php',
				data	= $('#form').serialize();
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}*/
		
			var url		= base_periode + 'hitung_denda/hitung_denda_proses.php',
				data	= $('#form').serialize();
				
			$.post(url, data, function(res) {
				alert(res);
			});
			
		return false;
	});
	
});
</script>
</form>
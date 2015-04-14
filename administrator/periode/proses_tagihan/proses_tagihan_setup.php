<div class="title-page">PROSES TAGIHAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<tr class="text-center">
	<td>PERIODE &nbsp;&nbsp;</td>
	<td><input type="text" name="periode_tag" id="periode_tag" class="mm-yyyy" size="10" value=""></td>
	<td><input type="button" id="proses" value=" Proses "></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$(document).on('click', '#proses', function(e) {
		e.preventDefault();
		
		$('#respon_air').html('');
				
		var url		= base_periode + 'proses_tagihan/proses_tagihan_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(result) {
			alert(result.msg);
		}, 'json');
			
		return false;
	});
	
});
</script>
</form>


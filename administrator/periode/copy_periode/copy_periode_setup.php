<script type="text/javascript">
jQuery(function($) {
	$('#close').click(function(e) {
		e.preventDefault();
		location.href = base_adm;
	});

	$('#proses').click(function(e) {
		e.preventDefault();
		var periode	= $('#periode').val(),
			url		= base_periode + 'copy_periode/copy_periode_proses.php',
			data	= $('#form').serialize();
		
		if (periode == '')
		{
			alert('Masukkan periode tagihan.');
			$('#periode').focus();
			return false;
		}
		
		$.post(url, data, function(result) {
			
			alert(result.msg);
		}, 'json');
	
		return false;
	});
});
</script>

<div class="title-page">PROSES TAGIHAN</div>

<form name="form" method="post" id="form">
<table class="t-control wauto-center">
<tr>
	<td><input type="checkbox" name="single_blok" id="sglb" value="1"> <label for="sglb">BLOK/NO. </label></td>
	<td>
		<input type="text" name="kode_blok" id="kode_blok" value="" size="12">&nbsp;&nbsp;&nbsp;
		DISKON IPL &nbsp;<input type="text" name="diskon_rupiah_ipl" id="diskon_rupiah_ipl" value="" size="13">
	</td>
</tr>

<tr>
	<td colspan="2"><hr><br></td>
</tr>

<tr>
	<td>PERIODE</td>
	<td><input type="text" name="periode" id="periode" class="mm-yyyy" size="10" value="">&nbsp;&nbsp;* Masukkan periode tagihan</td>
</tr>

<tr>
	<td>JML. PERIODE &nbsp;</td>
	<td><input type="text" name="jumlah_periode" id="jumlah_periode" class="text-center"  size="2" value="1"></td>
</tr>

<tr class="text-right">
	<td colspan="2">
		<hr>
		<input type="button" id="proses" value=" Proses ">&nbsp;&nbsp;
		<input type="button" id="close" value=" Tutup ">
	</td>
</tr>
</table>
</form>

<?php close($conn);?>

<div class="title-page">POSTING FAKTUR PAJAK</div>

<form name="form" id="form" method="post">
<table class="t-control wauto-center">
<tr class="text-center">
	<td width="120">TANGGAL POSTING</td>
	<td>
		<input type="text" name="tgl_post_fp" id="tgl_post_fp" size="13" class="apply dd-mm-yyyy" value="">&nbsp;&nbsp;
		<input type="button" id="posting" value=" Posting ">&nbsp;&nbsp;
		<input type="button" id="batal-posting" value=" Batal Posting ">
	</td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	/* -- BUTTON -- */
	$(document).on('click', '#posting', function(e) {
		e.preventDefault();
		var tgl_post_fp = $('#tgl_post_fp').val();
		
		if (tgl_post_fp == '') 
		{
			alert('Masukkan tanggal posting!');
			$('#tgl_post_fp').focus();
			return false;
		}
		else if (confirm('Benar tanggal "' + tgl_post_fp + '" yang akan di-posting!?') == true)
		{
			var url		= base_faktur_pajak + 'posting/posting_proses.php',
				data	= {tgl_post_fp : tgl_post_fp};
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}
		
		return false;
	});
	
	$(document).on('click', '#batal-posting', function(e) {
		e.preventDefault();
		var tgl_post_fp = $('#tgl_post_fp').val();
		
		if (tgl_post_fp == '') 
		{
			alert('Masukkan tanggal posting yang akan dibatalkan!');
			$('#tgl_post_fp').focus();
			return false;
		}
		else if (confirm('Benar tanggal "' + tgl_post_fp + '" yang akan dibatalkan !?') == true)
		{
			var url		= base_faktur_pajak + 'posting/posting_batal_proses.php',
				data	= {tgl_post_fp : tgl_post_fp};
				
			$.post(url, data, function(res) {
				alert(res);
			});
		}
		
		return false;
	});
	
});
</script>
</form>
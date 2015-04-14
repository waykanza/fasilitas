<?php
$conn = conn();
?>

<script type="text/javascript">
var itvl;

jQuery(function($) {

	$('#close').click(function(e) {
		e.preventDefault();
		location.href = base_adm;
	});
	
	$('#export').click(function(e) {
		e.preventDefault(); 
		
		$('#download, #respon').html('');
		
		var nama_bank = $('#nama_bank').val(),
			periode = $('#periode').val();
			
		if (periode == '')
		{
			alert('Masukkan periode.');
			$('#periode').focus();
			return false;
		}
		
		$.post(base_periode + 'export_sm/export_sm_proses.php' , { periode: periode }, function(data) {
			
			if (data.error == false)
			{
				ajax_start();
				itvl = setInterval(function() {
					$.get(base_periode + 'export_sm/export_sm_check.php', { periode: periode }, function(data) {
						if (data.status == 'FINISH') {
							clearInterval(itvl);
							ajax_stop();
							if (data.respon != '') {
								$('#respon').html(data.respon);
								alert('Something error !!!');
							} else {
								var link_download = '<a class="link-download" target="_blank" href="' + base_vb + 'export/sm/files/' + data.file_name + '">' + data.file_name + '</a>';
								$('#download').html(link_download);
								alert('Data selesai di-export.');
							}
						}
					}, 'json');
				}, 3000);
			}
			
			alert(data.msg)
			
		}, 'json');
		
		return false;
	});
});
</script>

<div class="title-page">DATA STAND METER<br>EXPORT KE BIMASAKTI</div>

<form name="form" method="post" id="form">
<table class="t-control wauto-center">
<tr class="text-center">
	<td width="70">PERIODE</td>
	<td>
		<input type="text" name="periode" id="periode" class="mm-yyyy" size="10">&nbsp;&nbsp;
		<input type="button" id="export" value=" Export ">&nbsp;&nbsp;
		<input type="button" id="close" value=" Tutup ">
	</td>
</tr>
<tr>
	<td colspan="2"><br>LINK TO DOWNLOAD : <span id="download"></span></td>
</tr>
</table>
</form>

<br>
<br>
<div class="clear"></div>
<div id="respon" style="background:#FFFFFF;border:2px solid #FF9900;padding:10px;"></div>

<?php close($conn); ?>
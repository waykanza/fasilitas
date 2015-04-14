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
	
	$('#import').on('click', function(e) {
		e.preventDefault();
		
		$.ajaxFileUpload({
			url : base_periode + 'import_sm/import_sm_upload.php', 
			secureuri : false,
			fileElementId : 'file_import',
			dataType : 'json',
			beforeSend : function() {
			
			},
			success: function(data, status) {
				
				if (data.error == false)
				{
					ajax_start();
					itvl = setInterval(function() {
						$.get(base_periode + 'import_sm/import_sm_check.php', function(data) {
							$('#respon').html(data.respon);
							if (data.status == 'FINISH') {
								clearInterval(itvl);
								ajax_stop();
								alert('Proses import selesai.');
							}
						}, 'json');
					}, 5000);
				}
				
				alert(data.msg);
			}
		});
    });
});
</script>

<div class="title-page">DATA STAND METER<br>IMPORT DARI BIMASAKTI</div>
<form method="post" enctype="multipart/form-data" name="form" id="form">
<table class="t-control wauto">
<tr>
	<td width="50">FILE</td>
	<td>
		<input type="file" name="file_import" id="file_import">(*.txt)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" id="import" value=" Import ">&nbsp;&nbsp;
		<input type="button" id="close" value=" Tutup ">
	</td>
</tr>
</table>
</form>

<br>
<br>
<div class="clear"></div>
<div id="respon" style="background:#FFFFFF;border:2px solid #FF9900;padding:10px;"></div>

<?php close($conn); ?>

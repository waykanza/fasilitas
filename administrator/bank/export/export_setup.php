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
		
		var nama_bank = $('#nama_bank').val();
			
		if (nama_bank == '')
		{
			alert('Pilih BANK.');
			$('#nama_bank').focus();
			return false;
		}
		
		$.post(base_bank + 'export/export_proses.php' , { nama_bank: nama_bank }, function(data) {
			
			if (data.error == false)
			{
				ajax_start();
				itvl = setInterval(function() {
					$.get(base_bank + 'export/export_check.php', { nama_bank: nama_bank }, function(data) {
						if (data.status == 'FINISH') {
							clearInterval(itvl);
							ajax_stop();
							if (data.respon != '') {
								$('#respon').html(data.respon);
								alert('Something error !!!');
							} else {
								$('#download').html(data.link);
								$('.link-download').each(function(){
									var file_link = $(this).attr('href');
									$(this).attr('href', base_vb + 'export/' + file_link);
								});
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

<div class="title-page">EXPORT DATA BANK</div>
<form name="form" method="post" id="form">
<table class="t-control wauto-center">
<tr>
	<td width="50">BANK</td>
	<td>
		<?php
		$list_bank = array(
			'BCA' => 'BCA',
			'BUKOPIN' => 'BUKOPIN',
			#'BUMIPUTERA' => 'BUMIPUTERA',
			'MANDIRI' => 'MANDIRI',
			'NIAGA' => 'NIAGA',
			'NIAGA_AD' => 'NIAGA AUTODEBET',
			'PERMATA' => 'PERMATA',
		);
		?>
		<select name="nama_bank" id="nama_bank">
			<?php 
			foreach ($list_bank AS $kb => $nb)
			{
				echo "<option value='$kb'> $nb </option>";
			}
			?>
		</select>&nbsp;&nbsp;
		<input type="button" id="export" value=" Export ">&nbsp;&nbsp;
		<input type="button" id="close" value=" Tutup ">&nbsp;&nbsp;
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
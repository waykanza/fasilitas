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
		
		var nama_bank = $("#nama_bank").val();
		
		$.ajaxFileUpload({
			url : base_bank + 'import/import_upload.php', 
			secureuri : false,
			fileElementId : 'file_import',
			data : { nama_bank : nama_bank },
			dataType : 'json',
			beforeSend : function() {
			
			},
			success: function(data, status) {
				
				if (data.error == false) {
					do_load(nama_bank);
				} else {
					alert(data.msg);
				}
			}
		});
    });
});


function do_load(nama_bank)
{
	jQuery('#t-detail').html('');
	jQuery('#t-detail').load(base_bank + 'import/import_load.php?nama_bank=' + nama_bank);
}
</script>

<div class="title-page">IMPORT DATA BANK</div>
<form method="post" enctype="multipart/form-data" name="form" id="form">
<table class="t-control wauto-center">
<tr>
	<td width="50">BANK</td>
	<td>
		<?php
		$list_bank = array(
			'BCA' => 'BCA (*.txt)',
			'BUKOPIN' => 'BUKOPIN (*.xls)',
			#'BUKOPIN_AD' => 'BUKOPIN AUTODEBET (*.xls)',
			#'BUMIPUTERA' => 'BUMIPUTERA (*.txt)',
			'MANDIRI' => 'MANDIRI (*.xls)',
			'NIAGA' => 'NIAGA (*.txt)',
			'NIAGA_AD' => 'NIAGA AUTODEBET (*.txt)',
			'PERMATA' => 'PERMATA (*.txt)',
		);
		?>
		<select name="nama_bank" id="nama_bank">
			<?php 
			foreach ($list_bank AS $kb => $nb)
			{
				echo "<option value='$kb'> $nb </option>";
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>FILE</td>
	<td>
		<input type="file" name="file_import" id="file_import">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" id="import" value=" Import ">&nbsp;&nbsp;
		<input type="button" id="close" value=" Tutup ">&nbsp;&nbsp;
	</td>
</tr>
</table>
</form>

<br>
<div id="t-detail"></div>
<?php conn($conn); ?>
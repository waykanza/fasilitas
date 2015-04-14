<?php
require_once('tarif_ksp_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
$(function() {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#tarif').inputmask('numeric', { repeat: '9' });
	$('#save_deposit').inputmask('numeric', { repeat: '9' });
	
	$('#kode_tipe').on('change', function(e) {
		e.preventDefault();
		var key_ksp = $('#kode_lokasi').val() + '-' + $(this).val();
		$('#key_ksp').val(key_ksp);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master_fa + 'tarif_ksp/tarif_ksp_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			if (data.error == true)
			{
				alert(data.msg);
			}
			else
			{
				if (data.act == 'Simpan')
				{
					alert(data.msg);
					$('#reset').click();
				}
				else if (data.act == 'Ubah')
				{
					alert(data.msg);
					parent.loadData();
				}
			}
		}, 'json');
		
		return false;
	});
});
</script>
</head>
<body class="popup">

<div id="msg"><?php echo $msg; ?></div>

<form name="form" id="form" method="post">
<table class="t-popup wauto">
<tr>
	<td width="120">KODE</td>
	<td>
		<input readonly="readonly" type="text" name="key_ksp" id="key_ksp" size="12" value="<?php echo $key_ksp; ?>">
	</td>
</tr>
<tr>
	<td>KATEGORI</td>
	<td>
		<select name="kode_tipe" id="kode_tipe">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_KSP ORDER BY NAMA_TIPE ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				$on = $obj->fields['NAMA_TIPE'];
				echo "<option value='$ov' ".is_selected($ov, $kode_tipe)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>TARIF</td>
	<td><input type="text" name="tarif" id="tarif" size="12" value="<?php echo $tarif; ?>"></td>
</tr>
<tr>
	<td>SAVE DEPOSIT</td>
	<td><input type="text" name="save_deposit" id="save_deposit" size="12" value="<?php echo $save_deposit; ?>"></td>
</tr>
<tr>
	<td></td>
	<td class="td-proses">
		<input type="submit" id="save" value=" <?php echo $act; ?> (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="kode_lokasi" id="kode_lokasi" value="<?php echo $kode_lokasi; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
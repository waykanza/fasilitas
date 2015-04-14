<?php
require_once('tarif_psp_proses.php');
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
	$('#lokasi').inputmask('varchar', { repeat: '400' });
	
	$('#kode_tipe, #kode_fungsi').on('change', function(e) {
		e.preventDefault();
		var key_psp = $('#kode_sk').val() + '-' + $('#kode_tipe').val() + '-' + $('#kode_fungsi').val();
		$('#key_psp').val(key_psp);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master_fa + 'tarif_psp/tarif_psp_proses.php',
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
	<td><input readonly="readonly" type="text" name="key_psp" id="key_psp" size="10" value="<?php echo $key_psp; ?>"></td>
</tr>
<tr>
	<td>KATEGORI</td>
	<td>
		<select name="kode_tipe" id="kode_tipe">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_TIPE FROM KWT_TIPE_PSP ORDER BY KODE_TIPE ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				echo "<option value='$ov' ".is_selected($ov, $kode_tipe)."> $ov </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>FUNGSI</td>
	<td>
		<select name="kode_fungsi" id="kode_fungsi">
			<option value=""> -- FUNGSI -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_FUNGSI, NAMA_FUNGSI FROM KWT_FUNGSI_PSP ORDER BY NAMA_FUNGSI ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_FUNGSI'];
				$on = $obj->fields['NAMA_FUNGSI'];
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
	<td>LOKASI</td>
	<td><textarea name="lokasi" id="lokasi" cols="50" rows="5"><?php echo $lokasi; ?></textarea></td>
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

<input type="hidden" name="kode_sk" id="kode_sk" value="<?php echo $kode_sk; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
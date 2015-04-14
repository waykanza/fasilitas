<?php
require_once('detail_tarif_mp_c_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../../config/js/main.js"></script>
<script type="text/javascript">
$(function() {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#tarif').inputmask('numeric', { repeat: '9' });
	
	$('#kode_lokasi').on('change', function(e) {
		e.preventDefault();
		var key_mpd = $('#key_mp').val() + '-' + $(this).val();
		$('#key_mpd').val(key_mpd);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$(document).on('click', '#save', function(e) {
		e.preventDefault();
		var url		= base_master_fa + 'tarif_mp/c/detail_tarif_mp_c_proses.php',
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
	<td><input readonly="readonly" type="text" name="key_mpd" id="key_mpd" size="15" value="<?php echo $key_mpd; ?>"></td>
</tr>
<tr>
	<td>LOKASI</td>
	<td>
		<select name="kode_lokasi" id="kode_lokasi">
			<option value=""> -- LOKASI -- </option>
			<?php
			$obj = $conn->execute("SELECT KODE_LOKASI, NAMA_LOKASI FROM KWT_LOKASI_MP ORDER BY NAMA_LOKASI ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_LOKASI'];
				$on = $obj->fields['NAMA_LOKASI'];
				echo "<option value='$ov' ".is_selected($ov, $kode_lokasi)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>TARIF</td>
	<td><input type="text" name="tarif" id="tarif" size="14" value="<?php echo to_number($tarif); ?>"></td>
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

<input type="hidden" name="key_mp" id="key_mp" value="<?php echo $key_mp; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
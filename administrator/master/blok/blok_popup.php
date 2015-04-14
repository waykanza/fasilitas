<?php
require_once('blok_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	$('#kode_sektor').on('change', function(e) {
		e.preventDefault();
		var kode_sektor = $(this).val();
		$('#kode_cluster').html('');
		$('#kode_cluster').load(base_master + 'opt_cluster.php?kode_sektor=' + kode_sektor);
	});
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#kode_blok').inputmask('varchar', { repeat: '15' });
	$('#luas_kavling, #luas_bangunan').inputmask('numericDecimal', { integerDigits: '7' });

	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'blok/blok_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				if (data.act == 'Simpan') {
					$('#reset').click();
				} else if (data.act == 'Ubah') {
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


<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="140">KODE BLOK</td>
	<td><input type="text" name="kode_blok" id="kode_blok" size="15" value="<?php echo $kode_blok; ?>"></td>
</tr>
<tr>
	<td>SEKTOR</td>
	<td>
		<select name="kode_sektor" id="kode_sektor">
			<option value=""> -- SEKTOR -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_SEKTOR, NAMA_SEKTOR FROM KWT_SEKTOR ORDER BY NAMA_SEKTOR ASC");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SEKTOR'];
				$on = $obj->fields['NAMA_SEKTOR'];
				echo "<option value='$ov' ".is_selected($ov, $kode_sektor)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>CLUSTER</td>
	<td>
		<select name="kode_cluster" id="kode_cluster">
			<option value=""> -- CLUSTER -- </option>
			<?php
			if ($kode_sektor != '')
			{
				$obj = $conn->Execute("SELECT KODE_CLUSTER, NAMA_CLUSTER FROM KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor' ORDER BY NAMA_CLUSTER ASC");
				
				while( ! $obj->EOF)
				{
					$ov = $obj->fields['KODE_CLUSTER'];
					$on = $obj->fields['NAMA_CLUSTER'];
					echo "<option value='$ov' ".is_selected($ov, $kode_cluster)."> $on ($ov) </option>";
					$obj->movenext();
				}
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>LUAS KAVLING</td>
	<td><input type="text" name="luas_kavling" id="luas_kavling" size="12" value="<?php echo $luas_kavling; ?>"> m&sup2;</td>
</tr>
<tr>
	<td>LUAS BANGUNAN</td>
	<td><input type="text" name="luas_bangunan" id="luas_bangunan" size="12" value="<?php echo $luas_bangunan; ?>"> m&sup2;</td>
</tr>
<tr>
	<td>STATUS</td>
	<td><b><?php echo strtoupper(status_blok($status_blok)); ?></b></td>
</tr>
<tr>
	<td></td>
	<td class="td-action">
		<input type="submit" id="save" value=" <?php echo $act; ?> (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
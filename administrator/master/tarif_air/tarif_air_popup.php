<?php
require_once('tarif_air_proses.php');
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
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#blok1, #blok2, #blok3, #blok4').inputmask('numeric', { repeat: '5' });
	$('#stand_min_pakai').inputmask('integer', { repeat: '2' });
	$('#tarif1, #tarif2, #tarif3, #tarif4').inputmask('numeric', { repeat: '8' });
	$('#abonemen').inputmask('numeric', { repeat: '6' });
	$('#key_air').inputmask('varchar', { repeat: '8' });
	
	$('#denda_standar_air').inputmask('percent');
	$('#denda_bisnis_air').inputmask('percent');
	
	$('#kode_tipe').on('change', function(e) {
		e.preventDefault();
		var key_air = $('#kode_sk').val() + '-' + $(this).val();
		$('#key_air').val(key_air);
		$('#stand_min_pakai').val('');
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_master + 'tarif_air/tarif_air_proses.php',
			data	= $('#form').serialize(),
			blok1	= $('#blok1').val(),
			blok2	= $('#blok2').val(),
			blok3	= $('#blok3').val(),
			blok4	= $('#blok4').val(),
			kode_tipe = $('#kode_tipe').val(),
			stand_min_pakai	= $('#stand_min_pakai').val(),
			abonemen	= $('#abonemen').val(),
			X0 = 'X0';
		
		blok1 = blok1.replace(/[^0-9.]/g, '');
		blok2 = blok2.replace(/[^0-9.]/g, '');
		blok3 = blok3.replace(/[^0-9.]/g, '');
		blok4 = blok4.replace(/[^0-9.]/g, '');
		stand_min_pakai = stand_min_pakai.replace(/[^0-9.]/g, '');
		abonemen = abonemen.replace(/[^0-9.]/g, '');
		
		blok1 = (blok1 == '') ? 0 : parseFloat(blok1);
		blok2 = (blok2 == '') ? 0 : parseFloat(blok2);
		blok3 = (blok3 == '') ? 0 : parseFloat(blok3);
		blok4 = (blok4 == '') ? 0 : parseFloat(blok4);
		stand_min_pakai = (stand_min_pakai == '') ? 0 : parseFloat(stand_min_pakai);
		abonemen = (abonemen == '') ? 0 : parseFloat(abonemen);
		
		if (kode_tipe == X0)
		{
			if (abonemen != stand_min_pakai != blok1 != blok2 != blok3 != blok4 != 0) { alert('(X0) Stand minimumn pakai, batas blok dan abonemen harus = 0.'); $('#blok1').focus(); return false; }
		}
		else
		{
			//if (blok2 <= blok1) { alert('Blok 2 harus > blok 1.'); $('#blok2').focus(); return false; }
			//if (blok3 <= blok2) { alert('Blok 3 harus > blok 2.'); $('#blok3').focus(); return false; }
			if (blok4 != blok3) { alert('Blok 4 harus = blok 3.'); $('#blok4').focus(); return false; }
			if (abonemen < 1) { alert('Abonemen 4 harus > 0.'); $('#blok4').focus(); return false; }
			if (stand_min_pakai >= blok1) { alert('Stand minimumn pakai harus lebih kecil dari stand blok 1.'); $('#stand_min_pakai').focus(); return false; }
		}
		
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

<div id="msg"><?php echo $msg; ?></div>

<form name="form" id="form" method="post">
<table class="t-popup wauto">
<tr>
	<td width="120">KEY#</td>
	<td><input readonly="readonly" type="text" name="key_air" id="key_air" size="15" class="text-center" value="<?php echo $key_air; ?>"></td>
</tr>
<tr>
	<td>KATEGORI</td>
	<td>
		<select name="kode_tipe" id="kode_tipe">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_AIR ORDER BY NAMA_TIPE ASC");
			
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
	<td>MIN. PAKAI</td>
	<td><input type="text" name="stand_min_pakai" id="stand_min_pakai" size="3" class="text-center" value="<?php echo $stand_min_pakai; ?>"></td>
</tr>
<tr>
	<td>BATAS BLOK</td>
	<td>
		<table style="width:320px;">
		<tr><th>1</th><th>2</th><th>3</th><th>4</th></tr>
		<tr>
			<th><input type="text" name="blok1" id="blok1" size="11" value="<?php echo $blok1; ?>"></th>
			<th><input type="text" name="blok2" id="blok2" size="11" value="<?php echo $blok2; ?>"></th>
			<th><input type="text" name="blok3" id="blok3" size="11" value="<?php echo $blok3; ?>"></th>
			<th><input type="text" name="blok4" id="blok4" size="11" value="<?php echo $blok4; ?>"></th>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>TARIF BLOK</td>
	<td>
		<table style="width:320px;">
		<tr><th>1</th><th>2</th><th>3</th><th>4</th></tr>
		<tr>
			<th><input type="text" name="tarif1" id="tarif1" size="11" value="<?php echo $tarif1; ?>"></th>
			<th><input type="text" name="tarif2" id="tarif2" size="11" value="<?php echo $tarif2; ?>"></th>
			<th><input type="text" name="tarif3" id="tarif3" size="11" value="<?php echo $tarif3; ?>"></th>
			<th><input type="text" name="tarif4" id="tarif4" size="11" value="<?php echo $tarif4; ?>"></th>
		</tr>
		</table>
	</td>
</tr>
<tr>
	<td>ABONEMEN</td>
	<td><input type="text" name="abonemen" id="abonemen" size="11" value="<?php echo $abonemen; ?>"></td>
</tr>

<tr>
	<td>DENDA STANDAR</td>
	<td><input type="text" name="denda_standar_air" id="denda_standar_air" size="5" value="<?php echo $denda_standar_air; ?>"> %</td>
</tr>

<tr>
	<td>DENDA BISNIS</td>
	<td><input type="text" name="denda_bisnis_air" id="denda_bisnis_air" size="5" value="<?php echo $denda_bisnis_air; ?>"> %</td>
</tr>

<tr>
	<td>KETERANGAN</td>
	<td><textarea name="keterangan" id="keterangan" rows="3" cols="45"><?php echo $keterangan; ?></textarea></td>
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

<input type="hidden" name="kode_sk" id="kode_sk" value="<?php echo $kode_sk; ?>">
<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php
close($conn);
?>
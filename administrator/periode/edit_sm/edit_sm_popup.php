<?php
require_once('edit_sm_proses.php');
require_once('../../../config/terbilang.php');
$terbilang = new Terbilang;
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
	
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#stand_akhir, #stand_lalu, #stand_angkat').inputmask('numeric', { repeat: '10' });
	$('#pakai').inputmask('numeric', { repeat: '10', allowMinus: true });
	
	$('#diskon_air, #diskon_ipl').inputmask('numeric', { repeat: '10' });
	$('#diskon_air_persen, #diskon_ipl_persen').inputmask('percent100');
	
	function edit_stand_akhir()
	{
		var stand_akhir = $('#stand_akhir').val(),
			stand_lalu = $('#stand_lalu').val(),
			stand_angkat = $('#stand_angkat').val(),
			pakai = 0;
				
		stand_akhir = stand_akhir.replace(/[^0-9.]/g, '');
		stand_lalu = stand_lalu.replace(/[^0-9.]/g, '');
		stand_angkat = stand_angkat.replace(/[^0-9.]/g, '');
			
		stand_akhir = (stand_akhir == '') ? 0 : parseFloat(stand_akhir);
		stand_lalu = (stand_lalu == '') ? 0 : parseFloat(stand_lalu);
		stand_angkat = (stand_angkat == '') ? 0 : parseFloat(stand_angkat);
			
		pakai = ((stand_angkat + stand_akhir) - stand_lalu);
		$('#pakai').val(pakai);
		
		return false;
	}
	
	$('#stand_akhir, #stand_lalu, #stand_angkat').on('change', function(e) {
		e.preventDefault();
		edit_stand_akhir();
	});

	$('#diskon_air_persen').on('change', function(e) {
		e.preventDefault();
		diskon_air_persen = $(this).val();
		diskon_air_persen = diskon_air_persen.replace(/[^0-9.]/g, '');
		diskon_air_persen = (diskon_air_persen == '') ? 0 : parseFloat(diskon_air_persen);
		
		jumlah_air = <?php echo $jumlah_air; ?>;
		diskon_air = jumlah_air * (diskon_air_persen / 100);
		
		$('#diskon_air').val(diskon_air.toFixed(0));
		
		return false;
	});
	
	$('#diskon_ipl_persen').on('change', function(e) {
		e.preventDefault();
		diskon_ipl_persen = $(this).val();
		diskon_ipl_persen = diskon_ipl_persen.replace(/[^0-9.]/g, '');
		diskon_ipl_persen = (diskon_ipl_persen == '') ? 0 : parseFloat(diskon_ipl_persen);
		
		jumlah_ipl = <?php echo $jumlah_ipl; ?>;
		diskon_ipl = jumlah_ipl * (diskon_ipl_persen / 100);
		
		$('#diskon_ipl').val(diskon_ipl.toFixed(0));
		
		return false;
	});
	
	$('#diskon_air').on('change', function(e) {
		e.preventDefault();
		diskon_air = $(this).val();
		diskon_air = diskon_air.replace(/[^0-9.]/g, '');
		diskon_air = (diskon_air == '') ? 0 : parseFloat(diskon_air);
		
		jumlah_air = <?php echo $jumlah_air; ?>;
		diskon_air_persen = (diskon_air / jumlah_air) * 100;
		
		$('#diskon_air_persen').val(diskon_air_persen.toFixed(2));
		
		return false;
	});
	
	$('#diskon_ipl').on('change', function(e) {
		e.preventDefault();
		diskon_ipl = $(this).val();
		diskon_ipl = diskon_ipl.replace(/[^0-9.]/g, '');
		diskon_ipl = (diskon_ipl == '') ? 0 : parseFloat(diskon_ipl);
		
		jumlah_ipl = <?php echo $jumlah_ipl; ?>;
		diskon_ipl_persen = (diskon_ipl / jumlah_ipl) * 100;
		
		$('#diskon_ipl_persen').val(diskon_ipl_persen.toFixed(2));
		
		return false;
	});
	
	/* ACTION */
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save_stand_meter').on('click', function(e) {
		e.preventDefault();
		
		$('#act').val('stand_meter');
		do_process();
		
		return false;
	});
	
	$('#save_diskon_air').on('click', function(e) {
		e.preventDefault();
		
		$('#act').val('diskon_air');
		do_process();
		
		return false;
	});
	
	$('#save_diskon_ipl').on('click', function(e) {
		e.preventDefault();
		
		$('#act').val('diskon_ipl');
		do_process();
		
		return false;
	});
	
	function do_process() {
		var url			= base_periode + 'edit_sm/edit_sm_proses.php',
			data		= $('#form').serialize();
		
		$.post(url, data, function(data) {
			alert(data.msg);
			if (data.error == false) {
				location.reload();
			}
		}, 'json');
		
		return false;
	}
	
});
</script>
<style type="text/css">
table.smf { 
	margin-top: 10px;
}
table.smf th,
table.smf td { 
	font-size:11px;
}
table.smf td:nth-child(2) { 
	width:20px;
	text-align:center;
}
</style>
</head>
<body class="popup">

<form name="form" id="form" method="post">

<!--=========== PELANGGAN ===========-->
<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>NO. PELANGGAN</td><td>:</td><td><?php echo fm_nopel($no_pelanggan); ?></td></tr>
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<tr><td>BLOK / NO.</td><td>:</td><td><?php echo $kode_blok; ?></td></tr>
	<tr><td>SEKTOR</td><td>:</td><td><?php echo $nama_sektor; ?></td></tr>
	<tr><td>CLUSTER</td><td>:</td><td><?php echo $nama_cluster; ?></td></tr>
	<tr><td>STATUS</td><td>:</td><td><?php echo status_blok($status_blok); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>BULAN</td><td>:</td><td><?php echo strtoupper(fm_periode($periode_tag)); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>AKTIF IPL</td><td>:</td><td><?php echo status_check($aktif_ipl); ?></td></tr>
	<tr><td>GOL. TARIF</td><td>:</td><td><?php echo $key_ipl; ?></td></tr>
	<tr><td>PERIODE AWAL</td><td>:</td><td><?php echo strtoupper(fm_periode_first($periode_ipl_awal)); ?></td></tr>
	<tr><td>PERIODE AKHIR</td><td>:</td><td><?php echo strtoupper(fm_periode_last($periode_ipl_akhir)); ?></td></tr>
	<tr><td>LUAS KAVL.</td><td>:</td><td class="text-right"><?php echo to_money($luas_kavling,2); ?> m&sup2;</td></tr>
	<tr><td>TARIF</td><td>:</td><td class="text-right">Rp. <?php echo to_money($tarif_ipl); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>AKTIF AIR</td><td>:</td><td><?php echo status_check($aktif_air); ?></td></tr>
	<tr><td>GOL. TARIF</td><td>:</td><td><?php echo $key_air; ?></td></tr>
	<tr><td>PERIODE</td><td>:</td><td><?php echo strtoupper(fm_periode($periode_air)); ?></td></tr>
	
	<?php if ($aktif_air == '1') { ?>
		<tr><td>STAND LALU</td><td>:</td><td><input type="text" name="stand_lalu" id="stand_lalu" size="7" value="<?php echo to_money($stand_lalu); ?>"> m&sup3;</td></tr>
		<tr><td>GANTI METER</td><td>:</td><td><input type="text" name="stand_angkat" id="stand_angkat" size="7" value="<?php echo to_money($stand_angkat); ?>"> m&sup3;</td></tr>
		<tr><td>STAND AKHIR</td><td>:</td><td><input type="text" name="stand_akhir" id="stand_akhir" size="7" value="<?php echo to_money($stand_akhir); ?>"> m&sup3;</td></tr>
		<tr><td>PEMAKAIAN</td><td>:</td><td><input disabled="disabled" type="text" id="pakai" size="7" value="<?php echo to_money($real_pemakaian); ?>"> m&sup3; <input type="button" id="save_stand_meter" value=" Proses "></td></tr>
	<?php } else { ?>
		<tr><td>STAND LALU</td><td>:</td><td class="text-right"><?php echo to_money($stand_lalu); ?> m&sup3;</td></tr>
		<tr><td>GANTI METER</td><td>:</td><td class="text-right"><?php echo to_money($stand_angkat); ?> m&sup3;</td></tr>
		<tr><td>STAND AKHIR</td><td>:</td><td class="text-right"><?php echo to_money($stand_akhir); ?> m&sup3;</td></tr>
		<tr><td>PEMAKAIAN</td><td>:</td><td class="text-right"><?php echo to_money($real_pemakaian); ?> m&sup3;</td></tr>
	<?php } ?>
</table>

<div class="clear"></div>
<hr><br>

<!--=========== TAGIHAN ===========-->
<table class="t-popup f-left wauto" style="margin-right:40px;">
<tr>
	<td width="150">PEMAKAIAN BLOK - 1</td>
	<td width="25">:</td>
	<td width="75" class="text-right"><?php echo $blok1; ?></td>
	<td width="25" class="text-center">x</td>
	<td width="25">Rp.</td>
	<td width="125" class="text-right"><?php echo to_money($tarif1); ?></td>
	<td width="25" class="text-center">=</td>
	<td width="25">Rp.</td>
	<td width="125" class="text-right"><?php echo to_money($blok1 * $tarif1); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN BLOK - 2</td>
	<td>:</td>
	<td class="text-right"><?php echo $blok2; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif2); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok2 * $tarif2); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN BLOK - 3</td>
	<td>:</td>
	<td class="text-right"><?php echo $blok3; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif3); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok3 * $tarif3); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN BLOK - 4</td>
	<td>:</td>
	<td class="text-right"><?php echo $blok4; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif4); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok4 * $tarif4); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN MINIMAL</td>
	<td>:</td>
	<td class="text-right"><?php echo $stand_min_pakai; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif_min_pakai); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($stand_min_pakai * $tarif_min_pakai); ?></td>
</tr>
<tr>
	<td colspan="7"></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td colspan="5"><b>KETERANGAN TAGIHAN : <b></td>
	
	<td class="text-right">BIAYA AIR</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($jumlah_air); ?></td>
</tr>
<tr>
	<td colspan="5" rowspan="4" class="va-top">
		<textarea name="ket_ivc" rows="4" class="w100"><?php echo $ket_ivc; ?></textarea>
	</td>
	
	<td class="text-right">ABONEMEN</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($abonemen); ?></td>
</tr>
<tr>
	<td class="text-right">IPL</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
</tr>
<tr>
	<td class="text-right">DENDA</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($denda); ?></td>
</tr>
<tr>
	<td class="text-right">ADM</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($adm); ?></td>
</tr>
<tr>
	<td colspan="7"></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td colspan="6" class="text-right"><b>TOTAL</b></td>
	<td class="text-center"><b>=</b></td>
	<td><b>Rp.</b></td>
	<td class="text-right"><b><?php echo to_money($total); ?></b></td>
</tr>
<tr>
	<td colspan="5"></td>
	
	<td class="text-right">DISKON</td>
	<td class="text-center"><b>=</b></td>
	<td><b>Rp.</b></td>
	<td class="text-right"><?php echo to_money($diskon); ?></td>
</tr>
<tr>
	<td colspan="5" rowspan="4" class="va-top"></td>
	<td></td>
	<td></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td class="text-right"><b>TOTAL TAGIHAN</b></td>
	<td class="text-center"><b>=</b></td>
	<td><b>Rp.</b></td>
	<td class="text-right"><b><?php echo to_money($total - $diskon); ?></b></td>
</tr>
<tr>
	<td></td>
	<td></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td class="text-right"><b>JUMLAH TERIMA</b></td>
	<td class="text-center"><b>=</b></td>
	<td><b>Rp.</b></td>
	<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
</tr>
<tr>
	<td colspan="7">
		<br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja($total - $diskon)); ?> rupiah.</b></i>
	</td>
</tr>
</table>

<!--=========== DISKON ===========-->
<table class="t-popup f-left wauto">
<?php
if ($aktif_air == '1') 
{
	?>
	<tr>
		<td colspan="3" class="text-center"><b>DISKON AIR<hr></b></td>
	</tr>
	<tr>
		<td width="70">USER</td>
		<td colspan="2" width="180">: <?php echo $user_diskon_air; ?></td>
	</tr>
	<tr>
		<td>TANGGAL</td>
		<td colspan="2">: <?php echo $tgl_diskon_air; ?></td>
	</tr>
	<tr>
		<td>DISKON</td>
		<td width="30">: Rp.</td>
		<td class="text-right">
			<input type="text" name="diskon_air" id="diskon_air" size="15" value="<?php echo to_money($diskon_air); ?>">
			<input type="text" id="diskon_air_persen" size="5" value="<?php echo to_money($diskon_air_persen, 2); ?>"> %
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="ket_diskon_air" id="ket_diskon_air" rows="5" class="w100"><?php echo $ket_diskon_air; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="text-right">
			<input type="button" id="save_diskon_air" value=" Proses Diskon Air ">
		</td>
	</tr>
	<?php
}

if ($aktif_ipl == '1') 
{
	?>
	<tr>
		<td colspan="3" class="text-center"><br><b>DISKON IPL<hr></b></td>
	</tr>
	<tr>
		<td width="70">USER</td>
		<td colspan="2">: <?php echo $user_diskon_ipl; ?></td>
	</tr>
	<tr>
		<td>TANGGAL</td>
		<td colspan="2">: <?php echo $tgl_diskon_ipl; ?></td>
	</tr>
	<tr>
		<td>DISKON</td>
		<td>: Rp.</td>
		<td class="text-right">
			<input type="text" name="diskon_ipl" id="diskon_ipl" size="15" value="<?php echo to_money($diskon_ipl); ?>">
			<input type="text" id="diskon_ipl_persen" size="5" value="<?php echo to_money($diskon_ipl_persen, 2); ?>"> %
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<textarea name="ket_diskon_ipl" id="ket_diskon_ipl" rows="5" class="w100"><?php echo $ket_diskon_ipl; ?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="3" class="text-right">
			<input type="button" id="save_diskon_ipl" value=" Proses Diskon IPL ">
		</td>
	</tr>
	<?php
}
?>
</table>

<div class="clear"></div>

<br><hr>

<table class="t-popup">
<tr>
	<td></td>
	<td class="td-action">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="">
</form>

</body>
</html>
<?php close($conn); ?>
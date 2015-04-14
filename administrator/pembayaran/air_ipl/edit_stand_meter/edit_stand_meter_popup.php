<?php
require_once('edit_stand_meter_proses.php');
require_once('../../../../config/terbilang.php');
$terbilang = new Terbilang;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#stand_akhir, #stand_lalu, #stand_angkat, #pakai').inputmask('numeric', { repeat: '10' });
	
	$('#daftar_piutang').on('click', function(e) {
		e.preventDefault();
		
		var url = base_pembayaran + 'air_ipl/pembayaran/daftar_piutang.php?from_master=1&no_pelanggan=<?php echo $no_pelanggan; ?>';
		
		setPopup('Rincian Piutang', url, winWidth-100, winHeight-100);
		
		return false;
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_pembayaran + 'air_ipl/edit_stand_meter/edit_stand_meter_proses.php',
			data	= $('#form').serialize();
			
		if (edit_stand_akhir() == false)
		{
			return false;
		}
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				parent.showPopup($('#id').val());
			}
		}, 'json');
		
		return false;
	});
	
	$('#stand_akhir, #stand_lalu, #stand_angkat').on('change', function(e) {
		e.preventDefault();
		edit_stand_akhir();
	});
});

function edit_stand_akhir()
{
	var stand_akhir = jQuery('#stand_akhir').val(),
		stand_lalu = jQuery('#stand_lalu').val(),
		stand_angkat = jQuery('#stand_angkat').val(),
		pakai = 0;
			
	stand_akhir = stand_akhir.replace(/[^0-9.]/g, '');
	stand_lalu = stand_lalu.replace(/[^0-9.]/g, '');
	stand_angkat = stand_angkat.replace(/[^0-9.]/g, '');
		
	stand_akhir = (stand_akhir == '') ? 0 : parseFloat(stand_akhir);
	stand_lalu = (stand_lalu == '') ? 0 : parseFloat(stand_lalu);
	stand_angkat = (stand_angkat == '') ? 0 : parseFloat(stand_angkat);
		
	pakai = ((stand_angkat + stand_akhir) - stand_lalu);
	if (pakai < 0)
	{
		alert('Pemakaian tidak boleh minus!\nKlik reset untuk mengembalikan nilai sebelumnya.');
		jQuery('#stand_akhir, #stand_lalu, #stand_angkat').val('0');
		jQuery('#pakai').val('0');
		return false;
	}
	else
	{
		jQuery('#pakai').val(pakai);
		return true;
	}
	
	return false;
}

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
	<tr><td>NO. PELANGGAN</td><td>:</td><td><?php echo no_pelanggan($no_pelanggan); ?></td></tr>
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<tr><td>BLOK / NO.</td><td>:</td><td><?php echo $kode_blok; ?></td></tr>
	<tr><td>SEKTOR</td><td>:</td><td><?php echo $nama_sektor; ?></td></tr>
	<tr><td>CLUSTER</td><td>:</td><td><?php echo $nama_cluster; ?></td></tr>
	<tr><td>STATUS</td><td>:</td><td><?php echo status_blok($status_blok); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>BULAN</td><td>:</td><td><?php echo strtoupper(fm_periode($periode)); ?></td></tr>
	<tr><td>NO. KWITANSI</td><td>:</td><td><?php echo $no_kwitansi; ?></td></tr>
	<tr><td>PEMBAYARAN VIA</td><td>:</td><td><?php echo $bayar_melalui; ?></td></tr>
	<tr><td>NO. FAKTUR</td><td>:</td><td><?php echo $no_faktur_pajak; ?></td></tr>
	<tr><td>JUMLAH PIUTANG</td><td>:</td><td><?php echo $jumlah_piutang; ?> <input type="button" id="daftar_piutang" value=" Rincian Piutang "></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>AKTIF IPL</td><td>:</td><td><?php echo status_proses($aktif_ipl); ?></td></tr>
	<tr><td>GOL. TARIF</td><td>:</td><td><?php echo $key_ipl; ?></td></tr>
	<tr><td>PERIODE AWAL</td><td>:</td><td><?php echo strtoupper(fm_periode_first($periode_awal)); ?></td></tr>
	<tr><td>PERIODE AKHIR</td><td>:</td><td><?php echo strtoupper(fm_periode_last($periode_akhir)); ?></td></tr>
	<tr><td>LUAS KAVL.</td><td>:</td><td class="text-right"><?php echo to_money($luas_kavling,2); ?> m&sup2;</td></tr>
	<tr><td>TARIF</td><td>:</td><td class="text-right">Rp. <?php echo to_money($tarif_ipl); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>AKTIF AIR</td><td>:</td><td><?php echo status_proses($aktif_air); ?></td></tr>
	<tr><td>GOL. TARIF</td><td>:</td><td><?php echo $key_air; ?></td></tr>
	<tr><td>STAND LALU</td><td>:</td><td class="text-right"><input type="text" name="stand_lalu" id="stand_lalu" size="7" value="<?php echo to_money($stand_lalu); ?>"> m&sup3;</td></tr>
	<tr><td>GANTI METER</td><td>:</td><td class="text-right"><input type="text" name="stand_angkat" id="stand_angkat" size="7" value="<?php echo to_money($stand_angkat); ?>"> m&sup3;</td></tr>
	<tr><td>STAND AKHIR</td><td>:</td><td class="text-right"><input type="text" name="stand_akhir" id="stand_akhir" size="7" value="<?php echo to_money($stand_akhir); ?>"> m&sup3;</td></tr>
	<tr><td>PEMAKAIAN</td><td>:</td><td class="text-right"><input disabled="disabled" type="text" id="pakai" size="7" value="<?php echo to_money($real_pemakaian); ?>"> m&sup3;</td></tr>
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
	<td>PEMAKAIAN BLOK - 2</td><td>:</td>
	<td class="text-right"><?php echo $blok2; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif2); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok2 * $tarif2); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN BLOK - 3</td><td>:</td>
	<td class="text-right"><?php echo $blok3; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif3); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok3 * $tarif3); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN BLOK - 4</td><td>:</td>
	<td class="text-right"><?php echo $blok4; ?></td>
	<td class="text-center">x</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($tarif4); ?></td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($blok4 * $tarif4); ?></td>
</tr>
<tr>
	<td>PEMAKAIAN MINIMAL</td><td>:</td>
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
	<td colspan="6" class="text-right">BIAYA AIR</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($jumlah_air); ?></td>
</tr>
<tr>
	<td colspan="6" class="text-right">ABONEMEN</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($abonemen); ?></td>
</tr>
<tr>
	<td colspan="6" class="text-right">IPL</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
</tr>
<tr>
	<td colspan="6" class="text-right">DENDA</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($denda); ?></td>
</tr>
<tr>
	<td colspan="6" class="text-right">ADMINISTRASI</td>
	<td class="text-center">=</td>
	<td>Rp.</td>
	<td class="text-right"><?php echo to_money($administrasi); ?></td>
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
	<td colspan="5"><b>KETERANGAN BAYAR : <b></td>
	
	<td class="text-right">DISKON</td>
	<td class="text-center"><b>=</b></td>
	<td><b>Rp.</b></td>
	<td class="text-right"><?php echo to_money($diskon); ?></td>
</tr>
<tr>
	<td colspan="5" rowspan="4" class="va-top">
		<textarea readonly="readonly" rows="4" class="w100"><?php echo $keterangan_bayar; ?></textarea>
	</td>
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
	<td colspan="7"><br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja($total - $diskon)); ?> rupiah.</b></i></td>
</tr>
</table>

<!--=========== DISKON ===========-->

<table class="t-popup f-left wauto">
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
	<td class="text-right"><?php echo to_money($diskon_rupiah_air); ?></td>
</tr>

<tr>
	<td colspan="3" class="text-center"><br><b>DISKON IPL<hr></b></td>
</tr>
<tr>
	<td>USER</td>
	<td colspan="2">: <?php echo $user_diskon_ipl; ?></td>
</tr>
<tr>
	<td>TANGGAL</td>
	<td colspan="2">: <?php echo $tgl_diskon_ipl; ?></td>
</tr>
<tr>
	<td>DISKON</td>
	<td>: Rp.</td>
	<td class="text-right"><?php echo to_money($diskon_rupiah_ipl); ?></td>
</tr>

<tr>
	<td colspan="3" class="text-center"><br><b>KETERANGAN DISKON<hr></b></td>
</tr>
<tr>
	<td colspan="3"><textarea readonly="readonly" rows="5" class="w100"><?php echo $keterangan_diskon; ?></textarea></td>
</tr>
</table>

<div class="clear"></div>

<br><hr>

<!--=========== STATUS ===========-->
<table class="t-popup wauto">
<tr>
	<td width="100">STATUS BAYAR</td>
	<td width="10">:</td>
	<td width="150"><?php echo status_bayar($status_bayar); ?></td>
	<td width="100">TGL. BAYAR</td>
	<td width="10">:</td>
	<td width="200"><?php echo $tgl_bayar; ?></td>
	<td width="150">USER PEMBATALAN</td>
	<td width="10">:</td>
	<td width="200"><?php echo $user_batal; ?></td>
</tr>
<tr>
	<td>STATUS CETAK</td>
	<td>:</td>
	<td><?php echo status_cetak_kwt($status_cetak_kwt); ?></td>
	<td>KASIR</td>
	<td>:</td>
	<td><?php echo $kasir; ?></td>
	<td>TGL. PEMBATALAN</td>
	<td>:</td>
	<td><?php echo $tgl_batal; ?></td>
</tr>
<tr>
	<td>JENIS BAYAR</td>
	<td>:</td>
	<td><?php echo jenis_bayar($jenis_bayar); ?></td>
</tr>
</table>

<hr>

<table class="t-popup">
<tr>
	<td></td>
	<td colspan="5" class="td-action">
		<input type="button" id="save" value=" Simpan (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
<?php
require_once('pkl_proses.php');
require_once('../../../../../config/terbilang.php');
$terbilang = new Terbilang;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../../config/js/main.js"></script>
<script type="text/javascript">
/* Data */

function calculate()
{
	var 
		tarif			= jQuery('#tarif').val(),
		luas			= jQuery('#luas').val(),
		durasi			= jQuery('#durasi').val(),
		administrasi	= jQuery('#administrasi').val(),
		persen_nilai_tambah	= jQuery('#persen_nilai_tambah').val(),
		persen_nilai_kurang	= jQuery('#persen_nilai_kurang').val();

	tarif		= tarif.replace(/[^0-9.]/g, '');
	luas		= luas.replace(/[^0-9.]/g, '');
	durasi		= durasi.replace(/[^0-9.]/g, '');
	administrasi = administrasi.replace(/[^0-9.]/g, '');
	persen_nilai_tambah = persen_nilai_tambah.replace(/[^0-9.]/g, '');
	persen_nilai_kurang = persen_nilai_kurang.replace(/[^0-9.]/g, '');
	
	tarif		= (tarif == '') ? 0 : parseFloat(tarif);
	luas 		= (luas == '') ? 0 : parseFloat(luas);
	durasi 		= (durasi == '') ? 1 : parseFloat(durasi);
	administrasi = (administrasi == '') ? 0 : parseFloat(administrasi);
	persen_nilai_tambah = (persen_nilai_tambah == '') ? 0 : parseFloat(persen_nilai_tambah);
	persen_nilai_kurang = (persen_nilai_kurang == '') ? 0 : parseFloat(persen_nilai_kurang);
	
	var 
	nilai_tambah = Math.round((tarif * persen_nilai_tambah) / 100),
	nilai_kurang = Math.round((tarif * persen_nilai_kurang) / 100),
	
	tarif_nilai  = tarif + nilai_tambah - nilai_kurang;
	
	if (<?php echo $satuan12; ?> == 1){
		tanah1bulan = tarif_nilai;
	} else{
		tanah1bulan = tarif_nilai * luas;
	}
	
	jumlah_bayar = (tanah1bulan * durasi) + administrasi;
	pembulatan = Math.round(jumlah_bayar/1000) * 1000;
	
	jQuery('#tarif').val(tarif);
	jQuery('#luas').val(luas);
	jQuery('#durasi').val(durasi);
	jQuery('#administrasi').val(administrasi);	
	jQuery('#persen_nilai_tambah').val(persen_nilai_tambah);
	jQuery('#persen_nilai_kurang').val(persen_nilai_kurang);
	jQuery('#nilai_tambah').val(nilai_tambah);
	jQuery('#nilai_kurang').val(nilai_kurang);
	jQuery('#jumlah_bayar').val(jumlah_bayar);
}

jQuery(function($) {

	$('#administrasi, #persen_nilai_tambah, #persen_nilai_kurang, #durasi, #luas').on('change', function(e) {
		e.preventDefault();
		calculate();
		return false;
	});
	
	$('#nama_pelanggan').inputmask('varchar', { repeat: '40' });
	$("#npwp").inputmask('99.999.999.9-999.99');
	$('#no_telepon').inputmask('varchar', { repeat: '30' });
	$('#no_hp').inputmask('varchar', { repeat: '30' });
	$('#uang_pangkal').inputmask('numeric', { repeat: '9' });
	$('#tarif').inputmask('numeric', { repeat: '9' });
	$('#luas').inputmask('numericDecimal', { integerDigits: '5' });
	$('#durasi').inputmask('numeric', { repeat: '6' });
	$('#administrasi').inputmask('numeric', { repeat: '6' });
	$('#persen_nilai_tambah').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('#persen_nilai_kurang').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('#nilai_tambah').inputmask('numericDecimal', { repeat: '9' });
	$('#nilai_kurang').inputmask('numericDecimal', { repeat: '9' });
	$('#jumlah_bayar').inputmask('numericDecimal', { repeat: '9' });
	$('#tgl_serahterima').Zebra_DatePicker({
		format: 'd-m-Y',
		pair: $('#tgl_pemutusan')
	});
	$('#tgl_pemutusan').Zebra_DatePicker({
		format: 'd-m-Y'
	});
	
	/*
	if ($('#act').val() == 'Ubah') {
		document.getElementById("no_ktp").readOnly = true;
		document.getElementById("nama_pelanggan").readOnly = true;
		document.getElementById("npwp").readOnly = true;
		document.getElementById("no_telepon").readOnly = true;
		document.getElementById("no_hp").readOnly = true;
		document.getElementById("alamat").readOnly = true;
		
	}
	*/
	
/* BUTTON */
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});

	$('#print').on('click', function(e) {
		e.preventDefault();
		var id_pembayaran = $('#id_pembayaran').val();
		if (id_pembayaran == '')
		{
			alert('Data sewa lokasi PKL belum diperpanjang.');
			return false;
		}
		
		window.open(base_pembayaran_fasilitas + 'pkl/perpanjang/pkl_print.php?id_pembayaran=' + id_pembayaran);
		
		return false;
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		if ($('#id_pembayaran').val() != '')
		{
			alert(data.msg);
			parent.loadData();
		}
		else if (confirm("Anda yakin data telah pembayaran telah terisi dengan benar ?") == false)
		{
			return false;
		}
		
		var url		= base_pembayaran_fasilitas + 'pkl/perpanjang/pkl_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			if (data.error == false)
			{
				$('#id_pembayaran').val(data.id_pembayaran);
				$('#no_kwitansi').val(data.no_kwitansi);
			}
			alert(data.msg);
		}, 'json');
	});
	
	$('#history_perpanjang').on('click', function(e) {
		e.preventDefault();
		
		var url = base_pembayaran_fasilitas + 'pkl/perpanjang/pkl_history.php?id_pembayaran=<?php echo $id; ?>';
		
		setPopup('History Perpanjang Sewa Lokasi PKL', url, winWidth-100, winHeight-100);
		
		return false;
	});
});
</script>
</head>
<body class="popup">


<form name="form" id="form" method="post">

<table class="smf t-popup wauto f-left">
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<tr><td>TIPE PKL</td><td>:</td><td><?php echo $tipe_pkl; ?></td></tr>	
	<tr><td>LOKASI</td><td>:</td><td><?php echo $lokasi; echo ' (',$nlokasi,')';?></td></tr>
	<tr><td>LUAS</td><td>:</td><td><?php echo $luas.' m&sup2;' ?></td></tr>
	
	<tr><td>HISTORY</td><td>:</td><td><?php  ?> <input type="button" id="history_perpanjang" value=" History Perpanjang Sewa "></td></tr> 
</table>

<!-- LOKASI -->
<table class="wauto f-right">
<tr><td>TARIF</td><td>
<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value="<?php echo $tarif; ?>"><span><?php echo ' '.$satuan ?></span></td></tr>

<tr><td>BIAYA STRATEGIS</td><td>
<input type="text" name="persen_nilai_tambah" id="persen_nilai_tambah" size="18" value="">%
<span>Rp. </span><input readonly="readonly" type="text" name="nilai_tambah" id="nilai_tambah" size="18" value=""></td></tr>

<tr><td>DISCOUNT</td><td>
<input type="text" name="persen_nilai_kurang" id="persen_nilai_kurang" size="18" value="">% 
<span>Rp. </span><input readonly="readonly" type="text" name="nilai_kurang" id="nilai_kurang" size="18" value=""></td></tr>

<tr><td>LUAS</td><td>
<input type="text" name="luas" id="luas" size="18" value=""> <span>m&sup2;</span></td></tr>

<tr><td>DURASI</td><td>
<input type="text" name="durasi" id="durasi" size="18" value=""> <span>Bulan</span></td></tr>

<tr><td>ADMINISTRASI</td><td>
<input type="text" name="administrasi" id="administrasi" size="18" value=""></td></tr>

<tr><td>JML. BAYAR</td><td>
<input readonly="readonly" type="text" name="jumlah_bayar" id="jumlah_bayar" size="18" value=""></td></tr>

<tr><td>TGL SERAHTERIMA</td><td>
<input type="text" name="tgl_serahterima" id="tgl_serahterima" size="13" value=""></td></tr>

<tr><td>TGL PEMUTUSAN</td><td>
<input type="text" name="tgl_pemutusan" id="tgl_pemutusan" size="13" value=""></td></tr>

<tr><td>KETERANGAN</td><td>
<textarea name="keterangan" id="keterangan" rows="3" cols="40"></textarea></td></tr>

<tr><td>KASIR</td><td>
<input type="text" readonly="readonly" name="kasir" id="kasir" size="40" value="<?php echo $_SESSION['ID_USER']; ?>"></td></tr>
</table>

<div class="clear"><br></div>	
<table>
<tr>	
	<td class="td-action">
		<input type="submit" id="save" value=" <?php echo $act; ?> (Alt+S) ">
		<input type="button" id="print" value=" Print (Alt+P) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
<input type="text" id="id_pembayaran" value="" class="hidden">
</form>

</body>
</html>
<?php close($conn); ?>
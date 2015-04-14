<?php
require_once('../../../config/config.php');
$conn = conn();
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
/* lookup 
function get_lookup(nk)
{
	if (nk.length == 0) {
		jQuery('#wrap_lookup').fadeOut(); 
	} else if (nk.length >= 3) {
		jQuery.post(base_master + 'pelanggan_lookup.php?act=list&no_ktp=' + nk, function(data) { 
			jQuery('#lookup_pelanggan').html(data);
			if (data == '') {
				jQuery('#wrap_lookup').fadeOut(); 
			} else {
				jQuery('#wrap_lookup').fadeIn(); 
			}
		});
	}
}

function cls_lookup() { jQuery('#wrap_lookup').fadeOut(); }

function pp(nk)
{
	jQuery('#wrap_lookup').fadeOut();
	jQuery.post(base_master + 'pelanggan_lookup.php?act=sel&no_ktp=' + nk, function(data) {
		jQuery('#no_ktp').val(data.no_ktp);
		jQuery('#nama_pelanggan').val(data.nama_pelanggan);
		jQuery("#npwp").val(data.npwp);
		jQuery('#no_telepon').val(data.no_telepon);
		jQuery('#no_hp').val(data.no_hp);
		jQuery('#alamat').val(data.alamat);
	}, 'json');
}
*/

function clear()
{
	jQuery('#ukuran, #key_mpd, #tarif').val('');	
}

function calculate(id)
{
	clear();	
	//if(typeof(id) === 'undefined') { id = '1'; }
	
	var sel_kode_lokasi	= jQuery('#kode_lokasi option:selected');
	
	var 
		key_mpd			= sel_kode_lokasi.data('key-mpd'),
		tarif			= sel_kode_lokasi.data('tarif').toString(),
		pembayaran			= jQuery('#pembayaran').val(),
		persen_nilai_tambah	= jQuery('#persen_nilai_tambah-'+id).val(),
		persen_nilai_kurang	= jQuery('#persen_nilai_kurang-'+id).val();
	
	tarif					= (tarif == '') ? 0 : parseFloat(tarif);
	pembayaran				= (pembayaran == '') ? 0 : parseFloat(pembayaran);
	persen_nilai_tambah		= (persen_nilai_tambah == '') ? 0 : parseFloat(persen_nilai_tambah);
	persen_nilai_kurang		= (persen_nilai_kurang == '') ? 0 : parseFloat(persen_nilai_kurang);	
	
	if ((jQuery('#kode_mp').val() == 'A') || (jQuery('#kode_mp').val() == 'B')){
		tarifperbulan 	= tarif / 12;
		tarif2		= pembayaran * tarifperbulan;
	} else{
		tarif2		= pembayaran * tarif;
	}
	
	//tarifperbulan 	= tarif / 12;
	//tarif2		= pembayaran * tarifperbulan;
	
	nilai_tambah = (tarif2 * persen_nilai_tambah) / 100;
	nilai_kurang = (tarif2 * persen_nilai_kurang) / 100;
	total		 = tarif2 + nilai_tambah - nilai_kurang;
	//total	     = Math.round(total23/1000) * 1000;
	
	
	jQuery('#key_mpd').val(key_mpd);
	jQuery('#tarif').val(tarif);
	jQuery('#pembayaran').val(pembayaran);
	jQuery('#tarif2').val(tarif2);
	jQuery('#persen_nilai_tambah-'+id).val(persen_nilai_tambah);
	jQuery('#persen_nilai_kurang-'+id).val(persen_nilai_kurang);
	jQuery('#nilai_tambah-'+id).val(nilai_tambah);
	jQuery('#nilai_kurang-'+id).val(nilai_kurang);
	jQuery('#total-'+id).val(total);
	
	
	
}

jQuery(function($) {
	$('#no_ktp').on('keyup', function(e) {
		e.preventDefault();
		get_lookup(this.value);
		return false;
	});
	
	$('#kode_mp').on('change', function(e) {
		e.preventDefault();
		$('#kode_tipe').load(base_periode + 'periode_mp/opt_kategori_mp.php?kode_mp=' + $(this).val());
		
		if (($(this).val() == 'A') || ($(this).val() == 'B')){
			$('.satuan').html(' Bulan');
			$('#tahun').html(' / Tahun');
		} else if ($(this).val() == 'C') {
			$('.satuan').html(' Minggu');
			$('#tahun').html(' / Minggu');
		} else {
			$('.satuan').html(' Hari');
			$('#tahun').html(' / Hari');
		}
		clear();
		return false;
	});
	
	$('#kode_tipe').on('change', function(e) {
		e.preventDefault();
		var 
			sel_kode_tipe	= jQuery('#kode_tipe option:selected'),
			key_mp			= sel_kode_tipe.data('key-mp');
			
		$('#kode_lokasi').load(base_periode + 'periode_mp/opt_lokasi_mp.php?key_mp=' + key_mp);
		
		
		var sel_kode_tipe	= jQuery('#kode_tipe option:selected'),
			ukuran1	= sel_kode_tipe.data('ukuran1');
			ukuran2	= sel_kode_tipe.data('ukuran2');
		if(typeof(ukuran1) === 'undefined') { ukuran1 = '0'; };	if(typeof(ukuran2) === 'undefined') { ukuran2 = '0'; };			
		jQuery('#ukuran').html(ukuran1+' - '+ukuran2+' m&sup2;');
	
		
		clear();
		return false;
	});
	
	$('#kode_tipe, #kode_lokasi, #kode_mp, #pembayaran').on('change', function(e) {
		e.preventDefault();
		calculate();
		return false;
	});
	
	$(document).on('change', '.persen_nilai_tambah, .persen_nilai_kurang', function(e) {
		var id = $(this).parents('tr').find( ".ini_id" ).val();
		calculate(id);
		return false;
	});
	
	/* ACTION */
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		if (confirm("Apakah anda yakin data telah terisi dengan benar ?") == false)
		{
			return false;
		}
		
		var url		= base_periode + 'periode_mp/periode_mp_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				parent.loadData();
			}
		}, 'json');
		
		return false;
	});

/* BUTTON */
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });	
	
	$('#tarif').inputmask('numeric', { repeat: '9' });
	$('#tarif2').inputmask('numeric', { repeat: '9' });
	$('#pembayaran').inputmask('numeric', { repeat: '6' });
	$('.persen_nilai_tambah').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('.persen_nilai_kurang').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('.nilai_tambah, .nilai_kurang, .total').inputmask('numeric', { repeat: '10' });
	$('#periode_awal-1').Zebra_DatePicker({
		format: 'd-m-Y',
		pair: $('#periode_akhir-1')
	});
	$('#periode_akhir-1').Zebra_DatePicker({
		format: 'd-m-Y'
	});
});


function add_blok()
{
	var max = Number(jQuery('#max').val());
	id = max + 1;
	jQuery('#max').val(id);
	jQuery('' + 
	'<tr id="tr-ref-'+id+'">' +
		'<td></td>' +
		'<td>:</td>' +
		'<td><input type="text" name="periode_awal-'+id+'" size="15" id="periode_awal-'+id+'" class="periode_awal" value=""></td>'+
		'<td><input type="text" name="periode_akhir-'+id+'" size="15" id="periode_akhir-'+id+'" class="periode_akhir" value=""></td>'+
		'<td><input type="text" name="persen_nilai_tambah-'+id+'" id="persen_nilai_tambah-'+id+'" class="persen_nilai_tambah" size="15" value="0"></td>'+
		'<td><input type="text" name="nilai_tambah-'+id+'" id="nilai_tambah-'+id+'" class="nilai_tambah" size="15" value="0" readonly="readonly" ></td>'+
		'<td><input type="text" name="persen_nilai_kurang-'+id+'" id="persen_nilai_kurang-'+id+'" class="persen_nilai_kurang" size="15" value="0" ></td>'+
		'<td><input type="text" name="nilai_kurang-'+id+'" id="nilai_kurang-'+id+'" class="nilai_kurang" size="15" value="0" readonly="readonly"></td>'+
		'<td><input type="text" name="total-'+id+'" id="total-'+id+'" class="total" size="15" value="0" readonly="readonly"></td>'+
		'<td>' +
			'<input type="button" value=" X " onclick="del_blok(\''+id+'\')"> ' +
			'<input type="hidden" value="'+id+'" class="ini_id">'+
		'</td>' +
	'</tr>' +
	'').insertAfter('#tr-ref-'+ max);
	
	jQuery('.nilai_tambah, .nilai_kurang, .total').inputmask('numeric', { repeat: '10' });
	jQuery('.persen_nilai_tambah, .persen_nilai_kurang').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	
	jQuery('#periode_awal-'+id).Zebra_DatePicker({
		format: 'd-m-Y'
	});
	jQuery('#periode_akhir-'+id).Zebra_DatePicker({
		format: 'd-m-Y'
	});
	return false;
}

function del_blok(id)
{
	var max = Number(jQuery('#max').val());
	jQuery('#max').val(max - 1);
	
	jQuery('#tr-ref-' + max).remove();
	
	return false;
}



</script>
</head>
<body class="popup">
<form name="form" id="form" method="post">

<table class="w50 f-left">
<tr><td width="120">NO KTP</td><td>
<input type="text" name="no_ktp" id="no_ktp" size="30" autocomplete="off" value="" maxlength="30">
<!--
<div id="wrap_lookup">
	<div id="lookup_close"><span onclick="cls_lookup()">Tutup [X]</span></div>
	<div id="lookup_pelanggan"></div>
</div>
-->
</td></tr>

<tr><td>NAMA PELANGGAN</td><td>
<input type="text" name="nama_pelanggan" id="nama_pelanggan" size="40" value=""></td></tr>

<tr><td>NPWP</td><td>
<input type="text" name="npwp" id="npwp" size="20" value=""></td></tr>

<tr><td>NO TELEPON</td><td>
<input type="text" name="no_telepon" id="no_telepon" size="20" value=""></td></tr>

<tr><td>NO HP</td><td>
<input type="text" name="no_hp" id="no_hp" size="20" value=""></td></tr>

<tr><td>ALAMAT</td><td>
<textarea name="alamat" id="alamat" rows="3" cols="40"></textarea></td></tr>

</table>


<table class="t-popup wauto">
<tr><td width="120">MEDIA PROMOSI</td><td>
<select name="kode_mp" id="kode_mp">
		<option value=""> -- MEDIA PROMOSI -- </option>
		<option value="A"> BILLBOARD / SIGN BOARD / PYLON SIGN (A) </option>
		<option value="B"> NEON BOX / NEON SIGN (B) </option>
		<option value="C"> SPANDUK / UMBUL-UMBUL / STANDING DISPLAY (C) </option>
		<option value="D"> BANNER / BALIHO (D) </option> 
	</select>
</td></tr>	

<tr><td>KATEGORI</td><td>
<select name="kode_tipe" id="kode_tipe">
	<option value="" data-key-mp="" data-tarif="0"> -- KATEGORI -- </option>
</select>
</td></tr>

<tr><td></td><td id="ukuran" style="padding-bottom:15px;"></td></tr>

<tr><td width="120">LOKASI</td><td>
<select name="kode_lokasi" id="kode_lokasi">
	<option value="0" data-key-mpd='' data-tarif='' data-ukuran1='' data-ukuran2=''> -- LOKASI -- </option>
</select>
</td></tr>

<tr><td>KODE TARIF</td><td>
<input readonly="readonly" type="text" name="key_mpd" id="key_mpd" size="13" value=""></td></tr>

<tr><td>TARIF</td><td>
<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value=""><span id="tahun"> / Tahun</span></tr>

<tr><td>KETERANGAN</td><td>
<textarea name="keterangan" id="keterangan" rows="3" cols="40"></textarea></td></tr>

<tr><td>KASIR</td><td>
<input type="text" readonly="readonly" name="kasir" id="kasir" size="40" value="<?php echo $_SESSION['ID_USER']; ?>"></td></tr>
</table>

<div class="clear"></div>
<br>
<div class="clear"></div>
<hr><br>

<table class="t-popup wauto">
<tr>
	<td width="120">CARA PEMBAYARAN</td>
	<td><input type="text" name="pembayaran" id="pembayaran" size="15" value="0"><span class="satuan"> Bulan</span></td>
</tr>	
<tr>	
	<td>TARIF</td>
	<td><input type="text" name="tarif2" id="tarif2" size="15" value="0" readonly="readonly"></td>
</tr>
</table>

<div class="clear"></div>
<br><br>

<table class="t-popup wauto">
<tr>
	<td rowspan="2"></td>
	<td rowspan="2"></td>
	<td rowspan="2" class="text-center"><b>PERIODE AWAL</b></td>
	<td rowspan="2" class="text-center"><b>PERIODE AKHIR</b></td>
	<td colspan="2" class="text-center"><b>BIAYA STRATEGIS</b></td>
	<td colspan="2" class="text-center"><b>DISCOUNT</b></td>
	<td rowspan="2" class="text-center"><b>TOTAL</b></td>
</tr>

<tr>
<td colspan="1" class="text-center">%</td>
<td colspan="1" class="text-center">Rp</td>
<td colspan="1" class="text-center">%</td>
<td colspan="1" class="text-center">Rp</td>
</tr>
<tr id="tr-ref-1">
	<td>Periode</td>
	<td>:</td>
	<td><input type="text" name="periode_awal-1" id="periode_awal-1" class="periode_awal" size="15" value=""></td>
	<td><input type="text" name="periode_akhir-1" id="periode_akhir-1" class="periode_akhir" size="15" value=""></td>
	<td><input type="text" name="persen_nilai_tambah-1" id="persen_nilai_tambah-1" class="persen_nilai_tambah" size="15" value="0"></td>
	<td><input type="text" name="nilai_tambah-1" id="nilai_tambah-1" class="nilai_tambah" size="15" value="0" readonly="readonly"></td>
	<td><input type="text" name="persen_nilai_kurang-1" id="persen_nilai_kurang-1" class="persen_nilai_kurang" size="15" value="0"></td>
	<td><input type="text" name="nilai_kurang-1" id="nilai_kurang-1" class="nilai_kurang" size="15" value="0" readonly="readonly"></td>
	<td><input type="text" name="total-1" id="total-1" class="total" size="15" value="0" readonly="readonly"></td>
	<td><input type="button" value=" + " onclick="add_blok()">
		<input type="hidden" value="1" class="ini_id">
	</td>
</tr>
</table>

<input type="hidden" name="max" id="max" value="1">

<table class="t-popup">
<tr>
	<td class="td-action">
		<input type="submit" id="save" value=" Simpan (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>
</form>

</body>
</html>
<?php close($conn); ?>
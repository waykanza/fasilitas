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
<link type="text/css" href="../../../plugin/css/zebra/jquery-ui.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-ui.js"></script>

<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<!--<script type="text/javascript" src="../../../plugin/window/javascripts/prototype.js"></script>-->
<script type="text/javascript" src="../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
function clear()
{
	jQuery('#ukuran, #key_mpd, #tarif').val('');	
}
function clear_nilai(){
	jQuery('#persen_nilai_kurang-1, #persen_nilai_tambah-1, #nilai_kurang-1, #nilai_tambah-1').val('0');
}

function calculate(id)
{	
	//if(typeof(id) === 'undefined') { id = '1'; }
	
	var sel_kode_lokasi	= jQuery('#kode_lokasi option:selected');
	
	var 
		key_mpd			= sel_kode_lokasi.data('key-mpd'),
		tarif			= jQuery('#total_tarif').val(),
		nilai_tambah 	= jQuery('#nilai_tambah-1').val(),
		nilai_kurang 	= jQuery('#nilai_kurang-1').val(),
		periode 		= jQuery('#periode').val();
		//pembayaran			= jQuery('#pembayaran').val(),
		//persen_nilai_tambah	= jQuery('#persen_nilai_tambah-'+id).val(),
		//persen_nilai_kurang	= jQuery('#persen_nilai_kurang-'+id).val();
	
	tarif					= (tarif == '') ? 0 : conv(tarif);
	nilai_tambah 			= (nilai_tambah == '') ? 0 : conv(nilai_tambah);
	nilai_kurang 			= (nilai_kurang == '') ? 0 : conv(nilai_kurang);
	periode 				= (periode == '') ? 0 : parseFloat(periode);
	//pembayaran				= (pembayaran == '') ? 0 : parseFloat(pembayaran);
	//persen_nilai_tambah		= (persen_nilai_tambah == '') ? 0 : parseFloat(persen_nilai_tambah);
	//persen_nilai_kurang		= (persen_nilai_kurang == '') ? 0 : parseFloat(persen_nilai_kurang);	
	
	//if ((jQuery('#kode_mp').val() == 'A') || (jQuery('#kode_mp').val() == 'B')){
	//	tarifperbulan 	= tarif / 12;
	//	tarif2		= pembayaran * tarifperbulan;
	//} else{
	//	tarif2		= pembayaran * tarif;
	//}
	//tarifperbulan 	= tarif / 12;
	//tarif2		= pembayaran * tarifperbulan;
	
	total		 = tarif + nilai_tambah - nilai_kurang;
	//total	     = Math.round(total23/1000) * 1000;
	
	jQuery('#key_mpd').val(key_mpd);
	//jQuery('#tarif').val(tarif);
	//jQuery('#pembayaran').val(pembayaran);
	//jQuery('#tarif2').val(tarif2);
	jQuery('#total-'+id).val(total);
}

function calculate_nilai(){
	var
		periode 			= jQuery('#periode').val(),
		tarif 				= jQuery('#total_tarif').val(),
		persen_nilai_kurang = jQuery('#persen_nilai_kurang-1').val(),
		persen_nilai_tambah = jQuery('#persen_nilai_tambah-1').val();

	total = (tarif == '') ? 0 : conv(tarif);
	total = periode * total;
	persen_nilai_kurang = (persen_nilai_kurang == '') ? 0 : parseFloat(persen_nilai_kurang);
	persen_nilai_tambah = (persen_nilai_tambah == '') ? 0 : parseFloat(persen_nilai_tambah);
	nilai_kurang = (persen_nilai_kurang/100)*total;
	nilai_tambah = (persen_nilai_tambah/100)*total;
	jQuery('#nilai_kurang-1').val(nilai_kurang);
	jQuery('#nilai_tambah-1').val(nilai_tambah);
	calculate(1);
}
function conv(x){
	 return parseFloat(x.replace(',','').replace(',','').replace(',',''));
}

function calculate_total_tarif(){
	var	
		total 				= 0;
		uang_pangkal		= jQuery('#uang_pangkal').val(),
		tarif 				= jQuery('#tarif').val(),
		luas 				= jQuery('#luas').val(),
		sel_kategori 		= jQuery('#kategori option:selected'),
		satuan				= sel_kategori.data('satuan');

	uang_pangkal = (uang_pangkal == '') ? 0 : conv(uang_pangkal);
	luas = (luas == '') ? 0 : conv(luas);
	tarif = (tarif == '') ? 0 : conv(tarif);

	if(satuan=='0'){
		total = (luas * tarif)+ uang_pangkal;
	}
	else{
		total = uang_pangkal + tarif;
	}
	jQuery('#total_tarif').val(total);
}

function calculate_persen(){
	var
		periode 			= jQuery('#periode').val(),
		tarif 				= jQuery('#total_tarif').val(),
		nilai_kurang 		= jQuery('#nilai_kurang-1').val(),
		nilai_tambah 		= jQuery('#nilai_tambah-1').val();

	total = (tarif == '') ? 0 : conv(tarif);
	total = periode * total;
	nilai_kurang = (nilai_kurang == '') ? 0 : conv(nilai_kurang);
	nilai_tambah = (nilai_tambah == '') ? 0 : conv(nilai_tambah);
	persen_nilai_kurang = (nilai_kurang/total)*100;
	persen_nilai_tambah = (nilai_tambah/total)*100;
	jQuery('#persen_nilai_kurang-1').val(persen_nilai_kurang);
	jQuery('#persen_nilai_tambah-1').val(persen_nilai_tambah);
	calculate(1);
}

jQuery(function($) {
	$('#kode_lokasi').load(base_periode + 'periode_pkl/opt_pkl_lokasi.php');

	$('#kode_lokasi').on('change', function(e) {
		e.preventDefault();
		var 
			key_lokasi		= jQuery('#kode_lokasi').val();
		$('#kategori').load(base_periode + 'periode_pkl/opt_pkl_kategori.php?lokasi=' + key_lokasi);
		
	});

	$('#luas').on('change', function(e) {
		e.preventDefault();
		calculate_total_tarif();
		calculate(1);
	});

	$('#kategori').on('change', function(e) {
		e.preventDefault();
		var 
			sel_kategori	= jQuery('#kategori option:selected'), 
			uang_pangkal	= sel_kategori.data('uang_pangkal'),
			key_pkl 		= sel_kategori.data('key_pkl'),
			tarif 			= sel_kategori.data('tarif'),
			satuan			= sel_kategori.data('satuan');
		jQuery('#uang_pangkal').val(uang_pangkal);
		jQuery('#tarif').val(tarif);
		jQuery('#key_pkl').val(key_pkl);
		if(satuan=='0'){
			jQuery('#satuan').html(' /M2');
		}else{
			jQuery('#satuan').html(' /BULAN');
		}
		calculate_total_tarif();
		calculate(1);
	});

	$('#kode_tipe, #kode_lokasi, #kode_mp, #pembayaran,#periode').on('change', function(e) {
		e.preventDefault();
		clear_nilai();
		calculate(1);
		return false;
	});
	
	$(document).on('change', '.persen_nilai_tambah, .persen_nilai_kurang', function(e) {
		calculate_nilai();
		return false;
	});

	$(document).on('change', '.nilai_tambah, .nilai_kurang', function(e) {
		calculate_persen();
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
		
		var url		= base_periode + 'periode_pkl/periode_pkl_proses.php',
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
	$('#total_tarif').inputmask('numeric', { repeat: '9' });
	$('#tarif').inputmask('numeric', { repeat: '9' });
	$('#uang_pangkal').inputmask('numeric', { repeat: '9' });
	$('#tarif2').inputmask('numeric', { repeat: '9' });
	$('#periode').inputmask('numeric', { repeat: '3' });
	$('#pembayaran').inputmask('numeric', { repeat: '6' });
	$('.persen_nilai_tambah').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('.persen_nilai_kurang').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
	$('.nilai_tambah, .nilai_kurang, .total').inputmask('numeric', { repeat: '12' });
	$('#periode_awal-1').Zebra_DatePicker({
		format: 'd-m-Y',
		pair: $('#periode_akhir-1')
	});
	$('#periode_akhir-1').Zebra_DatePicker({
		format: 'd-m-Y'
	});
});
/*
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
*/

</script>
</head>
<body class="popup">
<form name="form" id="form" method="post">

<table class="w50 f-left">
<tr><td width="120">NO VIRTUAL ACCOUNT</td><td>
	<input type="text" id="no_va" name = 'no_va'>
</div>
</td></tr>

<tr><td>NAMA PELANGGAN</td><td>
<textarea name="nama_pelanggan" id="nama" size="5"></textarea></td></tr>

<tr><td>NO TELEPON</td><td>
<textarea name="no_telepon" id="telepon" size="5"></textarea></td></tr>

<tr><td>ALAMAT</td><td>
<textarea name="alamat" id="jalan" rows="3" cols="40"></textarea></td></tr>

<tr><td>NPWP</td><td>
<textarea name="npwp" id="npwp" size="10"></textarea></td></tr>
</table>

<table class="t-popup wauto">
<tr><td width="120">LOKASI</td><td>
<select name="kode_lokasi" id="kode_lokasi">
	<option value="" data-kode-lokasi="" >  -- LOKASI --</option>	
</select>
</td></tr>	

<tr><td>KATEGORI</td><td>
<select name="kategori" id="kategori">
	<option > -- KATEGORI -- </option>
</select>
</td></tr>

<tr><td></td><td id="ukuran" style="padding-bottom:15px;"></td></tr>

<tr><td>KODE TARIF</td><td>
<input readonly="readonly" type="text" name="key_pkl" id="key_pkl" size="13" value=""></td></tr>

<tr><td>UANG PANGKAL</td><td>
<input readonly="readonly" type="text" name="uang_pangkal" id="uang_pangkal" size="13" value=""></tr>

<tr><td>TARIF</td><td>
<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value=""><span id="satuan"> / M2</span></tr>

<tr><td>LUAS</td><td>
<input type="text" name="luas" id="luas" size="13" value="1"></tr>

<tr><td>TOTAL TARIF</td><td>
<input readonly="readonly" type="text" name="total_tarif" id="total_tarif" size="13" value=""></tr>

<tr><td>PERIODE</td><td>
<input type="text" name="periode" id="periode" size="13" value="1"><span id="periode_type"> Bulan</span></tr>

<tr><td>KODE BLOK</td><td>
<input type="text" name="kode_blok" id="kode_blok" size="3" value="1"></tr>

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
<!--
<tr>
	<td width="120">CARA PEMBAYARAN</td>
	<td><input type="text" name="pembayaran" id="pembayaran" size="15" value="0"><span class="satuan"> Bulan</span></td>
</tr>

<tr>	
	<td>TARIF</td>
	<td><input type="text" name="tarif2" id="tarif2" size="15" value="0" readonly="readonly"></td>
</tr>
</table>
-->	
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
	<td><input type="text" name="nilai_tambah-1" id="nilai_tambah-1" class="nilai_tambah" size="15" value="0"></td>
	<td><input type="text" name="persen_nilai_kurang-1" id="persen_nilai_kurang-1" class="persen_nilai_kurang" size="15" value="0"></td>
	<td><input type="text" name="nilai_kurang-1" id="nilai_kurang-1" class="nilai_kurang" size="15" value="0"></td>
	<td><input type="text" name="total-1" id="total-1" class="total" size="15" value="0" readonly="readonly"></td>
	
	<!--
	<td><input type="button" value=" + " onclick="add_blok()">
		<input type="hidden" value="1" class="ini_id">
		-->
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
<!--
<script>
$(function() {
    <?php
	$obj = $conn->execute("
	SELECT 
		f.NO_PELANGGAN
	FROM FSL_PELANGGAN f
	ORDER BY f.NO_PELANGGAN ASC");
	?>
	
	 var availableTags = [
    <?php 
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['NO_PELANGGAN'];
		echo '"';
		echo $ov;
		echo '",';
		$obj->movenext();
	}
    ?>  
    ];
    $( "#no_va" ).autocomplete({
      source: availableTags
    });
  });
  
    $(document).ready(function() {
        $('#no_va').change(function() {
            var value = $(this).val();
            alert(value);
            $(this).val(value.replace(/00040E-/i, ''));
        });
    });         
function tampil_data(){
	no_va = document.getElementById('no_va').value;
	$.post('cari.php?no_va='+no_va, function(result)
	{
		var data = result.split("|");
		$("#jalan").html(data[2]);
		$("#nama").html(data[0]);
		$("#telepon").html(data[1]);
	});
	
}
</script>
-->
<?php close($conn); ?>
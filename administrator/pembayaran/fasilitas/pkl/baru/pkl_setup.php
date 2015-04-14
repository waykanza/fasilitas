<div class="title-page">PEMBAYARAN SEWA LOKASI PEDAGANG KAKI LIMA <br> PELANGGAN BARU </div>
<div id="msg"></div>

<form name="form" id="form" method="post">
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
		jQuery('#kode_bank').val(data.kode_bank);
		jQuery('#no_rekening').val(data.no_rekening);
	}, 'json');
}
*/
/* Data */
function clear()
{
	jQuery('#detail_lokasi, #satuan, .satuan').html('');
	jQuery('#key_pkl, #uang_pangkal, #tarif, #jumlah_bayar, #tgl_pemutusan').val('');
	
	
	var sel_kode_lokasi	= jQuery('#kode_lokasi option:selected'),
		detail_lokasi	= sel_kode_lokasi.data('detail-lokasi');
		
	jQuery('#detail_lokasi').html(detail_lokasi);
}

function calculate()
{
	clear();
	
	var sel_kode_tipe	= jQuery('#kode_tipe option:selected');
	
	var satuan			= sel_kode_tipe.data('satuan'),
		key_pkl			= sel_kode_tipe.data('key-pkl'),
		uang_pangkal	= sel_kode_tipe.data('uang-pangkal'),
		tarif			= sel_kode_tipe.data('tarif').toString(),
		luas			= jQuery('#luas').val(),
		durasi			= jQuery('#durasi').val(),
		administrasi	= jQuery('#administrasi').val(),
		persen_nilai_tambah	= jQuery('#persen_nilai_tambah').val(),
		persen_nilai_kurang	= jQuery('#persen_nilai_kurang').val(),
		durasi	= jQuery('#durasi').val();

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
	
	if (satuan == 1){
		tanah1bulan = tarif_nilai;
	} else{
		tanah1bulan = tarif_nilai * luas;
	}
	
	jumlah_bayar = (tanah1bulan * durasi) + administrasi + uang_pangkal;
	//pembulatan = Math.round(jumlah_bayar/1000) * 1000;
	
	jQuery('.satuan').html(((satuan == '1') ? 'Bulan' : 'm&sup2;'));
	jQuery('#key_pkl').val(key_pkl);
	jQuery('#uang_pangkal').val(uang_pangkal);
	jQuery('#satuan').val(satuan);
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
	
	$('#no_ktp').on('keyup', function(e) {
		e.preventDefault();
		get_lookup(this.value);
		return false;
	});
	
	$('#kode_lokasi').on('change', function(e) {
		e.preventDefault();
		$('#kode_tipe').load(base_pembayaran_fasilitas + 'pkl/baru/opt_kategori_pkl.php?kode_lokasi=' + $(this).val());
		clear();
		return false;
	});
	
	$('#kode_tipe, #administrasi, #persen_nilai_tambah, #persen_nilai_kurang, #durasi, #luas').on('change', function(e) {
		e.preventDefault();
		calculate();
		return false;
	});

	$('#nama_pelanggan').inputmask('varchar', { repeat: '40' });
	$("#npwp").inputmask('99.999.999.9-999.99');
	$('#no_telepon').inputmask('varchar', { repeat: '30' });
	$('#no_hp').inputmask('varchar', { repeat: '30' });
	$('#no_rekening').inputmask('varchar', { repeat: '40' });
	$('#no_trx').inputmask('varchar', { repeat: '40' });
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
	
/* BUTTON */
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
	$(document).on('click', '#reset', function(e) {
		$('#msg').html('');
		$('#form').reset();
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		var id_pembayaran = $('#id_pembayaran').val();
		if (id_pembayaran == '')
		{
			alertPopup('Perhatian!', "Data pembayaran belum disimpan.");
			return false;
		}
		
		window.open(base_pembayaran_fasilitas + 'pkl/baru/pkl_print.php?id_pembayaran=' + id_pembayaran);
		
		return false;
	});
	
	$(document).on('click', '#save', function(e) {
		e.preventDefault();
		if ($('#id_pembayaran').val() != '')
		{
			alert("Data pembayaran telah disimpan.");
			return false;
		}
		else if (confirm("Anda yakin data telah pembayaran telah terisi dengan benar ?") == false)
		{
			return false;
		}
		
		var url		= base_pembayaran_fasilitas + 'pkl/baru/pkl_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			if (data.error == false)
			{
				$('#id_pembayaran').val(data.id_pembayaran);
				$('#no_kwitansi').val(data.no_kwitansi);
			}
			alert(data.msg);
		}, 'json');
		
		return false;
	});
});
</script>

<div class="white-setup">
<table class="w50 f-left">
<tr><td width="120">NO KTP</td><td>
<input type="text" name="no_ktp" id="no_ktp" size="30" autocomplete="off" value="" maxlength="30">
<!--
<div id="wrap_lookup">
	<div id="lookup_close"><span onclick="cls_lookup()">Tutup [X]</span></div>
	<div id="lookup_pelanggan"></div>
</div></td></tr>
-->
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
<!--
<tr><td>JENIS BAYAR</td><td>
<select name="jenis_bayar" id="jenis_bayar">
	<option value="1"> TUNAI </option>
	<option value="2"> K. DEBIT </option>
	<option value="3"> K. KREDIT </option>
</select>
</td></tr>

<tr id="tr-kode_bank" style="display:none;"><td>KODE BANK</td><td>
<select name="kode_bank" id="kode_bank">
	<option value=""> -- KODE BANK -- </option>
	<?php
	$obj = $conn->execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_BANK'];
		$on = $obj->fields['NAMA_BANK'];
		echo "<option value='$ov'> $on ($ov) </option>";
		$obj->movenext();
	}
	?>
</select>
</td></tr>

<tr id="tr-no_rekening" style="display:none;"><td>NO REKENING</td>
<td><input type="text" name="no_rekening" id="no_rekening" size="40" value=""></td></tr>

<tr id="tr-no_trx" style="display:none;"><td>NO TRX</td>
<td><input type="text" name="no_trx" id="no_trx" size="40" value=""></td></tr>
-->
<tr><td></td>
<td><input readonly="readonly" type="text" name="no_kwitansi" id="no_kwitansi" size="40" value="" class="hidden"></td></tr>
</table>

<!-- LOKASI -->

<table class="w50 f-right">
<tr><td width="120">LOKASI</td><td>
<select name="kode_lokasi" id="kode_lokasi">
	<option value=""> -- LOKASI -- </option>
	<?php
	$obj = $conn->execute("
	SELECT l.KODE_LOKASI, l.NAMA_LOKASI, l.DETAIL_LOKASI 
	FROM 
		KWT_LOKASI_PKL l
		JOIN KWT_SK_SEWA s ON l.KODE_SK = s.KODE_SK
	WHERE
		s.STATUS_SK = 1
	ORDER BY l.NAMA_LOKASI ASC
	");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_LOKASI'];
		$on = $obj->fields['NAMA_LOKASI'];
		$od = $obj->fields['DETAIL_LOKASI'];
		echo "<option value='$ov' data-detail-lokasi='$od'> $od ($on) </option>";
		$obj->movenext();
	}
	?>
</select>
</td></tr>

<!--
<tr><td></td><td id="detail_lokasi" style="padding-bottom:15px;"></td></tr>
-->

<tr><td>KATEGORI</td><td>
<select name="kode_tipe" id="kode_tipe">
	<option value="" data-key-pkl="" data-uang-pangkal="0" data-tarif="0" data-satuan="1"> -- KATEGORI -- </option>
</select>
</td></tr>

<tr><td>KODE TARIF</td><td>
<input readonly="readonly" type="text" name="key_pkl" id="key_pkl" size="13" value=""></td></tr>

<tr><td>UANG PANGKAL</td><td>
<input readonly="readonly" type="text" name="uang_pangkal" id="uang_pangkal" size="13" value=""></td></tr>

<tr><td>TARIF</td><td>
<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value=""> / <span class="satuan">m&sup2;</span>
<input type="hidden" name="satuan" id="satuan" size="1" value=""></td></tr>

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

<input type="button" id="save" value=" Simpan (Alt+S) ">
<input type="button" id="print" value=" Print (Alt+P) ">
<input type="reset" id="reset" value=" Reset (Alt+R) ">
</div>

<input type="text" id="id_pembayaran" value="" class="">
</form>
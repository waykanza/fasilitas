<?php
require_once('../../../config/config.php');
require_once('../../../config/Terbilang.php');
$bilangan  = new Terbilang;
error_reporting(0);
$conn = conn();
$koneksi = conn();
$id = $_GET['id'];
$periode = $_GET['periode'];
$query = "SELECT *, PELANGGAN_PKL.KETERANGAN, PELANGGAN_PKL.TARIF AS tar, PELANGGAN_PKL.UANG_PANGKAL AS up  FROM PELANGGAN_PKL LEFT JOIN KWT_TARIF_PKL  on PELANGGAN_PKL.KEY_PKL = KWT_TARIF_PKL.KEY_PKL WHERE ID_PEMBAYARAN = $id AND PEMBAYARAN = $periode";
$data = $conn->Execute($query);
while(!$data->EOF)
	{
	$key = $data->fields['KEY_PKL'];
	$query = "SELECT * FROM KWT_TARIF_PKL WHERE KEY_PKL = '$key'";
	$tarif_db = $conn->Execute($query);
	$uang_pangkal = $tarif_db->fields['UANG_PANGKAL'];
	$tarif = $tarif_db->fields['TARIF'];
	$updated[0]='0';
	$updated[1]='0';
	if (isset($data->fields['NO_KWITANSI'])){
		$p_status = 'Sudah di Print';
		$tarif = $data->fields['tar'];
		$uang_pangkal = $data->fields['up'];
	}
	else {
		$p_status = 'Belum di Print';
		if($tarif!=$data->fields['tar']){
			$updated[1] = '1'; 
		}	
		else{
			$tarif = $data->fields['tar'];
		}
		if($uang_pangkal!=$data->fields['up']){
			$updated[0] = '1';
		}	
		else{
			$uang_pangkal = $data->fields['up'];
		}
	}

	
	
	
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
		function load_data(){
				$('#tarif').load(base_invoice + 'invois_pkl/load_harga.php?key_mpd=' + jQuery('#key_mpd').val());
		}
		function clear()
		{
			jQuery('#ukuran, #key_mpd, #tarif').val('');	
		}
		function clear_nilai(){
			jQuery('#persen_nilai_kurang-1, #persen_nilai_tambah-1, #nilai_kurang-1, #nilai_tambah-1').val('0');
		}
		function calculate(id)
		{
			var periode 			= jQuery('#periode').val(),
				tarif 				= jQuery('#tarif').val(),
				luas 				= jQuery('#luas').val(),
				uang_pangkal		= jQuery('#uang_pangkal').val(),
				satuan 				= jQuery('#tipe_tarif').val(),
				nilai_kurang 		= jQuery('#nilai_kurang-1').val(),
				nilai_tambah 		= jQuery('#nilai_tambah-1').val();
			tarif = (tarif == '') ? 0 : conv(tarif);
			uang_pangkal = (uang_pangkal == '') ? 0 : conv(uang_pangkal);
			nilai_kurang = (nilai_kurang == '') ? 0 : conv(nilai_kurang);
			nilai_tambah = (nilai_tambah == '') ? 0 : conv(nilai_tambah);
			if(satuan==0){
				total = uang_pangkal+ (luas*tarif) + nilai_tambah - nilai_kurang;
			}else{
				total = uang_pangkal + tarif + nilai_tambah - nilai_kurang;
			}
			jQuery('#total-1').val(total);
		}

		function calculate_nilai(){
			var
				periode 			= jQuery('#periode').val(),
				tarif 				= jQuery('#tarif').val(),
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
		function calculate_persen(){
			var
				periode 			= jQuery('#periode').val(),
				tarif 				= jQuery('#tarif').val(),
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
		function showPrint(id)
		{
			var status = <?php if($updated[0] == '1'||$updated[1] == '1'){echo 'true';} else {echo 'false';}?>;
			if(status==true){
				alert("Data harus disimpan terlebih dahulu");
				return false;
			}
			var url = base_invoice + 'invois_pkl/invoice_print.php?id_pembayaran='+id+'&periode='+jQuery('#periode_num').val();
			window.open(url,'_blank');
			return false;
		}

		jQuery(function($) {
			calculate(1);
			if(jQuery('#tipe_tarif').val()==0){
				jQuery('#tipe_bayar').html('M2');
			}else{
				jQuery('#tipe_bayar').html(' / Bulan');
			}
			/*
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
			*/
			$('#tarif').on('change', function(e) {
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

				var status = <?php if(isset($data->fields['NO_KWITANSI'])){echo 'true';}else{echo 'false';}?>;
				if(status==true){
					alert("Data sudah tidak bisa diupdate");
					return false;
				}

				var url		= base_invoice + 'invois_pkl/invoice_update.php',
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
			$('#uang_pangkal').inputmask('numeric', { repeat: '9' });
			$('#tarif_lama').inputmask('numeric', { repeat: '9' });
			$('#up_lama').inputmask('numeric', { repeat: '9' });
			$('#periode').inputmask('numeric', { repeat: '3' });
			$('#pembayaran').inputmask('numeric', { repeat: '6' });
			$('.persen_nilai_tambah').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
			$('.persen_nilai_kurang').inputmask('percent', { integerDigits:3, fractionalDigits:9, groupSize:3 });
			$('.nilai_tambah, .nilai_kurang, .total').inputmask('numeric', { repeat: '10' });
			/*$('#periode_awal-1').Zebra_DatePicker({
				format: 'd-m-Y',
				pair: $('#periode_akhir-1')
			});
			$('#periode_akhir-1').Zebra_DatePicker({
				format: 'd-m-Y'
			});*/
		});

		</script>
		</head>
		<body class="popup">
		<form name="form" id="form" method="post">

		<table class="w50 f-left">
		<tr><td width="120">NO VIRTUAL ACCOUNT</td><td>
			<input type="text" readonly="readonly" id="no_va" name="no_va" value="<?php echo $data->fields['NO_PELANGGAN'];?>">
			<input type="hidden" id = "id_pembayaran" name = 'id_pembayaran' value="<?php echo $id;?>">
		</div>
		</td></tr>

		<tr><td>NAMA PELANGGAN</td><td>
		<textarea name="nama_pelanggan" id="nama" size="5" readonly="readonly"><?php echo $data->fields['NAMA_PELANGGAN'];?></textarea></td></tr>

		<tr><td>NO TELEPON</td><td>
		<textarea name="no_telepon" id="telepon" size="5" readonly="readonly"><?php echo $data->fields['NO_TELEPON'];?></textarea></td></tr>

		<tr><td>ALAMAT</td><td>
		<textarea name="alamat" id="jalan" rows="3" cols="40" readonly="readonly"><?php echo $data->fields['ALAMAT'];?></textarea></td></tr>

		<tr><td>NPWP</td><td>
		<textarea name="npwp" id="npwp" size="10" readonly="readonly"><?php echo $data->fields['NPWP'];?></textarea></td></tr>

		</table>


		<table class="t-popup wauto">
		<tr><td>PERIODE</td><td>
		<input type="text" readonly="readonly" name="periode_now" id="periode_now" size="13" value="<?php echo $bilangan->nama_bln($data->fields['PEMBAYARAN']);?>">
		<input type="hidden" name = 'periode_num' id = 'periode_num' value="<?php echo $data->fields['PEMBAYARAN'];?>">
		</tr>

		<tr><td>KODE TARIF</td><td>
		<input readonly="readonly" type="text" name="key_mpd" id="key_mpd" size="13" value="<?php echo $data->fields['KEY_PKL'];?>"></td></tr>

		<tr><td>UANG_PANGKAL</td><td>
		<input readonly="readonly" type="text" name="uang_pangkal" id="uang_pangkal" size="13" value="<?php echo $uang_pangkal;?>">
		<span id = "uang_pangkal_update"><?php if($updated[0]=='1'){ echo "<tr><td style='color:red;'>Tarif Lama : <td><input style='color:red;' size = 13 type ='text' readonly='readonly' id = 'up_lama' value = '".$data->fields['up']."'/>";}?></span></tr>

		<tr><td>TARIF</td><td>
		<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value="<?php echo $tarif;?>">
		<input type="hidden" name="tipe_tarif" id="tipe_tarif" value="<?php echo $satuan?>">
		<span id="tipe_bayar">/ Bulan </span><span id = "tarif_update"><?php if($updated[1]=='1'){ echo " <tr><td style='color:red;'>Tarif Lama : <td><input style='color:red;' size = 13 type ='text' readonly='readonly' id = 'tarif_lama' value = '".$data->fields['tar']."'/>";}?></span>

		<tr><td>LUAS</td><td>
		<input readonly="readonly" type="text" name="luas" id="luas" size="13" value="<?php echo $data->fields['LUAS'];?>"> M2</tr>
		
		<tr><td>JUMLAH PERIODE</td><td>
		<input type="text" readonly="readonly" name="periode" id="periode" size="13" value="<?php echo $data->fields['JUMLAH_PERIODE'];?>"><span id="periode_type"> Bulan</span></tr>

		<tr><td>KODE BLOK</td><td>
		<input type="text" readonly="readonly" name="kode_blok" id="kode_blok" size="13" value="<?php echo $data->fields['KODE_BLOK'];?>"></tr>

		<tr><td>KETERANGAN</td><td>
		<textarea name="keterangan" id="keterangan" rows="3" cols="40" value = "<?php echo $data->fields['KETERANGAN'];?>"></textarea></td></tr>

		<tr><td>KASIR</td><td>
		<input type="text" readonly="readonly" name="kasir" id="kasir" size="40" value="<?php echo $_SESSION['ID_USER']; ?>"></td></tr>
		<tr><td>STATUS</td><td>
		<input type="text" readonly="readonly" name="status" id="status" size="40" value="<?php echo $p_status; ?>"></td></tr>
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
			<td><input type="text" name="periode_awal-1" id="periode_awal-1" class="periode_awal" size="15" value="<?php echo $data->fields['PERIODE_AWAL']?>" readonly = "readonly" ></td>
			<td><input type="text" name="periode_akhir-1" id="periode_akhir-1" class="periode_akhir" size="15" value="<?php echo $data->fields['PERIODE_AKHIR']?>" readonly = "readonly"></td>
			<td><input type="text" name="persen_nilai_tambah-1" id="persen_nilai_tambah-1" class="persen_nilai_tambah" size="15" value="<?php echo $data->fields['PERSEN_NILAI_TAMBAH']?>"></td>
			<td><input type="text" name="nilai_tambah-1" id="nilai_tambah-1" class="nilai_tambah" size="15" value="<?php echo $data->fields['NILAI_TAMBAH']?>"></td>
			<td><input type="text" name="persen_nilai_kurang-1" id="persen_nilai_kurang-1" class="persen_nilai_kurang" size="15" value="<?php echo $data->fields['PERSEN_NILAI_KURANG']?>"></td>
			<td><input type="text" name="nilai_kurang-1" id="nilai_kurang-1" class="nilai_kurang" size="15" value="<?php echo $data->fields['NILAI_KURANG']?>"></td>
			<td><input type="text" name="total-1" id="total-1" class="total" size="15" value="0" readonly="readonly"></td>
			
			<!--
			<td><input type="button" value=" + " onclick="add_blok()">
				<input type="hidden" value="1" class="ini_id">
				-->
			</td>
		</tr>
		</table>

		<input type="hidden" name="max" id="max" value="1">

		</form>
		<br/>
		<button id="save">Simpan</button>
		<button onclick="showPrint(<?php echo $id;?>)">Print</button>
		<button id="close">Tutup</button>
		</body>
		</html>
		<?php
		$data->movenext();
	}
?>
<?php close($conn);
close($koneksi); ?>
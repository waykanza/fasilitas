<?php
require_once('../../../config/config.php');
$conn = conn();
$id = $_GET['id'];
$query = "SELECT * FROM PELANGGAN_MP WHERE ID_PEMBAYARAN = $id";
$data = $conn->Execute($query);
while(!$data->EOF)
	{
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

		jQuery(function($) {
			
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
				
				var url		= base_periode + 'periode_mp/periode_mp_update.php',
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
			$('#periode').inputmask('numeric', { repeat: '3' });
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

		</script>
		</head>
		<body class="popup">
		<form name="form" id="form" method="post">

		<table class="w50 f-left">
		<tr><td width="120">NO VIRTUAL ACCOUNT</td><td>
			<input type="text" readonly="readonly" id="no_va" name="no_va" value="<?php echo $data->fields['NO_PELANGGAN'];?>">
			<input type="hidden" name="id_pembayaran" value="<?php echo $data->fields['ID_PEMBAYARAN'];?>">
		</div>
		</td></tr>

		<tr><td>NAMA PELANGGAN</td><td>
		<textarea name="nama_pelanggan" id="nama" size="5" ><?php echo $data->fields['NAMA_PELANGGAN'];?></textarea></td></tr>

		<tr><td>NO TELEPON</td><td>
		<textarea name="no_telepon" id="telepon" size="5"><?php echo $data->fields['NO_TELEPON'];?></textarea></td></tr>

		<tr><td>ALAMAT</td><td>
		<textarea name="alamat" id="jalan" rows="3" cols="40"><?php echo $data->fields['ALAMAT'];?></textarea></td></tr>

		<tr><td>NPWP</td><td>
		<textarea name="npwp" id="npwp" size="10"><?php echo $data->fields['NPWP'];?></textarea></td></tr>

		</table>

		<!--
		<table class="t-popup wauto">

		<tr><td>KODE TARIF</td><td>
		<input readonly="readonly" type="text" name="key_mpd" id="key_mpd" size="13" value="<?php echo $data->fields['KEY_MPD'];?>"></td></tr>

		<tr><td>TARIF</td><td>
		<input readonly="readonly" type="text" name="tarif" id="tarif" size="13" value="<?php echo $data->fields['TARIF'];?>"><span id="tahun">/ Tahun</span></tr>

		<tr><td>PERIODE</td><td>
		<input type="text" name="periode" id="periode" size="13" value="<?php echo $data->fields['JUMLAH_PERIODE'];?>"><span id="periode_type"> Tahun</span></tr>

		<tr><td>KODE BLOK</td><td>
		<input type="text" name="kode_blok" id="kode_blok" size="3" value="<?php echo $data->fields['KODE_BLOK'];?>"></tr>


		<tr><td>KETERANGAN</td><td>
		<textarea name="keterangan" id="keterangan" rows="3" cols="40" value = "<?php echo $data->fields['KETERANGAN'];?>"></textarea></td></tr>

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
		<!--
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
				
			</td>
		</tr>
		</table>
-->
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
		<?php
		$data->movenext();
	}
?>
<?php close($conn); ?>
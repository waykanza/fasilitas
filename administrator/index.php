<?php 
require_once('../config/config.php');
$conn = conn();
clogin();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Administrator</title>
<link type="image/x-icon" rel="icon" href="../images/favicon.ico">

<!-- CSS -->
<link type="text/css" href="../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../config/css/menu.css" rel="stylesheet">
<link type="text/css" href="../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../config/js/menu.js"></script>
<script type="text/javascript" src="../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../plugin/js/jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../config/js/main.js"></script>
<style type="text/css">
html { height:100%; }
body {
	position:relative;
	background:#990000;
	margin:0;
}
body { height:100%; }
</style>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<span class="pkb">
			<span class="big">P</span>engelola <span class="big">K</span>awasan <span class="big">B</span>intaro
			<span class="desc">Program Fasilitas</span>
		</span>
	</div>
	<div id="menu"><?php include('menu.php'); ?></div>
	<div id="content">
		<div class="clear"></div>
		<?php 
		$cmd = (isset($_REQUEST['cmd'])) ? strip_tags($_REQUEST['cmd']) : '';
		switch (trim(base64_decode($cmd)))
		{
			# Master
			case 'parameter' 	: cmodul('101'); include('master/parameter/parameter_setup.php');break;
			case 'sektor'		: cmodul('102'); include('master/sektor/sektor_setup.php');break;
			case 'cluster'		: cmodul('103'); include('master/cluster/cluster_setup.php');break;
			case 'blok'			: cmodul('104'); include('master/blok/blok_setup.php');break;
			case 'bank'			: cmodul('105'); include('master/bank/bank_setup.php');break;
				
				## AIR, IPL, DEPOSIT
				case 'diskon'			: cmodul('106'); include('master/diskon/diskon_setup.php');break; 
				case 'pompa'			: cmodul('107'); include('master/pompa/pompa_setup.php');break; 
				
				case 'sk_air'			: cmodul('111'); include('master/sk_air/sk_air_setup.php');break;
				case 'kategori_air'		: cmodul('112'); include('master/kategori_air/kategori_air_setup.php');break;
				case 'tarif_air'		: cmodul('113'); include('master/tarif_air/tarif_air_setup.php');break;
				
				case 'sk_ipl'			: cmodul('121'); include('master/sk_ipl/sk_ipl_setup.php');break;
				case 'tarif_ipl'		: cmodul('122'); include('master/tarif_ipl/tarif_ipl_setup.php');break;
				case 'kategori_ipl'		: cmodul('123'); include('master/kategori_ipl/kategori_ipl_setup.php');break;
				
				
				case 'pelanggan_baru'	: cmodul('134'); include('master/pelanggan/new/pelanggan_setup.php');break; 
				
				## Fasilitas
				case 'pelanggan'			: include('master/fasilitas/pelanggan/pelanggan_setup.php');break; 
				case 'sk_sewa'				: include('master/fasilitas/sk_sewa/sk_sewa_setup.php');break;
				case 'sk_psp'				: include('master/fasilitas/sk_psp/sk_psp_setup.php');break;
				
				case 'kategori_pkl'			: include('master/fasilitas/kategori_pkl/kategori_pkl_setup.php');break; 
				case 'kategori_ksp'			: include('master/fasilitas/kategori_ksp/kategori_ksp_setup.php');break; 
				case 'kategori_psp'			: include('master/fasilitas/kategori_psp/kategori_psp_setup.php');break;
				case 'kategori_mp'			: include('master/fasilitas/kategori_mp/kategori_mp_setup.php');break;
				case 'kategori_tarif_mp_a'	: include('master/fasilitas/tarif_mp/a/kategori_tarif_mp_a_setup.php');break; 
				case 'kategori_tarif_mp_b'	: include('master/fasilitas/tarif_mp/b/kategori_tarif_mp_b_setup.php');break; 
				case 'kategori_tarif_mp_c'	: include('master/fasilitas/tarif_mp/c/kategori_tarif_mp_c_setup.php');break; 
				case 'kategori_tarif_mp_d'	: include('master/fasilitas/tarif_mp/d/kategori_tarif_mp_d_setup.php');break; 
				
				case 'lokasi_pkl'			: include('master/fasilitas/lokasi_pkl/lokasi_pkl_setup.php');break; 
				case 'lokasi_ksp'			: include('master/fasilitas/lokasi_ksp/lokasi_ksp_setup.php');break;
				case 'lokasi_mp'			: include('master/fasilitas/lokasi_mp/lokasi_mp_setup.php');break; 
				case 'fungsi_psp'			: include('master/fasilitas/fungsi_psp/fungsi_psp_setup.php');break;
				
				case 'tarif_pkl'			: include('master/fasilitas/tarif_pkl/tarif_pkl_setup.php');break;
				case 'tarif_ksp'			: include('master/fasilitas/tarif_ksp/tarif_ksp_setup.php');break;
				case 'tarif_psp'			: include('master/fasilitas/tarif_psp/tarif_psp_setup.php');break;
				
				case 'detail_tarif_mp_a'	: include('master/fasilitas/tarif_mp/a/detail_tarif_mp_a_setup.php');break; 
				case 'detail_tarif_mp_b'	: include('master/fasilitas/tarif_mp/b/detail_tarif_mp_b_setup.php');break; 
				case 'detail_tarif_mp_c'	: include('master/fasilitas/tarif_mp/c/detail_tarif_mp_c_setup.php');break; 
				case 'detail_tarif_mp_d'	: include('master/fasilitas/tarif_mp/d/detail_tarif_mp_d_setup.php');break; 
				case 'tarif_mp_e'			: include('master/fasilitas/tarif_mp/e/tarif_mp_e_setup.php');break; 
				case 'tarif_mp_f'			: include('master/fasilitas/tarif_mp/f/tarif_mp_f_setup.php');break; 
			
			# Proses Tagihan
				case 'periode_bangun'		: cmodul('201'); include('periode/periode_bangun/periode_bangun_setup.php');break;
				case 'periode_renovasi'		: cmodul('202'); include('periode/periode_renovasi/periode_renovasi_setup.php');break;
								
				case 'copy_periode'			: cmodul('203'); include('periode/copy_periode/copy_periode_setup.php');break;
				case 'export_sm'			: cmodul('204'); include('periode/export_sm/export_sm_setup.php');break;
				case 'import_sm'			: cmodul('205'); include('periode/import_sm/import_sm_setup.php');break; 
				
				case 'hitung_denda'			: cmodul('206'); include('periode/hitung_denda/hitung_denda_setup.php');break;
				case 'hapus_tagihan'		: cmodul('207'); include('periode/hapus_tagihan/hapus_tagihan_setup.php');break;
				
				case 'periode_mp'		: include('periode/periode_mp/periode_mp_setup.php');break;
				case 'periode_sl'		: include('periode/periode_sl/periode_sl_setup.php');break;
				
			# Bank
				case 'export_bank'				: cmodul('301'); include('bank/export/export_setup.php');break;
				case 'import_bank'				: cmodul('302'); include('bank/import/import_setup.php');break;
			
			# Pembayaran
				## Air & IPL
				case 'ai_pembayaran'			: cmodul('401'); include('pembayaran/air_ipl/pembayaran/pembayaran_setup.php');break;
				case 'ai_pembatalan_dan_diskon'	: include('pembayaran/air_ipl/pembatalan_dan_diskon/pembatalan_dan_diskon_setup.php');break;
				case 'ai_pelanggan_baru'		: cmodul('404'); include('pembayaran/air_ipl/pelanggan_baru/pelanggan_baru_setup.php');break;
				case 'ai_edit_stand_meter'		: cmodul('405'); include('pembayaran/air_ipl/edit_stand_meter/edit_stand_meter_setup.php');break;
				
				## Deposit
				case 'dp_pembayaran'			: cmodul('411'); include('pembayaran/deposit/pembayaran/pembayaran_setup.php');break;
				case 'dp_pembatalan_dan_diskon'	: include('pembayaran/deposit/pembatalan_dan_diskon/pembatalan_dan_diskon_setup.php');break;
				
				## Fasilitas
				//case 'pembayaran_mp_abcd'		: include('pembayaran/fasilitas/mp/mp_abcd/mp_setup.php');break;
				//case 'pembayaran_mp_ef'			: include('pembayaran/fasilitas/mp/mp_ef/mp_setup.php');break;
				case 'pembayaran_mp'			: include('pembayaran/fasilitas/mp/mp_setup.php');break;
				case 'pembayaran_pkl_baru'		: include('pembayaran/fasilitas/pkl/baru/pkl_setup.php');break;
				case 'pembayaran_pkl_perpanjang': include('pembayaran/fasilitas/pkl/perpanjang/pkl_setup.php');break;
				case 'pembayaran_ksp'			: include('pembayaran/fasilitas/ksp/ksp_setup.php');break;
				case 'pembayaran_psp'			: include('pembayaran/fasilitas/psp/psp_setup.php');break;
			
			# Laporan
				## Hunian
					### Rencana
					case 'ai_rencana_rincian'		: cmodul('501'); include('laporan/air_ipl/rencana_rincian/rencana_rincian_setup.php');break; 
					case 'ai_rencana_rekap'			: cmodul('502'); include('laporan/air_ipl/rencana_rekap/rencana_rekap_setup.php');break; 
				
					### Penerimaan
					case 'ai_penerimaan_rincian'	: cmodul('503'); include('laporan/air_ipl/penerimaan_rincian/penerimaan_rincian_setup.php');break; 
					case 'ai_penerimaan_rekap'		: cmodul('504'); include('laporan/air_ipl/penerimaan_rekap/penerimaan_rekap_setup.php');break;
					
					### Piutang
					case 'ai_piutang_rincian'		: cmodul('505'); include('laporan/air_ipl/piutang_rincian/piutang_rincian_setup.php');break; 
					case 'ai_piutang_rekap'			: cmodul('506'); include('laporan/air_ipl/piutang_rekap/piutang_rekap_setup.php');break;
					case 'ai_piutang_umur'			: cmodul('507'); include('laporan/air_ipl/piutang_umur/piutang_umur_setup.php');break;
				
					case 'ai_pemutusan'				: cmodul('508'); include('laporan/air_ipl/pemutusan/pemutusan_setup.php');break;
					case 'ai_pemakaian_air'			: cmodul('509'); include('laporan/air_ipl/pemakaian_air/pemakaian_air_setup.php');break;
					
				## Deposit
					### Rencana
					case 'dp_rencana_rincian'		: cmodul('511'); include('laporan/deposit/rencana_rincian/rencana_rincian_setup.php');break; 
					case 'dp_rencana_rekap'			: cmodul('512'); include('laporan/deposit/rencana_rekap/rencana_rekap_setup.php');break; 
				
					### Penerimaan
					case 'dp_penerimaan_rincian'	: cmodul('513'); include('laporan/deposit/penerimaan_rincian/penerimaan_rincian_setup.php');break; 
					case 'dp_penerimaan_rekap'		: cmodul('514'); include('laporan/deposit/penerimaan_rekap/penerimaan_rekap_setup.php');break; 
				
					### Piutang
					case 'dp_piutang_rincian'		: cmodul('515'); include('laporan/deposit/piutang_rincian/piutang_rincian_setup.php');break; 
					case 'dp_piutang_rekap'			: cmodul('516'); include('laporan/deposit/piutang_rekap/piutang_rekap_setup.php');break;
					case 'dp_piutang_umur'			: cmodul('517'); include('laporan/deposit/piutang_umur/piutang_umur_setup.php');break;
				
				## Pelanggan
					case 'pelanggan_rincian'		: cmodul('521'); include('laporan/pelanggan/pelanggan_rincian/pelanggan_rincian_setup.php');break;
					case 'pelanggan_rekap'			: cmodul('522'); include('laporan/pelanggan/pelanggan_rekap/pelanggan_rekap_setup.php');break;
					case 'pelanggan_daftar'			: cmodul('523'); include('laporan/pelanggan/pelanggan_daftar/pelanggan_daftar_setup.php');break;
				
				case 'daftar_faktur_pajak'			: cmodul('524'); include('laporan/daftar_faktur_pajak/daftar_faktur_pajak_setup.php');break;
				
			# Utilitas
				## Invoice
					case 'ai_invoice'			: cmodul('601'); include('invoice/air_ipl/invoice_setup.php');break; 
					case 'dp_invoice'			: cmodul('602'); include('invoice/deposit/invoice_setup.php');break; 
				
				## Posting 
					case 'posting_air_ipl'		: cmodul('603'); include('posting/air_ipl/air_ipl_setup.php');break; 
					
				## Faktur Pajak
				case 'fp_penomoran'				: cmodul('604'); include('faktur_pajak/penomoran/penomoran_setup.php');break; 
				case 'fp_cetak'					: cmodul('605'); include('faktur_pajak/cetak/cetak_setup.php');break; 
				case 'fp_posting'				: cmodul('606'); include('faktur_pajak/posting/posting_setup.php');break; 
				
				case 'user_management'			:  include('user_management/user_management_setup.php');break; 
				case 'user_log'					: cmodul('622'); include('user/log/log_setup.php');break;
				
			default: break;
		}
		?>
		<div class="clear"></div>
	</div>
</div>
<div id="footer">&copy; 2014 - PT. Jaya Real Property, Tbk<br>Built By ASYS IT Consultant</div>
</body>
</html>
<?php close($conn); ?>
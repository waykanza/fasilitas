<?php
require_once('management_proses.php');
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
	
	$('#id_user').inputmask('varchar', { repeat: '6' });
	$('#nama_user').inputmask('varchar', { repeat: '40' });
	$('#aktif_user').inputmask('varchar', { repeat: '2' });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_user + 'management/management_proses.php',
			data	= $('#form').serialize();
			
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


<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="130">ID</td>
	<td><input <?php echo $ro_login_user; ?> type="text" name="login_user" id="login_user" size="25" value="<?php echo $login_user; ?>"></td>
</tr>
<tr>
	<td>PASSWORD</td>
	<td><input type="password" name="pass_user" id="pass_user" size="25" value=""></td>
</tr>
<tr>
	<td>CONF. PASSWORD</td>
	<td><input type="password" name="conf_pass_user" id="conf_pass_user" size="25" value=""></td>
</tr>
<tr>
	<td>NAMA</td>
	<td><input type="text" name="nama_user" id="nama_user" size="50" value="<?php echo $nama_user; ?>"></td>
</tr>
<tr>
	<td>STATUS</td>
	<td>
		<input type="radio" name="aktif_user" id="auy" <?php echo is_checked('1', $aktif_user); ?> value="1"> <label for="auy">AKTIF</label>
		<input type="radio" name="aktif_user" id="aun" <?php echo is_checked('0', $aktif_user); ?> value="0"> <label for="aun">TIDAK AKTIF</label>
	</td>
</tr>

<tr>
	<td class="va-top"><br><br>MODUL</td>
	<td class="va-top"><br>
		<table class="wauto">
		<tr>
			<td colspan="4" class="va-top"><b>Master</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="M1" id="M1" <?php echo (in_array('M1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M1">Parameter</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M2" id="M2" <?php echo (in_array('M2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M2">Sektor</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M3" id="M3" <?php echo (in_array('M3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M3">Cluster</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M4" id="M4" <?php echo (in_array('M4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M4">Bank</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M5" id="M5" <?php echo (in_array('M5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M5">Diskon Khusus</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M6" id="M6" <?php echo (in_array('M6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M6">Rumah Pompa</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="M7" id="M7" <?php echo (in_array('M7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M7">SK Air</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M8" id="M8" <?php echo (in_array('M8', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M8">Kategori Air</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M9" id="M9" <?php echo (in_array('M9', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M9">Tarif Air</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="M10" id="M10" <?php echo (in_array('M10', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M10">SK IPL</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M11" id="M11" <?php echo (in_array('M11', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M11">Kategori IPL</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M12" id="M12" <?php echo (in_array('M12', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M12">Tarif IPL</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="M13" id="M13" <?php echo (in_array('M13', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M13">Pelanggan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M15" id="M15" <?php echo (in_array('M15', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M15">Pelanggan (Full Access)</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="M14" id="M14" <?php echo (in_array('M14', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="M14">Pelanggan Baru</label>
			</td>
		</tr>
		
		<tr>
			<td class="va-top"><br><b>Proses Tagihan</b><hr></td>
			<td class="va-top"><br><b>Bank</b><hr></td>
			<td colspan="2" class="va-top"><br><b>Pembayaran</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="T1" id="T1" <?php echo (in_array('T1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T1">Periode Bangun</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T2" id="T2" <?php echo (in_array('T2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T2">Periode Renovasi</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T3" id="T3" <?php echo (in_array('T3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T3">Proses Tagihan (Temp)</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T8" id="T8" <?php echo (in_array('T8', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T8">Edit Stand Meter</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T4" id="T4" <?php echo (in_array('T4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T4">Export Stand Meter</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T5" id="T5" <?php echo (in_array('T5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T5">Import Stand Meter</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T6" id="T6" <?php echo (in_array('T6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T6">Hitung Denda</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="T7" id="T7" <?php echo (in_array('T7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="T7">Proses Tagihan</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="B1" id="B1" <?php echo (in_array('B1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="B1">Export</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="B2" id="B2" <?php echo (in_array('B2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="B2">Import</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="PA1" id="PA1" <?php echo (in_array('PA1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="PA1">Pembayaran Air & IPL</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="PA2" id="PA2" <?php echo (in_array('PA2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="PA2">Pelanggan Baru</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="PD1" id="PD1" <?php echo (in_array('PD1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="PD1">Pembayaran Deposit</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="PL1" id="PL1" <?php echo (in_array('PL1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="PL1">Pembayaran Biaya Lain-lain</label>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="va-top"><br><b>Laporan Air & IPL</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LA1" id="LA1" <?php echo (in_array('LA1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA1">Rincian Rencana</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LA2" id="LA2" <?php echo (in_array('LA2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA2">Rekap Rencana</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LA3" id="LA3" <?php echo (in_array('LA3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA3">Rincian Penerimaan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LA4" id="LA4" <?php echo (in_array('LA4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA4">Rekap Penerimaan</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LA5" id="LA5" <?php echo (in_array('LA5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA5">Rincian Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LA6" id="LA6" <?php echo (in_array('LA6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA6">Rekap Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LA7" id="LA7" <?php echo (in_array('LA7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA7">Umur Piutang</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LA8" id="LA8" <?php echo (in_array('LA8', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA8">Pemutusan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LA9" id="LA9" <?php echo (in_array('LA9', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LA9">Pemakaian Air</label><br>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="va-top"><br><b>Laporan Deposit</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LD1" id="LD1" <?php echo (in_array('LD1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD1">Rincian Rencana</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LD2" id="LD2" <?php echo (in_array('LD2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD2">Rekap Rencana</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LD3" id="LD3" <?php echo (in_array('LD3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD3">Rincian Penerimaan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LD4" id="LD4" <?php echo (in_array('LD4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD4">Rekap Penerimaan</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LD5" id="LD5" <?php echo (in_array('LD5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD5">Rincian Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LD6" id="LD6" <?php echo (in_array('LD6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD6">Rekap Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LD7" id="LD7" <?php echo (in_array('LD7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LD7">Umur Piutang</label><br>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="va-top"><br><b>Laporan Biaya Lain-lain</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LL1" id="LL1" <?php echo (in_array('LL1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL1">Rincian Rencana</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LL2" id="LL2" <?php echo (in_array('LL2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL2">Rekap Rencana</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LL3" id="LL3" <?php echo (in_array('LL3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL3">Rincian Penerimaan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LL4" id="LL4" <?php echo (in_array('LL4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL4">Rekap Penerimaan</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LL5" id="LL5" <?php echo (in_array('LL5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL5">Rincian Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LL6" id="LL6" <?php echo (in_array('LL6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL6">Rekap Piutang</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LL7" id="LL7" <?php echo (in_array('LL7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LL7">Umur Piutang</label><br>
			</td>
		</tr>
		
		<tr>
			<td class="va-top"><br><b>Laporan Pelanggan</b><hr></td>
			<td class="va-top"><br><b>Laporan Bank Debet</b><hr></td>
			<td colspan="2" class="va-top"><br><b>Laporan Faktur Pajak</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LP1" id="LP1" <?php echo (in_array('LP1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LP1">Rincian Pelanggan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LP2" id="LP2" <?php echo (in_array('LP2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LP2">Rekap Pelanggan</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LP3" id="LP3" <?php echo (in_array('LP3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LP3">Daftar Pelanggan</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LB1" id="LB1" <?php echo (in_array('LB1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LB1">Rincian Bank Debet</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="LB2" id="LB2" <?php echo (in_array('LB2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LB2">Rekap Bank Debet</label>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="LB3" id="LB3" <?php echo (in_array('LB3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="LB3">Daftar Faktur Pajak</label>
			</td>
		</tr>
		
		<tr>
			<td colspan="4" class="va-top"><br><b>Utilitas</b><hr></td>
		</tr>
		<tr>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="U1" id="U1" <?php echo (in_array('U1', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U1">Invoice Air & IPL</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U2" id="U2" <?php echo (in_array('U2', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U2">Invoice Deposit</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U10" id="U10" <?php echo (in_array('U10', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U10">Invoice Biaya Lain-lain</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="U3" id="U3" <?php echo (in_array('U3', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U3">Posting Air, IPL & Deposit</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U4" id="U4" <?php echo (in_array('U4', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U4">Proses Bank Depet</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="U5" id="U5" <?php echo (in_array('U5', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U5">Penomoran Faktur Pajak</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U6" id="U6" <?php echo (in_array('U6', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U6">Cetak Faktur Pajak</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U7" id="U7" <?php echo (in_array('U7', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U7">Postings Faktur Pajak</label><br>
			</td>
			<td class="va-top">
				<input type="checkbox" name="id_modul_ary[]" value="U8" id="U8" <?php echo (in_array('U8', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U8">User Management</label><br>
				<input type="checkbox" name="id_modul_ary[]" value="U9" id="U9" <?php echo (in_array('U9', $list_aktif_modul)) ? 'checked="checked"' : ''; ?>> <label for="U9">User Log</label><br>
			</td>
		</tr>
		</table>
	</td>
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

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
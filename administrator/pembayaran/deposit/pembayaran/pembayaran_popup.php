<?php
require_once('pembayaran_proses.php');
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
	key('alt+p', function(e) { e.preventDefault(); $('#status_cetak_kwt').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_pembayaran + 'deposit/pembayaran/pembayaran_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				location.reload();
			}
		}, 'json');
		
		return false;
	});
	
	$('#daftar_piutang').on('click', function(e) {
		e.preventDefault();
		
		var url = base_pembayaran + 'deposit/pembayaran/daftar_piutang.php?no_pelanggan=<?php echo $no_pelanggan; ?>';
		
		setPopup('Rincian Piutang', url, winWidth-100, winHeight-100);
		
		return false;
	});
	
	$('#status_cetak_kwt').on('click', function(e) {
		e.preventDefault();
		
		open_print(base_pembayaran + 'deposit/pembayaran/cetak_kwitansi.php?id_pembayaran=' + $('#id').val(), '1');
		
		return false;
	});
	
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
	<tr><td>PERIODE AWAL</td><td>:</td><td><?php echo strtoupper(fm_periode_first($periode_awal)); ?></td></tr>
	<tr><td>PERIODE AKHIR</td><td>:</td><td><?php echo strtoupper(fm_periode_last($periode_akhir)); ?></td></tr>
	<tr><td>LUAS KAVL.</td><td>:</td><td class="text-right"><?php echo to_money($luas_kavling,2); ?> m&sup2;</td></tr>
</table>

<div class="clear"></div>
<hr><br>

<!--=========== TAGIHAN ===========-->
<table class="t-popup f-left wauto" style="margin-right:40px;">
<tr>
	<td width="250">DEPOSIT</td>
	<td width="20">:</td>
	<td width="25"> Rp. </td>
	<td width="125" class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
</tr>
<tr>
	<td>DENDA</td>
	<td>:</td>
	<td> Rp. </td>
	<td class="text-right"><?php echo to_money($denda); ?></td>
</tr>
<tr>
	<td>ADMINISTRASI</td>
	<td>:</td>
	<td> Rp. </td>
	<td class="text-right"><?php echo to_money($administrasi); ?></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td><b>TOTAL</b></td>
	<td>:</td>
	<td><b> Rp. </b></td>
	<td class="text-right"><b><?php echo to_money($total); ?></b></td>
</tr>
<tr>
	<td>DISKON</td>
	<td>:</td>
	<td> Rp. </td>
	<td class="text-right"><?php echo to_money($diskon); ?></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td><b>TOTAL TAGIHAN</b></td>
	<td>:</td>
	<td><b> Rp. </b></td>
	<td class="text-right"><b><?php echo to_money($total - $diskon); ?></b></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td colspan="2"><hr></td>
</tr>
<tr>
	<td><b>JUMLAH TERIMA</b></td>
	<td>:</td>
	<td><b> Rp. </b></td>
	<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
</tr>
<tr>
	<td colspan="4">
		<br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja(($status_bayar == '2') ? $jumlah_bayar : ($total - $diskon))); ?> rupiah.</b></i>
	</td>
</tr>

<tr>
	<td colspan="4">
		<br><b>KETERANGAN BAYAR : <b><br>
		<textarea <?php echo ($status_bayar == '2') ? 'readonly="readonly"' : ''; ?> name="keterangan_bayar" rows="4" class="w100"><?php echo $keterangan_bayar; ?></textarea>
	</td>
</tr>
</table>

<!--=========== DISKON ===========-->

<table class="t-popup f-left wauto">
<tr>
	<td colspan="3" class="text-center"><b>DISKON DEPOSIT<hr></b></td>
</tr>
<tr>
	<td width="70">USER</td>
	<td colspan="2" width="180">: <?php echo $user_diskon_ipl; ?></td>
</tr>
<tr>
	<td>TANGGAL</td>
	<td colspan="2">: <?php echo $tgl_diskon_ipl; ?></td>
</tr>
<tr>
	<td>DISKON</td>
	<td width="30">: Rp.</td>
	<td class="text-right"><?php echo to_money($diskon_rupiah_ipl); ?></td>
</tr>

<tr>
	<td colspan="3" class="text-center"><br><b>KETERANGAN DISKON<hr></b></td>
</tr>
<tr>
	<td colspan="3">
		<textarea readonly="readonly" rows="5" class="w100"><?php echo $keterangan_diskon; ?></textarea>
	</td>
</tr>
</table>

<div class="clear"></div>

<br><hr>

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
	<td>JENIS BAYAR</td><td>:</td>
	<td>
		<?php
		if ($status_bayar != '2')
		{
			?>
			<select name="jenis_bayar" id="jenis_bayar">
				<option value=""> -- JENIS BAYAR -- </option>
				<option value="2"> K. DEBET </option>
				<option value="3"> K. KREDIT </option>
			</select>
			<?php
		}
		else
		{
			echo jenis_bayar($jenis_bayar);
		}
		?>
	</td>
</tr>
</table>

<hr>

<table class="t-popup">
<tr>
	<td></td>
	<td colspan="5" class="td-action">
		<input type="button" id="save" value=" Simpan (Alt+S) ">
		<input type="button" id="status_cetak_kwt" value=" Cetak Kwitansi (Alt+P) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
<?php
require_once('pembayaran_proses.php');
require_once('../../../config/terbilang.php');
$terbilang = new Terbilang;
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
	
	key('alt+s', function(e) { e.preventDefault(); $('#save_pembayaran').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#cetak_kwitansi').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#diskon_ipl').inputmask('numeric', { repeat: '10' });
	$('#diskon_ipl_persen').inputmask('percent100');
	
	$('#diskon_ipl_persen').on('change', function(e) {
		e.preventDefault();
		diskon_ipl_persen = $(this).val();
		diskon_ipl_persen = diskon_ipl_persen.replace(/[^0-9.]/g, '');
		diskon_ipl_persen = (diskon_ipl_persen == '') ? 0 : parseFloat(diskon_ipl_persen);
		
		jumlah_ipl = <?php echo $jumlah_ipl; ?>;
		diskon_ipl = jumlah_ipl * (diskon_ipl_persen / 100);
		
		$('#diskon_ipl').val(diskon_ipl.toFixed(0));
		
		return false;
	});
	
	$('#diskon_ipl').on('change', function(e) {
		e.preventDefault();
		diskon_ipl = $(this).val();
		diskon_ipl = diskon_ipl.replace(/[^0-9.]/g, '');
		diskon_ipl = (diskon_ipl == '') ? 0 : parseFloat(diskon_ipl);
		
		jumlah_ipl = <?php echo $jumlah_ipl; ?>;
		diskon_ipl_persen = (diskon_ipl / jumlah_ipl) * 100;
		
		$('#diskon_ipl_persen').val(diskon_ipl_persen.toFixed(2));
		
		return false;
	});
	
	/* ACTION */
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save_diskon_ipl').on('click', function(e) {
		e.preventDefault();
		
		$('#act').val('diskon_ipl');
		do_process();
		
		return false;
	});
	
	$('#save_pembatalan').on('click', function(e) {
		e.preventDefault();
		$('#act').val('pembatalan');
		do_process();
		
		return false;
	});
	
	$('#save_pembayaran').on('click', function(e) {
		e.preventDefault();
		$('#act').val('pembayaran');
		do_process();
		
		return false;
	});
	
	function do_process() {
		var url			= base_pembayaran + 'lain_lain/pembayaran_proses.php',
			data		= $('#form').serialize();
		
		$.post(url, data, function(data) {
			alert(data.msg);
			if (data.error == false) {
				location.reload();
			}
		}, 'json');
		
		return false;
	}
	
	$('#daftar_piutang').on('click', function(e) {
		e.preventDefault();
		
		var url = base_pembayaran + 'lain_lain/daftar_piutang.php?no_pelanggan=<?php echo $no_pelanggan; ?>';
		
		setPopup('Rincian Piutang', url, winWidth-10, winHeight-100);
		
		return false;
	});
	
	$('#cetak_kwitansi').on('click', function(e) {
		e.preventDefault();
		
		open_print(base_pembayaran + 'lain_lain/cetak_kwitansi.php?tipe=1&idp=' + $('#id').val(), '1');
		
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
	<tr><td>NO. PELANGGAN</td><td>:</td><td><?php echo fm_nopel($no_pelanggan); ?></td></tr>
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<tr><td>BLOK / NO.</td><td>:</td><td><?php echo $kode_blok; ?></td></tr>
	<tr><td>SEKTOR</td><td>:</td><td><?php echo $nama_sektor; ?></td></tr>
	<tr><td>CLUSTER</td><td>:</td><td><?php echo $nama_cluster; ?></td></tr>
	<tr><td>STATUS</td><td>:</td><td><?php echo status_blok($status_blok); ?></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>BULAN</td><td>:</td><td><?php echo strtoupper(fm_periode($periode_tag)); ?></td></tr>
	<tr><td>NO. TAGIHAN</td><td>:</td><td><?php echo $no_invoice; ?></td></tr>
	<tr><td>NO. KWITANSI</td><td>:</td><td><?php echo $no_kwitansi; ?></td></tr>
	<tr><td>PEMBAYARAN VIA</td><td>:</td><td><?php echo $bayar_via; ?></td></tr>
	<tr><td>NO. FAKTUR</td><td>:</td><td><?php echo $no_fp; ?></td></tr>
	<tr><td>JUMLAH PIUTANG</td><td>:</td><td><?php echo $jumlah_piutang; ?> <input type="button" id="daftar_piutang" value=" Rincian Piutang "></td></tr>
</table>

<table class="smf t-popup wauto f-left" style="margin-right:35px">
	<tr><td>AKTIF IPL</td><td>:</td><td><?php echo status_check($aktif_ipl); ?></td></tr>
	<tr><td>GOL. TARIF</td><td>:</td><td><?php echo $key_ipl; ?></td></tr>
	<tr><td>PERIODE AWAL</td><td>:</td><td><?php echo strtoupper(fm_periode_first($periode_ipl_awal)); ?></td></tr>
	<tr><td>PERIODE AKHIR</td><td>:</td><td><?php echo strtoupper(fm_periode_last($periode_ipl_akhir)); ?></td></tr>
	<tr><td>LUAS KAVL.</td><td>:</td><td class="text-right"><?php echo to_money($luas_kavling,2); ?> m&sup2;</td></tr>
</table>

<div class="clear"></div>
<hr><br>

<!--=========== TAGIHAN ===========-->
<table class="t-popup f-left wauto" style="margin-right:40px;">
<tr>
	<td width="250">BIAYA LAIN-LAIN</td>
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
	<td>ADM</td>
	<td>:</td>
	<td> Rp. </td>
	<td class="text-right"><?php echo to_money($adm); ?></td>
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
		<br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja(($status_bayar == '1') ? $jumlah_bayar : ($total - $diskon))); ?> rupiah.</b></i>
	</td>
</tr>

<tr>
	<td colspan="4">
		<br><b>KETERANGAN TAGIHAN : <b><br>
		<textarea readonly="readonly" rows="4" class="w100"><?php echo $ket_ivc; ?></textarea>
	</td>
</tr>
<tr>
	<td colspan="4">
		<br><b>KETERANGAN BAYAR : <b><br>
		<textarea <?php echo ($status_bayar == '1') ? 'readonly="readonly"' : ''; ?> name="ket_bayar" rows="4" class="w100"><?php echo $ket_bayar; ?></textarea>
	</td>
</tr>
</table>

<!--=========== DISKON ===========-->
<table class="t-popup f-left wauto">
<tr>
	<td colspan="3" class="text-center"><br><b>DISKON IPL<hr></b></td>
</tr>
<tr>
	<td>USER</td>
	<td colspan="2" width="180">: <?php echo $user_diskon_ipl; ?></td>
</tr>
<tr>
	<td>TANGGAL</td>
	<td colspan="2">: <?php echo $tgl_diskon_ipl; ?></td>
</tr>
<tr>
	<td>DISKON</td>
	<td>: Rp.</td>
	<td class="text-right">
		<input type="text" name="diskon_ipl" id="diskon_ipl" size="15" value="<?php echo to_money($diskon_ipl); ?>">
		<input type="text" id="diskon_ipl_persen" size="5" value="<?php echo to_money($diskon_ipl_persen, 2); ?>"> %
	</td>
</tr>
<tr>
	<td colspan="3">
		<textarea name="ket_diskon_ipl" id="ket_diskon_ipl" rows="5" class="w100"><?php echo $ket_diskon_ipl; ?></textarea>
	</td>
</tr>
<tr>
	<td colspan="3" class="text-right">
		<input type="button" id="save_diskon_ipl" value=" Proses Diskon IPL ">
	</td>
</tr>

</table>

<div class="clear"></div>

<br><hr>

<!--=========== STATUS ===========-->
<table class="t-popup wauto">
<tr>
	<td class="va-top">
		<table>
		<tr>
			<td width="120">CARA BAYAR</td><td width="10">:</td><td width="200">
			<?php if ($status_bayar == '1') { echo cara_bayar($cara_bayar); } else { ?>
				<select name="cara_bayar" id="cara_bayar">
					<option value=""> -- CARA BAYAR -- </option>
					<option value="2"> K. DEBET </option>
					<option value="3"> K. KREDIT </option>
				</select>
				<?php
			}
			?>
			</td>
		</tr>
		
		<tr>
			<td width="120">PEMBAYARAN VIA</td><td width="10">:</td><td width="200">
			<?php if ($status_bayar == '1') { echo $bayar_via; } else { ?>
				<select name="bayar_via" id="bayar_via">
					<option value=""> -- KODE BANK -- </option>
					<?php
					$obj = $conn->Execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
					while( ! $obj->EOF)
					{
						$ov = $obj->fields['KODE_BANK'];
						$on = $obj->fields['NAMA_BANK'];
						echo "<option value='$ov'> $on ($ov) </option>";
						$obj->movenext();
					}
					?>
				</select>
				<?php
			}
			?>
			</td>
		</tr>
		
		<tr>
			<td>TGL. BAYAR (BANK)</td><td>:</td><td>
				<?php if ($status_bayar == '1') { echo fm_date($tgl_bayar_bank, '%d %b %Y %H:%M:%S'); } else { ?> 
				<input type="text" name="tgl_bayar_bank" id="tgl_bayar_bank" class="dd-mm-yyyy" size="13" value=""> <?php } ?>
			</td>
		</tr>
		
		<tr><td>TGL. BAYAR (SYS)</td><td>:</td><td><?php echo ($status_bayar == '1') ? fm_date($tgl_bayar_sys, '%d %b %Y %H:%M:%S') : ''; ?></td></tr>
		</table>
	</td>
	
	<td class="va-top">
		<table>
		<tr><td width="120">STATUS BAYAR</td><td width="10">:</td><td width="200"><?php echo status_check($status_bayar); ?></td></tr>
		<tr><td>KASIR</td><td>:</td><td><?php echo $user_bayar; ?></td></tr>
		<tr><td>STATUS CETAK</td><td>:</td><td><?php echo status_check($status_cetak_kwt); ?></td></tr>
		<tr><td>USER CETAK</td><td>:</td><td><?php echo $user_cetak_kwt; ?></td></tr>
		<tr><td>TGL. CETAK</td><td>:</td><td><?php echo ($status_cetak_kwt == '1') ? fm_date($tgl_cetak_kwt, '%d %b %Y %H:%M:%S') : ''; ?></td></tr>
		</table>
	</td>
	
	<td class="va-top">
		<table>
		<tr><td width="120">USER PEMBATALAN</td><td width="10">:</td><td width="200"><?php echo $user_batal; ?></td></tr>
		<tr><td>TGL. PEMBATALAN</td><td>:</td><td><?php echo ($status_batal == '1') ? fm_date($tgl_batal, '%d %b %Y %H:%M:%S') : ''; ?></td></tr>
		<tr><td colspan="3"><textarea name="ket_batal" id="ket_batal" rows="2" class="w100"><?php echo $ket_batal; ?></textarea></td></tr>
		<tr><td colspan="3" class="text-right"><input type="button" id="save_pembatalan" value=" Proses Pembatalan "></td></tr>
		</table>
	</td>
</tr>
</table>

<hr>

<table class="t-popup">
<tr>
	<td></td>
	<td class="td-action">
		<input type="button" id="save_pembayaran" value=" Proses Pembayaran (Alt+S) ">
		<input type="button" id="cetak_kwitansi" value=" Cetak Kwitansi (Alt+P) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="">
</form>

</body>
</html>
<?php close($conn); ?>
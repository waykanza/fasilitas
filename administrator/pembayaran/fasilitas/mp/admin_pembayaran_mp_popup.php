<?php
require_once('pembayaran_mp_proses.php');
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
	key('alt+b', function(e) { e.preventDefault(); $('#batal').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#cetak').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var 
			url			= base_pembayaran_fasilitas + 'mp/pembayaran_mp_proses.php',
			data		= $('#form').serialize();

		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				location.reload();
			}
		}, 'json');
		
		return false;
	});
	
	$('#batal').on('click', function(e) {
		e.preventDefault();
		var 
			url			= base_pembayaran_fasilitas + 'mp/batal_mp_proses.php',
			data		= $('#form').serialize();

		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				location.reload();
			}
		}, 'json');
		
		return false;
	});
	
		
	$('#cetak').on('click', function(e) {
		e.preventDefault();
		
		window.open(base_pembayaran_fasilitas + 'mp/cetak_kwitansi.php?id_pembayaran=' +  $('#id').val(), '1');
		
		return false;
	});

	
	$('#no_rekening').inputmask('varchar', { repeat: '40' });
	$('#tgl_bayar').Zebra_DatePicker({
		format: 'd-m-Y'
	});
	
	if (<?php echo $status_bayar; ?> == 2) {
		$('#save').hide();
		$('#batal').show();
		$('#a').hide();
		$('#b').show();
		$('#btgl_bayar').val('<?php echo $tanggal_bayar; ?>');
		$('#bjenis_bayar').val('<?php echo $jenis_bayar; ?>');
		$('#bkode_bank').val('<?php echo $kode_bank; ?>');
		$('#bno_kwitansi').val('<?php echo $no_kwitansi; ?>');
		$('#bno_rekening').val('<?php echo $no_rekening; ?>');
		$('#bketerangan').val('<?php echo $keterangan; ?>');
		//$('#bkasir').val('<?php echo $kasir; ?>');
		
	} else {
		$('#save').show();
		$('#batal').hide();
		$('#a').show();
		$('#b').hide();
	}
	
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
<table class="smf t-popup wauto f-left" style="margin-right:100px">
	<!--<tr><td>NO. KTP</td><td>:</td><td><?php echo $no_ktp; ?></td></tr>-->
	<tr><td>NAMA PELANGGAN</td><td>:</td><td><?php echo $nama_pelanggan; ?></td></tr>
	<input type="hidden" value="<?php echo $id;?>" name = "id_pembayaran" >
	<tr><td>NPWP</td><td>:</td><td><?php echo $npwp; ?></td></tr>
	<tr><td>ALAMAT</td><td>:</td><td><?php echo $alamat; ?></td></tr>
	<tr><td>NO. TELEPON</td><td>:</td><td><?php echo $no_tlp; ?></td></tr>
	<!--<tr><td>NO. HP</td><td>:</td><td><?php echo $no_hp; ?></td></tr>-->
	
	<tr><td>MEDIA PROMOSI</td><td>:</td><td><?php echo $mp; ?></td></tr>	
	<tr><td>KATEGORI</td><td>:</td><td><?php echo $kategori; ?></td></tr>	
	<tr><td>LOKASI</td><td>:</td><td><?php echo $lokasi;?></td></tr>
	<tr><td>TARIF</td><td>:</td><td><?php echo 'Rp. '.$tarif.$tahun;?></td></tr>
	
	<tr><td>PERIODE</td><td>:</td><td><?php echo $awal; ; echo ' s/d '; echo $akhir; ?></td></tr>
	<!--<tr><td>CARA PEMBAYARAN</td><td>:</td><td><?php echo $satuan;?></td></tr>-->
</table>


<table class="smf t-popup wauto" style="">
	<!--<tr><td>TARIF</td><td>=</td><td>Rp.</td><td size="50" align="right"><?php echo $tarif2; ?></td><td>(<?php echo $satuan; ?>)</td></tr>-->
	<tr><td>BIAYA STRATEGIS</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_tambah; ?></td><td>(<?php echo $persen_nilai_tambah; ?>%)</td></tr>
	<tr><td>DISCOUNT</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_kurang; ?></td><td>(<?php echo $persen_nilai_kurang; ?>%)</td></tr>
	<tr><td></td><td colspan="4"><hr></td></tr>
	<tr><td>TOTAL</td><td>=</td><td>Rp.</td><td align="right"><?php echo $total; ?></td></tr>
	<tr><td>PPN</td><td>=</td><td>Rp.</td><td align="right"><?php echo $nilai_ppn; ?></td><td>(<?php echo $persen_ppn; ?>%)</td></tr>
	<tr><td></td><td colspan="4"><hr></td></tr>
	<tr><td><b>TOTAL BAYAR</td><td><b>=</td><td><b>Rp.</td><td align="right"><b><?php echo $total_bayar; ?></td></tr>
	
</table>

<table class="smf t-popup wauto" style="">
<tr>
	<td colspan="5">
		<br><i>Terbilang : <b><?php echo ucfirst($terbilang->eja($total_bayar2)); ?> rupiah.</b></i>
	</td>
</tr>
</table>


<div class="clear"></div>
<br>
<div class="clear"></div>
<hr><br>

<table id="a" class="w50 f-left">
	<tr><td>TANGGAL BAYAR</td><td>
	<input type="text" name="tgl_bayar" id="tgl_bayar" size="13" value=""></td></tr>
	
	<tr><td>JENIS BAYAR</td><td>
	<select name="jenis_bayar" id="jenis_bayar">
		<option value=""> -- JENIS BAYAR -- </option>
		<option value="2"> K. DEBIT </option>
		<option value="4"> TRANSFER </option>
	</select>
	</td></tr>

	<?php $obj = $conn->execute("SELECT BANK, NO_REKENING, KODE_BANK FROM KWT_PARAMETER ");?>
	<tr><td>KODE BANK</td><td>
	<input type="text" readonly="readonly" name="kode_bank" id="kode_bank" value="<?php echo $obj->fields['KODE_BANK'];?>">
	</td></tr>

	<tr><td>NAMA BANK</td><td>
	<input type="text" readonly="readonly" name="nama_bank" id="nama_bank" value="<?php echo $obj->fields['BANK'];?>">
	</td></tr>

	<tr id="tr-no_rekening"><td>NO REKENING</td>
	<td><input type="text" readonly="readonly" name="no_rekening" id="no_rekening" size="40" value="<?php echo $obj->fields['NO_REKENING'];?>"></td></tr>

	<tr><td>NO KWITANSI</td>
	<td><input readonly="readonly" type="text" name="no_kwitansi" id="no_kwitansi" size="40" value="<?php echo $no_kwitansi;?>"></td></tr>

	<tr><td>KETERANGAN BAYAR</td>
	<td><textarea name="keterangan" id="keterangan" rows="3" cols="40"></textarea></td></tr>
</table>

<table id="b" class="w50 f-left">
	<tr><td>TANGGAL BAYAR</td><td>
	<input readonly="readonly" type="text" name="btgl_bayar" id="btgl_bayar" size="13" value=""></td></tr>
	
	<tr><td>JENIS BAYAR</td>
	<td><input readonly="readonly" type="text" name="bjenis_bayar" id="bjenis_bayar" size="20" value=""></td></tr>

	<tr><td>KODE BANK</td>
	<td><input readonly="readonly" type="text" name="bkode_bank" id="bkode_bank" size="30" value=""></td></td></tr>

	<tr><td>NO REKENING</td>
	<td><input readonly="readonly" type="text" name="bno_rekening" id="bno_rekening" size="40" value=""></td></tr>

	<tr><td>NO KWITANSI</td>
	<td><input readonly="readonly" type="text" name="bno_kwitansi" id="bno_kwitansi" size="40"></td></tr>

	<tr><td>KETERANGAN BAYAR</td>
	<td><textarea readonly="readonly" name="bketerangan" id="bketerangan" rows="3" cols="40"></textarea></td></tr>
	
</table>

<table class="t-popup">
<tr>
	<td></td>
	<td class="td-action">
		<input type="button" id="save" value=" Simpan (Alt+S) ">
		<input type="button" id="batal" value=" Batal (Alt+B) ">
		<input type="button" id="cetak" value=" Cetak Kwitansi (Alt+P) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
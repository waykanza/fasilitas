<?php
require_once('pelanggan_proses.php');
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
/* lookup */
function get_lookup(nk)
{
	if (nk.length == 0) {
		jQuery('#wrap-lookup').fadeOut(); 
	} else if (nk.length >= 3) {
		jQuery.post(base_master + 'pelanggan_lookup.php?act=list&no_ktp=' + nk, function(data) { 
			jQuery('#lookup-pelanggan').html(data);
			if (data == '') {
				jQuery('#wrap-lookup').fadeOut(); 
			} else {
				jQuery('#wrap-lookup').fadeIn(); 
			}
		});
	}
}

function cls_lookup() { jQuery('#wrap-lookup').fadeOut(); }

function pp(nk)
{
	jQuery('#wrap-lookup').fadeOut();
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

jQuery(function($) {
	
	key('alt+a', function(e) { e.preventDefault(); $('#find_key_air').trigger('click'); });
	key('alt+i', function(e) { e.preventDefault(); $('#find_key_ipl').trigger('click'); });
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#tgl_pemutusan').Zebra_DatePicker({format: 'd-m-Y'});
	$('#periode_putus, #periode_next').Zebra_DatePicker({format: 'm-Y'});
	
	$('#nama_pelanggan, #sm_nama_pelanggan').inputmask('varchar', { repeat: '40' });
	$("#npwp").inputmask('99.999.999.9-999.99');
	$('#no_telepon, #sm_no_telepon').inputmask('varchar', { repeat: '30' });
	$('#no_hp, #sm_no_hp').inputmask('varchar', { repeat: '20' });
	$('#no_rekening').inputmask('varchar', { repeat: '40' });
	
	$('#status').val($.trim($('#status').val()));
	$('#status').inputmask('varchar', { repeat: '1' });
	$('#petugas').inputmask('varchar', { repeat: '30' });
	
	$('#jumlah_piutang_ai, #jumlah_piutang_dp').inputmask('numeric', { repeat: '10' });
	$('#nilai_piutang_ai, #nilai_piutang_dp').inputmask('numeric', { repeat: '10' });
	
	$('#no_ktp').on('keyup', function(e) {
		e.preventDefault();
		get_lookup(this.value);
		return false;
	});
	
	$('#status_blok').on('change', function(e) {
		e.preventDefault();
		$('#tipe_ipl').load(base_master + 'pelanggan/opt_kategori_ipl.php?status_blok=' + $(this).val());
		$('#key_ipl').val('');
		return false;
	});
	
	/* AIR */
	$('#tipe_air').on('change', function(e) {
		e.preventDefault();
		$('#key_air').val('');
		return false;
	});
	
	$('#find_key_ipl').on('click', function(e) {
		e.preventDefault();
		var status_blok = $('#status_blok').val(),
			tipe_ipl = $('#tipe_ipl').val(),
			url = base_master + 'pelanggan/find_key_ipl.php?kode_tipe=' + tipe_ipl;
		if (tipe_ipl == '')
		{
			alert('Pilih kategori IPL!');
			$('#tipe_ipl').focus();
			return false;
		}
		else if (status_blok == '')
		{
			alert('Pilih status blok!');
			$('#status_blok').focus();
			return false;
		}
		
		setPopup('Cari Tarif IPL', url, winWidth-100, winHeight-100);
		
		return false;
	});
	
	/* IPL */
	$('#tipe_ipl').on('change', function(e) {
		e.preventDefault();
		$('#key_ipl').val('');
		return false;
	});
	
	$('#find_key_air').on('click', function(e) {
		e.preventDefault();
		var tipe_air = $('#tipe_air').val(),
			url = base_master + 'pelanggan/find_key_air.php?kode_tipe=' + tipe_air;
		if (tipe_air == '')
		{
			alert('Pilih kategori Air!');
			$('#tipe_air').focus();
			return false;
		}
		setPopup('Cari Tarif Air', url, winWidth-100, winHeight-100);
		
		return false;
	});
	
	/* ACTION */
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var aktif_air = $('#aktif_air:checked').length,
			aktif_ipl = $('#aktif_ipl:checked').length;
			
		if (aktif_air > 0)
		{
			if ($('#key_air').val() == '') {
				alert('Pilih kode tarif air!');
				$('#key_air').focus();
				return false;
			}
		}
		if (aktif_ipl > 0) {
			if ($('#key_ipl').val() == '') {
				alert('Pilih kode tarif IPL!');
				$('#key_ipl').focus();
				return false;
			}
		}
		
		var url		= base_master + 'pelanggan/pelanggan_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				parent.loadData();
			}
		}, 'json');
		
		return false;
	});
	
	$('#daftar_piutang_ai').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'air_ipl/pembayaran/daftar_piutang.php?from_master=1&no_pelanggan=' + $('#id').val();
		setPopup('Rincian Piutang Air & IPL', url, winWidth-100, winHeight-100);
		return false;
	});
	$('#daftar_piutang_dp').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'deposit/pembayaran/daftar_piutang.php?from_master=1&no_pelanggan=' + $('#id').val();
		setPopup('Rincian Piutang Deposit', url, winWidth-100, winHeight-100);
		return false;
	});
});
</script>
</head>
<body class="popup">

<div id="msg" style="margin-bottom:5px;"></div>

<form name="form" id="form" method="post">

<table class="t-popup">
<tr>
	<td class="w10">SEKTOR</td>
	<td class="w2">:</td>
	<td class="w38"><b><?php echo $nama_sektor; ?></b></td>
	<td class="w10">NO. PELANGGAN</td>
	<td class="w2">:</td>
	<td class="w38">
		<span style="font-weight:bold;font-size:16px;"><?php echo $no_pelanggan; ?></span>
	</td>
</tr>

<tr>
	<td>CLUSTER</td>
	<td>:</td>
	<td><b><?php echo $nama_cluster; ?></b></td>
	<td>STATUS BLOK</td>
	<td>:</td>
	<td>
		<select name="status_blok" id="status_blok">
			<option value=""> -- STATUS BLOK -- </option>
				<option value="1" <?php echo is_selected('1', $status_blok); ?>> KAVLING KOSONG </option>
				<option value="2" <?php echo is_selected('2', $status_blok); ?>> MASA MEMBANGUN </option>
				<option value="4" <?php echo is_selected('4', $status_blok); ?>> HUNIAN </option>
				<option value="5" <?php echo is_selected('5', $status_blok); ?>> RENOVASI </option>
			<?php /*
			if ($status_blok == '1') { ?>
				<option value="1" selected="selected"> KAVLING KOSONG </option>
				<option value="2"> MASA MEMBANGUN </option>
			<?php } elseif ($status_blok == '2') { ?>
				<option value="1"> KAVLING KOSONG </option>
				<option value="2" selected="selected"> MASA MEMBANGUN </option>
				<option value="4"> HUNIAN </option>
			<?php } elseif ($status_blok == '4' || $status_blok == '5') { ?>
				<option value="4" <?php echo is_selected('4', $status_blok); ?>> HUNIAN </option>
				<option value="5" <?php echo is_selected('5', $status_blok); ?>> RENOVASI </option>
			<?php } */?>
		</select>
	</td>
</tr>

<tr>
	<td>KODE BLOK</td>
	<td>:</td>
	<td><b><?php echo $kode_blok; ?></b></td>
	<td>INFO TAGIHAN</td>
	<td>:</td>
	<td><input type="checkbox" name="info_tagihan" id="info_tagihan" <?php echo is_checked('1', $info_tagihan); ?> value="1"></td>
</tr>

<tr>
	<td>LUAS</td>
	<td>:</td>
	<td>
		KAVLING : <b><?php echo to_money($luas_kavling,2); ?> m&sup2;</b>
		&nbsp;&nbsp;&nbsp;
		BANGUNAN : <b><?php echo to_money($luas_bangunan,2); ?> m&sup2;</b>
	</td>
	<td>TGL PPJB</td>
	<td>:</td>
	<td><b><?php echo fm_date($tgl_ppjb); ?></b></td>
</tr>
</table>

<hr><div class="clear"></div>

<!-- PELANGGAN -->
<table class="t-popup f-left w48">
<tr>
	<td colspan="3"><br><b>PELANGGAN</b><hr></td>
</tr>
<tr>
	<td width="120">NO. KTP</td>
	<td width="10">:</td>
	<td>
		<input type="text" name="no_ktp" id="no_ktp" size="40" autocomplete="off" value="<?php echo $no_ktp; ?>" maxlength="30">
		<div id="wrap-lookup">
			<div id="lookup-close"><span onclick="cls_lookup()">Tutup [X]</span></div>
			<div id="lookup-pelanggan"></div>
		</div>
	</td>
</tr>
<tr>
	<td>NAMA</td>
	<td>:</td>
	<td><input type="text" name="nama_pelanggan" id="nama_pelanggan" size="40" value="<?php echo $nama_pelanggan; ?>"></td>
</tr>
<tr>
	<td>NPWP</td>
	<td>:</td>
	<td><input type="text" name="npwp" id="npwp" value="<?php echo $npwp; ?>"></td>
</tr>
<tr>
	<td class="va-top">ALAMAT</td>
	<td class="va-top">:</td>
	<td class="va-top"><textarea name="alamat" id="alamat" class="w90" rows="3"><?php echo $alamat; ?></textarea></td>
</tr>
<tr>
	<td>NO. TELEPON</td>
	<td>:</td>
	<td><input type="text" name="no_telepon" id="no_telepon" size="20" value="<?php echo $no_telepon; ?>"></td>
</tr>
<tr>
	<td>NO. HP</td>
	<td>:</td>
	<td><input type="text" name="no_hp" id="no_hp" size="20" value="<?php echo $no_hp; ?>"></td>
</tr>

<!-- SURAT MENYURAT -->
<tr>
	<td colspan="3"><br>
		<input type="checkbox" name="pakai_sm" id="pakai_sm" <?php echo is_checked('1', $pakai_sm); ?> value="1">
		<b>SURAT MENYURAT</b><hr>
	</td>
</tr>
<tr>
	<td>NAMA</td>
	<td>:</td>
	<td><input type="text" name="sm_nama_pelanggan" id="nama_pelanggan" size="40" value="<?php echo $sm_nama_pelanggan; ?>"></td>
</tr>
<tr>
	<td class="va-top">ALAMAT</td>
	<td class="va-top">:</td>
	<td class="va-top"><textarea name="sm_alamat" id="sm_alamat" class="w90" rows="3"><?php echo $sm_alamat; ?></textarea></td>
</tr>
<tr>
	<td>NO. TELEPON</td>
	<td>:</td>
	<td><input type="text" name="sm_no_telepon" id="sm_no_telepon" size="20" value="<?php echo $sm_no_telepon; ?>"></td>
</tr>
<tr>
	<td>NO. HP</td>
	<td>:</td>
	<td><input type="text" name="sm_no_hp" id="sm_no_hp" size="20" value="<?php echo $sm_no_hp; ?>"></td>
</tr>

<!-- PEMBAYARAN -->
<tr>
	<td colspan="3"><br><b>PEMBAYARAN AUTODEBET</b><hr></td>
</tr>
<tr>
	<td>DEBET BANK</td>
	<td>:</td>
	<td>
		<input type="checkbox" name="debet_bank" id="debet_bank" <?php echo is_checked('1', $debet_bank); ?> value="1">
		&nbsp; KODE BANK : 
		<select name="kode_bank" id="kode_bank">
			<option value=""> -- KODE BANK -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_BANK'];
				$on = $obj->fields['NAMA_BANK'];
				echo "<option value='$ov' ".is_selected($ov, $kode_bank)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>NO. REKENING</td>
	<td>:</td>
	<td><input type="text" name="no_rekening" id="no_rekening" size="40" value="<?php echo $no_rekening; ?>"></td>
</tr>
</table>

<table class="t-popup f-right w48">
<!-- PIUTANG -->
<tr>
	<td colspan="3"><br><b>PIUTANG</b><hr></td>
</tr>
<tr>
	<td width="120">AIR & IPL</td>
	<td width="10">:</td>
	<td>
		<input readonly="readonly" type="text" id="jumlah_piutang_ai" size="5" class="text-center" value="<?php echo intval($jumlah_piutang_ai); ?>">
		<input readonly="readonly" type="text" id="nilai_piutang_ai" size="15" value="<?php echo to_money($nilai_piutang_ai); ?>">
		<input type="button" id="daftar_piutang_ai" value=" Rincian Piutang ">
	</td>
</tr>
<tr>
	<td>DEPOSIT</td>
	<td>:</td>
	<td>
		<input readonly="readonly" type="text" id="jumlah_piutang_dp" size="5" class="text-center" value="<?php echo intval($jumlah_piutang_dp); ?>">
		<input readonly="readonly" type="text" id="nilai_piutang_dp" size="15" value="<?php echo to_money($nilai_piutang_dp); ?>">
		<input type="button" id="daftar_piutang_dp" value=" Rincian Piutang ">
	</td>
</tr>

<!-- AIR -->
<tr>
	<td colspan="3"><br>
		<input type="checkbox" name="aktif_air" id="aktif_air" <?php echo is_checked('1', $aktif_air); ?> value="1">
		<b>AIR</b><hr>
	</td>
</tr>
<tr>
	<td>PERIODE PUTUS</td>
	<td>:</td>
	<td><input type="text" name="periode_putus" id="periode_putus" size="10" value="<?php echo $periode_putus; ?>"></td>
</tr>
<tr>
	<td>TGL PEMUTUSAN</td>
	<td>:</td>
	<td><input type="text" name="tgl_pemutusan" id="tgl_pemutusan" size="13" value="<?php echo $tgl_pemutusan; ?>"></td>
</tr>
<tr>
	<td>PETUGAS</td>
	<td>:</td>
	<td><input type="text" <?php echo $readonly; ?> name="petugas" id="petugas" size="40" value="<?php echo $petugas; ?>"></td>
</tr>
<tr>
	<td>ZONA METER</td>
	<td>:</td>
	<td>
		<select name="kode_zona" id="kode_zona">
			<option value=""> -- ZONA METER -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_ZONA, NAMA_ZONA FROM KWT_ZONA_METER_BALANCE ORDER BY NAMA_ZONA ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_ZONA'];
				$on = $obj->fields['NAMA_ZONA'];
				echo "<option value='$ov' ".is_selected($ov, $kode_zona)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>KATEGORI</td>
	<td>:</td>
	<td>
		<select name="tipe_air" id="tipe_air">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_AIR ORDER BY KODE_TIPE ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				$on = $obj->fields['NAMA_TIPE'];
				echo "<option value='$ov' ".is_selected($ov, $tipe_air)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>KODE TARIF</td>
	<td>:</td>
	<td>
		<input readonly="readonly" type="text" name="key_air" id="key_air" size="15" class="text-center" value="<?php echo $key_air; ?>">
		<input type="button" id="find_key_air" value=" Cari (Alt+A) ">
	</td>
</tr>

<!-- IPL -->
<tr>
	<td colspan="3"><br>
		<input type="checkbox" name="aktif_ipl" id="aktif_ipl" <?php echo is_checked('1', $aktif_ipl); ?> value="1">
		<b>IPL</b><hr>
	</td>
</tr>
<tr>
	<td>KATEGORI</td>
	<td>:</td>
	<td>
		<select name="tipe_ipl" id="tipe_ipl">
			<option value=""> -- KATEGORI -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_IPL WHERE STATUS_BLOK = '$status_blok' ORDER BY KODE_TIPE ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_TIPE'];
				$on = $obj->fields['NAMA_TIPE'];
				echo "<option value='$ov' ".is_selected($ov, $tipe_ipl)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>KODE TARIF</td>
	<td>:</td>
	<td>
		<input readonly="readonly" type="text" name="key_ipl" id="key_ipl" size="15" class="text-center" value="<?php echo $key_ipl; ?>">
		<input type="button" id="find_key_ipl" value=" Cari (Alt+I) ">
	</td>
</tr>

<!-- LAIN-LAIN -->
<tr>
	<td colspan="3" class="text-center"><br><hr></td>
</tr>
<tr>
	<td class="va-top">KETERANGAN</td>
	<td class="va-top">:</td>
	<td class="va-top"><textarea name="keterangan" class="w90" rows="4"><?php echo $keterangan; ?></textarea></td>
</tr>
</table>

<div class="clear"></div>

<table class="t-popup">
<tr>
	<td></td>
	<td class="td-action">
		<input type="submit" id="save" value=" Ubah (Alt+S) ">
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
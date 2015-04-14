<?php
require_once('pelanggan_fa_proses.php');
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
		
		jQuery('#sm_no_ktp').val(data.no_ktp);
		jQuery('#sm_nama_pelanggan').val(data.sm_nama_pelanggan);
		jQuery('#sm_npwp').val(data.npwp);
		jQuery('#sm_no_telepon').val(data.sm_no_telepon);
		jQuery('#sm_no_hp').val(data.sm_no_hp);
		jQuery('#sm_alamat').val(data.sm_alamat);
		
		jQuery('#kode_bank').val(data.kode_bank);
		jQuery('#no_rekening').val(data.no_rekening);
	}, 'json');
}

jQuery(function($) {
	
	$('#kode_sektor').on('change', function(e) {
		e.preventDefault();
		$('#kode_cluster').load(base_master + 'opt_cluster.php?kode_sektor=' + $(this).val());
		return false;
	});
	
	key('alt+a', function(e) { e.preventDefault(); $('#find_key_air').trigger('click'); });
	key('alt+i', function(e) { e.preventDefault(); $('#find_key_ipl').trigger('click'); });
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#luas_kavling, #luas_bangunan').inputmask('numericDecimal', { integerDigits: 15, fractionalDigits: 2 });
	
	$('#tgl_ppjb, #tgl_pemutusan').Zebra_DatePicker({format: 'd-m-Y'});
	$('#periode_putus').Zebra_DatePicker({format: 'm-Y'});
	
	$('#nama_pelanggan, #sm_nama_pelanggan').inputmask('varchar', { repeat: '100' });
	$("#npwp, #sm_npwp").inputmask('99.999.999.9-999.999');
	$('#no_telepon, #sm_no_telepon').inputmask('varchar', { repeat: '50' });
	$('#no_hp, #sm_no_hp').inputmask('varchar', { repeat: '50' });
	
	$('#no_rekening').inputmask('varchar', { repeat: '50' });
	$('#petugas').inputmask('varchar', { repeat: '100' });
	
	$('#jumlah_piutang_ai, #jumlah_piutang_dp, #jumlah_piutang_ll').inputmask('numeric', { repeat: '10' });
	$('#nilai_piutang_ai, #nilai_piutang_dp, #nilai_piutang_ll').inputmask('numeric', { repeat: '10' });
	
	$('#no_ktp').on('keyup', function(e) {
		e.preventDefault();
		get_lookup(this.value);
		return false;
	});
	
	$('#status_blok').on('change', function(e) {
		e.preventDefault();
		$('#tipe_ipl').load(base_master + 'pelanggan_fa/opt_kategori_ipl.php?status_blok=' + $(this).val());
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
			url = base_master + 'pelanggan_fa/find_key_ipl.php?kode_tipe=' + tipe_ipl;
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
			url = base_master + 'pelanggan_fa/find_key_air.php?kode_tipe=' + tipe_air;
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
		
		var url		= base_master + 'pelanggan_fa/pelanggan_fa_proses.php',
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
	
	$('#proses_pemutusan').on('click', function(e) {
		e.preventDefault();
		
		var url		= base_master + 'pelanggan_fa/pelanggan_fa_proses.php';
		
		$.post(url, {
			act: "Putus",
			id: $("#id").val(),
			periode_putus: $("#periode_putus").val(),
			tgl_pemutusan: $("#tgl_pemutusan").val(),
			petugas: $("#petugas").val()
		}, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				location.reload();
			}
		}, 'json');
		
		return false;
	});
	
	$('#daftar_piutang_ai').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'air_ipl/daftar_piutang.php?from_master=1&no_pelanggan=' + $('#id').val();
		setPopup('Rincian Piutang Air & IPL', url, winWidth-100, winHeight-100);
		return false;
	});
	$('#daftar_piutang_dp').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'deposit/daftar_piutang.php?from_master=1&no_pelanggan=' + $('#id').val();
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
	<td class="w38">
		<select name="kode_sektor" id="kode_sektor">
			<option value=""> -- Pilih -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_SEKTOR, NAMA_SEKTOR FROM KWT_SEKTOR ORDER BY KODE_SEKTOR ASC");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SEKTOR'];
				$on = $obj->fields['NAMA_SEKTOR'];
				echo "<option value='$ov' ".is_selected($ov, $kode_sektor)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
	<td class="w10">NO. PELANGGAN</td>
	<td class="w2">:</td>
	<td class="w38">
		<span style="font-weight:bold;font-size:16px;"><?php echo $no_pelanggan; ?></span>
	</td>
</tr>

<tr>
	<td>CLUSTER</td>
	<td>:</td>
	<td>
		<select name="kode_cluster" id="kode_cluster">
			<option value=""> -- Pilih -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_CLUSTER, NAMA_CLUSTER FROM KWT_CLUSTER ORDER BY KODE_CLUSTER ASC");
			
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_CLUSTER'];
				$on = $obj->fields['NAMA_CLUSTER'];
				echo "<option value='$ov' ".is_selected($ov, $kode_cluster)."> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
	<td>STATUS BLOK</td>
	<td>:</td>
	<td>
		<select name="status_blok" id="status_blok">
			<option value=""> -- STATUS BLOK -- </option>
				<option value="<?php echo $trx_kv; ?>" <?php echo is_selected($trx_kv, $status_blok); ?>> KAVLING KOSONG </option>
				<option value="<?php echo $trx_bg; ?>" <?php echo is_selected($trx_bg, $status_blok); ?>> MASA MEMBANGUN </option>
				<option value="<?php echo $trx_hn; ?>" <?php echo is_selected($trx_hn, $status_blok); ?>> HUNIAN </option>
				<option value="<?php echo $trx_rv; ?>" <?php echo is_selected($trx_rv, $status_blok); ?>> RENOVASI </option>
			<?php 
			/*
			if ($status_blok == $trx_kv) { ?>
				<option value="<?php echo $trx_kv; ?>" selected="selected"> KAVLING KOSONG </option>
				<option value="<?php echo $trx_bg; ?>"> MASA MEMBANGUN </option>
			<?php } elseif ($status_blok == $trx_bg) { ?>
				<option value="<?php echo $trx_kv; ?>"> KAVLING KOSONG </option>
				<option value="<?php echo $trx_bg; ?>" selected="selected"> MASA MEMBANGUN </option>
				<option value="<?php echo $trx_hn; ?>"> HUNIAN </option>
			<?php } elseif ($status_blok == $trx_hn || $status_blok == $trx_rv) { ?>
				<option value="<?php echo $trx_hn; ?>" <?php echo is_selected($trx_hn, $status_blok); ?>> HUNIAN </option>
				<option value="<?php echo $trx_rv; ?>" <?php echo is_selected($trx_rv, $status_blok); ?>> RENOVASI </option>
			<?php } 
			*/
			?>
		</select>
	</td>
</tr>

<tr>
	<td>KODE BLOK</td>
	<td>:</td>
	<td><input type="text" name="kode_blok" id="kode_blok" size="13" value="<?php echo $kode_blok; ?>"></td>
	<td>INFO TAGIHAN</td>
	<td>:</td>
	<td><input type="checkbox" name="info_tagihan" id="info_tagihan" <?php echo is_checked('1', $info_tagihan); ?> value="1"></td>
</tr>

<tr>
	<td>LUAS</td>
	<td>:</td>
	<td>
		KAVLING : <input type="text" name="luas_kavling" id="luas_kavling" size="13" value="<?php echo to_money($luas_kavling,2); ?>"> m&sup2;
		&nbsp;&nbsp;&nbsp;
		BANGUNAN : <input type="text" name="luas_bangunan" id="luas_bangunan" size="13" value="<?php echo to_money($luas_bangunan,2); ?>"> m&sup2;
	</td>
	<td>TGL. PPJB</td>
	<td>:</td>
	<td><input type="text" name="tgl_ppjb" id="tgl_ppjb" size="13" value="<?php echo $tgl_ppjb; ?>"></td>
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
		<input type="checkbox" name="aktif_sm" id="aktif_sm" <?php echo is_checked('1', $aktif_sm); ?> value="1">
		<b>SURAT MENYURAT</b><hr>
	</td>
</tr>
<tr>
	<td>NO. KTP</td>
	<td>:</td>
	<td><input type="text" name="sm_no_ktp" id="sm_no_ktp" size="40" value="<?php echo $sm_no_ktp; ?>" maxlength="30"></td>
</tr>
<tr>
	<td>NAMA</td>
	<td>:</td>
	<td><input type="text" name="sm_nama_pelanggan" id="sm_nama_pelanggan" size="40" value="<?php echo $sm_nama_pelanggan; ?>"></td>
</tr>
<tr>
	<td>NPWP</td>
	<td>:</td>
	<td><input type="text" name="sm_npwp" id="sm_npwp" value="<?php echo $sm_npwp; ?>"></td>
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
		<input type="checkbox" name="aktif_ad" id="aktif_ad" <?php echo is_checked('1', $aktif_ad); ?> value="1">
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
<tr>
	<td>BIAYA LAIN-LAIN</td>
	<td>:</td>
	<td>
		<input readonly="readonly" type="text" id="jumlah_piutang_ll" size="5" class="text-center" value="<?php echo intval($jumlah_piutang_ll); ?>">
		<input readonly="readonly" type="text" id="nilai_piutang_ll" size="15" value="<?php echo to_money($nilai_piutang_ll); ?>">
		<input type="button" id="daftar_piutang_ll" value=" Rincian Piutang ">
	</td>
</tr>

<!-- AIR -->
<tr>
	<td colspan="3"><br>
		GOLONGAN : 
		<input type="radio" name="golongan" <?php echo is_checked('0', $golongan); ?> value="0"><b>STANDAR</b>
		<input type="radio" name="golongan" <?php echo is_checked('1', $golongan); ?> value="1"><b>BISNIS</b><hr>
	</td>
</tr>
<tr>
	<td colspan="3"><br>
		<input type="checkbox" name="aktif_air" id="aktif_air" <?php echo is_checked('1', $aktif_air); ?> value="1">
		<b>AIR</b><hr>
	</td>
</tr>
<tr>
	<td>PERIODE PUTUS</td>
	<td>:</td>
	<td><input type="text" name="periode_putus" id="periode_putus" size="10" value=""></td>
</tr>
<tr>
	<td>TGL. PEMUTUSAN</td>
	<td>:</td>
	<td><input type="text" name="tgl_pemutusan" id="tgl_pemutusan" size="13" value=""></td>
</tr>
<tr>
	<td>PETUGAS</td>
	<td>:</td>
	<td>
		<input type="text" name="petugas" id="petugas" size="40" value="">
		<input type="button" name="proses_pemutusan" id="proses_pemutusan" value=" Proses ">
	</td>
</tr>
<tr>
	<td>ZONA METER</td>
	<td>:</td>
	<td>
		<select name="kode_zona" id="kode_zona">
			<option value=""> -- ZONA METER -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_ZONA, NAMA_ZONA FROM KWT_ZMB ORDER BY NAMA_ZONA ASC");
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
<tr>
	<td>TIPE DENDA</td>
	<td>:</td>
	<td>
		<input type="radio" name="tipe_denda" <?php echo is_checked('0', $tipe_denda); ?> value="0"><b>RUPIAH</b>
		<input type="radio" name="tipe_denda" <?php echo is_checked('1', $tipe_denda); ?> value="1"><b>PERSENTASE</b>
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
			$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_IPL WHERE STATUS_BLOK = $status_blok ORDER BY KODE_TIPE ASC");
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
	<td class="va-top"><textarea name="ket" class="w90" rows="4"><?php echo $ket; ?></textarea></td>
</tr>
<tr>
	<td class="va-top">HISTORY PEMUTUSAN AIR<br><br></td>
	<td class="va-top">:</td>
	<td>
	<?php
	$obj = $conn->Execute("
	SELECT 
		PERIODE_PUTUS, 
		CONVERT(VARCHAR (10), TGL_PEMUTUSAN, 105) AS TGL_PEMUTUSAN, 
		PETUGAS 
	FROM KWT_PEMUTUSAN_AIR 
	WHERE NO_PELANGGAN = '$id' 
	ORDER BY CAST(PERIODE_PUTUS AS INT) ");
	$i = 1;
	while( ! $obj->EOF)
	{
		?>
		<table>
		<tr>
			<th>PERIODE</th>
			<th>TANGGAL</th>
			<th>PETUGAS</th>
		</tr>
		<tr>
			<th class="text-center"><?php echo fm_periode($obj->fields['PERIODE_PUTUS']); ?></th>
			<th class="text-center"><?php echo fm_date($obj->fields['TGL_PEMUTUSAN']); ?></th>
			<th><?php echo $obj->fields['PETUGAS']; ?></th>
		</tr>
		</table>
		<?php
		
		$i++;
		$obj->movenext();
	}
	echo '<br>';
	?>
	</td>
</tr>
<tr>
	<td class="va-top">HISTORY BLOK</td>
	<td class="va-top">:</td>
	<td>
	<?php
	if ($join_to != '') {
		echo '<b> BLOK DIGABUNG MENJADI : ' . $join_to . '</b><br>';
		
		$obj = $conn->Execute("SELECT KODE_BLOK FROM KWT_PELANGGAN WHERE JOIN_TO = '$join_to' ORDER BY KODE_BLOK");
		$i = 1;
		while( ! $obj->EOF)
		{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $i . '. ' . $obj->fields['KODE_BLOK'] . '<br>';
			
			$i++;
			$obj->movenext();
		}
		echo '<br>';
	} 
	
	if ($status_join != '') {
		echo '<b> BLOK HASIL GABUNGAN DARI : </b><br>';
		
		$obj = $conn->Execute("SELECT KODE_BLOK FROM KWT_PELANGGAN WHERE JOIN_TO = '$kode_blok' ORDER BY KODE_BLOK");
		$i = 1;
		while( ! $obj->EOF)
		{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $i . '. ' . $obj->fields['KODE_BLOK'] . '<br>';
			
			$i++;
			$obj->movenext();
		}
		echo '<br>';
	}
	
	if ($split_from != '') {
		echo '<b> DIPISAH DARI BLOK : ' . $split_from . '</b><br>';
		
		$obj = $conn->Execute("SELECT KODE_BLOK FROM KWT_PELANGGAN WHERE SPLIT_FROM = '$split_from' ORDER BY KODE_BLOK");
		$i = 1;
		while( ! $obj->EOF)
		{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $i . '. ' . $obj->fields['KODE_BLOK'] . '<br>';
			
			$i++;
			$obj->movenext();
		}
		echo '<br>';
	} 
	
	if ($status_split != '') {
		echo '<b> DIPISAH MENJADI BLOK : </b><br>';
		
		$obj = $conn->Execute("SELECT KODE_BLOK FROM KWT_PELANGGAN WHERE SPLIT_FROM = '$kode_blok' ORDER BY KODE_BLOK");
		$i = 1;
		while( ! $obj->EOF)
		{
			echo '&nbsp;&nbsp;&nbsp;&nbsp;' . $i . '. ' . $obj->fields['KODE_BLOK'] . '<br>';
			
			$i++;
			$obj->movenext();
		}
		echo '<br>';
	}
	?>
	</td>
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
<div class="title-page">LAPORAN REKAP PENERIMAAN<br>SAVE DEPOSIT</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">TGL TRANSAKSI</td>
	<td>
		<select name="jenis_laporan" id="jenis_laporan" class="wauto">
			<option value="HARIAN"> HARIAN </option>
			<option value="BULANAN"> BULANAN </option>
			<option value="REKAP_TANGGAL"> REKAP TANGGAL </option>
		</select>
		<select name="jenis_tgl_trx" id="jenis_tgl_trx" class="wauto">
			<option value="TGL_BAYAR"> TGL BAYAR </option>
			<option value="TGL_TERIMA_BANK"> TGL TERIMA BANK </option>
		</select>
		<input type="text" name="tgl_trx" id="tgl_trx" class="apply" value="">
	</td>
</tr>

<tr>
	<td>JENIS BAYAR</td>
	<td>
		<select name="jenis_bayar" id="jenis_bayar" class="wauto">
			<option value=""> -- JENIS BAYAR -- </option>
			<option value="1"> TUNAI </option>
			<option value="2"> K. DEBIT </option>
			<option value="3"> K. KREDIT </option>
			<option value="4"> T. BANK </option>
		</select>
		
		<select name="bayar_melalui" id="bayar_melalui" class="wauto">
			<option value=""> -- BANK -- </option>
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
	<td>SEKTOR / CLUSTER</td>
	<td>
		<select name="kode_sektor" id="kode_sektor" class="wauto">
			<option value=""> -- SEKTOR -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_SEKTOR, NAMA_SEKTOR FROM KWT_SEKTOR ORDER BY NAMA_SEKTOR ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_SEKTOR'];
				$on = $obj->fields['NAMA_SEKTOR'];
				echo "<option value='$ov'> $on ($ov) </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="3"> MASA MEMBANGUN </option>
			<option value="6"> RENOVASI </option>
		</select>
	</td>
<tr>

<tr>
	<td>VALIDASI</td>
	<td>
		<select name="kasir" id="kasir" class="wauto">
			<option value=""> -- VALIDASI -- </option>
			<?php
			$obj = $conn->Execute("SELECT ID_USER, NAMA_USER FROM KWT_USER ORDER BY NAMA_USER ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['ID_USER'];
				$on = $obj->fields['NAMA_USER'];
				echo "<option value='$ov'> $on </option>";
				$obj->movenext();
			}
			?>
		</select>
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="button" id="apply" value=" Apply (Enter) "></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	/* -- FILTER -- */
	
	set_ddmmyyyy($('#tgl_trx'));
	$(document).on('change', '#jenis_laporan', function(e) {
		if ($(this).val() == 'HARIAN') {
			set_ddmmyyyy($('#tgl_trx'));
		} else {
			set_mmyyyy($('#tgl_trx'));
		}
		return false;
	});
	
	$('#jenis_tgl_trx, #bayar_melalui, #span_tgl_terima_bank').hide();
	$(document).on('change', '#jenis_bayar', function(e) {
		if ($(this).val() == '4') {
			$('#jenis_tgl_trx, #bayar_melalui, #span_tgl_terima_bank').show();
		} else {
			$('#jenis_tgl_trx, #bayar_melalui, #span_tgl_terima_bank').hide();
		}
		return false;
	});
	
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		if (jQuery('#tgl_trx').val() == '') {
			alert('Masukkan tanggal bayar / terima bank!');
			jQuery('#periode').focus();
			return false;
		}
		if (jQuery('#jenis_laporan').val() == 'REKAP_TANGGAL') {
			var file = 'penerimaan_rekap_tanggal_print.php?';
		} else {
			var file = 'penerimaan_rekap_print.php?';
		}
		
		location.href = base_laporan + 'deposit/penerimaan_rekap/' + file + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#tgl_trx').val() == '') {
			alert('Masukkan tanggal bayar / terima bank!');
			jQuery('#periode').focus();
			return false;
		}
		if (jQuery('#jenis_laporan').val() == 'REKAP_TANGGAL') {
			var file = 'penerimaan_rekap_tanggal_print.php?';
		} else {
			var file = 'penerimaan_rekap_print.php?';
		}
		var url = base_laporan + 'deposit/penerimaan_rekap/' + file + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#tgl_trx').val() == '') 
	{
		alert('Masukkan tanggal bayar / terima bank!');
		jQuery('#periode').focus();
		return false;
	}
	if (jQuery('#jenis_laporan').val() == 'REKAP_TANGGAL') {
		var file = 'penerimaan_rekap_tanggal_load.php';
	} else {
		var file = 'penerimaan_rekap_load.php';
	}
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'deposit/penerimaan_rekap/' + file, data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
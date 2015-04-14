<div class="title-page">LAPORAN RINCIAN PENERIMAAN<br>AIR & IPL</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">PERIODE</td>
	<td><input type="text" name="periode" id="periode" class="apply mm-yyyy" size="9" value=""></td>
</tr>

<tr>
	<td>TGL TRANSAKSI</td>
	<td>
		<select name="jenis_laporan" id="jenis_laporan" class="wauto">
			<option value="HARIAN"> HARIAN </option>
			<option value="BULANAN"> BULANAN </option>
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
		
		<select name="kode_cluster" id="kode_cluster" class="wauto">
			<option value=""> -- CLUSTER -- </option>
		</select>
	</td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="1"> KAVLING KOSONG </option>
			<option value="2"> MASA MEMBANGUN </option>
			<option value="4"> HUNIAN </option>
			<option value="5"> RENOVASI </option>
		</select>
	</td>
</tr>

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
	<td>AKTIF</td>
	<td>
		<input type="checkbox" name="aktif_air" id="aktif_air" value="1"> <label for="aktif_air">AIR</label>
		<input type="checkbox" name="aktif_ipl" id="aktif_ipl" value="1"> <label for="aktif_ipl">IPL</label>
	</td>
</tr>

<tr>
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class="apply text-center" value="20">
		<input type="button" id="apply" value=" Apply (Enter) ">
	</td>
</tr>

<tr>
	<td>TOTAL DATA</td>
	<td id="total-data"></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$('#kode_sektor').on('change', function(e) {
		e.preventDefault();
		$('#kode_cluster').load(base_master + 'opt_cluster.php?kode_sektor=' + $(this).val());
		return false;
	});
	
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
		if (jQuery('#tgl_trx').val() == '' && jQuery('#periode').val() == '' ) {
			alert('Masukkan waktu pembayaran!');
			return false;
		} else if (jQuery('#kode_sektor').val() == '') {
			alert('Pilih sektor!');
			jQuery('#kode_sektor').focus();
			return false;
		} 
		
		location.href = base_laporan + 'air_ipl/penerimaan_rincian/penerimaan_rincian_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#tgl_trx').val() == '' && jQuery('#periode').val() == '' ) {
			alert('Masukkan waktu pembayaran!');
			return false;
		} else if (jQuery('#kode_sektor').val() == '') {
			alert('Pilih sektor!');
			jQuery('#kode_sektor').focus();
			return false;
		} 
		
		var url = base_laporan + 'air_ipl/penerimaan_rincian/penerimaan_rincian_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	$(document).on('click', '#next_page', function(e) {
		e.preventDefault();
		var total_page = parseInt($('#total_page').val()),
			page_num = parseInt($('.page_num').val()) + 1;
		if (page_num <= total_page)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	$(document).on('click', '#prev_page', function(e) {
		e.preventDefault();
		var page_num = parseInt($('.page_num').val()) - 1;
		if (page_num > 0)
		{
			$('.page_num').val(page_num);
			$('#apply').trigger('click');
		}
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#tgl_trx').val() == '' && jQuery('#periode').val() == '' ) {
		alert('Masukkan waktu pembayaran!');
		return false;
	} else if (jQuery('#kode_sektor').val() == '') {
		alert('Pilih sektor!');
		jQuery('#kode_sektor').focus();
		return false;
	} 
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'air_ipl/penerimaan_rincian/penerimaan_rincian_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
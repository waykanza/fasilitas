<div class="title-page">LAPORAN PEMAKAIAN AIR<br>BERDASARKAN KELOMPOK PELANGGAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="130">PERIODE</td>
	<td><input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value=""></td>
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
<tr>

<tr>
	<td>ASUMSI KENAIKAN</td>
	<td>
		<input type="radio" name="asumsi" id="ass1" checked="checked" value="saat_ini"> <label for="ass1">SAAT INI</label>
		<input type="radio" name="asumsi" id="ass2" value="naik_tetap"> <label for="ass2">NAIK TETAP</label>
		<input type="radio" name="asumsi" id="ass3" value="bervariasi"> <label for="ass3">BERVARIASI</label>
	</td>
</tr>

<tr id="tr_tarif_baru" style="display:none;">
	<td>TARIF BARU</td>
	<td>
		<input type="text" name="tarif_baru" id="tarif_baru" size="5" class="" value="0.00"> %
	</td>
</tr>

<tr>
	<td></td>
	<td><input type="button" id="apply" value=" Apply (Enter) "></td>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$('#kode_sektor').on('change', function(e) {
		e.preventDefault();
		$('#kode_cluster').load(base_master + 'opt_cluster.php?kode_sektor=' + $(this).val());
		return false;
	});
	
	$('input[type=radio][name=asumsi]').change(function() {
        if (this.value == 'naik_tetap') {
            $('#tr_tarif_baru').show();
        } else {
			$('#tr_tarif_baru').hide();
        }
		
		if (this.value == 'bervariasi') {
			alert('Anda memilih ASUMSI KENAIKAN BERVARIASI !\nPastikan nilai kenaikan pada MASTER KATEGORi AIR\nsudah anda isi dengan benar !!');
		}
    });
	
	/* -- FILTER -- */
	
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
		if (jQuery('#periode').val() == '') {
			alert('Masukkan periode!');
			jQuery('#periode').focus();
			return false;
		} 
		
		location.href = base_laporan + 'air_ipl/pemakaian_air/pemakaian_air_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#periode').val() == '') {
			alert('Masukkan periode!');
			jQuery('#periode').focus();
			return false;
		} 
		
		var url = base_laporan + 'air_ipl/pemakaian_air/pemakaian_air_print.php?' + $('#form').serialize();
		open_print(url)
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+x', function(e) { e.preventDefault(); $('#excel').trigger('click'); });
	
	$('#tarif_baru').inputmask('percent100');
});

function loadData()
{
	if (jQuery('#periode').val() == '') {
		alert('Masukkan periode!');
		jQuery('#periode').focus();
		return false;
	} 
	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'air_ipl/pemakaian_air/pemakaian_air_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
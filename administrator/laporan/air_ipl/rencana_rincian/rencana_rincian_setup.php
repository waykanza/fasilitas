<div class="title-page">LAPORAN RINCIAN RENCANA PENERIMAAN<br>AIR & IPL</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">PERIODE</td>
	<td> <input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value=""></td>
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
	<td>WMB</td>
	<td>
		<select name="kode_zona" id="kode_zona" class="wauto">
			<option value=""> -- WMB -- </option>
			<?php
			$obj = $conn->Execute("SELECT KODE_ZONA, NAMA_ZONA FROM KWT_ZONA_METER_BALANCE ORDER BY NAMA_ZONA ASC");
			while( ! $obj->EOF)
			{
				$ov = $obj->fields['KODE_ZONA'];
				$on = $obj->fields['NAMA_ZONA'];
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
			<option value="1"> KAVLING KOSONG </option>
			<option value="2"> MASA MEMBANGUN </option>
			<option value="4"> HUNIAN </option>
			<option value="5"> RENOVASI </option>
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
	<td>DETAIL LAPORAN</td>
	<td>
		<input type="radio" name="jdl" id="jdlg" value="0" checked="checked"> <label for="jdlg">NON GROUP</label>
		<input type="radio" name="jdl" id="jdlng" value="1"> <label for="jdlng">GROUP</label>
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
			alert('Masukkan periode laporan!');
			jQuery('#periode').focus();
			return false;
		} else if (jQuery('#kode_sektor').val() == '') {
			alert('Pilih sektor!');
			jQuery('#kode_sektor').focus();
			return false;
		}
		
		location.href = base_laporan + 'air_ipl/rencana_rincian/rencana_rincian_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if (jQuery('#periode').val() == '') {
			alert('Masukkan periode laporan!');
			jQuery('#periode').focus();
			return false;
		} else if (jQuery('#kode_sektor').val() == '') {
			alert('Pilih sektor!');
			jQuery('#kode_sektor').focus();
			return false;
		}
		
		var url = base_laporan + 'air_ipl/rencana_rincian/rencana_rincian_print.php?' + $('#form').serialize();
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
	key('alt+x', function(e) { e.preventDefault(); $('#excel').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#periode').val() == '') {
		alert('Masukkan periode laporan!');
		jQuery('#periode').focus();
		return false;
	} else if (jQuery('#kode_sektor').val() == '') {
		alert('Pilih sektor!');
		jQuery('#kode_sektor').focus();
		return false;
	} 
	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'air_ipl/rencana_rincian/rencana_rincian_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
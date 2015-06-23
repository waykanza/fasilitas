<div class="title-page">INVOICE SEWA LAHAN</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="110">BLOK / CLUSTER</td>
	<td>
		<select name="tipe_cari" id="tipe_cari">
			<option value="1"> KODE BLOK </option>
			<option value="2"> CLUSTER </option>
		</select>
		
		<input type="text" name="kode_blok" id="kode_blok" class="apply" value="">
		
		<select id="kode_sektor" style="display:none;">
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
		
		<select name="kode_cluster" id="kode_cluster" style="display:none;">
			<option value=""> -- CLUSTER -- </option>
		</select>
	</td>
</tr>
<!--
<tr>
	<td>FILTER</td>
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
-->
<tr>
	<td></td>
	<td>
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
	});
	
	$('#tipe_cari').on('change', function(e) {
		e.preventDefault();
		var tipe_cari = $('#tipe_cari').val();
		
		if (tipe_cari == '1')
		{
			$('#kode_blok').show();
			$('#kode_sektor, #kode_cluster').hide();
		}
		else if (tipe_cari == '2')
		{
			$('#kode_blok').hide();
			$('#kode_sektor, #kode_cluster').show();
		}
	});
	
	/* -- BUTTON -- */
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length;
		if (checked < 1)
		{
			alert('Pilih data yang akan dicetak.');
		} 
		else
		{
			open_print(base_invoice + 'air_ipl/invoice_proses.php?' + $('#form').serialize());
		}
		
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	
});

function loadData()
{
	var periode = jQuery('#periode').val(),
		tipe_cari = jQuery('#tipe_cari').val(),
		kode_blok = jQuery('#kode_blok').val(),
		kode_sektor = jQuery('#kode_sektor').val(),
		kode_cluster = jQuery('#kode_cluster').val();
		
	if (periode == '')
	{
		alert('Masukkan periode');
		jQuery('#periode').focus();
		return false;
	} 
	else if (tipe_cari == '1')
	{
		if (kode_blok == '')
		{
			alert('Masukkan kode blok');
			jQuery('#kode_blok').focus();
			return false;
		}
	}
	else if (tipe_cari == '2')
	{
		if (kode_cluster == '')
		{
			alert('Pilih cluster');
			jQuery('#kode_cluster').focus();
			return false;
		}
	}
	
	jQuery('#t-detail').load(base_invoice + 'air_ipl/invoice_load.php', jQuery('#form').serialize());
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
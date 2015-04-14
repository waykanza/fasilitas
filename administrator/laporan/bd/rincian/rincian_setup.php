<div class="title-page">LAPORAN BD</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">TGL. BAYAR (SYS)</td>
	<td>
		<select name="jenis_tgl_bd" id="jenis_tgl_bd" class="wauto">
			<option value="HARIAN"> HARIAN </option>
			<option value="BULANAN"> BULANAN </option>
		</select>
		
		<input type="text" name="tgl_bd" id="tgl_bd" class="apply mm-yyyy" size="9" value="">
	</td>
</tr>
<tr>
	<td>NO. KWT</td>
	<td><input type="text" name="no_kwitansi" id="no_kwitansi" class="apply" size="15" value=""></td>
</tr>
<tr>
	<td>BANK</td>
	<td>
		<select name="bank_bd" id="bank_bd" class="wauto">
			<option value=""> -- BANK -- </option>
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
	</td>
</tr>

<tr>
	<td>STATUS BLOK</td>
	<td>
		<select name="trx" id="trx" class="wauto">
			<option value=""> -- STATUS BLOK -- </option>
			<option value="<?php echo $trx_kv; ?>"> KAVLING KOSONG </option>
			<option value="<?php echo $trx_bg; ?>"> MASA MEMBANGUN </option>
			<option value="<?php echo $trx_dbg; ?>"> MASA MEMBANGUN (DEPOSIT) </option>
			<option value="<?php echo $trx_lbg; ?>"> MASA MEMBANGUN (LAIN-LAIN) </option>
			<option value="<?php echo $trx_hn; ?>"> HUNIAN </option>
			<option value="<?php echo $trx_rv; ?>"> RENOVASI </option>
			<option value="<?php echo $trx_drv; ?>"> RENOVASI (DEPOSIT) </option>
			<option value="<?php echo $trx_lrv; ?>"> RENOVASI (LAIN-LAIN) </option>
		</select>
		
		<select name="aktif_air" id="aktif_air" class="wauto">
			<option value=""> -- STATUS AIR -- </option>
			<option value="1"> AIR (AKTIF) </option>
			<option value="0"> AIR (TDK AKTIF) </option>
		</select>
		
		<select name="aktif_ipl" id="aktif_ipl" class="wauto">
			<option value=""> -- STATUS IPL -- </option>
			<option value="1"> IPL (AKTIF) </option>
			<option value="0"> IPL (TDK AKTIF) </option>
		</select>
	</td>
<tr>

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
	
	$(document).on('keypress', '.apply', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#apply').trigger('click'); return false; }
	});
	
	/* -- FILTER -- */
	set_ddmmyyyy($('#tgl_bd'));
	$(document).on('change', '#jenis_tgl_bd', function(e) {
		if ($(this).val() == 'HARIAN') {
			set_ddmmyyyy($('#tgl_bd'));
		} else {
			set_mmyyyy($('#tgl_bd'));
		}
		return false;
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		loadData();
		return false;
	});
	
	$(document).on('click', '#excel', function(e) {
		e.preventDefault();
		if ($('#tgl_bd').val() == '') {
			alert('Masukkan waktu pembayaran!');
			return false;
		} else if ($('#bank_bd').val() == '') {
			alert('Pilih bank debet!');
			return false;
		}
		
		location.href = base_laporan + 'bd/rincian/rincian_xls.php?' + $('#form').serialize();
		return false;
	});
	
	$(document).on('click', '#print', function(e) {
		e.preventDefault();
		if ($('#tgl_bd').val() == '') {
			alert('Masukkan waktu pembayaran!');
			return false;
		} else if ($('#bank_bd').val() == '') {
			alert('Pilih bank debet!');
			return false;
		}
		
		var url = base_laporan + 'bd/rincian/rincian_print.php?' + $('#form').serialize();
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
	if (jQuery('#tgl_bd').val() == '') {
		alert('Masukkan waktu pembayaran!');
		return false;
	} else if (jQuery('#bank_bd').val() == '') {
			alert('Pilih bank debet!');
			return false;
	}
	
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_laporan + 'bd/rincian/rincian_load.php', data);
	
	return false;
}

</script>

<div id="t-detail"></div>
</form>
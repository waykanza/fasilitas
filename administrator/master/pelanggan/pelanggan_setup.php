<div class="title-page">MASTER PELANGGAN</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="120">PENCARIAN</td>
	<td>
		<select name="field1" id="field1" class="wauto">
			<option value="KODE_BLOK"> KODE BLOK </option>
			<option value="NO_PELANGGAN"> NO. PELANGGAN </option>
			<option value="NAMA_PELANGGAN"> NAMA PELANGGAN </option>
			<option value="KETERANGAN"> KETERANGAN </option>
			<option value="KEY_AIR"> GOL. AIR </option>
			<option value="KEY_IPL"> GOL. IPL </option>
		</select>
		<input type="text" name="search1" id="search1" size="30" class="apply" value="">
	</td>
</tr>

<tr>
	<td></td>
	<td>
		<select name="kode_sektor" id="kode_sektor" class="apply wauto">
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
	
		<select name="kode_cluster" id="kode_cluster" class="apply wauto">
			<option value=""> -- CLUSTER -- </option>
		</select>
	</td>
</tr>

<tr>
	<td></td>
	<td>
		<select name="status_blok" id="status_blok" class="wauto">
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
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class=" apply text-center" value="20">
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
	
	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		showPopup(id);
		return false;
	});
	
	$(document).on('click', '#join', function(e) {
		e.preventDefault();
		var url = base_master + 'pelanggan/join/join_popup.php';
		setPopup('Penggabungan Kode Blok', url, winWidth-100, winHeight-100);
		return false;
	});
	
	$(document).on('click', '#split', function(e) {
		e.preventDefault();
		var url = base_master + 'pelanggan/split/split_popup.php';
		setPopup('Pemisahan Kode Blok', url, winWidth-100, winHeight-100);
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('alt+n', function(e) { e.preventDefault(); $('#tambah').trigger('click'); });
	key('alt+d', function(e) { e.preventDefault(); $('#hapus').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	loadData();
});

function loadData()
{
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_master + 'pelanggan/pelanggan_load.php', data);
	
	return false;
}

function showPopup(id)
{
	var url = base_master + 'pelanggan/pelanggan_popup.php?act=Ubah&id=' + id;
	setPopup('Ubah Data Pelanggan', url, winWidth-100, winHeight-100);
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
<div class="title-page">PELANGGAN BARU</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">BULAN IMPORT</td>
	<td>
		<input type="text" name="created_date" id="created_date" size="9" class="apply mm-yyyy" value="<?php echo date('m-Y'); ?>">
		<input type="button" id="apply" value=" Apply (Enter) ">
		<input type="button" id="import" value=" Import ">
	</td>
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
	
	$(document).on('click', '#import', function(e) {
		e.preventDefault();
		
		$.get("<?php echo ORCL . 'serah_terima.php'; ?>", function(result) {
			if (result == 'YES') {
				$('#respon').load(base_master + 'pelanggan_baru/pelanggan_baru_import.php');
			} else if (result == 'NO') {
				alert('Tidak ada blok serah terima baru');
			} else {
				alert(result);
			}
		});
		
		return false;
	});
	
	$(document).on('click', 'tr.onclick td:not(.notclick)', function(e) {
		e.preventDefault();
		var id = $(this).parent().attr('id');
		showPopup(id);
		return false;
	});
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	
});

function loadData()
{
	if (jQuery('#created_date').val() == '') {
		alert('Masukkan tanggal import!');
		jQuery('#created_date').focus();
		return false;
	}
	
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#respon').html('');
	jQuery('#t-detail').load(base_master + 'pelanggan_baru/pelanggan_baru_load.php', data);
	
	return false;
}

function showPopup(id)
{
	var url = base_master + 'pelanggan_baru/pelanggan_baru_popup.php?act=Ubah&id=' + id;
	setPopup('Ubah Data Pelanggan', url, winWidth-100, winHeight-100);
	
	return false;
}
</script>
<div id="respon" style="background:#FFFFFF;border:2px solid #FF9900;padding:10px;"></div><br>
<div id="t-detail"></div>
</form>
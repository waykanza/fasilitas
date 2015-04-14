<div class="title-page">PELANGGAN BARU</div>

<form name="form" id="form" method="post">
<table class="t-control wauto">
<tr>
	<td width="100">BULAN IMPORT</td>
	<td>
		<input type="text" name="import_date" id="import_date" size="9" class="apply mm-yyyy" value="<?php echo date('m-Y'); ?>">
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
		
		var url		= base_master + 'pelanggan/new/orcl.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				parent.loadData();
			}
		}, 'json');
		
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
	if (jQuery('#import_date').val() == '') {
		alert('Masukkan tanggal import!');
		jQuery('#import_date').focus();
		return false;
	}
	
	if (popup) { popup.close(); }
	var data = jQuery('#form').serialize();
	jQuery('#t-detail').load(base_master + 'pelanggan/new/pelanggan_load.php', data);
	
	return false;
}

function showPopup(id)
{
	var url = base_master + 'pelanggan/new/pelanggan_popup.php?act=Ubah&id=' + id;
	setPopup('Ubah Data Pelanggan', url, winWidth-100, winHeight-100);
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
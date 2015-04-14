<div class="title-page">PENOMORAN FAKTUR PAJAK</div>

<form name="form" id="form" method="post">
<table class="t-control">
<tr>
	<td width="120">PERIODE</td>
	<td>
		<input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value="">&nbsp;&nbsp;&nbsp;
		<input type="button" id="apply" value=" Tampilkan ">&nbsp;&nbsp;&nbsp;
		<input type="button" name="proses" id="proses" value=" Proses Penomoran " style="display:none;">&nbsp;&nbsp;&nbsp;
		<input type="button" name="save" id="save" value=" Simpan " style="display:none;">
	</td>
</tr>

<tr>
	<td>JUMLAH BARIS</td>
	<td>
		<input type="text" name="per_page" size="3" id="per_page" class="text-center" value="20">
		<input type="hidden" id="step" value="1">
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
	
	$(document).on('keypress', '.proses', function(e) {
		var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) { $('#proses').trigger('click'); return false; }
	});
	
	/* -- BUTTON -- */
	$(document).on('click', '#apply', function(e) {
		e.preventDefault();
		$('#step').val('1');
		step_1();
		return false;
	});
	
	$(document).on('click', '#proses', function(e) {
		e.preventDefault();
		$('#step').val('2');
		step_2();
		return false;
	});
	
	$(document).on('click', '#next_page', function(e) {
		e.preventDefault();
		var total_page = parseInt($('#total_page').val()),
			page_num = parseInt($('.page_num').val()) + 1;
		if (page_num <= total_page)
		{
			$('.page_num').val(page_num);
			
			var step = $('#step').val();
			if (step == '1') {
				$('#apply').trigger('click');
			} if (step == '2') {
				$('#proses').trigger('click');
			}
		}
	});
	
	$(document).on('click', '#prev_page', function(e) {
		e.preventDefault();
		var page_num = parseInt($('.page_num').val()) - 1;
		if (page_num > 0)
		{
			$('.page_num').val(page_num);
			
			var step = $('#step').val();
			if (step == '1') {
				$('#apply').trigger('click');
			} if (step == '2') {
				$('#proses').trigger('click');
			}
		}
	});
	
	/* SAVE */
	$(document).on('click', '#save', function(e) {
		
		var periode = $('#periode').val();
		if (periode == '') 
		{
			alert('Masukkan periode');
			$('#periode').focus();
			return false;
		}
		
		$.post(base_faktur_pajak + 'penomoran/penomoran_save.php' , { periode: periode }, function(data) {
			
			if (data.error == false) {
				$('#t-detail').html('');
				$('#step').hide('1');
				$('#proses').hide();
				$('#save').hide();
			}
			
			qalert(data.msg);
			
		}, 'json');
		
		return false;
	});
	
});

/* LOAD */
function step_1()
{
	jQuery('#save').hide();
	
	var periode = jQuery('#periode').val();
	if (periode == '') 
	{
		alert('Masukkan periode');
		jQuery('#periode').focus();
		return false;
	}
	
	jQuery('#t-detail').load(base_faktur_pajak + 'penomoran/penomoran_load.php', jQuery('#form').serialize());
	
	return false;
}

/* PROSES */
function step_2()
{
	var periode = jQuery('#periode').val();
	if (periode == '') 
	{
		alert('Masukkan periode');
		jQuery('#periode').focus();
		return false;
	}
	
	jQuery('#t-detail').load(base_faktur_pajak + 'penomoran/penomoran_proses.php', jQuery('#form').serialize());
	
	return false;
}
</script>

<div id="t-detail"></div>
</form>
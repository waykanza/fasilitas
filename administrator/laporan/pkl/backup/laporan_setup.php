<script type="text/javascript">
	function cetak()
	{
	var tgl = jQuery('#periode').val().toString();
	var res = tgl.split('-');
	var url = base_laporan + 'pkl/laporan_print.php?bln='+res[0]+'&thn='+res[1];
	window.open(url,'_blank');
		return false;
	}
</script>
<div class="title-page">CETAK LAPORAN MEDIA PROMOSI</div>
<table class="t-control wauto">
<tr>
	<td>PERIODE</td>
	<td><input type="text" name = 'periode' id= 'periode' class="mm-yyyy" /></td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><button id='cetak' onclick="cetak()">Cetak</button></td>
</tr>


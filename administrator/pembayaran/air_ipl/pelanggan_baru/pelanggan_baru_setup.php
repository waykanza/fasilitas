<div class="title-page">PEMBAYARAN PELANGGAN BARU<br>AIR & IPL</div>

<form name="form" id="form" method="post">
<table class="t-data w50" style="margin:0 auto;">
<tr>
	<th width="150">PERIODE AWAL</th>
	<td><input type="text" name="periode" id="periode" size="10" class="apply mm-yyyy" value=""></td>
</tr>
<tr>
	<th>JML. PERIODE</th>
	<td><input type="text" name="jumlah_periode" id="jumlah_periode" size="5" class="text-center" value="1"></td>
</tr>
<tr>
	<th>NO. PELANGGAN</th>
	<td>
		<input readonly="readonly" type="text" name="no_pelanggan" id="no_pelanggan" value="">
		<input type="hidden" name="trx" id="trx" value="">
		<input type="button" id="find_pelanggan" value=" Cari (Alt+C) ">
	</td>
</tr>
<tr>
	<th>NAMA PELANGGAN</th>
	<td id="td-nama_pelanggan"></td>
</tr>
<tr>
	<th>SEKTOR</th>
	<td id="td-nama_sektor"></td>
</tr>
<tr>
	<th>CLUSTER</th>
	<td id="td-nama_cluster"></td>
</tr>
<tr>
	<th>KODE BLOK</th>
	<td id="td-kode_blok"></td>
</tr>
<tr>
	<th>KODE TARIF AIR</th>
	<td id="td-key_air"></td>
</tr>
<tr>
	<th>KODE TARIF IPL</th>
	<td id="td-key_ipl"></td>
</tr>
<tr>
	<th colspan="2" class="text-center">STAND METER</th>
</tr>
<tr>
	<th>STAND AKHIR</th>
	<td><input type="text" name="stand_akhir" id="stand_akhir" size="10" value=""></td>
</tr>
<tr>
	<th>STAND LALU</th>
	<td><input type="text" name="stand_lalu" id="stand_lalu" size="10" value=""></td>
</tr>
<tr>
	<th>PEMAKAIAN</th>
	<td><input readonly="readonly" type="text" id="pemakaian" size="10" value=""></td>
</tr>
<tr>
	<th colspan="2" class="text-right">
		<input type="button" id="save" value=" Simpan (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) ">&nbsp;
	</th>
</tr>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	key('alt+c', function(e) { e.preventDefault(); $('#find_pelanggan').trigger('click'); });
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#stand_akhir, #stand_lalu').inputmask('numeric', { repeat: '10' });
	$('#pemakaian').inputmask('numeric', { repeat: '10', allowMinus: true });
	
	$('#stand_akhir, #stand_lalu').on('change', function(e) {
		e.preventDefault();
		
		var stand_akhir	= $('#stand_akhir').val(),
			stand_lalu	= $('#stand_lalu').val();

		stand_akhir = stand_akhir.replace(/[^0-9.]/g, '');
		stand_lalu = stand_lalu.replace(/[^0-9.]/g, '');
		
		stand_akhir = (stand_akhir == '') ? 0 : parseFloat(stand_akhir);
		stand_lalu = (stand_lalu == '') ? 0 : parseFloat(stand_lalu);
		
		var pemakaian = (stand_akhir - stand_lalu);
		
		$('#pemakaian').val(pemakaian);
	});
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		location.href = base_adm;
	});
	
	$('#reset').on('click', function(e) {
		e.preventDefault();
		$('#no_pelanggan').val('');
		$('#td-nama_pelanggan, #td-nama_sektor, #td-nama_cluster, #td-kode_blok, #td-key_air, #td-key_ipl').empty();
		$('#stand_akhir, #stand_lalu, #pemakaian').val('');
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		
		var periode = $('#periode').val(),
			no_pelanggan = $('#no_pelanggan').val(),
			pemakaian	= $('#pemakaian').val(),
			pemakaian	= pemakaian.replace(/[^-0-9.]/g, ''),
			pemakaian	= (pemakaian == '') ? 0 : parseFloat(pemakaian);
			
		if (periode == '')
		{
			alert('Masukkan periode pemakaian.');
			$('#periode').focus();
			return false;
		}
		else if (no_pelanggan == '')
		{
			alert('Pilih pelangan.');
			$('#no_pelanggan').focus();
			return false;
		}
		else if (pemakaian < 0)
		{
			alert('Pemakaian tidak boleh minus..');
			$('#stand_akhir').focus();
			return false;
		}
		
		var url		= base_pembayaran + 'air_ipl/pelanggan_baru/pelanggan_baru_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false)
			{
				$('#reset').trigger('click');
			}
		}, 'json');
		
		return false;
	});
	
	$('#find_pelanggan').on('click', function(e) {
		e.preventDefault();
		var periode = $('#periode').val();
		if (periode == '')
		{
			alert('Masukkan periode pembayaran (Awal).');
			$('#periode').focus();
			return false;
		}
		
		var url = base_pembayaran + 'air_ipl/pelanggan_baru/find_pelanggan.php?periode=' + periode;
		
		setPopup('Cari Pelanggan', url, winWidth-100, 500);
		
		return false;
	});
	
});
</script>

<div id="t-detail"></div>
</form>
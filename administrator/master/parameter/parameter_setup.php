<?php
$conn = conn();
$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
?>

<div class="title-page">PARAMETER</div>

<form name="form" id="form" method="post">

<script type="text/javascript">
jQuery(function($) {

	$('#jrp_pt').inputmask('varchar', { repeat: '100' }); 
	$('#jrp_alamat_1').inputmask('varchar', { repeat: '200' }); 
	$('#jrp_alamat_2').inputmask('varchar', { repeat: '200' });
	$('#jrp_kota').inputmask('varchar', { repeat: '100' }); 
	$('#jrp_kode_pos').inputmask('varchar', { repeat: '5' });
	$('#jrp_telp').inputmask('varchar', { repeat: '30' }); 
	$('#jrp_fax').inputmask('varchar', { repeat: '30' });
	$('#jrp_email').inputmask('varchar', { repeat: '30' }); 
	
	$('#unit_nama').inputmask('varchar', { repeat: '100' }); 
	$('#unit_alamat_1').inputmask('varchar', { repeat: '200' }); 
	$('#unit_alamat_2').inputmask('varchar', { repeat: '200' });
	$('#unit_kota').inputmask('varchar', { repeat: '100' }); 
	$('#unit_kode_pos').inputmask('varchar', { repeat: '5' });
	$('#unit_telp').inputmask('varchar', { repeat: '30' }); 
	$('#unit_fax').inputmask('varchar', { repeat: '30' });
	$('#unit_email').inputmask('varchar', { repeat: '30' }); 
	
	$('#nama_pimpinan').inputmask('varchar', { repeat: '100' });
	$('#jbt_pimpinan').inputmask('varchar', { repeat: '100' });
	$('#nama_pajak').inputmask('varchar', { repeat: '100' });
	$('#jbt_pajak').inputmask('varchar', { repeat: '100' });
	$('#nama_administrasi').inputmask('varchar', { repeat: '100' }); 
	$('#jbt_administrasi').inputmask('varchar', { repeat: '100' });
	
	$('#cou_np').inputmask('numeric', { repeat: '7' });
	$('#persen_ppn').inputmask('percent');
	$('#reg_npwp').inputmask('varchar', { repeat: '30' });
	$('#reg_fp').inputmask('varchar', { repeat: '15' });
	$('#cou_fp').inputmask('numeric', { repeat: '8' });
	
	$('#administrasi_kv').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_kv').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_kv').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_kv').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_kv').inputmask('numeric', { repeat: '7' });
	
	$('#administrasi_bg').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_bg').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_bg').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_bg').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_bg').inputmask('numeric', { repeat: '7' });
	
	$('#administrasi_db').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_db').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_db').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_db').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_db').inputmask('numeric', { repeat: '7' });
	
	$('#administrasi_hn').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_hn').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_hn').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_hn').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_hn').inputmask('numeric', { repeat: '7' });
	
	$('#administrasi_rv').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_rv').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_rv').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_rv').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_rv').inputmask('numeric', { repeat: '7' });
	
	$('#administrasi_dr').inputmask('numeric', { repeat: '6' });
	$('#reg_ivc_dr').inputmask('varchar', { repeat: '30' });
	$('#cou_ivc_dr').inputmask('numeric', { repeat: '7' });
	$('#reg_kwt_dr').inputmask('varchar', { repeat: '30' });
	$('#cou_kwt_dr').inputmask('numeric', { repeat: '7' });
	
	$(document).on('click', '#tab-control a', function(e) {
		e.preventDefault();
		$('#tab-control a').removeClass('tab-active');
		$(this).addClass('tab-active');
		var act = $(this).attr('href');
		
		$('.tab-content').hide();
		$('#tab-' + act).show();
		
	});
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url = base_master + 'parameter/parameter_proses.php',
			data = $('#form').serialize();
				
		$.post(url, data, function(data) {
			alert(data.msg);
		}, 'json');
		
		return false;
	});
	
	$('#tab-control a:first').trigger('click');
});
</script>
<div style="position:relative;overflow:hidden;border:2px solid #CCCCCC;">
	<div id="tab-control">
		<ul>
			<li><a href="perusahaan">PERUSAHAAN</a></li>
			<li><a href="organisasi">ORGANISASI</a></li>
			<li><a href="global">GLOBAL</a></li>
			<li><a href="kavling-kosong">REG. KAVLING KOSONG</a></li>
			<li><a href="masa-membangun">REG. MASA MEMBANGUN</a></li>
			<li><a href="hunian">REG. HUNIAN</a></li>
			<li><a href="renovasi">REG. RENOVASI</a></li>
		</ul>
	</div>

	<?php
	$query = "SELECT * FROM KWT_PARAMETER";
	$obj = $conn->Execute($query);
	?>

	<div class="tab-content" id="tab-perusahaan">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td colspan="6" class="text-center"><br><b>P E R U S A H A A N</b><hr></td>
		</tr>
		
		<tr>
			<td width="70">NAMA PT</td><td width="1">:</td>
			<td><input type="text" name="jrp_pt" id="jrp_pt" size="45" value="<?php echo $obj->fields['JRP_PT']; ?>"></td>
			<td width="80">KODE POS</td><td width="1">:</td>
			<td><input type="text" name="jrp_kode_pos" id="jrp_kode_pos" size="7" value="<?php echo $obj->fields['JRP_KODE_POS']; ?>"></td></td>
		</tr>
		
		<tr>
			<td>ALAMAT 1</td><td>:</td>
			<td><input type="text" name="jrp_alamat_1" id="jrp_alamat_1" size="45" value="<?php echo $obj->fields['JRP_ALAMAT_1']; ?>"></td>
			<td>TELEPON</td><td>:</td>
			<td><input type="text" name="jrp_telp" id="jrp_telp" size="45" value="<?php echo $obj->fields['JRP_TELP']; ?>"></td>
		</tr>
		
		<tr>
			<td>ALAMAT 2</td><td>:</td>
			<td><input type="text" name="jrp_alamat_2" id="jrp_alamat_2" size="45" value="<?php echo $obj->fields['JRP_ALAMAT_2']; ?>"></td>
			<td>FAX</td><td>:</td>
			<td><input type="text" name="jrp_fax" id="jrp_fax" size="45" value="<?php echo $obj->fields['JRP_FAX']; ?>"></td>
		</tr>
		
		<tr>
			<td>KOTA</td><td>:</td>
			<td><input type="text" name="jrp_kota" id="jrp_kota" size="45" value="<?php echo $obj->fields['JRP_KOTA']; ?>"></td>
			<td>EMAIL</td><td>:</td>
			<td><input type="text" name="jrp_email" id="jrp_email" size="45" value="<?php echo $obj->fields['JRP_EMAIL']; ?>"></td>
		</tr>
		
		<tr>
			<td colspan="6" class="text-center"><br><b>U N I T</b><hr></td>
		</tr>
		
		<tr>
			<td>NAMA UNIT</td><td>:</td>
			<td><input type="text" name="unit_nama" id="unit_nama" size="45" value="<?php echo $obj->fields['UNIT_NAMA']; ?>"></td>
			<td>KODE POS</td><td>:</td>
			<td><input type="text" name="unit_kode_pos" id="unit_kode_pos" size="7" value="<?php echo $obj->fields['UNIT_KODE_POS']; ?>"></td></td>
		</tr>
		
		<tr>
			<td>ALAMAT 1</td><td>:</td>
			<td><input type="text" name="unit_alamat_1" id="unit_alamat_1" size="45" value="<?php echo $obj->fields['UNIT_ALAMAT_1']; ?>"></td>
			<td>TELEPON</td><td>:</td>
			<td><input type="text" name="unit_telp" id="unit_telp" size="45" value="<?php echo $obj->fields['UNIT_TELP']; ?>"></td>
		</tr>
		
		<tr>
			<td>ALAMAT 2</td><td>:</td>
			<td><input type="text" name="unit_alamat_2" id="unit_alamat_2" size="45" value="<?php echo $obj->fields['UNIT_ALAMAT_2']; ?>"></td>
			<td>FAX</td><td>:</td>
			<td><input type="text" name="unit_fax" id="unit_fax" size="45" value="<?php echo $obj->fields['UNIT_FAX']; ?>"></td>
		</tr>
		
		<tr>
			<td>KOTA</td><td>:</td>
			<td><input type="text" name="unit_kota" id="unit_kota" size="45" value="<?php echo $obj->fields['UNIT_KOTA']; ?>"></td>
			<td>EMAIL</td><td>:</td>
			<td><input type="text" name="unit_email" id="unit_email" size="45" value="<?php echo $obj->fields['UNIT_EMAIL']; ?>"></td>
		</tr>
		</table>
	</div>

	<div class="tab-content" id="tab-organisasi">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td width="120">NAMA PIMPINAN</td><td width="1">:</td>
			<td><input type="text" name="nama_pimpinan" id="nama_pimpinan" size="45" value="<?php echo $obj->fields['NAMA_PIMPINAN']; ?>"></td>
			<td width="120">JBT PIMPINAN</td><td width="1">:</td>
			<td><input type="text" name="jbt_pimpinan" id="jbt_pimpinan" size="45" value="<?php echo $obj->fields['JBT_PIMPINAN']; ?>"></td></td>
		</tr>
		
		<tr>
			<td>NAMA PAJAK</td><td>:</td>
			<td><input type="text" name="nama_pajak" id="nama_pajak" size="45" value="<?php echo $obj->fields['NAMA_PAJAK']; ?>"></td>
			<td>JBT PAJAK</td><td>:</td>
			<td><input type="text" name="jbt_pajak" id="jbt_pajak" size="45" value="<?php echo $obj->fields['JBT_PAJAK']; ?>"></td>
		</tr>
		
		<tr>
			<td>NAMA ADMINISTRASI</td><td>:</td>
			<td><input type="text" name="nama_administrasi" id="nama_administrasi" size="45" value="<?php echo $obj->fields['NAMA_ADMINISTRASI']; ?>"></td>
			<td>JBT ADMINISTRASI</td><td>:</td>
			<td><input type="text" name="jbt_administrasi" id="jbt_administrasi" size="45" value="<?php echo $obj->fields['JBT_ADMINISTRASI']; ?>"></td>
		</tr>
		</table>
	</div>
	
	<div class="tab-content" id="tab-global">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td width="120">NPWP</td><td width="1">:</td>
			<td width="350">
				<input type="text" name="reg_npwp" id="reg_npwp" size="28" value="<?php echo $obj->fields['REG_NPWP']; ?>">&nbsp;&nbsp;
				PPN
				<input type="text" name="persen_ppn" id="persen_ppn" size="10" value="<?php echo $obj->fields['PERSEN_PPN']; ?>"> %
			</td>
			<td width="170">COUNTER NO. PELANGGAN</td><td width="1">:</td>
			<td><input type="text" name="cou_np" id="cou_np" size="15" value="<?php echo $obj->fields['COU_NP']; ?>"></td></td>
		</tr>
		<tr>
			<td>REG. FAKTUR PAJAK</td><td>:</td>
			<td><input type="text" name="reg_fp" id="reg_fp" size="45" value="<?php echo $obj->fields['REG_FP']; ?>"></td>
			<td>COUNTER NO. FAKTUR PAJAK</td><td>:</td>
			<td><input type="text" name="cou_fp" id="cou_fp" size="15" value="<?php echo $obj->fields['COU_FP']; ?>"></td>
		</tr>
		</table>
	</div>

	<div class="tab-content" id="tab-kavling-kosong">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_kv" id="administrasi_kv" size="7" value="<?php echo $obj->fields['ADMINISTRASI_KV']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>		

		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_kv" id="reg_ivc_kv" size="45" value="<?php echo $obj->fields['REG_IVC_KV']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_kv" id="cou_ivc_kv" size="11" value="<?php echo $obj->fields['COU_IVC_KV']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_kv" id="reg_kwt_kv" size="45" value="<?php echo $obj->fields['REG_KWT_KV']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_kv" id="cou_kwt_kv" size="11" value="<?php echo $obj->fields['COU_KWT_KV']; ?>"></td>
		</tr>
		</table>
	</div>

	<div class="tab-content" id="tab-masa-membangun">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td colspan="6" class="text-center"><br><b>AIR & IPL</b><hr></td>
		</tr>
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_bg" id="administrasi_bg" size="7" value="<?php echo $obj->fields['ADMINISTRASI_BG']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>		

		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_bg" id="reg_ivc_bg" size="45" value="<?php echo $obj->fields['REG_IVC_BG']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_bg" id="cou_ivc_bg" size="11" value="<?php echo $obj->fields['COU_IVC_BG']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_bg" id="reg_kwt_bg" size="45" value="<?php echo $obj->fields['REG_KWT_BG']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_bg" id="cou_kwt_bg" size="11" value="<?php echo $obj->fields['COU_KWT_BG']; ?>"></td>
		</tr>
		
		<tr>
			<td colspan="6" class="text-center"><br><b>DEPOSIT</b><hr></td>
		</tr>
		
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_db" id="administrasi_db" size="7" value="<?php echo $obj->fields['ADMINISTRASI_DB']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>		

		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_db" id="reg_ivc_db" size="45" value="<?php echo $obj->fields['REG_IVC_DB']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_db" id="cou_ivc_db" size="11" value="<?php echo $obj->fields['COU_IVC_DB']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_db" id="reg_kwt_db" size="45" value="<?php echo $obj->fields['REG_KWT_DB']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_db" id="cou_kwt_db" size="11" value="<?php echo $obj->fields['COU_KWT_DB']; ?>"></td>
		</tr>
		</table>
	</div>
	
	<div class="tab-content" id="tab-hunian">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_hn" id="administrasi_hn" size="7" value="<?php echo $obj->fields['ADMINISTRASI_HN']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>
		
		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_hn" id="reg_ivc_hn" size="45" value="<?php echo $obj->fields['REG_IVC_HN']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_hn" id="cou_ivc_hn" size="11" value="<?php echo $obj->fields['COU_IVC_HN']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_hn" id="reg_kwt_hn" size="45" value="<?php echo $obj->fields['REG_KWT_HN']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_hn" id="cou_kwt_hn" size="11" value="<?php echo $obj->fields['COU_KWT_HN']; ?>"></td>
		</tr>
		</table>
	</div>
	
	<div class="tab-content" id="tab-renovasi">
		<table class="t-form">
		<tr><td colspan="6"><br></td></tr>
		<tr>
			<td colspan="6" class="text-center"><br><b>AIR & IPL</b><hr></td>
		</tr>
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_rv" id="administrasi_rv" size="7" value="<?php echo $obj->fields['ADMINISTRASI_RV']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>		

		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_rv" id="reg_ivc_rv" size="45" value="<?php echo $obj->fields['REG_IVC_RV']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_rv" id="cou_ivc_rv" size="11" value="<?php echo $obj->fields['COU_IVC_RV']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_rv" id="reg_kwt_rv" size="45" value="<?php echo $obj->fields['REG_KWT_RV']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_rv" id="cou_kwt_rv" size="11" value="<?php echo $obj->fields['COU_KWT_RV']; ?>"></td>
		</tr>
		
		<tr>
			<td colspan="6" class="text-center"><br><b>DEPOSIT</b><hr></td>
		</tr>
		
		<tr>
			<td width="90">ADMINISTRASI</td>
			<td width="1">:</td>
			<td width="170"><input type="text" name="administrasi_dr" id="administrasi_dr" size="7" value="<?php echo $obj->fields['ADMINISTRASI_DR']; ?>"></td>
			<td width="140"></td>
			<td width="1"></td>
			<td></td>
		</tr>		

		<tr>
			<td>REG. TAGIHAN</td><td>:</td>
			<td><input type="text" name="reg_ivc_dr" id="reg_ivc_dr" size="45" value="<?php echo $obj->fields['REG_IVC_DR']; ?>"></td>
			<td>COUNTER NO. TAGIHAN</td><td>:</td>
			<td><input type="text" name="cou_ivc_dr" id="cou_ivc_dr" size="11" value="<?php echo $obj->fields['COU_IVC_DR']; ?>"></td>
		</tr>
		
		<tr>
			<td>REG. KWITANSI</td><td>:</td>
			<td><input type="text" name="reg_kwt_dr" id="reg_kwt_dr" size="45" value="<?php echo $obj->fields['REG_KWT_DR']; ?>"></td>
			<td>COUNTER NO. KWITANSI</td><td>:</td>
			<td><input type="text" name="cou_kwt_dr" id="cou_kwt_dr" size="11" value="<?php echo $obj->fields['COU_KWT_DR']; ?>"></td>
		</tr>
		</table>
	</div>
	
	<div>
		<table class="t-form">
		<tr><td><br></td></tr>
		<tr>
			<td>
				<input type="submit" id="save" value=" Simpan (Alt+S) ">
				<input type="reset" id="reset" value=" Reset (Alt+R) ">
			</td>
		</tr>
		</table>
	</div>
</div>

</form>

<?php close($conn); ?>
<script type="text/javascript">
	function cetak()
	{
	var status;
		try{
			status = document.querySelector('input[name=status_bayar]:checked').value; 
			if(status=='10'){
				status = '';
			}
		}
		catch(err){
			status = '';
		}

	var tgl = jQuery('#periode').val().toString(),
		tgl2= jQuery('#batas_periode').val().toString();
	var res = tgl.split('-'), 
		res2=tgl2.split('-');
	var url = base_laporan + 'pkl/laporan_detail.php?bln='+res[0]+'&thn='+res[1]+'&bln2='+res2[0]+'&thn2='+res2[1]+'&sektor='+jQuery('#sektor').val()+'&status_bayar='+status
				+'&no_va='+jQuery('#no_va').val()+'&nama='+jQuery('#nama').val()+'&kode_blok='+jQuery('#kode_blok').val()+'&jenis_sewa='+jQuery('#jenis_sewa').val();
	window.open(url,'_blank');
		return false;
	}

</script>
<div class="title-page">CETAK LAPORAN PEDAGANG KAKI LIMA</div>
<table class="t-control wauto">
<tr>
	<td>PERIODE</td>
	<td><input type="text" name = 'periode' id= 'periode' class="mm-yyyy" /></td>
</tr>
<tr>
	<td>BATAS PERIODE</td>
	<td><input type="text" name = 'batas_periode' id= 'batas_periode' class="mm-yyyy" /></td>
</tr>
<tr>
	<td>SEKTOR</td>
	<td><input type="text" name="sektor" id="sektor"></td>
</tr>
<tr>
	<td>TIPE LAPORAN</td>
	<td><input type="radio" name="status_bayar" value="10" id="status_bayar"> Rencana<input type="radio" name="status_bayar" value="2" id="status_bayar"> Realisasi <input type="radio" name="status_bayar" value="0" id="status_bayar"> Piutang</td>
</tr>
<tr>
	<td>NO VA</td>
	<td><input type="text" name="no_va" id="no_va"></td>
</tr>
<tr>
	<td>NAMA</td>
	<td><input type="text" name="nama" id="nama"></td>
</tr>
<tr>
	<td>KODE BLOK</td>
	<td><input type="text" name=" kode_blok" id="kode_blok"></td>
</tr>
<tr>
	<td>JENIS USAHA</td>
	<td>
		<select name="jenis_sewa" id="jenis_sewa">
			<option value="">Semua</option>
			<?php
				$data = $conn->Execute("SELECT * FROM KWT_TIPE_PKL");
				while(!$data->EOF){
					?>
					<option value="<?php echo $data->fields['KODE_TIPE'];?>"><?php echo $data->fields['NAMA_TIPE'];?></option>
					<?php
					$data->movenext();
				}
			?>
		</select>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td><button id='cetak' onclick="cetak()">Cetak</button></td>
</tr>
</table>


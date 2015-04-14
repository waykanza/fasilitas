<?php
$i = 1;

$sum_piutang_ai			= 0;
$sum_piutang_dp			= 0;
$sum_piutang_ll			= 0;
$sum_jumlah_piutang		= 0;
$sum_jumlah_bayar_bln	= 0;
$sum_jumlah_bayar		= 0;
$sum_selisih			= 0;

$in_no_pelanggan = implode("', '", $in_no_pelanggan);

$query = "
SELECT 
	(SELECT TOP 1 NO_PELANGGAN FROM KWT_PEMBAYARAN_AI WHERE NO_PELANGGAN = p.NO_PELANGGAN) AS MB,
	p.NO_PELANGGAN AS MP,
	p.NAMA_PELANGGAN,
	p.KODE_BLOK,
	(
		SELECT
			CAST
			(
				SUM
				(
					CASE WHEN $where_trx_air_ipl
					THEN
						(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_IPL - DISKON_AIR)
					ELSE 0 END
				) 
			AS VARCHAR(50))
			+ '|' +
			CAST
			(
				SUM
				(
					CASE WHEN $where_trx_deposit
					THEN
						(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_IPL - DISKON_AIR)
					ELSE 0 END
				) 
			AS VARCHAR(50))
			+ '|' +
			CAST
			(
				SUM
				(
					CASE WHEN $where_trx_lain_lain
					THEN
						(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_IPL - DISKON_AIR)
					ELSE 0 END
				) 
			AS VARCHAR(50))
		FROM
			KWT_PEMBAYARAN_AI
		WHERE
			STATUS_BAYAR = 0 AND
			NO_PELANGGAN = p.NO_PELANGGAN
	) AS JUMLAH_PIUTANG,
	(
		SELECT 
			SUM(JUMLAH_BAYAR) AS TOTAL 
		FROM 
			KWT_PEMBAYARAN_AI 
		WHERE 
			STATUS_BAYAR = 1 AND 
			BAYAR_VIA = '$bayar_via' AND 
			RIGHT(CONVERT(VARCHAR(10), TGL_BAYAR_SYS, 105), 7) = RIGHT(CONVERT(VARCHAR(10), GETDATE(), 105), 7) AND 
			NO_PELANGGAN = p.NO_PELANGGAN
	) AS JUMLAH_BAYAR_BLN
FROM
	KWT_PELANGGAN p
WHERE
	p.NO_PELANGGAN IN ('$in_no_pelanggan') 
";

$obj = $conn->Execute($query);
while( ! $obj->EOF)
{
	$ket = "";
	$selisih = 0;
	$mb = $obj->fields['MB'];
	$mp = $obj->fields['MP'];
	$nama_pelanggan = $obj->fields['NAMA_PELANGGAN'];
	$kode_blok = $obj->fields['KODE_BLOK'];
	$expl = explode('|', $obj->fields['JUMLAH_PIUTANG']);
	$piutang_ai = (isset($expl[0])) ? $expl[0] : 0;
	$piutang_dp = (isset($expl[1])) ? $expl[1] : 0;
	$piutang_ll = (isset($expl[2])) ? $expl[2] : 0;
	$jumlah_piutang = (int) ($piutang_ai + $piutang_dp + $piutang_ll);
	
	$jumlah_bayar_bln = (int) $obj->fields['JUMLAH_BAYAR_BLN'];
	
	$no_pelanggan = $mp;
	$jumlah_bayar = $data_imp["$mp"]['jb'];
	$tgl_bayar_bank = $data_imp["$mp"]['tbb'];
	
	$e = FALSE;
	$br = '';
	if ($mb == '' || $mp == '')
	{
		if ($mb == '') { $e = TRUE; $ket .= '<font color="red">Tidak terdaftar di rencana pembayaran</font>'; $br = ',<br>'; }
		if ($mp == '') { $e = TRUE; $ket .= $br . '<font color="red">Tidak terdaftar di master pelanggan.</font>'; }
	}
	else
	{
		if ($jumlah_bayar < 1)
		{ 
			$e = TRUE; $ket = '<font color="red">Jumlah bayar tidak boleh nol.</font>';
		}
		elseif ($jumlah_piutang < 1) 
		{
			if ($jumlah_bayar_bln == $jumlah_bayar)
			{
				$e = TRUE; $ket = '<font color="green">Tagihan sudah dibayar.</font>';
			}
			else
			{
				$e = TRUE; $ket = 'Pelanggan tidak memiliki piutang.';
			}
		}
		else
		{
			if ($jumlah_piutang <> $jumlah_bayar)
			{
				$e = TRUE; $ket = '<font color="blue">Berbeda dengan jumlah piutang.</font>';
				$selisih = $jumlah_bayar - $jumlah_piutang;
			}
		}
	}
	
	$id = $no_pelanggan;
	?>
		<tr id="<?php echo $id; ?>">
			<td width="30" class="text-center"><?php echo $i; ?></td>
			<td width="30" class="text-center">
				<?php
				if ($e === FALSE)
				{
					?>
					<input type="checkbox" name="cb_data[<?php echo $i; ?>]" value="<?php echo $id; ?>" class="cb_data">
					<input type="hidden" name="vb_tgl[<?php echo $i; ?>]" value="<?php echo $tgl_bayar_bank; ?>">
					<?php
				}
				?>
			</td>
			<td><?php echo $id; ?></td>
			<td><?php echo $nama_pelanggan; ?></td>
			<td><?php echo $kode_blok; ?></td>
			<td class="text-center"><?php echo $tgl_bayar_bank; ?></td>
			<td class="text-right"><?php echo to_money($piutang_ai); ?></td>
			<td class="text-right"><?php echo to_money($piutang_dp); ?></td>
			<td class="text-right"><?php echo to_money($piutang_ll); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_piutang); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_bayar_bln); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_bayar); ?></td>
			<td class="text-right"><?php echo to_money($selisih); ?></td>
			<td><?php echo $ket; ?></td>
		</tr>
	<?php
	
	$sum_piutang_ai		+= $piutang_ai;
	$sum_piutang_dp		+= $piutang_dp;
	$sum_piutang_ll		+= $piutang_ll;
	$sum_jumlah_piutang	+= $jumlah_piutang;
	$sum_jumlah_bayar_bln += $jumlah_bayar_bln;
	$sum_jumlah_bayar	+= $jumlah_bayar;
	$sum_selisih		+= $selisih;
	
	$i++;
	$obj->movenext();
}
?>

<tfoot>
<tr>
	<td colspan="6">TOTAL .........</td>
	<td><?php echo to_money($sum_piutang_ai); ?></td>
	<td><?php echo to_money($sum_piutang_dp); ?></td>
	<td><?php echo to_money($sum_piutang_ll); ?></td>
	<td><?php echo to_money($sum_jumlah_piutang); ?></td>
	<td><?php echo to_money($sum_jumlah_bayar_bln); ?></td>
	<td><?php echo to_money($sum_jumlah_bayar); ?></td>
	<td><?php echo to_money($sum_selisih); ?></td>
	<td></td>
</tr>
</tfoot>
</table>

<script type="text/javascript">
jQuery(function($) {
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").size();
		if (checked < 1)
		{
			alert('Pilih data yang akan disimpan.');
			return false;
		}
		
		var url		= base_bank + 'import/proses_data.php',
			data	= $('#form-data').serializeArray();
		
		$.post(url, data, function(result) {
		
			if (result.error == false)
			{
				var list_id_sukses = result.list_id_sukses.join(', #');
				$('#' + list_id_sukses).remove();
			}
			
			alert(result.msg);
			
		}, 'json');
			
		return false;
	});
});
</script>
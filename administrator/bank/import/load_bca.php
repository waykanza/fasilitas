<table class="t-control">
<tr>
	<td>
		<input type="button" id="save" value=" Simpan Data Pembayaran ">
	</td>
</tr>
</table>

<form name="form-data" id="form-data" method="post">
<table class="t-data" style="min-width:100%;">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2"><input type="checkbox" id="cb_all"></th>
	<th rowspan="2">NO. PELANGGAN</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th rowspan="2">KODE BLOK</th>
	<th rowspan="2">TANGGAL<br>BAYAR</th>
	<th colspan="3">PIUTANG</th>
	<th rowspan="2">JUMLAH<br>BAYAR<br>(<?php echo date('M-Y'); ?>)</th>
	<th rowspan="2">JUMLAH<br>BAYAR<br>(IMPORT)</th>
	<th rowspan="2">SELISIH</th>
	<th rowspan="2">KETERANGAN</th>
</tr>
<tr>
	<th class="">AIR & IPL</th>
	<th class="">DEPOSIT</th>
	<th class="">TOTAL</th>
</tr>

<?php
if ( ! file_exists($path . "upload.$ext"))
{
	echo '<tr><td colspan="13">Error. File import tidak ditemukan.</td></tr>';
	exit;
}

$l = 0;
$list = array();
$file = fopen($path . "upload.$ext", 'r');
while ( ! feof($file))
{
	$l++;
	
	$line = fgets($file);
	if (strlen($line) < 71 || $line == '')
	{
		echo '<tr><td colspan="9">Error. Format baris : '.$l.'</td></tr>';
		continue;
	}
	$list[$l] = array(
		'np' => (string) clean(substr($line, 8, 19)),
		'jb' => to_decimal(substr($line, 33, 21)),
		'db' => clean(substr($line, 57, 8)),
		'tb' => clean(substr($line, 67, 8))
	);
}
fclose($file);

$i = 1;

$sum_piutang_ai = 0;
$sum_piutang_dp = 0;
$sum_jumlah_piutang = 0;
$sum_jumlah_bayar_bln = 0;
$sum_jumlah_bayar = 0;
$sum_selisih = 0;

foreach ($list as $x)
{
	$no_pelanggan = (string) $x['np'];
	$jumlah_bayar = (int) $x['jb'];
	
	$spl = explode('/', $x['db']);
	if ((count($spl) != 3) || ( ! checkdate($spl[1], $spl[0], $spl[2])))
	{
		echo '<tr><td colspan="2"></td><td>'.$no_pelanggan.'</td><td colspan="7">Error. Format tanggal tidak valid.</td></tr>';
		continue;
	}
	
	$tgl_bayar = $spl[0].'-'.$spl[1].'-'.$spl[2].' '.$x['tb'];
	$query = "
	SELECT TOP 1
		b.NO_PELANGGAN AS MB,
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
							(ISNULL(JUMLAH_AIR,0) + ISNULL(ABONEMEN,0) + ISNULL(JUMLAH_IPL,0) + ISNULL(DENDA,0)) -
							(ISNULL(DISKON_RUPIAH_IPL,0) + ISNULL(DISKON_RUPIAH_AIR,0))
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
							(ISNULL(JUMLAH_AIR,0) + ISNULL(ABONEMEN,0) + ISNULL(JUMLAH_IPL,0) + ISNULL(DENDA,0)) -
							(ISNULL(DISKON_RUPIAH_IPL,0) + ISNULL(DISKON_RUPIAH_AIR,0))
						ELSE 0 END
					) 
				AS VARCHAR(50))
			FROM
				KWT_PEMBAYARAN_AI
			WHERE
				STATUS_BAYAR IS NULL AND
				NO_PELANGGAN = p.NO_PELANGGAN
		) AS JUMLAH_PIUTANG,
		(
			SELECT 
				SUM(ISNULL(JUMLAH_BAYAR,0)) AS TOTAL 
			FROM 
				KWT_PEMBAYARAN_AI 
			WHERE 
				STATUS_BAYAR = '2' AND 
				BAYAR_MELALUI = '$bayar_melalui' AND 
				RIGHT(CONVERT(VARCHAR(10), TGL_TERIMA_BANK, 105), 7) = RIGHT(CONVERT(VARCHAR(10), GETDATE(), 105), 7) AND 
				NO_PELANGGAN = p.NO_PELANGGAN
		) AS JUMLAH_BAYAR_BLN
	FROM
		KWT_PELANGGAN p
		LEFT JOIN KWT_PEMBAYARAN_AI b ON p.NO_PELANGGAN = b.NO_PELANGGAN
	WHERE
		p.NO_PELANGGAN = '$no_pelanggan'
	";
	
	$obj = $conn->Execute($query);
	$keterangan = "";
	$selisih = 0;
	$mb = $obj->fields['MB'];
	$mp = $obj->fields['MP'];
	$nama_pelanggan = $obj->fields['NAMA_PELANGGAN'];
	$kode_blok = $obj->fields['KODE_BLOK'];
	$expl = explode('|', $obj->fields['JUMLAH_PIUTANG']);
	$piutang_ai = (isset($expl[0])) ? $expl[0] : 0;
	$piutang_dp = (isset($expl[1])) ? $expl[1] : 0;
	$jumlah_piutang = (int) ($piutang_ai + $piutang_dp);
	
	$jumlah_bayar_bln = (int) $obj->fields['JUMLAH_BAYAR_BLN'];
	
	$e = FALSE;
	$br = '';
	if ($mb == '' || $mp == '')
	{
		if ($mb == '') { $e = TRUE; $keterangan .= '<font color="red">Tidak terdaftar di rencana pembayaran</font>'; $br = ',<br>'; }
		if ($mp == '') { $e = TRUE; $keterangan .= $br . '<font color="red">Tidak terdaftar di master pelanggan.</font>'; }
	}
	else
	{
		if ($jumlah_bayar < 1)
		{ 
			$e = TRUE; $keterangan = '<font color="red">Jumlah bayar tidak boleh nol.</font>';
		}
		elseif ($jumlah_piutang < 1) 
		{
			if ($jumlah_bayar_bln == $jumlah_bayar)
			{
				$e = TRUE; $keterangan = '<font color="green">Tagihan sudah dibayar.</font>';
			}
			else
			{
				$e = TRUE; $keterangan = 'Pelanggan tidak memiliki piutang.';
			}
		}
		else
		{
			if ($jumlah_piutang <> $jumlah_bayar)
			{
				$e = TRUE; $keterangan = '<font color="blue">Berbeda dengan jumlah piutang.</font>';
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
					<input type="hidden" name="vb_tgl[<?php echo $i; ?>]" value="<?php echo $tgl_bayar; ?>">
					<?php
				}
				?>
			</td>
			<td><?php echo $id; ?></td>
			<td><?php echo $nama_pelanggan; ?></td>
			<td><?php echo $kode_blok; ?></td>
			<td class="text-center"><?php echo $tgl_bayar; ?></td>
			<td class="text-right"><?php echo to_money($piutang_ai); ?></td>
			<td class="text-right"><?php echo to_money($piutang_dp); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_piutang); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_bayar_bln); ?></td>
			<td class="text-right"><?php echo to_money($jumlah_bayar); ?></td>
			<td class="text-right"><?php echo to_money($selisih); ?></td>
			<td><?php echo $keterangan; ?></td>
		</tr>
	<?php
	
	$sum_piutang_ai += $piutang_ai;
	$sum_piutang_dp += $piutang_dp;
	$sum_jumlah_piutang += $jumlah_piutang;
	$sum_jumlah_bayar_bln += $jumlah_bayar_bln;
	$sum_jumlah_bayar += $jumlah_bayar;
	$sum_selisih += $selisih;

	$i++;
}
?>

	<tfoot>
	<tr>
		<td colspan="6">TOTAL .........</td>
		<td><?php echo to_money($sum_piutang_ai); ?></td>
		<td><?php echo to_money($sum_piutang_dp); ?></td>
		<td><?php echo to_money($sum_jumlah_piutang); ?></td>
		<td><?php echo to_money($sum_jumlah_bayar_bln); ?></td>
		<td><?php echo to_money($sum_jumlah_bayar); ?></td>
		<td><?php echo to_money($sum_selisih); ?></td>
		<td></td>
	</tr>
	</tfoot>

<input type="hidden" name="bayar_melalui" id="bayar_melalui" value="<?php echo $bayar_melalui; ?>">
</form>

<script type="text/javascript">
jQuery(function($) {
	t_strip('.t-data');

	$('#save').on('click', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").size();
		if (checked < 1)
		{
			alert('Pilih data yang akan disimpan.');
			return false;
		}
		
		var url		= base_bank + 'import/import_proses.php',
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
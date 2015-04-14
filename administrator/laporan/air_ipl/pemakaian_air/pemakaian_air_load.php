<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';
$query_bervariasi = '';

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$asumsi			= (isset($_REQUEST['asumsi'])) ? clean($_REQUEST['asumsi']) : '';
$tarif_baru		= (isset($_REQUEST['tarif_baru'])) ? to_decimal($_REQUEST['tarif_baru']) : '';
$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($kode_cluster != '')
{
	$query_search .= " AND b.KODE_CLUSTER = '$kode_cluster' ";
}
if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
}

$persen_asumsi1 = 1;
$persen_asumsi2 = 1;
$persen_asumsi3 = 1;
$persen_asumsi4 = 1;
if ($asumsi == 'naik_tetap')
{
	$tarif_baru = $tarif_baru /100;
	$persen_asumsi1 += $tarif_baru;
	$persen_asumsi2 += $tarif_baru;
	$persen_asumsi3 += $tarif_baru;
	$persen_asumsi4 += $tarif_baru;
}
elseif ($asumsi == 'bervariasi')
{
	$query_bervariasi = "
		t.PERSEN_ASUMSI_1,
		t.PERSEN_ASUMSI_2,
		t.PERSEN_ASUMSI_3,
		t.PERSEN_ASUMSI_4,
	";
}
?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<input type="button" id="excel" value=" Excel (Alt+X) ">
		<input type="button" id="print" value=" Print (Alt+P) ">
	</td>
</tr>
</table>

<table class="t-data t-nowrap">
<tr>
	<th rowspan="2">KELOMPOK PELANGGAN</th>
	<th colspan="3">JUMLAH</th>
	<th colspan="3">PEMAKAIAN BLOK 1</th>
	<th colspan="3">PEMAKAIAN BLOK 2</th>
	<th colspan="3">PEMAKAIAN BLOK 3</th>
	<th colspan="3">PEMAKAIAN BLOK 4</th>
</tr>
<tr>
	<th>PEL.</th><th>M&#179</th><th>NILAI Rp.</th>
	<th>PEL.</th><th>M&#179</th><th>NILAI Rp.</th>
	<th>PEL.</th><th>M&#179</th><th>NILAI Rp.</th>
	<th>PEL.</th><th>M&#179</th><th>NILAI Rp.</th>
	<th>PEL.</th><th>M&#179</th><th>NILAI Rp.</th>
</tr>

<?php
	error_reporting(E_ALL ^ E_NOTICE);
	$query = "
	SELECT 
		b.KEY_AIR,
		ISNULL(t.NAMA_TIPE, '-') AS NAMA_TIPE,
		ISNULL(t.KODE_TIPE, '-') AS KODE_TIPE,
		$query_bervariasi
		b.BLOK1,
		b.BLOK2,
		b.BLOK3,
		b.BLOK4,
		b.TARIF1,
		b.TARIF2,
		b.TARIF3,
		b.TARIF4
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_TARIF_AIR d ON b.KEY_AIR = d.KEY_AIR
		LEFT JOIN KWT_TIPE_AIR t ON d.KODE_TIPE = t.KODE_TIPE
	WHERE
		$where_trx_air_ipl AND 
		b.AKTIF_AIR = '1' AND
		b.PERIODE = '$periode'  
		$query_search
	ORDER BY t.KODE_TIPE ASC
	";
	$obj = $conn->Execute($query);
	
	$kel_pel = array();
	$pelanggan = array(); $meter = array(); $nilai = array();
	$sum_pelanggan = array(); $sum_meter = array(); $sum_nilai = array();
	
	while( ! $obj->EOF)
	{
		if ($asumsi == 'bervariasi')
		{
			$persen_asumsi1 = 1 + ($obj->fields['PERSEN_ASUMSI_1'] / 100);
			$persen_asumsi2 = 1 + ($obj->fields['PERSEN_ASUMSI_2'] / 100);
			$persen_asumsi3 = 1 + ($obj->fields['PERSEN_ASUMSI_3'] / 100);
			$persen_asumsi4 = 1 + ($obj->fields['PERSEN_ASUMSI_4'] / 100);
		}
		
		$pakai = ($obj->fields['BLOK1'] + $obj->fields['BLOK2'] + $obj->fields['BLOK3'] + $obj->fields['BLOK4']);
		$harga = 
		($obj->fields['BLOK1'] * ($obj->fields['TARIF1'] * $persen_asumsi1)) +
		($obj->fields['BLOK2'] * ($obj->fields['TARIF2'] * $persen_asumsi2)) + 
		($obj->fields['BLOK3'] * ($obj->fields['TARIF3'] * $persen_asumsi3)) + 
		($obj->fields['BLOK4'] * ($obj->fields['TARIF4'] * $persen_asumsi4));
		
		$n = '';
		if ($pakai <= 10) {$n = 1;}
		elseif ($pakai > 10 AND $pakai <= 20) {$n = 2;}
		elseif ($pakai > 20 AND $pakai <= 40) {$n = 3;}
		elseif ($pakai > 40) {$n = 4;}
		
		# DIKELOMPOKAN BERDASARKAN
		# $k = $obj->fields['KEY_AIR'];
		$k = $obj->fields['KODE_TIPE'];
		
		if ($n != '')
		{
			$kel_pel[$k] = $obj->fields['NAMA_TIPE']; # CUMA UNTUK NAMA TIPE
			$pelanggan[$n][$k]++;
			$meter[$n][$k] += $pakai;
			$nilai[$n][$k] += $harga;
			
			$sum_pelanggan[$n]++;
			$sum_meter[$n] += $pakai;
			$sum_nilai[$n] += $harga;
			
			# Untuk Jumlah
			$pelanggan[0][$k]++;
			$meter[0][$k] += $pakai;
			$nilai[0][$k] += $harga;
			
			$sum_pelanggan[0]++;
			$sum_meter[0] += $pakai;
			$sum_nilai[0] += $harga;
		}
		
		$obj->movenext();
	}

if ( ! empty($kel_pel))
{
	foreach ($kel_pel AS $k => $x)
	{
		?>
		<tr> 
			<td><?php echo $k . ' ( '.$kel_pel[$k] . ' )'; ?></td>
			
			<td class="text-right"><?php echo to_money($pelanggan[0][$k]); ?></td>
			<td class="text-right"><?php echo to_money($meter[0][$k]); ?></td>
			<td class="text-right"><?php echo to_money($nilai[0][$k]); ?></td>
			
			<td class="text-right"><?php echo to_money($pelanggan[1][$k]); ?></td>
			<td class="text-right"><?php echo to_money($meter[1][$k]); ?></td>
			<td class="text-right"><?php echo to_money($nilai[1][$k]); ?></td>
			
			<td class="text-right"><?php echo to_money($pelanggan[2][$k]); ?></td>
			<td class="text-right"><?php echo to_money($meter[2][$k]); ?></td>
			<td class="text-right"><?php echo to_money($nilai[2][$k]); ?></td>
			
			<td class="text-right"><?php echo to_money($pelanggan[3][$k]); ?></td>
			<td class="text-right"><?php echo to_money($meter[3][$k]); ?></td>
			<td class="text-right"><?php echo to_money($nilai[3][$k]); ?></td>
			
			<td class="text-right"><?php echo to_money($pelanggan[4][$k]); ?></td>
			<td class="text-right"><?php echo to_money($meter[4][$k]); ?></td>
			<td class="text-right"><?php echo to_money($nilai[4][$k]); ?></td>
		</tr>
		<?php
	}
}
?>
	<tfoot>
	<tr>
		<td>GRAND TOTAL .........</td>
		
		<td class="text-right"><?php echo to_money($sum_pelanggan[0]); ?></td>
		<td class="text-right"><?php echo to_money($sum_meter[0]); ?></td>
		<td class="text-right"><?php echo to_money($sum_nilai[0]); ?></td>
		
		<td class="text-right"><?php echo to_money($sum_pelanggan[1]); ?></td>
		<td class="text-right"><?php echo to_money($sum_meter[1]); ?></td>
		<td class="text-right"><?php echo to_money($sum_nilai[1]); ?></td>
		
		<td class="text-right"><?php echo to_money($sum_pelanggan[2]); ?></td>
		<td class="text-right"><?php echo to_money($sum_meter[2]); ?></td>
		<td class="text-right"><?php echo to_money($sum_nilai[2]); ?></td>
		
		<td class="text-right"><?php echo to_money($sum_pelanggan[3]); ?></td>
		<td class="text-right"><?php echo to_money($sum_meter[3]); ?></td>
		<td class="text-right"><?php echo to_money($sum_nilai[3]); ?></td>
		
		<td class="text-right"><?php echo to_money($sum_pelanggan[4]); ?></td>
		<td class="text-right"><?php echo to_money($sum_meter[4]); ?></td>
		<td class="text-right"><?php echo to_money($sum_nilai[4]); ?></td>
	</tr>
	</tfoot>
</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
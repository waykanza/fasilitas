<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$jenis_bayar	= (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
$bayar_melalui	= (isset($_REQUEST['bayar_melalui'])) ? clean($_REQUEST['bayar_melalui']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';

$field_tgl_trx = " b.TGL_BAYAR ";

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($trx != '')
{
	$query_search .= "AND TRX = '$trx' ";
}

if ($kasir != '')
{
	$query_search .= " AND b.KASIR = '$kasir' ";
}
if ($jenis_bayar != '')
{
	$query_search .= " AND b.JENIS_BAYAR = '$jenis_bayar' ";
	if ($jenis_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($bayar_melalui != '')
		{
			$query_search .= " AND b.BAYAR_MELALUI = '$bayar_melalui' ";
		}
	}
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

<?php
$max_tgl = (int) date('t', strtotime(to_periode($tgl_trx) . '01'));

$x = array();
for ($x = 1; $x <= $max_tgl; $x++)
{
	$xjumlah_ipl[$x] = 0;
	$xdenda[$x] = 0;
	$xadministrasi[$x] = 0;
	$xdiskon_rupiah_ipl[$x] = 0;
	$xnilai_ppn[$x] = 0;
	$xexc_ppn[$x] = 0;
	$xjumlah_bayar[$x] = 0;
}

$query = "
SELECT 
	CONVERT(VARCHAR(2), $field_tgl_trx, 105) AS TANGGAL,
	
	SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(b.DENDA) AS DENDA,
	SUM(b.ADMINISTRASI) AS ADMINISTRASI,
	SUM(b.DISKON_RUPIAH_IPL) AS DISKON_RUPIAH_IPL,
	
	SUM(CASE WHEN b.NILAI_PPN = 0 
			THEN ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100))
			ELSE b.NILAI_PPN
		END) AS NILAI_PPN,
		
	SUM(CASE WHEN b.NILAI_PPN = 0 
			THEN (b.JUMLAH_BAYAR - ((b.JUMLAH_BAYAR - b.ADMINISTRASI - b.DENDA) * (b.PERSEN_PPN / 100)))
			ELSE (b.JUMLAH_BAYAR - b.NILAI_PPN)
		END) AS EXC_PPN,
		
	SUM(b.JUMLAH_BAYAR) AS JUMLAH_BAYAR
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_deposit AND 
	b.STATUS_BAYAR = '2' AND
	RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx'
	$query_search
GROUP BY CONVERT(VARCHAR(2), $field_tgl_trx, 105)
";

$obj = $conn->Execute($query);
$sum_jumlah_ipl = 0;
$sum_denda = 0;
$sum_administrasi = 0;
$sum_diskon_rupiah_ipl = 0;
$sum_nilai_ppn = 0;
$sum_exc_ppn = 0;
$sum_jumlah_bayar = 0;

while( ! $obj->EOF)
{
	$i = (int) $obj->fields['TANGGAL'];
	
	$xjumlah_ipl[$i]		= $obj->fields['JUMLAH_IPL'];
	$xdenda[$i]				= $obj->fields['DENDA'];
	$xadministrasi[$i]		= $obj->fields['ADMINISTRASI'];
	$xdiskon_rupiah_ipl[$i]	= $obj->fields['DISKON_RUPIAH_IPL'];
	$xnilai_ppn[$i]			= $obj->fields['NILAI_PPN'];
	$xexc_ppn[$i]			= $obj->fields['EXC_PPN'];
	$xjumlah_bayar[$i]		= $obj->fields['JUMLAH_BAYAR'];
	
	$sum_jumlah_ipl			+= $xjumlah_ipl[$i];
	$sum_denda				+= $xdenda[$i];
	$sum_administrasi		+= $xadministrasi[$i];
	$sum_diskon_rupiah_ipl	+= $xdiskon_rupiah_ipl[$i];
	$sum_nilai_ppn			+= $xnilai_ppn[$i];
	$sum_exc_ppn			+= $xexc_ppn[$i];
	$sum_jumlah_bayar		+= $xjumlah_bayar[$i];
	
	$obj->movenext();
}
?>

<table class="t-data">
<tr>
	<th width="150">TANGGAL</th>
	<th>DEPOSIT</th>
	<th>DENDA</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>PPN</th>
	<th>TOTAL<br>EXC. PPN</th>
	<th>TOTAL<br>BAYAR</th>
</tr>
<?php
$fm_periode = fm_periode(to_periode($tgl_trx), '%b %Y');

foreach ($xjumlah_bayar AS $k => $v)
{
	?>
	<tr> 
		<td class="text-center"><?php echo $k.' '.$fm_periode; ?></td>
		<td class="text-right"><?php echo to_money($xjumlah_ipl[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdenda[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xadministrasi[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdiskon_rupiah_ipl[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xnilai_ppn[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xexc_ppn[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xjumlah_bayar[$k]); ?></td>
	</tr>
	<?php
}
?>
<tfoot>
<tr>
	<td>GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
	<td><?php echo to_money($sum_administrasi); ?></td>
	<td><?php echo to_money($sum_diskon_rupiah_ipl); ?></td>
	<td><?php echo to_money($sum_nilai_ppn); ?></td>
	<td><?php echo to_money($sum_exc_ppn); ?></td>
	<td><?php echo to_money($sum_jumlah_bayar); ?></td>
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
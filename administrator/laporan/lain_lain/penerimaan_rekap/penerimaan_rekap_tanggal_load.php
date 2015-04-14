<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LL4');
$conn = conn();
die_conn($conn);

$query_search = '';

$jenis_tgl_trx	= (isset($_REQUEST['jenis_tgl_trx'])) ? clean($_REQUEST['jenis_tgl_trx']) : '';
$tgl_trx		= (isset($_REQUEST['tgl_trx'])) ? clean($_REQUEST['tgl_trx']) : '';
$cara_bayar		= (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
$bayar_via		= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$user_bayar		= (isset($_REQUEST['user_bayar'])) ? clean($_REQUEST['user_bayar']) : '';

$field_tgl_trx = " b.TGL_BAYAR_BANK ";

if ($kode_sektor != '')
{
	$query_search .= " AND b.KODE_SEKTOR = '$kode_sektor' ";
}
if ($trx != '')
{
	$query_search .= " AND TRX = $trx ";
}

if ($user_bayar != '')
{
	$query_search .= " AND b.USER_BAYAR = '$user_bayar' ";
}
if ($cara_bayar != '')
{
	$query_search .= " AND b.CARA_BAYAR = $cara_bayar ";
	if ($cara_bayar == '4')
	{
		$field_tgl_trx = " b.$jenis_tgl_trx ";
		if ($bayar_via != '')
		{
			$query_search .= " AND b.BAYAR_VIA = '$bayar_via' ";
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
	$xadm[$x] = 0;
	$xdiskon_ipl[$x] = 0;
	$xjumlah_bayar[$x] = 0;
}

$query = "
SELECT 
	CONVERT(VARCHAR(2), $field_tgl_trx, 105) AS TANGGAL,
	
	SUM(b.JUMLAH_IPL) AS JUMLAH_IPL,
	SUM(b.DENDA) AS DENDA,
	SUM(b.ADM) AS ADM,
	SUM(b.DISKON_IPL) AS DISKON_IPL,
	SUM(b.JUMLAH_BAYAR) AS JUMLAH_BAYAR
FROM 
	KWT_PEMBAYARAN_AI b
WHERE
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 1 AND
	RIGHT(CONVERT(VARCHAR(10), $field_tgl_trx, 105), 7) = '$tgl_trx'
	$query_search
GROUP BY CONVERT(VARCHAR(2), $field_tgl_trx, 105)
";

$obj = $conn->Execute($query);
$sum_jumlah_ipl = 0;
$sum_denda = 0;
$sum_adm = 0;
$sum_diskon_ipl = 0;
$sum_jumlah_bayar = 0;

while( ! $obj->EOF)
{
	$i = (int) $obj->fields['TANGGAL'];
	
	$xjumlah_ipl[$i]		= $obj->fields['JUMLAH_IPL'];
	$xdenda[$i]				= $obj->fields['DENDA'];
	$xadm[$i]		= $obj->fields['ADM'];
	$xdiskon_ipl[$i]	= $obj->fields['DISKON_IPL'];
	$xjumlah_bayar[$i]		= $obj->fields['JUMLAH_BAYAR'];
	
	$sum_jumlah_ipl			+= $xjumlah_ipl[$i];
	$sum_denda				+= $xdenda[$i];
	$sum_adm		+= $xadm[$i];
	$sum_diskon_ipl	+= $xdiskon_ipl[$i];
	$sum_jumlah_bayar		+= $xjumlah_bayar[$i];
	
	$obj->movenext();
}
?>

<table class="t-data t-nowrap wm100">
<tr>
	<th width="150">TANGGAL</th>
	<th>BIAYA LAIN-LAIN</th>
	<th>ADM</th>
	<th>DISKON</th>
	<th>DENDA</th>
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
		<td class="text-right"><?php echo to_money($xadm[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdiskon_ipl[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xdenda[$k]); ?></td>
		<td class="text-right"><?php echo to_money($xjumlah_bayar[$k]); ?></td>
	</tr>
	<?php
}
?>
<tfoot>
<tr>
	<td>GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_jumlah_ipl); ?></td>
	<td><?php echo to_money($sum_adm); ?></td>
	<td><?php echo to_money($sum_diskon_ipl); ?></td>
	<td><?php echo to_money($sum_denda); ?></td>
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
<?php
require_once('../../../../config/config.php');
die_login();
die_mod('LA5');
$conn = conn();
die_conn($conn);

$query_search = '';

$id				= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$aktif_air		= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
$aktif_ipl		= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
$status_bintang = (isset($_REQUEST['status_bintang'])) ? clean($_REQUEST['status_bintang']) : '';
$banyak_tangihan	= (isset($_REQUEST['banyak_tangihan'])) ? to_number($_REQUEST['banyak_tangihan']) : '1';

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
	$query_search .= " AND TRX = $trx ";
}
if ($aktif_air != '') {
	$query_search .= " AND b.AKTIF_AIR = $aktif_air ";
}
if ($aktif_ipl != '') {
	$query_search .= " AND b.AKTIF_IPL = $aktif_ipl ";
}
if ($status_bintang != '')
{
	$query_search .= "";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	
	/* -- SHORTCUT -- */
	key('enter', function(e) { e.preventDefault(); $('#apply').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	key('alt+left', function(e) { e.preventDefault(); $('#prev_page').trigger('click'); });
	key('alt+right', function(e) { e.preventDefault(); $('#next_page').trigger('click'); });
	
	$('#close').on('click', function(e) {
		parent.window.focus();
		parent.window.popup.close();
	});
	
});
</script>
</head>
<body class="popup">


<form name="form" id="form" method="post">
<table class="t-data t-nowrap wm100">
<tr>
	<th rowspan="3">NO.</th>
	<th rowspan="3">PERIODE</th>
	<th colspan="2">PELANGGAN</th>
	<th colspan="11">BLOK</th>
	<th rowspan="3">AIR</th>
	<th rowspan="3">ABONEMEN</th>
	<th rowspan="3">IPL</th>
	<th rowspan="3">TOTAL<br>TAGIHAN</th>
	<th rowspan="3">KET</th>
</tr>
<tr>
	<th rowspan="2">NAMA</th>
	<th rowspan="2">BLOK / NO.</th>
	<th rowspan="2">STATUS</th>
	<th colspan="5">AIR</th>
	<th colspan="5">IPL</th>
</tr>
<tr>
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>AKHIR</th>
	<th>LALU</th>
	<th>GANTI</th>
	
	<th>AKTIF</th>
	<th>GOL.</th>
	<th>AWAL</th>
	<th>AKHIR</th>
	<th>JML.</th>
</tr>

<?php

	$query = "
	SELECT 		
		b.ID_PEMBAYARAN, 
		b.NO_INVOICE, 
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		dbo.PTPS(b.PERIODE_AIR) AS PERIODE_AIR,
		dbo.PTPS(b.PERIODE_IPL_AWAL) AS PERIODE_IPL_AWAL,
		dbo.PTPS(b.PERIODE_IPL_AKHIR) AS PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
		
		b.STAND_AKHIR, 
		b.STAND_LALU, 
		b.STAND_ANGKAT, 
		(b.STAND_AKHIR - b.STAND_LALU + b.STAND_ANGKAT) AS PEMAKAIAN,
		b.STAND_MIN_PAKAI, 
		
		p.NAMA_PELANGGAN,
		b.NO_PELANGGAN,
		b.KODE_BLOK,
		b.STATUS_BLOK,
		b.LUAS_KAVLING,
		p.LUAS_BANGUNAN,
		b.AKTIF_AIR,
		b.KEY_AIR,
		b.AKTIF_IPL,
		b.KEY_IPL,
		
		b.JUMLAH_AIR, 
		b.ABONEMEN, 
		b.JUMLAH_IPL, 
		(JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA - DISKON_AIR - DISKON_IPL) AS JUMLAH_TAGIHAN,
		KET_IVC
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
	WHERE
		$where_trx_air_ipl AND 
		b.KODE_BLOK = '$id' AND
		b.STATUS_BAYAR = 0 
		
		$query_search
	ORDER BY b.PERIODE_TAG DESC, b.PERIODE_IPL_AWAL DESC
	";
	$obj = $conn->Execute($query);

	$i = 1;
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['AKTIF_AIR']); ?></td>
			<td><?php echo $obj->fields['KEY_AIR']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_AKHIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_LALU']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_ANGKAT']); ?></td>
			<td class="text-center"><?php echo status_check($obj->fields['AKTIF_IPL']); ?></td>
			<td><?php echo $obj->fields['KEY_IPL']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AWAL']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AKHIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_TAGIHAN']); ?></td>
			<td><?php echo $obj->fields['KET_IVC']; ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}

?>
</table>

<table class="t-popup">
<tr>
	<td class="td-action">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
</tr>
</table>

</form>

<script type="text/javascript">
jQuery(function($) {
	t_strip('.t-data');
});
</script>

</body>
</html>
<?php close($conn); ?>
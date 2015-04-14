<?php
require_once('../../../../../config/config.php');
$conn = conn();

$no_pelanggan = (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
$('#close').on('click', function(e) {
	e.preventDefault();
		parent.window.focus();
		parent.window.popup.close();
	});
	
});
</script>
</head>
<body class="popup">

<form name="form" id="form" method="post">

<table class="t-data">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">PERIODE</th>
	<th rowspan="2">TIPE PKL</th>
	<th rowspan="2">LOKASI</th>
	<th colspan="2">Harga Sewa</th>
	<th colspan="2">Biaya<br>Strategis</th>
	<th colspan="2">Discount</th>
	<th rowspan="2">LUAS<br>(m&sup2;)</th>	
	<th rowspan="2">Durasi<br>(Bulan)</th>
	<th rowspan="2">JUMLAH<br>BAYAR</th>
	<th rowspan="2">TANGGAL<br>BAYAR</th>
</tr>
<tr>
	<th colspan="1">UANG<br>PANGKAL</th>
	<th colspan="1">TARIF</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
</tr>

<?php
	$query = "
	SELECT
		p.*,
		CONVERT(VARCHAR(11),p.TGL_SERAHTERIMA,106) AS TGL_SERAHTERIMA,
		CONVERT(VARCHAR(11),p.TGL_PEMUTUSAN,106) AS TGL_PEMUTUSAN,
		l.NAMA_LOKASI,
		l.DETAIL_LOKASI,
		t.NAMA_TIPE,
		b.NAMA_BANK,
		c.NAMA_PELANGGAN
	FROM 
		KWT_PEMBAYARAN_PKL p
		LEFT JOIN KWT_LOKASI_PKL l ON p.KODE_LOKASI = l.KODE_LOKASI
		LEFT JOIN KWT_TIPE_PKL t ON p.KODE_TIPE = t.KODE_TIPE
		LEFT JOIN KWT_BANK b ON p.KODE_BANK = b.KODE_BANK
		LEFT JOIN KWT_PELANGGAN_PKL c ON p.NO_PELANGGAN = c.NO_PELANGGAN
	WHERE p.NO_PELANGGAN = '$no_pelanggan'
	";
	$obj = $conn->Execute($query);
	$i = 1;
	
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		$satuan	= ($obj->fields['SATUAN'] == 0) ? 'm&sup2;' : 'Bulan';
		?>
		<tr class="onclick" id="<?php echo $id; ?>">
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['TGL_SERAHTERIMA'],' s/d ',$obj->fields['TGL_PEMUTUSAN']; ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td><?php echo $obj->fields['DETAIL_LOKASI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['UANG_PANGKAL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF']); ?><?php echo ' / '; echo $satuan; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PERSEN_NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PERSEN_NILAI_KURANG']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_KURANG']); ?></td>
			<td class="text-center"><?php if ($obj->fields['SATUAN'] == 1){
					echo '-'; 
				}
				else{
					echo $obj->fields['LUAS']; 
				}
			?></td>
			<td class="text-center"><?php echo to_money($obj->fields['DURASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
			<td class="text-center"><?php echo date("d M Y", strtotime($obj->fields['CREATED_DATE'])); ?></td>
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
</body>
</html>
<?php close($conn); ?>
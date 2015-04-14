<?php
require_once('../../../config/config.php');

$conn = conn();
$id = (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#add').on('click', function(e) {
		e.preventDefault();
		showPopup('Simpan', '');
		return false;
	});
	
});

function showPopup(act, idd)
{
	var url =	base_periode + 'periode_renovasi/periode_renovasi_add_edit.php' +
				'?act=' + act +
				'&idd=' + idd +
				'&kode_blok=<?php echo $id; ?>';
		title	= (act == 'Simpan') ? 'Tambah' : act;
	
	setPopup(title + ' Periode', url, 550, 400);
	
	return false;
}

function deleteData(idd)
{
	var url =	base_periode + 'periode_renovasi/periode_renovasi_proses.php' +
				'?act=delete' +
				'&idd=' + idd;
	
	jQuery.post(url, function(result) {
		
		alert(result.msg);
		if (result.error == false)
		{
			location.reload();
		}
		
	}, 'json');

	return false;
}
</script>
</head>
<body class="popup">

<form name="form" id="form" method="post">

<input type="button" id="add" value=" Tambah ">
<input type="button" id="close" value=" Tutup ">
<br><br>

<table class="t-data w100">
<tr>
	<th rowspan="2">NO.</th>
	<th colspan="2">PERIODE</th>
	<th rowspan="2">JML.<br>PERIODE</th>
	<th rowspan="2">DEPOSIT</th>
	<th rowspan="2">STATUS<br>PROSES</th>
	<th rowspan="2">KETERANGAN</th>
	<th rowspan="2"></th>
</tr>
<tr>
	<th>AWAL</th>
	<th>AKHIR</th>
</tr>
<?php
	$query = "
	SELECT
		d.ID_DEPOSIT,
		p.STATUS_BLOK,
		d.KODE_BLOK,
		d.PERIODE_AWAL,
		d.PERIODE_AKHIR,
		d.JUMLAH_PERIODE,
		
		d.NILAI_DEPOSIT,
		d.STATUS_PROSES,
		
		d.STATUS_EDIT,
		d.USER_EDIT,
		CONVERT(VARCHAR(10),d.TGL_EDIT,105) AS TGL_EDIT,
		d.KETERANGAN
	FROM 
		KWT_PERIODE_DEPOSIT d
		LEFT JOIN KWT_PELANGGAN p ON d.KODE_BLOK = p.KODE_BLOK
	WHERE 
		d.TRX = '6' AND 
		d.KODE_BLOK = '$id'";
	
	$obj = $conn->Execute($query);
	
	$i = 1;
	while( ! $obj->EOF)
	{
		$idd = base64_encode($obj->fields['ID_DEPOSIT']);
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo fm_periode_first($obj->fields['PERIODE_AWAL']); ?></td>
			<td class="text-center"><?php echo fm_periode_last($obj->fields['PERIODE_AKHIR']); ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_DEPOSIT']); ?></td>
			<td class="text-center"><?php echo status_proses($obj->fields['STATUS_PROSES']); ?></td>
			<td><?php echo $obj->fields['KETERANGAN']; ?></td>
			<td class="text-center">
				<input type="button" value=" Ubah " onclick="showPopup('Ubah', '<?php echo $idd; ?>');">
				<input type="button" value=" Hapus " onclick="deleteData('<?php echo $idd; ?>');">
			</td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
?>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
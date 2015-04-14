<?php
require_once('../../../../../config/config.php');
$conn = conn();

$kode_lokasi = (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';

$echo = '<option value="" data-key-pkl="" data-uang-pangkal="0" data-tarif="0" data-satuan="1"> -- KATEGORI -- </option>';

if ($kode_lokasi != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_TIPE, 
		t.NAMA_TIPE, 
		f.KEY_PKL,
		ISNULL(f.UANG_PANGKAL, '0') AS UANG_PANGKAL,
		ISNULL(f.TARIF, '0') AS TARIF,
		f.SATUAN
	FROM KWT_TARIF_PKL f
	LEFT JOIN KWT_TIPE_PKL t ON f.KODE_TIPE = t.KODE_TIPE
	WHERE f.KODE_LOKASI = '$kode_lokasi'
	ORDER BY NAMA_TIPE ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_TIPE'];
		$on = $obj->fields['NAMA_TIPE'];
		$dkp = $obj->fields['KEY_PKL'];
		$dup = $obj->fields['UANG_PANGKAL'];
		$dt = $obj->fields['TARIF'];
		$ds = $obj->fields['SATUAN'];
		$echo .= "<option value='$ov' data-key-pkl='$dkp' data-uang-pangkal='$dup' data-tarif='$dt' data-satuan='$ds'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
<?php
require_once('../../../../config/config.php');
$conn = conn();

$kode_lokasi = (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';

$echo = '<option value="" data-key-ksp="" data-save-deposit="0" data-tarif="0"> -- KATEGORI -- </option>';

if ($kode_lokasi != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_TIPE, 
		t.NAMA_TIPE, 
		f.KEY_KSP,
		ISNULL(f.SAVE_DEPOSIT, '0') AS SAVE_DEPOSIT,
		ISNULL(f.TARIF, '0') AS TARIF
	FROM KWT_TARIF_KSP f
	LEFT JOIN KWT_TIPE_KSP t ON f.KODE_TIPE = t.KODE_TIPE
	WHERE f.KODE_LOKASI = '$kode_lokasi'
	ORDER BY NAMA_TIPE ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_TIPE'];
		$on = $obj->fields['NAMA_TIPE'];
		$dkp = $obj->fields['KEY_KSP'];
		$dsd = $obj->fields['SAVE_DEPOSIT'];
		$dt = $obj->fields['TARIF'];
		$echo .= "<option value='$ov' data-key-ksp='$dkp' data-save-deposit='$dsd' data-tarif='$dt'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
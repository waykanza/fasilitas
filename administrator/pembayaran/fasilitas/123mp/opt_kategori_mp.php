<?php
require_once('../../../../config/config.php');
$conn = conn();

$kode_mp = (isset($_REQUEST['kode_mp'])) ? clean($_REQUEST['kode_mp']) : '';

$echo = '<option value="" data-key-mp="" data-tarif="0"> -- KATEGORI -- </option>';

if ($kode_mp != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_TIPE, 
		t.NAMA_TIPE, 
		f.KEY_MP,
		ISNULL(f.TARIF, '0') AS TARIF
	FROM KWT_TARIF_MP f
	LEFT JOIN KWT_TIPE_MP t ON f.KODE_TIPE = t.KODE_TIPE
	WHERE f.kode_mp = '$kode_mp'
	ORDER BY NAMA_TIPE ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_TIPE'];
		$on = $obj->fields['NAMA_TIPE'];
		$dkp = $obj->fields['KEY_MP'];
		$dt = $obj->fields['TARIF'];
		$echo .= "<option value='$ov' data-key-mp='$dkp' data-tarif='$dt'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
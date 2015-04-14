<?php
require_once('../../../../config/config.php');
$conn = conn();

$kode_tipe = (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';

$echo = '<option value="" data-key-psp="" data-detail-lokasi="" data-tarif="0"> -- FUNGSI -- </option>';

if ($kode_tipe != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_FUNGSI, 
		z.NAMA_FUNGSI, 
		f.KEY_PSP,
		ISNULL(f.TARIF, '0') AS TARIF,
		f.LOKASI
	FROM 
		KWT_TARIF_PSP f
		LEFT JOIN KWT_FUNGSI_PSP z ON f.KODE_FUNGSI = z.KODE_FUNGSI
		JOIN KWT_SK_PSP s ON f.KODE_SK = s.KODE_SK
	WHERE 
		f.KODE_TIPE = '$kode_tipe' AND 
		s.STATUS_SK = 1
	ORDER BY z.NAMA_FUNGSI ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_FUNGSI'];
		$on = $obj->fields['NAMA_FUNGSI'];
		$dkp = $obj->fields['KEY_PSP'];
		$ddl = $obj->fields['LOKASI'];
		$dt = $obj->fields['TARIF'];
		$echo .= "<option value='$ov' data-key-psp='$dkp' data-detail-lokasi='$ddl' data-tarif='$dt'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
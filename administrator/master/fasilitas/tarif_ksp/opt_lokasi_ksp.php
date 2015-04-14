<?php
require_once('../../../../config/config.php');
$conn = conn();

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';

$echo = '<option value=""> -- LOKASI -- </option>';

if ($kode_sk != '')
{
	$obj = $conn->execute("SELECT KODE_LOKASI, TIPE_LOKASI FROM KWT_LOKASI_KSP WHERE KODE_SK = '$kode_sk' ORDER BY TIPE_LOKASI ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_LOKASI'];
		$on = tipe_lokasi($obj->fields['TIPE_LOKASI']);
		$echo .= "<option value='$ov'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
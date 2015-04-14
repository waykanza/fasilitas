<?php
require_once('../../config/config.php');

$kode_sektor = (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';

$echo = '<option value=""> -- CLUSTER -- </option>';

if ($kode_sektor != '')
{
	$conn = conn();
	$obj = $conn->Execute("SELECT KODE_CLUSTER, NAMA_CLUSTER FROM KWT_CLUSTER WHERE KODE_SEKTOR = '$kode_sektor' ORDER BY NAMA_CLUSTER ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_CLUSTER'];
		$on = $obj->fields['NAMA_CLUSTER'];
		$echo .= "<option value='$ov'> $on ($ov) </option>";
		$obj->movenext();
	}
	close($conn);
}

echo $echo;
?>
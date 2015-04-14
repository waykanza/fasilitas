<?php
require_once('../../config/config.php');
$conn = conn();

$status_blok = (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';

$echo = '<option value=""> -- KATEGORI -- </option>';

if ($status_blok != '')
{
	$obj = $conn->Execute("SELECT KODE_TIPE, NAMA_TIPE FROM KWT_TIPE_IPL WHERE STATUS_BLOK = '$status_blok' ORDER BY NAMA_TIPE ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_TIPE'];
		$on = $obj->fields['NAMA_TIPE'];
		$echo .= "<option value='$ov'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
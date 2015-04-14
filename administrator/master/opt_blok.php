<?php
require_once('../../config/config.php');
$conn = conn();

$kode_cluster = (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';

$echo = '<option value=""> -- KODE BLOK -- </option>';

if ($kode_cluster != '')
{
	$obj = $conn->Execute("SELECT KODE_BLOK FROM KWT_BLOK WHERE KODE_CLUSTER = '$kode_cluster' ORDER BY KODE_BLOK ASC");
	
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_BLOK'];
		$echo .= "<option value='$ov'> $ov </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
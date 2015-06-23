<?php
require_once('../../../config/config.php');
$conn = conn();

$echo = '<option value="" data-kode-lokasi="" >  -- LOKASI --</option>';
$obj = $conn->execute("SELECT * from KWT_LOKASI_PKL");

while( ! $obj->EOF)
{
	$dl = $obj->fields['DETAIL_LOKASI'];
	$kl = $obj->fields['KODE_LOKASI'];
	$echo .= "<option value='$kl' data-kode-lokasi='$kl' > $dl </option>";
	$obj->movenext();
}
close($conn);
echo $echo;
?>
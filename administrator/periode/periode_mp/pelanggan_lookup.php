<?php
require_once('../../../config/config.php');
$conn = conn();

$no_va = (isset($_REQUEST['no_va'])) ? clean($_REQUEST['no_va']) : '';

$echo = '<option value=""> </option>';

if ($no_va != '')
{
	$obj = $conn->execute("
	SELECT 
		f.NO_PELANGGAN, 
		f.NAMA_PELANGGAN, 
		f.NO_TELEPON,
		f.ALAMAT,
	FROM FSL_PELANGGAN f
	WHERE f.NO_PELANGGAN LIKE '%$no_va%'
	ORDER BY NO_PELANGGAN ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['NO_PELANGGAN'];
		$on = $obj->fields['NAMA_PELANGGAN'];
		$dkp = $obj->fields['NO_TELEPON'];
		$dt = $obj->fields['ALAMAT'];
		$echo .= "<option value='$ov' data-nama='$dkp' data-telepon='$dt' data-alamat='$du1'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
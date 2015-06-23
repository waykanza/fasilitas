<?php
require_once('../../../config/config.php');
$conn = conn();

$no_va = (isset($_REQUEST['no_va'])) ? clean($_REQUEST['no_va']) : '';



if ($no_va != '')
{
	$obj = $conn->execute("
	SELECT 
		f.NO_PELANGGAN, 
		f.NAMA_PELANGGAN, 
		f.NO_TELEPON,
		f.ALAMAT
	FROM FSL_PELANGGAN f
	WHERE f.NO_PELANGGAN LIKE '%$no_va%'
	");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['NO_PELANGGAN'];
		$nama = $obj->fields['NAMA_PELANGGAN'];
		$telp = $obj->fields['NO_TELEPON'];
		$alamat = $obj->fields['ALAMAT'];
		echo $nama."|".$telp."|".$alamat;
		$obj->movenext();
	}
}

close($conn);

?>
<?php
require_once('../../../config/config.php');
$conn = conn();

$lokasi = (isset($_REQUEST['lokasi'])) ? clean($_REQUEST['lokasi']) : '';

$echo = '<option value="" data-key_pkl = "" data-uang_pangkal="" data-tarif="0" data-satuan = ""> -- KATEGORI -- </option>';

if ($lokasi != '')
{
	$obj = $conn->execute("SELECT * from KWT_TARIF_PKL a left join KWT_TIPE_PKL b on a.KODE_TIPE = b.KODE_TIPE where KODE_LOKASI = '$lokasi'");
	
	while( ! $obj->EOF)
	{
		$s 		= $obj->fields['SATUAN'];
		$pkl 	= $obj->fields['KEY_PKL'];
		$up 	= $obj->fields['UANG_PANGKAL'];
		$dt 	= $obj->fields['TARIF'];
		$kt 	= $obj->fields['NAMA_TIPE'];
		$echo 	.= "<option value='$pkl' data-key_pkl = '$pkl' data-uang_pangkal='$up' data-tarif='$dt' data-satuan = '$s'> $kt</option>";
		$obj->movenext();
	}
	
}

close($conn);
echo $echo;
?>
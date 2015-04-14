<?php
require_once('../../../../../config/config.php');
$conn = conn();

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';

$echo = '<option value=""> -- KATEGORI TARIF -- </option>';

if ($kode_sk != '')
{
	$obj = $conn->execute("
	SELECT 
		m.KEY_MP, 
		t.NAMA_TIPE 
	FROM 
		KWT_TARIF_MP m
		LEFT JOIN KWT_TIPE_MP t ON m.KODE_TIPE = t.KODE_TIPE
	WHERE 
		m.KODE_SK = '$kode_sk' AND
		m.KODE_MP = 'D'
	ORDER BY t.NAMA_TIPE ASC
	");
	
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KEY_MP'];
		$on = $obj->fields['NAMA_TIPE'];
		$echo .= "<option value='$ov'> $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
<?php
require_once('../../../config/config.php');
$conn = conn();

$kode_mp = (isset($_REQUEST['kode_mp'])) ? clean($_REQUEST['kode_mp']) : '';

$echo = '<option value="" data-key-mp="" data-tarif="0"> -- KATEGORI -- </option>';

if ($kode_mp != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_TIPE, 
		t.NAMA_TIPE, 
		f.KEY_MP,
		f.UKURAN_1,
		f.UKURAN_2,
		ISNULL(f.TARIF, '0') AS TARIF
	FROM KWT_TARIF_MP f
	LEFT JOIN KWT_TIPE_MP t ON f.KODE_TIPE = t.KODE_TIPE
	WHERE f.kode_mp = '$kode_mp'
	ORDER BY NAMA_TIPE ASC");
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_TIPE'];
		$on = $obj->fields['NAMA_TIPE'];
		$dkp = $obj->fields['KEY_MP'];
		$dt = $obj->fields['TARIF'];
		$du1 = $obj->fields['UKURAN_1'];
		$du2 = $obj->fields['UKURAN_2'];
		$echo .= "<option value='$ov' data-key-mp='$dkp' data-tarif='$dt' data-ukuran1='$du1' data-ukuran2='$du2' > $on ($ov) </option>";
		$obj->movenext();
	}
}

close($conn);
echo $echo;
?>
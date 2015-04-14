<?php
require_once('../../../../../config/config.php');
$conn = conn();

$key_mp = (isset($_REQUEST['key_mp'])) ? clean($_REQUEST['key_mp']) : '';

$echo = '<option value="" data-key-mp="" data-tarif="0"> -- LOKASI -- </option>';

if ($key_mp != '')
{
	$obj = $conn->execute("
	SELECT 
		f.KODE_LOKASI, 
		t.NAMA_LOKASI, 
		f.KEY_MPD,
		x.KEY_MP,
		ISNULL(f.TARIF, '0') AS TARIF
	FROM KWT_TARIF_MPD f
	LEFT JOIN KWT_LOKASI_MP t ON f.KODE_LOKASI = t.KODE_LOKASI
	LEFT JOIN KWT_TARIF_MP x ON x.KEY_MP = f.KEY_MP
	WHERE x.key_mp = '$key_mp'
	ORDER BY NAMA_LOKASI ASC");
	
	while( ! $obj->EOF)
	{
		$ov = $obj->fields['KODE_LOKASI'];
		$on = $obj->fields['NAMA_LOKASI'];
		$dkp = $obj->fields['KEY_MPD'];
		$dt = $obj->fields['TARIF'];
		$echo .= "<option value='$ov' data-key-mpd='$dkp' data-tarif='$dt'> $on ($ov) </option>";
		$obj->movenext();
	}
	
}

close($conn);
echo $echo;
?>
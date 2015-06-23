<?php
require_once('../../../config/config.php');
$conn = conn();

$key_mp = (isset($_REQUEST['key_mpd'])) ? clean($_REQUEST['key_mpd']) : '';
$obj = $conn->execute("
SELECT TARIF FROM KWT_TARIF_MPD WHERE KEY_MPD = $key_mp");
	
	while( ! $obj->EOF)
	{
		$tarif = $obj->fields['TARIF'];
		$echo = $key_mp;
		$obj->movenext();
	}
	

close($conn);
echo $echo;
?>
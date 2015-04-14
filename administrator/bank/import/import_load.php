<?php
require_once('../../../config/config.php');

$nama_bank = (isset($_REQUEST['nama_bank'])) ? clean($_REQUEST['nama_bank']) : '';

$conn = conn();

	switch ($nama_bank)
	{
		case 'BCA'			: $ext = 'txt'; $bayar_melalui = 'BC'; break;
		case 'BUKOPIN'		: $ext = 'xls'; $bayar_melalui = 'BK'; break;
		#case 'BUKOPIN_AD'	: $ext = 'xls'; $bayar_melalui = 'XX'; break;
		#case 'BUMIPUTERA'	: $ext = 'txt'; $bayar_melalui = 'XX'; break;
		case 'MANDIRI'		: $ext = 'xls'; $bayar_melalui = 'BM'; break;
		case 'NIAGA'		: $ext = 'txt'; $bayar_melalui = 'BN'; break;
		case 'NIAGA_AD'		: $ext = 'txt'; $bayar_melalui = 'BN'; break;
		case 'PERMATA'		: $ext = 'txt'; $bayar_melalui = 'BP'; break;
	}

if ($nama_bank == '')
{
	echo '<script type="text/javascript">alert("Proses import untuk bank yang anda pilih belum tersedia.");</script>';
}
else
{
	$path = EXPORT_PATH . strtolower($nama_bank) . '\\';
	
	require_once('load_'. strtolower($nama_bank) .'.php');
}

close($conn);
?>
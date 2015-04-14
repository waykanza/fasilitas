<?php
require_once('../../../config/config.php');

$nama_bank = (isset($_REQUEST['nama_bank'])) ? clean($_REQUEST['nama_bank']):'';

$error = FALSE;
$msg = '';
$path = '';
$exe = '';

	switch ($nama_bank)
	{
		case 'BCA'			: $path = 'bca'; break;
		case 'BUKOPIN'		: $path = 'bukopin'; break;
		case 'BUMIPUTERA'	: $path = 'bumiputera'; break;
		case 'MANDIRI'		: $path = 'mandiri'; break;
		case 'NIAGA'		: $path = 'niaga'; break;
		case 'NIAGA_AD'		: $path = 'niaga_ad'; break;
		case 'PERMATA'		: $path = 'permata'; break;
	}
	
try
{
	ex_empty($nama_bank, 'Pilih Kode Bank.');
	ex_empty($path, 'Aplikasi export bank yang anda pilih belum tersedia');
	
	$path = EXPORT_PATH . $path . '\\';
	$exe = $path . 'export.exe';
	
	$status = read_file($path . 'status.txt');
	if ($status == 'PROSES')
	{
		throw new Exception('Proses export sedang dijalankan oleh user lain.');
	}
	
	$wshshell = new COM('WScript.Shell');
	$wshshell->Run($exe, 0, FALSE);
	sleep(3);
	
	$msg = 'Proses export sedang berjalan';
}
catch(Exception $e)
{
	$msg = $e->getMessage();
	$error = TRUE;
}

echo json_encode(array('error' => $error, 'msg' => $msg));
exit;
?>	
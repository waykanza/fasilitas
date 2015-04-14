<?php
require_once('../../../config/config.php');

$error = FALSE;
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
	
	try
	{
		ex_empty($periode, 'Masukkan periode.');
		
		$path = EXPORT_PATH . 'sm\\';
		$exe = $path . 'export.exe';
		
		$status = read_file($path . 'status.txt');
		if ($status == 'PROSES')
		{
			throw new Exception('Proses export sedang dijalankan oleh user lain.');
		}
		
		write_file($path . 'periode.txt', $periode);
		
		$wshshell = new COM('WScript.Shell');
		$wshshell->Run($exe, 0, FALSE);
		sleep(2);
		
		$msg = 'Proses export sedang berjalan';
	}
	catch (Exception $e)
	{
		$msg = $e->getMessage();
		$error = TRUE;
	}
}

echo json_encode(array('error' => $error, 'msg' => $msg));
exit;
?>	
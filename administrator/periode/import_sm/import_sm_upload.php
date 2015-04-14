<?php
require_once('../../../config/config.php');

$error = FALSE;
$msg = '';
$ext = 'txt';
$path = IMPORT_PATH . 'sm\\';
$exe = $path . 'import.exe';

$status = read_file($path . 'status.txt');
if ($status == 'PROSES')
{
	$msg = 'Proses export sedang dijalankan oleh user lain.';
}
else
{
	try
	{

		if ( ! isset($_FILES) AND ! isset($_FILES['file_import']))
		{
			throw new Exception('Error upload file.');
		}
		
		$tmp_name	= $_FILES['file_import']['tmp_name'];
		$file_name	= $_FILES['file_import']['name'];
		$file_type	= pathinfo(basename($file_name), PATHINFO_EXTENSION);
		$error_upload = $_FILES['file_import']['error'];
		
		if ($error_upload > 0)
		{
			throw new Exception("Error upload file. error code : [$error_upload]");
		}
		
		if ($file_type != $ext)
		{
			throw new Exception("Tipe file import harus ($ext)");
		}
		
		if ( ! copy($tmp_name, $path . "upload.$ext"))
		{
			throw new Exception('Error upload file.');
		}

		$wshshell = new COM('WScript.Shell');
		$wshshell->Run($exe, 0, FALSE);
			
		$msg = 'Proses import sedang berjalan';
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
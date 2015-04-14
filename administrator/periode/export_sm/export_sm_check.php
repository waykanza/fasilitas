<?php
require_once('../../../config/config.php');

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']):'';

$status = '';
$file_name = '';
$respon = '';

if ($periode != '')
{
	$path = EXPORT_PATH . 'sm\\';
	
	$status = read_file($path . 'status.txt');
	
	if ($status == 'FINISH')
	{
		$respon = read_file($path . 'respon.txt');
		if ($respon == '')
		{
			$file_name = 'BIMASAKTI_EXPORT_'.$periode.'.txt';
		}
	}
}

echo json_encode(array('status' => $status, 'file_name' => $file_name, 'respon' => $respon));
exit;
?>
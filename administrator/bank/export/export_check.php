<?php
require_once('../../../config/config.php');

$nama_bank = (isset($_REQUEST['nama_bank'])) ? clean($_REQUEST['nama_bank']):'';

$ext = '';
$status = '';
$link = '';
$respon = '';

switch ($nama_bank)
{
	case 'BCA'			: $ext = 'xls'; break;
	case 'BUKOPIN'		: $ext = 'txt'; break;
	case 'BUMIPUTERA'	: $ext = 'txt'; break;
	case 'MANDIRI'		: $ext = 'xls'; break;
	case 'NIAGA'		: $ext = 'txt'; break;
	case 'NIAGA_AD'		: $ext = 'txt'; break;
	case 'PERMATA'		: $ext = 'txt'; break;
}

if ($nama_bank != '' AND $ext != '')
{
	$path = EXPORT_PATH . strtolower($nama_bank) . '\\';
	
	$status = read_file($path . 'status.txt');
	
	if ($status == 'FINISH')
	{
		$respon = read_file($path . 'respon.txt');
		
		if ($respon == '')
		{
			$periode_file = date('Ym');
			$file_name = $nama_bank . "_EXPORT_$periode_file.$ext";
			
			if ($nama_bank == 'NIAGA')
			{
				$d11 = '11D_' . $file_name;
				$d12 = '12D_' . $file_name;
				
				$link = "<a class='link-download' target='_blank' href='$nama_bank/files/$d11'>$d11</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				$link .= "<a class='link-download' target='_blank' href='$nama_bank/files/$d12'>$d12</a>";
			}
			else
			{
				
				$link = "<a class='link-download' target='_blank' href='$nama_bank/files/$file_name'>$file_name</a>";
			}
		}
	}
}

echo json_encode(array('status' => $status, 'link' => $link, 'respon' => $respon));
exit;
?>

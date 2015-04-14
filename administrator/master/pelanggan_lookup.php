<?php
require_once('../../config/config.php');
$conn = conn();

$no_ktp = (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';

if ($act == 'list')
{
	$echo = '';
	
	if ($no_ktp != '')
	{
		$obj = $conn->Execute("
		SELECT
			NO_KTP,
			LOWER(NAMA_PELANGGAN) AS NAMA_PELANGGAN,
			NPWP,
			ALAMAT,
			NO_TELEPON,
			NO_HP,
			KODE_BANK,
			NO_REKENING
		FROM
			KWT_PELANGGAN_LOOKUP
		WHERE
			NO_KTP LIKE '$no_ktp%'
		ORDER BY NAMA_PELANGGAN ASC
		");
		while( ! $obj->EOF)
		{
			$id = $obj->fields['NO_KTP'];
			$echo .= "
			<div class='lp' onclick='javascript:pp(\"$id\")'>
				<div class='lp-nm'>".ucwords($obj->fields['NAMA_PELANGGAN'])." <span class='lp-nk'>$id</span></div>
			</div>";
			
			$obj->movenext();
		}
		
		close($conn);
		echo $echo;
	}
}
elseif ($act == 'sel')
{
	$echo = array(
		'no_ktp' => '',
		'nama_pelanggan' => '',
		'npwp' => '',
		'alamat' => '',
		'no_telepon' => '',
		'no_hp' => '',
		'kode_bank' => '',
		'no_rekening' => ''
	);
	
	if ($no_ktp != '')
	{
		$obj = $conn->Execute("
		SELECT
			NO_KTP,
			NAMA_PELANGGAN,
			NPWP,
			ALAMAT,
			NO_TELEPON,
			NO_HP,
			KODE_BANK,
			NO_REKENING
		FROM
			KWT_PELANGGAN_LOOKUP
		WHERE
			NO_KTP = '$no_ktp'
		");
		
		$echo = array(
			'no_ktp' => $obj->fields['NO_KTP'],
			'nama_pelanggan' => $obj->fields['NAMA_PELANGGAN'],
			'npwp' => $obj->fields['NPWP'],
			'alamat' => $obj->fields['ALAMAT'],
			'no_telepon' => $obj->fields['NO_TELEPON'],
			'no_hp' => $obj->fields['NO_HP'],
			'kode_bank' => $obj->fields['KODE_BANK'],
			'no_rekening' => $obj->fields['NO_REKENING']
		);
	}
	
	close($conn);
	echo json_encode($echo);
}
?>
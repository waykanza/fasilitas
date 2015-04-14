<?php
require_once('../../../config/config.php');

$conn = conn();
$msg = '';


$tipe_tagihan = (isset($_REQUEST['tipe_tagihan'])) ? clean($_REQUEST['tipe_tagihan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		$now_periode = date('Y-m') . '-01';
		
		/* ex_empty($tipe_tagihan, 'Pilih tipe tagihan!');
		
		switch ($tipe_tagihan) 
		{
			case 'air_ipl' : include_once('air_ipl.php'); break;
			case 'deposit' : include_once('deposit.php'); break;
			default : throw new Exception('Error status blok, Hubungi Developer/MSI !!'); break;
		} */
		
		include_once('air_ipl.php');
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Proses hitung denda selesai.';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$conn->rollbacktrans();
	}

	close($conn);
	echo $msg;
	exit;
}
?>
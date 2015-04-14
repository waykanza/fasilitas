<?php
require_once('../../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;

$id = (isset($_REQUEST['id'])) ? base64_decode(clean($_REQUEST['id'])) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
			
		$conn->begintrans();
		
		$query="
			UPDATE KWT_PEMBAYARAN_MP_DETAIL
			SET TANGGAL_BAYAR=NULL, JENIS_BAYAR =NULL, KODE_BANK=NULL, NO_REKENING=NULL, KETERANGAN=NULL, 
			CREATED_DATE=NULL, STATUS_BAYAR=0, KASIR=NULL, NO_KWITANSI=NULL
			WHERE ID_PEMBAYARAN = '$id'
		";
		
		ex_false($conn->Execute($query), $query);
		
		
		$conn->committrans();
		
		$msg = "Data pembayaran berhasil dibatalkan.";
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}

	close($conn);
	$json = array('msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}

$id = base64_encode($id);
?>
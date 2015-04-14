<?php
require_once('../../../config/config.php');

$conn = conn();
$msg = '';

$trx = (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$tgl_bayar = (isset($_REQUEST['tgl_bayar'])) ? clean($_REQUEST['tgl_bayar']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($trx, 'Pilih status blok!');
		ex_empty($tgl_bayar, 'Masukkan tanggal posting!');
		
		# CHECK ALREADY POSTED
		$query = "
		SELECT COUNT(*) AS TOTAL FROM KWT_POST_PEMBAYARAN
		WHERE TRX = '$trx' AND CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_bayar'";
		ex_notfound($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal $tgl_bayar belum di-posting!");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET STATUS_POST_PB = NULL
		WHERE TRX = '$trx' AND CONVERT(VARCHAR(10), TGL_BAYAR, 105) = '$tgl_bayar'";
		ex_false($conn->Execute($query), $query);
		
		# INSERT POSTED LOG
		$query = "
		DELETE FROM KWT_POST_PEMBAYARAN 
		WHERE TRX = '$trx' AND CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_bayar'";
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Proses unposting selesai.';
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
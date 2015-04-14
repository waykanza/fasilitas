<?php
require_once('../../../config/config.php');

$conn = conn();
$msg = '';

$tgl_post_fp = (isset($_REQUEST['tgl_post_fp'])) ? clean($_REQUEST['tgl_post_fp']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($tgl_post_fp, 'Masukkan tanggal posting!');
		
		# CHECK ALREADY POSTED
		$query = "
		SELECT COUNT(*) AS TOTAL FROM KWT_POST_FAKTUR_PAJAK
		WHERE CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_post_fp'";
		ex_notfound($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal $tgl_post_fp belum di-posting!");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET TGL_POST_FP = NULL
		WHERE CONVERT(VARCHAR(10), TGL_POST_FP, 105) = '$tgl_post_fp'";
		ex_false($conn->Execute($query), $query);
		
		# INSERT POSTED LOG
		$query = "DELETE FROM KWT_POST_FAKTUR_PAJAK WHERE CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_post_fp'";
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
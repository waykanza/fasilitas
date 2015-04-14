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
		ex_found($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal $tgl_post_fp sudah di-posting!");
		
		# CHECK ROW FOR UPDATE
		$query = "
		SELECT COUNT(ID_PEMBAYARAN) AS TOTAL FROM KWT_PEMBAYARAN_AI 
		WHERE TGL_POST_FP IS NULL";
		ex_notfound($conn->Execute($query)->fields['TOTAL'], "Tidak ada data transaksi tanggal $tgl_post_fp yang akan di-posting!");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET TGL_POST_FP = CONVERT(DATETIME, '$tgl_post_fp', 105)
		WHERE TGL_POST_FP IS NULL
		";
		ex_false($conn->Execute($query), $query);
		
		# INSERT POSTED LOG
		$user_post = $_SESSION['ID_USER'];
		$query = "
		INSERT INTO KWT_POST_FAKTUR_PAJAK (USER_POST, TGL_POST)
		VALUES ('$user_post', CONVERT(DATETIME, '$tgl_post_fp', 105))";
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Proses posting selesai.';
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
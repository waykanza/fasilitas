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
		ex_found($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal \"$tgl_bayar\" sudah di-posting!");
		
		# CHECK ROW FOR UPDATE
		$query = "
		SELECT COUNT(ID_PEMBAYARAN) AS TOTAL FROM KWT_PEMBAYARAN_AI 
		WHERE TRX = '$trx' AND CONVERT(VARCHAR(10), TGL_BAYAR, 105) = '$tgl_bayar'";
		ex_notfound($conn->Execute($query)->fields['TOTAL'], "Tidak ada data transaksi tanggal \"$tgl_bayar\" yang akan diposting !");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET STATUS_POST_PB = '1'
		WHERE TRX = '$trx' AND CONVERT(VARCHAR(10), TGL_BAYAR, 105) = '$tgl_bayar'";
		ex_false($conn->Execute($query), $query);
		
		# INSERT POSTED LOG
		$user_post = $_SESSION['ID_USER'];
		$query = "
		INSERT INTO KWT_POST_PEMBAYARAN (TRX, USER_POST, TGL_POST)
		VALUES ('$trx', '$user_post', CONVERT(DATETIME, '$tgl_bayar', 105))";
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
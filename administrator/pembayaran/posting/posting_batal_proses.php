<?php
require_once('../../../config/config.php');
die_login();
die_mod('U10');
$conn = conn();
die_conn($conn);

$msg = '';

$tgl_bayar_bank = (isset($_REQUEST['tgl_bayar_bank'])) ? clean($_REQUEST['tgl_bayar_bank']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($tgl_bayar_bank, 'Masukkan tanggal posting!');
		
		# CHECK ALREADY POSTED
		$query = "
		SELECT COUNT(*) AS TOTAL FROM KWT_POST_PEMBAYARAN
		WHERE CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_bayar_bank'";
		ex_not_found($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal $tgl_bayar_bank belum di-posting!");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET STATUS_POST_PB = 0, 
			USER_MODIFIED = '$sess_id_user', 
			MODIFIED_DATE = GETDATE() 
		WHERE CONVERT(VARCHAR(10), TGL_BAYAR_BANK, 105) = '$tgl_bayar_bank'";
		ex_false($conn->Execute($query), $query);
		
		# DELETE POSTED LOG
		$query = "
		DELETE FROM KWT_POST_PEMBAYARAN 
		WHERE CONVERT(VARCHAR(10), TGL_POST, 105) = '$tgl_bayar_bank'";
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
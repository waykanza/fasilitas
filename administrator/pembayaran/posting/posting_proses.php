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
		ex_found($conn->Execute($query)->fields['TOTAL'], "Data transaksi tanggal \"$tgl_bayar_bank\" sudah di-posting!");
		
		# CHECK ROW FOR UPDATE
		$query = "
		SELECT COUNT(ID_PEMBAYARAN) AS TOTAL FROM KWT_PEMBAYARAN_AI 
		WHERE 
			CONVERT(VARCHAR(10), TGL_BAYAR_BANK, 105) = '$tgl_bayar_bank' AND 
			STATUS_BAYAR = 1";
		ex_not_found($conn->Execute($query)->fields['TOTAL'], "Tidak ada data transaksi tanggal \"$tgl_bayar_bank\" yang akan diposting !");
		
		# UPDATE ROW
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET STATUS_POST_PB = 1, 
			USER_MODIFIED = '$sess_id_user', 
			MODIFIED_DATE = GETDATE() 
		WHERE CONVERT(VARCHAR(10), TGL_BAYAR_BANK, 105) = '$tgl_bayar_bank'";
		ex_false($conn->Execute($query), $query);
		
		# INSERT POSTED LOG
		$query = "
		INSERT INTO KWT_POST_PEMBAYARAN 
		(
			USER_POST, 
			TGL_POST,
			
			JUMLAH_AIR, 
			ABONEMEN, 
			JUMLAH_IPL, 
			DENDA, 
			ADM, 
			JUMLAH_BAYAR, 
			
			USER_CREATED
		)
		SELECT 
			'$sess_id_user' AS USER_POST, 
			CONVERT(DATETIME, '$tgl_bayar_bank', 105) AS TGL_POST,
			
			SUM(JUMLAH_AIR) AS JUMLAH_AIR, 
			SUM(ABONEMEN) AS ABONEMEN, 
			SUM(JUMLAH_IPL) AS JUMLAH_IPL, 
			SUM(DENDA) AS DENDA, 
			SUM(ADM) AS ADM, 
			SUM(JUMLAH_BAYAR) AS JUMLAH_BAYAR, 
			
			'$sess_id_user'
		FROM KWT_PEMBAYARAN_AI 
		WHERE 
			STATUS_BAYAR = 1 AND 
			CONVERT(VARCHAR(10), TGL_BAYAR_BANK, 105) = '$tgl_bayar_bank' 
		";
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
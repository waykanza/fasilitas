<?php
require_once('../../../config/config.php');
die_login();
die_mod('PL1');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;
$list_idp = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		$cara_bayar = (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
		$bayar_via = (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
		$tgl_bayar_bank = (isset($_REQUEST['tgl_bayar_bank'])) ? to_date($_REQUEST['tgl_bayar_bank']) : '';
		
		ex_empty($cara_bayar, 'Pilih jenis pembayaran.');
		ex_empty($bayar_via, 'Pilih kode bank.');
		ex_empty($tgl_bayar_bank, 'Masukkan tanggal bayar.');
		
		$cb_data = (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();
		$cb_ket_bayar = (isset($_REQUEST['cb_ket_bayar'])) ? $_REQUEST['cb_ket_bayar'] : array();

		if (empty($cb_data)) {
			throw new Exception("Pilih jenis pembayaran.");
		}
		
		foreach ($cb_data as $i => $x)
		{
			$id = base64_decode($x);
			$list_idp[] = $x;
			
			$ket_bayar = clean($cb_ket_bayar[$i]);
			
			$obj = $conn->Execute("
				SELECT 
					dbo.PTPS(PERIODE_TAG) AS PERIODE_TAG, 
					STATUS_BAYAR 
				FROM KWT_PEMBAYARAN_AI 
				WHERE ID_PEMBAYARAN = '$id' 
			");
			
			$periode_tag = $obj->fields['PERIODE_TAG'];
			$status_bayar = $obj->fields['STATUS_BAYAR'];
			
			if ($status_bayar == '1') {
				$msg .= "\nTagihan periode \"$periode_tag\" sudah dibayar.";
				continue;
			} 
			
			$query = "
			DECLARE 
			@adm_lbg INT,
			@adm_lrv INT
			
			SELECT TOP 1 
			@adm_lbg = ADM_DBG ,
			@adm_lrv = ADM_DRV
			FROM KWT_PARAMETER 
			
			UPDATE KWT_PEMBAYARAN_AI 
			SET 
				STATUS_BAYAR = 1, 
				CARA_BAYAR = $cara_bayar,
				BAYAR_VIA = '$bayar_via', 
				
				TGL_BAYAR_BANK = '$tgl_bayar_bank',
				TGL_BAYAR_SYS = GETDATE(), 
				USER_BAYAR = '$sess_id_user',
				KET_BAYAR = '$ket_bayar',
				
				ADM = 
				(
					CASE TRX
						WHEN $trx_lbg THEN @adm_lbg
						WHEN $trx_lrv THEN @adm_lrv 
					END
				),
				JUMLAH_BAYAR = 
				(
					JUMLAH_IPL + DENDA + 
					CASE TRX
						WHEN $trx_lbg THEN @adm_lbg
						WHEN $trx_lrv THEN @adm_lrv 
					END - 
					DISKON_IPL 
				), 
					
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$msg .= "\nTagihan periode \"$periode_tag\" berhasil dibayar.";
		}
		
		$conn->committrans();
	}
	catch(Exception $e)
	{
		$msg .= "\n" . $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}

	$list_idp = implode('||', $list_idp);
	
	close($conn);
	echo json_encode(array('msg' => $msg, 'error'=> $error, 'list_idp' => $list_idp));
	exit;
}
?>
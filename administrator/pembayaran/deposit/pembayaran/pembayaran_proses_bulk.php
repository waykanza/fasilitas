<?php
require_once('../../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;
$list_id = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		$kasir = $_SESSION['ID_USER'];
		$jenis_bayar = (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
		$cb_data = (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();

		ex_empty($jenis_bayar, 'Pilih jenis pembayaran.');
		if (empty($cb_data))
		{
			throw new Exception("Pilih jenis pembayaran.");
		}
		
		foreach ($cb_data as $x)
		{
			$id = base64_decode($x);
			$list_id[] = $x;
			
			$query = "
			SELECT 
				dbo.PTPS(PERIODE) AS PERIODE,
				STATUS_BAYAR, 
				JENIS_BAYAR
			FROM KWT_PEMBAYARAN_AI
			WHERE 
				ID_PEMBAYARAN = '$id'";
			
			$obj = $conn->Execute($query);
			
			$periode = $obj->fields['PERIODE'];
			
			if ($obj->fields['STATUS_BAYAR'] == '2')
			{
				$msg .= "\nTagihan periode \"$periode\" sudah dibayar.";
				continue;
			}
			
			$query = "
			DECLARE 
			@adm_db INT,
			@adm_dr INT
			
			SELECT TOP 1 
			@adm_db = ISNULL(ADMINISTRASI_DB, 0) ,
			@adm_dr = ISNULL(ADMINISTRASI_DR, 0)
			FROM KWT_PARAMETER
			
			UPDATE KWT_PEMBAYARAN_AI
			SET 
				TGL_BAYAR = GETDATE(),
				STATUS_BAYAR = '2',
				BAYAR_MELALUI = 'KS',
				KASIR = '$kasir',
				JENIS_BAYAR = '$jenis_bayar',
				ADMINISTRASI = 
				(
					CASE TRX
						WHEN '3' THEN @adm_db
						WHEN '6' THEN @adm_dr 
					END
				),
				JUMLAH_BAYAR = 
				(
					(
						JUMLAH_IPL + DENDA + 
						CASE TRX
							WHEN '3' THEN @adm_db
							WHEN '6' THEN @adm_dr 
						END
					) - DISKON_RUPIAH_IPL
				)
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$msg .= "\nTagihan periode \"$periode\" berhasil dibayar.";
		}
		
		$conn->committrans();
	}
	catch(Exception $e)
	{
		$msg .= "\n" . $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}

	$list_id = implode('||', $list_id);
	
	close($conn);
	echo json_encode(array('msg' => $msg, 'error'=> $error, 'list_id' => $list_id));
	exit;
}
?>
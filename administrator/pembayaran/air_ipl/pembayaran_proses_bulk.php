<?php
require_once('../../../config/config.php');
die_login();
die_mod('PA1');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;
$list_idp = array();


$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$cara_bayar = (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
	$bayar_via = (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
	$tgl_bayar_bank = (isset($_REQUEST['tgl_bayar_bank'])) ? to_date($_REQUEST['tgl_bayar_bank']) : '';
	$cb_data = (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();
	$cb_ket_bayar = (isset($_REQUEST['cb_ket_bayar'])) ? $_REQUEST['cb_ket_bayar'] : array();
			
	if ($act == 'pembayaran')
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($cara_bayar, 'Pilih jenis pembayaran.');
			ex_empty($bayar_via, 'Pilih kode bank.');
			ex_empty($tgl_bayar_bank, 'Masukkan tanggal bayar.');			

			if (empty($cb_data)) {
				throw new Exception("Pilih jenis pembayaran.");
			}
			
			foreach ($cb_data as $i => $x)
			{
				$id = base64_decode($x);
				$list_idp[] = $x;
				
				$ket_bayar = $cb_ket_bayar[$i];
				
				$obj = $conn->Execute("
					SELECT 
						dbo.PTPS(PERIODE_TAG) AS PERIODE_TAG, 
						STATUS_POST_PB, 
						STATUS_BAYAR, 
						JUMLAH_AIR, 
						JUMLAH_IPL 
					FROM KWT_PEMBAYARAN_AI 
					WHERE ID_PEMBAYARAN = '$id' 
				");
				
				$periode_tag = $obj->fields['PERIODE_TAG'];
				$status_post_pb = $obj->fields['STATUS_POST_PB'];
				$status_bayar = $obj->fields['STATUS_BAYAR'];
				$jumlah_air = $obj->fields['JUMLAH_AIR'];
				$jumlah_ipl = $obj->fields['JUMLAH_IPL'];				
				
				if ($status_post_pb == '1') {
					$msg .= "\nTagihan periode \"$periode_tag\" sudah di-posting!.";
					continue;
				} elseif ($status_bayar == '1') {
					$msg .= "\nTagihan periode \"$periode_tag\" sudah dibayar.";
					continue;
				}
				
				$query = "
				DECLARE 
				@adm_kv INT,
				@adm_bg INT,
				@adm_hn INT,
				@adm_rv INT
				
				SELECT TOP 1 
				@adm_kv = ADM_KV ,
				@adm_bg = ADM_BG ,
				@adm_hn = ADM_HN ,
				@adm_rv = ADM_RV
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
							WHEN $trx_kv THEN @adm_kv
							WHEN $trx_bg THEN @adm_bg 
							WHEN $trx_hn THEN @adm_hn
							WHEN $trx_rv THEN @adm_rv
						END
					),
					JUMLAH_BAYAR = 
					(
						JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA + 
						CASE TRX
							WHEN $trx_kv THEN @adm_kv
							WHEN $trx_bg THEN @adm_bg 
							WHEN $trx_hn THEN @adm_hn
							WHEN $trx_rv THEN @adm_rv
						END - 
						DISKON_AIR - DISKON_IPL 
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
	}
	elseif ($act == 'save_diskon')
	{
		try
		{
			$conn->begintrans();
			
			if (empty($cb_data)) {
				throw new Exception("Pilih jenis pembayaran.");
			}
			
			$cb_diskon_air_persen	= (isset($_REQUEST['cb_diskon_air_persen'])) ? $_REQUEST['cb_diskon_air_persen'] : array();
			$cb_diskon_ipl_persen	= (isset($_REQUEST['cb_diskon_ipl_persen'])) ? $_REQUEST['cb_diskon_ipl_persen'] : array();
			$ket_diskon_air			= (isset($_REQUEST['ket_diskon_air'])) ? $_REQUEST['ket_diskon_air'] : '';
			$ket_diskon_ipl			= (isset($_REQUEST['ket_diskon_ipl'])) ? $_REQUEST['ket_diskon_ipl'] : '';
			
			foreach ($cb_data as $i => $x)
			{
				$id = base64_decode($x);
				$list_idp[] = $x;
				
				$diskon_air_persen	= to_decimal($cb_diskon_air_persen[$i]);
				$diskon_ipl_persen	= to_decimal($cb_diskon_ipl_persen[$i]);
				
				$obj = $conn->Execute("
					SELECT 
						dbo.PTPS(PERIODE_TAG) AS PERIODE_TAG, 
						STATUS_POST_PB,
						STATUS_BAYAR,
						DISKON_AIR,
						DISKON_IPL,
						JUMLAH_AIR,
						JUMLAH_IPL
					FROM KWT_PEMBAYARAN_AI 
					WHERE ID_PEMBAYARAN = '$id' 
				");
				
				$periode_tag	= $obj->fields['PERIODE_TAG'];
				$status_post_pb	= $obj->fields['STATUS_POST_PB'];
				$status_bayar	= $obj->fields['STATUS_BAYAR'];
				
				if ($status_post_pb == '1') {
					$msg .= "\nTagihan periode \"$periode_tag\" sudah di-posting!.";
					continue;
				} elseif ($status_bayar == '1') {
					$msg .= "\nTagihan periode \"$periode_tag\" sudah dibayar.";
					continue;
				}
				
				$jumlah_air			= $obj->fields['JUMLAH_AIR'];
				$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
				$diskon_air			= $jumlah_air * ($diskon_air_persen / 100);
				$diskon_ipl			= $jumlah_ipl * ($diskon_ipl_persen / 100);
				
				$old_diskon_air		= $obj->fields['DISKON_AIR'];
				$old_diskon_ipl		= $obj->fields['DISKON_IPL'];
				
				if ($diskon_air != $old_diskon_air || $diskon_ipl != $old_diskon_ipl)
				{
					$query_diskon = '';
					
					if ($diskon_air != $old_diskon_air) {
						$query_diskon .= "
						,USER_DISKON_AIR = '$sess_id_user'
						,TGL_DISKON_AIR = GETDATE()
						,DISKON_AIR = '$diskon_air'
						,KET_DISKON_AIR = '$ket_diskon_air'";
					}
					
					if ($diskon_ipl != $old_diskon_ipl) {
						$query_diskon .= "
						,USER_DISKON_IPL = '$sess_id_user'
						,TGL_DISKON_IPL = GETDATE()
						,DISKON_IPL = '$diskon_ipl'
						,KET_DISKON_IPL = '$ket_diskon_ipl'";
					}
					
					$query = "
					UPDATE KWT_PEMBAYARAN_AI 
					SET 
						USER_MODIFIED = '$sess_id_user', MODIFIED_DATE = GETDATE() 
						$query_diskon
					WHERE
						ID_PEMBAYARAN = '$id'
					";
					
					ex_false($conn->Execute($query), $query);
					
					$msg .= "\nDiskon Tagihan periode \"$periode_tag\" berhasil disimpan.";
				}
			}
			
			$conn->committrans();
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
		
		$list_idp = implode('||', $list_idp);
	}
	
	close($conn);
	echo json_encode(array('act' => $act, 'msg' => $msg, 'error'=> $error, 'list_idp' => $list_idp));
	exit;
}
?>
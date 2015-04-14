<?php
require_once('../../../config/config.php');
die_login();
die_mod('B2');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;

$user_bayar	= (isset($_REQUEST['user_bayar'])) ? clean($_REQUEST['user_bayar']) : '';
$bayar_via	= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
$cb_data	= (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();
$vb_tgl		= (isset($_REQUEST['vb_tgl'])) ? $_REQUEST['vb_tgl'] : array();
$ket_bayar	= (isset($_REQUEST['ket_bayar'])) ? clean($_REQUEST['ket_bayar']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
		$list_id_sukses = array();
		$list_id_error = array();
		
		try
		{
			$conn->begintrans();
			
			ex_empty($cb_data, 'Pilih data yang akan disimpan.');
			ex_empty($bayar_via, 'Error. Kode bank, please contact developer.');
			ex_empty($vb_tgl, 'Error. Tanggal bayar, please contact developer.');
			
			$error_update = FALSE;
			
			foreach ($cb_data AS $x => $id_save)
			{
				$spl = $vb_tgl[$x];
				$a = explode(' ',$vb_tgl[$x]);
				$b = explode('-',$a[0]);
				$tgl_bayar_bank = $b[2].'-'.$b[1].'-'.$b[0].' '.$a[1];
				$tgl_bayar_bank = date('Y-m-d H:i:s', strtotime($tgl_bayar_bank));
				
				if ( ! checkdate($b[1], $b[0], $b[2]))
				{
					throw new Exception('Error, format tanggal bayar');
				}
				
				$query = "
				UPDATE
					KWT_PEMBAYARAN_AI 
				SET 
					STATUS_BAYAR = 1, 
					CARA_BAYAR = 4,
					BAYAR_VIA = '$bayar_via', 
					
					TGL_BAYAR_BANK = CONVERT(VARCHAR(19), '$tgl_bayar_bank', 20),
					TGL_BAYAR_SYS = GETDATE(), 
					USER_BAYAR = '$user_bayar', 
					KET_BAYAR = '$ket_bayar',
				
					JUMLAH_BAYAR = (JUMLAH_AIR + ABONEMEN + DENDA + JUMLAH_IPL - DISKON_AIR - DISKON_IPL), 
					
					USER_MODIFIED = '$sess_id_user', 
					MODIFIED_DATE = GETDATE() 
				WHERE
					STATUS_BAYAR = 0 AND
					NO_PELANGGAN = '$id_save'";
						
				if ($conn->Execute($query)) {
					$list_id_sukses[] = $id_save;
				} else {
					$error_update = TRUE;
					$list_id_error[] = $id_save;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error_update) ? 'Sebagian data gagal disimpan.' : 'Data pembayaran berhasil disimpan.';
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
			$error = TRUE;
			$list_id_sukses = array();
			$list_id_error = array();
			$conn->rollbacktrans();
		}

	close($conn);
	
	$json = array(
		'list_id_sukses' => $list_id_sukses,
		'list_id_error' => $list_id_error,
		'msg' => $msg, 
		'error'=> $error
	);
	echo json_encode($json);
	exit;
}
<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$bayar_melalui = (isset($_REQUEST['bayar_melalui'])) ? clean($_REQUEST['bayar_melalui']) : '';
$cb_data = (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : '';
$vb_tgl	= (isset($_REQUEST['vb_tgl'])) ? $_REQUEST['vb_tgl'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
		$list_id_sukses = array();
		$list_id_error = array();
		
		try
		{
			$conn->begintrans();
			
			ex_empty($cb_data, 'Pilih data yang akan disimpan.');
			ex_empty($bayar_melalui, 'Error. Kode bank, please contact developer.');
			ex_empty($vb_tgl, 'Error. Tanggal bayar, please contact developer.');
			
			$error_update = FALSE;
			
			foreach ($cb_data AS $x => $id_save)
			{
				$spl = $vb_tgl[$x];
				$a = explode(' ',$vb_tgl[$x]);
				$b = explode('-',$a[0]);
				$tgl_bayar = $b[2].'-'.$b[1].'-'.$b[0].' '.$a[1];
				$tgl_bayar = date('Y-m-d H:i:s', strtotime($tgl_bayar));
				
				if ( ! checkdate($b[1], $b[0], $b[2]))
				{
					throw new Exception('Error, format tanggal bayar');
				}
				
				$query = "
				UPDATE
					KWT_PEMBAYARAN_AI 
				SET 
					STATUS_BAYAR = '2', 
					JENIS_BAYAR = '4', 
					BAYAR_MELALUI = '$bayar_melalui', 
					TGL_TERIMA_BANK = GETDATE(), 
					JUMLAH_BAYAR = ((ISNULL(JUMLAH_AIR,0) + ISNULL(ABONEMEN,0) + ISNULL(DENDA,0) + ISNULL(JUMLAH_IPL,0)) - (ISNULL(DISKON_RUPIAH_AIR,0) + ISNULL(DISKON_RUPIAH_IPL,0))), 
					TGL_BAYAR = CONVERT(VARCHAR(19), '$tgl_bayar', 20)
				WHERE
					STATUS_BAYAR IS NULL AND
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
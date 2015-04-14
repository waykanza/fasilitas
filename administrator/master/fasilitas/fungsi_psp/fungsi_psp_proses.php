<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_fungsi	= (isset($_REQUEST['kode_fungsi'])) ? clean($_REQUEST['kode_fungsi']) : '';
$nama_fungsi	= (isset($_REQUEST['nama_fungsi'])) ? clean($_REQUEST['nama_fungsi']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_fungsi, 'Kode harus diisi.');
			ex_empty($nama_fungsi, 'Nama fungsi harus diisi.');
			
			$query = "SELECT KODE_FUNGSI FROM KWT_FUNGSI_PSP WHERE KODE_FUNGSI = '$kode_fungsi'";
			ex_found($conn->Execute($query)->recordcount(), "Kode fungsi \"$kode_fungsi\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_FUNGSI_PSP (KODE_FUNGSI, NAMA_FUNGSI)
			VALUES(
				'$kode_fungsi', 
				'$nama_fungsi'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Fungsi pembukaan sarana prasaranan lingkungan \"$nama_fungsi\" berhasil disimpan.";
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'Ubah') /* Proses Ubah */
	{
		try
		{
			$conn->begintrans();

			ex_empty($kode_fungsi, 'Kode harus diisi.');
			ex_empty($nama_fungsi, 'Nama fungsi harus diisi.');
			
			if ($kode_fungsi != $id)
			{
				$query = "SELECT KODE_FUNGSI FROM KWT_FUNGSI_PSP WHERE KODE_FUNGSI = '$kode_fungsi'";
				ex_found($conn->Execute($query)->recordcount(), "Kode fungsi \"$kode_fungsi\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_FUNGSI_PSP 
			SET 
				KODE_FUNGSI = '$kode_fungsi',
				NAMA_FUNGSI = '$nama_fungsi'
			WHERE
				KODE_FUNGSI = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Fungsi pembukaan sarana prasaranan lingkungan berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'delete') /* Proses Delete */
	{
		$act = array();
		$id_error = array();
		
		try
		{
			$conn->begintrans();
			
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{
				$query = "DELETE FROM KWT_FUNGSI_PSP WHERE KODE_FUNGSI = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data fungsi pembukaan sarana prasaranan lingkungan berhasil dihapus.';
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	
	close($conn);
	$json = array('act' => $act, 'msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}

if ($act == 'Ubah')
{
	$query = "
	SELECT *
	FROM KWT_FUNGSI_PSP
	WHERE KODE_FUNGSI = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_fungsi = $obj->fields['KODE_FUNGSI'];
	$nama_fungsi = $obj->fields['NAMA_FUNGSI'];
}
?>
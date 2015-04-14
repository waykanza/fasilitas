<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_lokasi	= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$nama_lokasi	= (isset($_REQUEST['nama_lokasi'])) ? clean($_REQUEST['nama_lokasi']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_lokasi, 'Kode harus diisi.');
			ex_empty($nama_lokasi, 'Lokasi harus diisi.');
			
			$query = "SELECT KODE_LOKASI FROM KWT_LOKASI_MP WHERE KODE_LOKASI = '$kode_lokasi'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_lokasi\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_LOKASI_MP (KODE_LOKASI, NAMA_LOKASI)
			VALUES(
				'$kode_lokasi',
				'$nama_lokasi'
			)";
			ex_false($conn->execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Lokasi media promosi \"$nama_lokasi\" berhasil disimpan.";
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

			ex_empty($kode_lokasi, 'Kode harus diisi.');
			ex_empty($nama_lokasi, 'Lokasi harus diisi.');
			
			if ($kode_lokasi != $id)
			{
				$query = "SELECT KODE_LOKASI FROM KWT_LOKASI_MP WHERE KODE_LOKASI = '$kode_lokasi'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_lokasi\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_LOKASI_MP 
			SET 
				KODE_LOKASI = '$kode_lokasi',
				NAMA_LOKASI = '$nama_lokasi'
			WHERE
				KODE_LOKASI = '$id'
			";
			
			ex_false($conn->execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Lokasi media promosi berhasil diubah.';
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
		
		try
		{
			$conn->begintrans();
			
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{
				$query = "DELETE FROM KWT_LOKASI_MP WHERE KODE_LOKASI = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data Lokasi media promosi berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_LOKASI_MP WHERE KODE_LOKASI = '$id'";
	$obj = $conn->execute($query);
	$kode_lokasi = $obj->fields['KODE_LOKASI'];
	$nama_lokasi = $obj->fields['NAMA_LOKASI'];
}
?>
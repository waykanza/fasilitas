<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_zona	= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
$nama_zona	= (isset($_REQUEST['nama_zona'])) ? clean($_REQUEST['nama_zona']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_zona, 'Kode harus diisi.');
			ex_empty($nama_zona, 'Rumah pompa harus diisi.');
			
			$query = "SELECT KODE_ZONA FROM KWT_ZONA_METER_BALANCE WHERE KODE_ZONA = '$kode_zona'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_zona\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_ZONA_METER_BALANCE (KODE_ZONA, NAMA_ZONA)
			VALUES(
				'$kode_zona',
				'$nama_zona'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Rumah pompa \"$nama_zona\" berhasil disimpan.";
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'Ubah') # Proses Ubah
	{
		try
		{
			$conn->begintrans();

			ex_empty($kode_zona, 'Kode harus diisi.');
			ex_empty($nama_zona, 'Rumah pompa harus diisi.');
			
			if ($kode_zona != $id)
			{
				$query = "SELECT KODE_ZONA FROM KWT_ZONA_METER_BALANCE WHERE KODE_ZONA = '$kode_zona'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_zona\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_ZONA_METER_BALANCE 
			SET 
				KODE_ZONA = '$kode_zona',
				NAMA_ZONA = '$nama_zona'
			WHERE
				KODE_ZONA = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Rumah pompa berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'delete') # Proses Delete
	{
		$act = array();
		
		try
		{
			$conn->begintrans();
			
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{
				$query = "DELETE FROM KWT_ZONA_METER_BALANCE WHERE KODE_ZONA = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data rumah pompa berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_ZONA_METER_BALANCE WHERE KODE_ZONA = '$id'";
	$obj = $conn->Execute($query);
	$kode_zona = $obj->fields['KODE_ZONA'];
	$nama_zona = $obj->fields['NAMA_ZONA'];
}
?>
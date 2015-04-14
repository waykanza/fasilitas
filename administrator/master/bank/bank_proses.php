<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_bank	= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
$nama_bank	= (isset($_REQUEST['nama_bank'])) ? clean($_REQUEST['nama_bank']) : '';
$cb_bank	= (isset($_REQUEST['cb_bank'])) ? clean($_REQUEST['cb_bank']) : '';
$alamat		= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_bank, 'Kode harus diisi.');
			ex_empty($nama_bank, 'Nama bank harus diisi.');
			
			$query = "SELECT KODE_BANK FROM KWT_BANK WHERE KODE_BANK = '$kode_bank'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_bank\" bank telah terdaftar.");
				
			$query = "INSERT INTO KWT_BANK (KODE_BANK, NAMA_BANK, CB_BANK, ALAMAT)
			VALUES(
				'$kode_bank', 
				'$nama_bank', 
				'$cb_bank',
				'$alamat'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Bank \"$nama_bank\" berhasil disimpan.";
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

			ex_empty($kode_bank, 'Kode harus diisi.');
			ex_empty($nama_bank, 'Nama bank harus diisi.');
			
			if ($kode_bank != $id)
			{
				$query = "SELECT KODE_BANK FROM KWT_BANK WHERE KODE_BANK = '$kode_bank'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_bank\" bank telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_BANK 
			SET 
				KODE_BANK = '$kode_bank',
				NAMA_BANK = '$nama_bank',
				CB_BANK = '$cb_bank',
				ALAMAT = '$alamat'
			WHERE
				KODE_BANK = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Bank berhasil diubah.';
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
				$query = "DELETE FROM KWT_BANK WHERE KODE_BANK = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data bank berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_BANK WHERE KODE_BANK = '$id'";
	$obj = $conn->Execute($query);
	$kode_bank	= $obj->fields['KODE_BANK'];
	$nama_bank	= $obj->fields['NAMA_BANK'];
	$cb_bank	= $obj->fields['CB_BANK'];
	$alamat		= $obj->fields['ALAMAT'];
}
?>
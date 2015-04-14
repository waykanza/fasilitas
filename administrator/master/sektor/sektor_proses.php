<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$nama_sektor	= (isset($_REQUEST['nama_sektor'])) ? clean($_REQUEST['nama_sektor']) : '';
$kode_pel		= (isset($_REQUEST['kode_pel'])) ? clean($_REQUEST['kode_pel']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_sektor, 'Kode harus diisi.');
			ex_empty($nama_sektor, 'Nama sektor harus diisi.');
			
			$query = "SELECT KODE_SEKTOR FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$kode_sektor'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_sektor\" sektor telah terdaftar.");
			
			$query = "SELECT NAMA_SEKTOR FROM KWT_SEKTOR WHERE NAMA_SEKTOR = '$nama_sektor'";
			ex_found($conn->Execute($query)->recordcount(), "Nama sektor \"$nama_sektor\" telah terdaftar.");
				
			$query = "INSERT INTO KWT_SEKTOR (KODE_SEKTOR, NAMA_SEKTOR, KODE_PEL)
			VALUES(
				'$kode_sektor', 
				'$nama_sektor', 
				'$kode_pel'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Sektor \"$nama_sektor\" berhasil disimpan.";
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

			ex_empty($kode_sektor, 'Kode harus diisi.');
			ex_empty($nama_sektor, 'Nama sektor harus diisi.');
			
			if ($kode_sektor != $id)
			{
				$query = "SELECT KODE_SEKTOR FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$kode_sektor'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_sektor\" sektor telah terdaftar.");
			}
			
			$old_nama_sektor = $conn->Execute("SELECT NAMA_SEKTOR FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$id'")->fields['NAMA_SEKTOR'];
			if ($nama_sektor != $old_nama_sektor)
			{
				$query = "SELECT NAMA_SEKTOR FROM KWT_SEKTOR WHERE NAMA_SEKTOR = '$nama_sektor'";
				ex_found($conn->Execute($query)->recordcount(), "Nama sektor \"$nama_sektor\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_SEKTOR 
			SET 
				KODE_SEKTOR = '$kode_sektor',
				NAMA_SEKTOR = '$nama_sektor',
				KODE_PEL = '$kode_pel'
			WHERE
				KODE_SEKTOR = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Sektor berhasil diubah.';
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
				$query = "DELETE FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data sektor berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$id'";
	$obj = $conn->Execute($query);
	$kode_sektor	= $obj->fields['KODE_SEKTOR'];
	$nama_sektor	= $obj->fields['NAMA_SEKTOR'];
	$kode_pel		= $obj->fields['KODE_PEL'];
}
?>
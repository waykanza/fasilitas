<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$nama_tipe		= (isset($_REQUEST['nama_tipe'])) ? clean($_REQUEST['nama_tipe']) : '';
$status_blok	= (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';
$golongan		= (isset($_REQUEST['golongan'])) ? clean($_REQUEST['golongan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_tipe, 'Kode harus diisi.');
			ex_empty($nama_tipe, 'Kategori IPL harus diisi.');
			ex_empty($status_blok, 'Pilih Status blok.');
			ex_empty($golongan, 'Pilih golongan.');
			
			$query = "SELECT KODE_TIPE FROM KWT_TIPE_IPL WHERE KODE_TIPE = '$kode_tipe'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_tipe\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TIPE_IPL (KODE_TIPE, NAMA_TIPE, STATUS_BLOK, GOLONGAN)
			VALUES(
				'$kode_tipe',
				'$nama_tipe',
				'$status_blok',
				'$golongan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Kategori IPL \"$nama_tipe\" berhasil disimpan.";
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

			ex_empty($kode_tipe, 'Kode harus diisi.');
			ex_empty($nama_tipe, 'Kategori IPL harus diisi.');
			ex_empty($status_blok, 'Pilih Status blok.');
			ex_empty($golongan, 'Pilih golongan.');
			
			if ($kode_tipe != $id)
			{
				$query = "SELECT KODE_TIPE FROM KWT_TIPE_IPL WHERE KODE_TIPE = '$kode_tipe'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_tipe\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TIPE_IPL 
			SET 
				KODE_TIPE = '$kode_tipe',
				NAMA_TIPE = '$nama_tipe',
				STATUS_BLOK = '$status_blok',
				GOLONGAN = '$golongan'
			WHERE
				KODE_TIPE = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Kategori IPL berhasil diubah.';
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
				$query = "DELETE FROM KWT_TIPE_IPL WHERE KODE_TIPE = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data Kategori IPL berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_TIPE_IPL WHERE KODE_TIPE = '$id'";
	$obj = $conn->Execute($query);
	
	$kode_tipe		= $obj->fields['KODE_TIPE'];
	$nama_tipe		= $obj->fields['NAMA_TIPE'];
	$status_blok	= $obj->fields['STATUS_BLOK'];
	$golongan		= $obj->fields['GOLONGAN'];
}
?>
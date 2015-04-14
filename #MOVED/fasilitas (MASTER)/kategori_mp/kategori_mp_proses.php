<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
$kode_mp = (isset($_REQUEST['kode_mp'])) ? clean($_REQUEST['kode_mp']) : '';

$kode_tipe	= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$nama_tipe	= (isset($_REQUEST['nama_tipe'])) ? clean($_REQUEST['nama_tipe']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_mp, 'Something wrong, call MSI!');
			ex_empty($kode_tipe, 'Kode harus diisi.');
			ex_empty($nama_tipe, 'Kategori harus diisi.');
			
			$kode_tipe = $kode_mp . $kode_tipe;
			
			$query = "SELECT KODE_TIPE FROM KWT_TIPE_MP WHERE KODE_TIPE = '$kode_tipe'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_tipe\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TIPE_MP (KODE_TIPE, KODE_MP, NAMA_TIPE)
			VALUES(
				'$kode_tipe',
				'$kode_mp',
				'$nama_tipe'
			)";
			ex_false($conn->execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Kategori media promosi \"$nama_tipe\" berhasil disimpan.";
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

			ex_empty($kode_mp, 'Something wrong, call MSI!');
			ex_empty($kode_tipe, 'Kode harus diisi.');
			ex_empty($nama_tipe, 'Kategori harus diisi.');
			
			$kode_tipe = $kode_mp . $kode_tipe;
			
			if ($kode_tipe != $id)
			{
				$query = "SELECT KODE_TIPE FROM KWT_TIPE_MP WHERE KODE_TIPE = '$kode_tipe'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$kode_tipe\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TIPE_MP 
			SET 
				KODE_TIPE = '$kode_tipe',
				KODE_MP = '$kode_mp',
				NAMA_TIPE = '$nama_tipe'
			WHERE
				KODE_TIPE = '$id'
			";
			
			ex_false($conn->execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Kategori media promosi berhasil diubah.';
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
				$query = "DELETE FROM KWT_TIPE_MP WHERE KODE_TIPE = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data Kategori media promosi berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_TIPE_MP WHERE KODE_TIPE = '$id'";
	$obj = $conn->execute($query);
	$kode_tipe = substr($obj->fields['KODE_TIPE'], 1, 2);
	$nama_tipe = $obj->fields['NAMA_TIPE'];
}
?>
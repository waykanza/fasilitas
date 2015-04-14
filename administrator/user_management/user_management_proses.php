<?php
require_once('../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$id_user	= (isset($_REQUEST['id_user'])) ? clean($_REQUEST['id_user']) : '';
$nama_user	= (isset($_REQUEST['nama_user'])) ? clean($_REQUEST['nama_user']) : '';
$pass_user	= (isset($_REQUEST['pass_user'])) ? clean($_REQUEST['pass_user']) : '';
$conf_pass_user	= (isset($_REQUEST['conf_pass_user'])) ? clean($_REQUEST['conf_pass_user']) : '';
$aktif_user	= (isset($_REQUEST['aktif_user'])) ? clean($_REQUEST['aktif_user']) : '';
$id_modul_ary = (isset($_REQUEST['id_modul_ary'])) ? $_REQUEST['id_modul_ary'] : array();
$list_aktif_modul = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($id_user, 'Id harus diisi.');
			ex_empty($nama_user, 'Nama harus diisi.');
			ex_empty($pass_user, 'Pass harus diisi.');
			ex_empty($aktif_user, 'Status aktif harus diisi.');
			
			if ($pass_user != $conf_pass_user) {
				throw new Exception('Password tidak sama dengan Conf. Password');
			}
			
			$query = "SELECT ID_USER FROM KWT_USER WHERE ID_USER = '$id_user'";
			ex_found($conn->Execute($query)->recordcount(), "Kode \"$id_user\" user telah terdaftar.");
			
			$query = "INSERT INTO KWT_USER (ID_USER, NAMA_USER, PASS_USER, AKTIF_USER)
			VALUES('$id_user', '$nama_user', '$pass_user', '$aktif_user')";
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("DELETE FROM KWT_USER_MODUL WHERE ID_USER = '$id_user'");
			foreach ($id_modul_ary as $id_modul) {
				ex_false($conn->Execute("INSERT INTO KWT_USER_MODUL (ID_USER, ID_MODUL) VALUES ('$id_user', '$id_modul')"), 'Error Insert Modul!');
			}
			
			$conn->committrans();
			
			$msg = "User \"$nama_user\" berhasil disimpan.";
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

			ex_empty($id_user, 'Id harus diisi.');
			ex_empty($nama_user, 'Nama harus diisi.');
			ex_empty($aktif_user, 'Status aktif harus diisi.');
			
			if ($id_user != $id)
			{
				$query = "SELECT ID_USER FROM KWT_USER WHERE ID_USER = '$id_user'";
				ex_found($conn->Execute($query)->recordcount(), "Kode \"$id_user\" user telah terdaftar.");
			}
			
			$query_pass = '';
			if ($pass_user != '' || $conf_pass_user != '') {
				
				if ($pass_user != $conf_pass_user) {
					throw new Exception('Password tidak sama dengan Conf. Password');
				} else {
					$query_pass = " PASS_USER = '$pass_user', ";
				}
			}
			
			$query = "
			UPDATE KWT_USER 
			SET 
				ID_USER = '$id_user',
				$query_pass
				NAMA_USER = '$nama_user',
				AKTIF_USER = '$aktif_user'
			WHERE
				ID_USER = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("DELETE FROM KWT_USER_MODUL WHERE ID_USER = '$id_user'");
			foreach ($id_modul_ary as $id_modul) {
				ex_false($conn->Execute("INSERT INTO KWT_USER_MODUL (ID_USER, ID_MODUL) VALUES ('$id_user', '$id_modul')"), 'Error Insert Modul!');
			}
			
			$conn->committrans();
			
			$msg = 'User berhasil diubah.';
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
				$query = "DELETE FROM KWT_USER WHERE ID_USER = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data user berhasil dihapus.';
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
	$query		= "SELECT * FROM KWT_USER WHERE ID_USER = '$id'";
	$obj		= $conn->Execute($query);
	$id_user	= $obj->fields['ID_USER'];
	$nama_user	= $obj->fields['NAMA_USER'];
	$aktif_user	= $obj->fields['AKTIF_USER'];
	
	$obj = $conn->Execute("SELECT ID_MODUL FROM KWT_USER_MODUL WHERE ID_USER = '$id'");
	
	while( ! $obj->EOF) {
		$list_aktif_modul[] = $obj->fields['ID_MODUL'];
		$obj->movenext();
	}
}
?>
<?php
require_once('../../../config/config.php');
die_login();
die_mod('U8');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$login_user			= (isset($_REQUEST['login_user'])) ? clean($_REQUEST['login_user']) : '';
$nama_user			= (isset($_REQUEST['nama_user'])) ? clean($_REQUEST['nama_user']) : '';
$pass_user			= (isset($_REQUEST['pass_user'])) ? clean($_REQUEST['pass_user']) : '';
$conf_pass_user		= (isset($_REQUEST['conf_pass_user'])) ? clean($_REQUEST['conf_pass_user']) : '';
$aktif_user			= (isset($_REQUEST['aktif_user'])) ? clean($_REQUEST['aktif_user']) : '';
$id_modul_ary		= (isset($_REQUEST['id_modul_ary'])) ? $_REQUEST['id_modul_ary'] : array();
$list_aktif_modul	= array();

$ro_login_user			= '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($login_user, 'Id harus diisi.');
			ex_empty($nama_user, 'Nama harus diisi.');
			ex_empty($pass_user, 'Pass harus diisi.');
			ex_empty($aktif_user, 'Status aktif harus diisi.');
			
			if (preg_match('/[^a-zA-z0-9]/i', $login_user)) {
				throw new Exception('Karakter ID : [a-z] [A-z] [0-9]');
			}
			if (preg_match('/[^a-zA-z0-9]/i', $pass_user)) {
				throw new Exception('Karakter password : [a-z] [A-z] [0-9]');
			}
			
			if ($pass_user != $conf_pass_user) {
				throw new Exception('Password tidak sama dengan Conf. Password');
			}
			
			$login_user = strtoupper($login_user);
			$pass_user = md5(strtoupper($pass_user));
			
			$query = "SELECT COUNT(LOGIN_USER) AS TOTAL FROM KWT_USER WHERE LOGIN_USER = '$login_user'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode \"$login_user\" user telah terdaftar.");
			
			$query = "INSERT INTO KWT_USER (LOGIN_USER, NAMA_USER, PASS_USER, AKTIF_USER, USER_CREATED)
			VALUES('$login_user', '$nama_user', '$pass_user', '$aktif_user', '$sess_id_user')";
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("DELETE FROM KWT_USER_MODUL WHERE ID_USER = '$id'");
			foreach ($id_modul_ary as $id_modul) {
				ex_false($conn->Execute("
				INSERT INTO KWT_USER_MODUL (ID_USER, ID_MODUL, USER_CREATED) 
				VALUES ('$id', '$id_modul', '$sess_id_user') "), 'Error Insert Modul!');
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

			ex_empty($nama_user, 'Nama harus diisi.');
			ex_empty($aktif_user, 'Status aktif harus diisi.');
			
			$query_pass = '';
			if ($pass_user != '' || $conf_pass_user != '') {
				
				if ($pass_user != $conf_pass_user) {
					throw new Exception('Password tidak sama dengan Conf. Password');
				} else {
					if (preg_match('/[^a-zA-z0-9]/i', $pass_user)) {
						throw new Exception('Karakter password : [a-z] [A-z] [0-9]');
					}
					
					$pass_user = md5(strtoupper($pass_user));
					
					$query_pass = " PASS_USER = '$pass_user', ";
				}
			}
			
			$query = "
			UPDATE KWT_USER 
			SET 
				$query_pass
				LOGIN_USER = '$login_user',
				NAMA_USER = '$nama_user',
				AKTIF_USER = '$aktif_user', 
				
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				ID_USER = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("DELETE FROM KWT_USER_MODUL WHERE ID_USER = '$id'");
			foreach ($id_modul_ary as $id_modul) {
				ex_false($conn->Execute("
				INSERT INTO KWT_USER_MODUL (ID_USER, ID_MODUL, USER_CREATED) 
				VALUES ('$id', '$id_modul', '$sess_id_user') "), 'Error Insert Modul!');
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
				$query = "DELETE FROM KWT_USER WHERE LOGIN_USER = '$id_del'";
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
	$login_user	= $obj->fields['LOGIN_USER'];
	$nama_user	= $obj->fields['NAMA_USER'];
	$aktif_user	= $obj->fields['AKTIF_USER'];
	
	$obj = $conn->Execute("SELECT ID_MODUL FROM KWT_USER_MODUL WHERE ID_USER = '$id'");
	
	while( ! $obj->EOF) {
		$list_aktif_modul[] = $obj->fields['ID_MODUL'];
		$obj->movenext();
	}
	
	$ro_login_user = 'readonly="readonly"';
}
?>
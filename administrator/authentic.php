<?php 
require_once('../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$login_user	= (isset($_REQUEST['login_user'])) ? clean($_REQUEST['login_user']) : '';
$pass_user	= (isset($_REQUEST['pass_user'])) ? clean($_REQUEST['pass_user']) : '';
$do			= (isset($_REQUEST['do'])) ? clean($_REQUEST['do']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'AND $do == 'login')
{
	try
		{
			$conn->begintrans();
			ex_empty($login_user, 'Masukkan Id user.');
			ex_empty($pass_user, 'Masukkan Password.');
			
			$login_user = strtoupper($login_user);
			$pass_user = md5(strtoupper($pass_user));
			
			$id_user = '';
			$nama_user = '';
			$id_modul = array();
			
			$query = "SELECT COUNT(ID_USER) AS TOTAL FROM KWT_USER WHERE LOGIN_USER = '$login_user' AND PASS_USER = '$pass_user' AND AKTIF_USER = 1";
			if ($conn->Execute($query)->fields['TOTAL'] != 1)
			{
				throw new Exception('User id dan Password tidak terdaftar.');
			}
			else
			{
				$obj = $conn->Execute("SELECT * FROM KWT_USER WHERE LOGIN_USER = '$login_user' AND PASS_USER = '$pass_user' AND AKTIF_USER = 1");
				$id_user = $obj->fields['ID_USER'];
				$nama_user = $obj->fields['NAMA_USER'];
			}
			
			$obj = $conn->Execute("SELECT ID_MODUL FROM KWT_USER_MODUL WHERE ID_USER = '$id_user'");
	
			while( ! $obj->EOF) {
				$id_modul[] = $obj->fields['ID_MODUL'];
				
				$obj->movenext();
			}
			
			$conn->committrans();
			
			#============ SESSION PROTOTYPE ==========
			$_SESSION['ID_USER']	= $id_user;
			$_SESSION['NAMA_USER']	= $nama_user;
			$_SESSION['ID_MODUL']	= $id_modul;
			
			$msg = 'Login sukses.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	
	close($conn);
	$json = array('msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}
elseif ($do == 'logout') {
	session_destroy();
	header('location: ' . BASE_URL);
}

?>
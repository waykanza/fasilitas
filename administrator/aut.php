<?php 
require_once('../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$id_user	= (isset($_REQUEST['id_user'])) ? clean($_REQUEST['id_user']) : '';
$pass_user	= (isset($_REQUEST['pass_user'])) ? clean($_REQUEST['pass_user']) : '';
$do			= (isset($_REQUEST['do'])) ? clean($_REQUEST['do']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST'AND $do == 'login')
{
	try
		{
			$conn->begintrans();
			ex_empty($id_user, 'Masukkan Id user.');
			ex_empty($pass_user, 'Masukkan Password.');
			
			$query = "SELECT COUNT(ID_USER) AS TOTAL FROM KWT_USER WHERE ID_USER = '$id_user' AND PASS_USER = '$pass_user' AND AKTIF_USER = '1'";
			if ($conn->Execute($query)->fields['TOTAL'] != 1) {
				throw new Exception('User id dan Password tidak terdaftar.');
			} else {
				$query = "SELECT * FROM KWT_USER WHERE ID_USER = '$id_user' AND PASS_USER = '$pass_user' AND AKTIF_USER = '1'";
				$dt = $conn->Execute($query);
			}
			
			$obj = $conn->Execute("SELECT ID_MODUL FROM KWT_USER_MODUL WHERE ID_USER = '$id_user'");
	
			$id_modul = array();
			while( ! $obj->EOF) {
				$id_modul[] = $obj->fields['ID_MODUL'];
				$obj->movenext();
			}
			
			$conn->committrans();
			
			#============ SESSION PROTOTYPE ==========
			$readonly = 'readonly="readonly"';
			$_SESSION['ID_USER'] = $dt->fields['ID_USER'];
			$_SESSION['NAMA_USER'] = $dt->fields['NAMA_USER'];
			$_SESSION['ID_MODUL'] = $id_modul;
			
			header('location: ' . BASE_URL . 'administrator');
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	
	echo $msg;
}
elseif ($do == 'logout') {
	session_destroy();
	header('location: ' . BASE_URL);
}

?>
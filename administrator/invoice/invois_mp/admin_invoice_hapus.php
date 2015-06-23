<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$id_pembayaran			= (isset($_REQUEST['id_pembayaran'])) ? to_number($_REQUEST['id_pembayaran']) : '';
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		$query = "DELETE FROM PELANGGAN_MP WHERE ID_PEMBAYARAN = $id_pembayaran";

		ex_false($conn->Execute($query), $query);
		$conn->committrans();	
		$msg = 'Data invoice media promosi berhasil dihapus';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}
}

close($conn);
$json = array('msg' => $msg, 'error'=> $error);
echo json_encode($json);
exit;
?>
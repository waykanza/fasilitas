<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$id_pembayaran	= (isset($_REQUEST['id_pembayaran'])) ? clean($_REQUEST['id_pembayaran']) : '';
$id = '1';
$periode_awal			= (isset($_REQUEST['periode_awal-'.$id])) ? clean($_REQUEST['periode_awal-'.$id]) : '';
$periode_awal = date("m-Y", strtotime($periode_awal));
$period = explode('-', $periode_awal);

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		/*
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		ex_empty($no_va, 'No va harus diisi.');		
		ex_empty($no_hp, 'No HP harus diisi.');
		ex_empty($alamat, 'Alamat harus diisi.'); 
		ex_empty($kode_mp, 'Media Promosi harus diisi.');
		ex_empty($kode_tipe, 'Kategori harus diisi.');
		ex_empty($kode_lokasi, 'Lokasi harus diisi.');
		//ex_empty($pembayaran, 'Cara Pembayaran harus diisi.');

		ex_empty($periode_awal, 'Periode Awal harus diisi.');
		ex_empty($periode_akhir, 'Periode Akhir harus diisi.');
		ex_empty($total, 'Total harus diisi.');
		*/
		
		$query = "
		DELETE from PELANGGAN_PKL where  ID_PEMBAYARAN = $id_pembayaran and MONTH(PERIODE_AWAL) = $period[0] and YEAR(PERIODE_AWAL) = $period[1];
		";

		ex_false($conn->Execute($query), $query);
		$conn->committrans();	
		$msg = 'Data invoice media promosi berhasil dihapus.';
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
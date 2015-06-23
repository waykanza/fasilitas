<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$no_va			= (isset($_REQUEST['no_va'])) ? clean($_REQUEST['no_va']) : '';
$nama_pelanggan	= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
$npwp			= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
$no_telepon		= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
//$no_hp			= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
$alamat			= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';

$kode_mp		= (isset($_REQUEST['kode_mp'])) ? clean($_REQUEST['kode_mp']) : '';
$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$kode_lokasi	= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$key_mpd		= (isset($_REQUEST['key_mpd'])) ? clean($_REQUEST['key_mpd']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';
$periode		= (isset($_REQUEST['periode'])) ? clean($_REQUEST['periode']) : '';
$id_pembayaran		= (isset($_REQUEST['id_pembayaran'])) ? clean($_REQUEST['id_pembayaran']) : '';

$id=1;
$periode_awal			= (isset($_REQUEST['periode_awal-'.$id])) ? clean($_REQUEST['periode_awal-'.$id]) : '';
$periode_akhir			= (isset($_REQUEST['periode_akhir-'.$id])) ? clean($_REQUEST['periode_akhir-'.$id]) : '';
$persen_nilai_tambah	= (isset($_REQUEST['persen_nilai_tambah-'.$id])) ? to_decimal($_REQUEST['persen_nilai_tambah-'.$id]) : '';
$nilai_tambah			= (isset($_REQUEST['nilai_tambah-'.$id])) ? to_decimal($_REQUEST['nilai_tambah-'.$id]) : '';
$persen_nilai_kurang	= (isset($_REQUEST['persen_nilai_kurang-'.$id])) ? to_decimal($_REQUEST['persen_nilai_kurang-'.$id]) : '';
$nilai_kurang			= (isset($_REQUEST['nilai_kurang-'.$id])) ? to_decimal($_REQUEST['nilai_kurang-'.$id]) : '';
$total					= (isset($_REQUEST['total-'.$id])) ? to_decimal($_REQUEST['total-'.$id]) : '';
$persen_ppn =10;
$nilai_ppn				= ($total * $persen_ppn) / 100;
$total_bayar			= $total + $nilai_ppn;
//$pembayaran		= (isset($_REQUEST['pembayaran'])) ? to_number($_REQUEST['pembayaran']) : '';
//$max	= (isset($_REQUEST['max'])) ? to_number($_REQUEST['max']) : '';

//$tarif2			= (isset($_REQUEST['tarif2'])) ? to_number($_REQUEST['tarif2']) : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		ex_empty($no_va, 'No va harus diisi.');		
		//ex_empty($no_hp, 'No HP harus diisi.');
		ex_empty($alamat, 'Alamat harus diisi.'); 
		//ex_empty($kode_mp, 'Media Promosi harus diisi.');
		//ex_empty($kode_tipe, 'Kategori harus diisi.');
		//ex_empty($kode_lokasi, 'Lokasi harus diisi.');
		//ex_empty($pembayaran, 'Cara Pembayaran harus diisi.');

		//ex_empty($periode_awal, 'Periode Awal harus diisi.');
		//ex_empty($periode_akhir, 'Periode Akhir harus diisi.');
		//ex_empty($total, 'Total harus diisi.');
		
		$query = "
		UPDATE PELANGGAN_MP SET NAMA_PELANGGAN = '$nama_pelanggan', NO_TELEPON = '$no_telepon', ALAMAT = '$alamat', NPWP = '$npwp' WHERE ID_PEMBAYARAN = $id_pembayaran
			
		";

		ex_false($conn->Execute($query), $query);
		$conn->committrans();	
		$msg = 'Data periode media promosi berhasil disimpan.';
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
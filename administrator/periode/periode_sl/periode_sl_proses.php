<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$no_ktp			= (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
$nama_pelanggan	= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
$npwp			= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
$no_telepon		= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
$no_hp			= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
$alamat			= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';
$lokasi			= (isset($_REQUEST['lokasi'])) ? clean($_REQUEST['lokasi']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';
$pembayaran		= (isset($_REQUEST['pembayaran'])) ? to_number($_REQUEST['pembayaran']) : '';
$max	= (isset($_REQUEST['max'])) ? to_number($_REQUEST['max']) : '';

$tarif2			= (isset($_REQUEST['tarif2'])) ? to_number($_REQUEST['tarif2']) : '';


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		/*ex_empty($no_ktp, 'No KTP harus diisi.');
		
		ex_empty($no_hp, 'No HP harus diisi.');
		ex_empty($alamat, 'Alamat harus diisi.'); */
		ex_empty($lokasi, 'Lokasi harus diisi.');
		ex_empty($tarif, 'Tarif harus diisi.');
		ex_empty($pembayaran, 'Cara Pembayaran harus diisi.');

		$query = "
		INSERT INTO KWT_PELANGGAN_SL(
			NO_KTP, NAMA_PELANGGAN, NPWP, NO_TELEPON, NO_HP, ALAMAT, 
			LOKASI, TARIF, KETERANGAN, KASIR, 
			PEMBAYARAN, JUMLAH_PERIODE)
		VALUES (
			'$no_ktp', '$nama_pelanggan', '$npwp', '$no_telepon', '$no_hp', '$alamat', 
			'$lokasi', $tarif, '$keterangan', '$kasir', 
			$pembayaran, $max)
		";
		ex_false($conn->Execute($query), $query);
		
		$query 	= "SELECT MAX(NO_PELANGGAN) AS ID_REF FROM KWT_PELANGGAN_SL";
		$obj 	= $conn->Execute($query);
		$id_ref	= $obj->fields['ID_REF'];

		$persen_ppn =10;
		
		$id=1;
		while($id<=$max) {
			$periode_awal			= (isset($_REQUEST['periode_awal-'.$id])) ? clean($_REQUEST['periode_awal-'.$id]) : '';
			$periode_akhir			= (isset($_REQUEST['periode_akhir-'.$id])) ? clean($_REQUEST['periode_akhir-'.$id]) : '';
			$persen_nilai_tambah	= (isset($_REQUEST['persen_nilai_tambah-'.$id])) ? to_decimal($_REQUEST['persen_nilai_tambah-'.$id]) : '';
			$nilai_tambah			= (isset($_REQUEST['nilai_tambah-'.$id])) ? to_decimal($_REQUEST['nilai_tambah-'.$id]) : '';
			$persen_nilai_kurang	= (isset($_REQUEST['persen_nilai_kurang-'.$id])) ? to_decimal($_REQUEST['persen_nilai_kurang-'.$id]) : '';
			$nilai_kurang			= (isset($_REQUEST['nilai_kurang-'.$id])) ? to_decimal($_REQUEST['nilai_kurang-'.$id]) : '';
			$total					= (isset($_REQUEST['total-'.$id])) ? to_decimal($_REQUEST['total-'.$id]) : '';
			$nilai_ppn				= ($total * $persen_ppn) / 100;
			$total_bayar			= $total + $nilai_ppn;
			$id++;
			
			ex_empty($periode_awal, 'Periode Awal harus diisi.');
			ex_empty($periode_akhir, 'Periode Akhir harus diisi.');
			ex_empty($total, 'Total harus diisi.');
			
			$query = "
			INSERT INTO KWT_PEMBAYARAN_SL (
				NO_PELANGGAN, PERIODE_AWAL, PERIODE_AKHIR, 
				PERSEN_PPN, NILAI_PPN, PERSEN_NILAI_TAMBAH, NILAI_TAMBAH, PERSEN_NILAI_KURANG, NILAI_KURANG, TOTAL, TOTAL_BAYAR) 
			VALUES (
				$id_ref, CONVERT(DATETIME,'$periode_awal',105), CONVERT(DATETIME,'$periode_akhir',105),
				$persen_ppn, $nilai_ppn, $persen_nilai_tambah, $nilai_tambah, $persen_nilai_kurang, $nilai_kurang, $total, $total_bayar)
				
			";
			ex_false($conn->Execute($query), $query);
		}
			
		$conn->committrans();	
		$msg = 'Data periode sewa lahan berhasil disimpan.';
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
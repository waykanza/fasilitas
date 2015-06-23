<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$no_va			= (isset($_REQUEST['no_va'])) ? clean($_REQUEST['no_va']) : '';
//$no_ktp			= (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
$nama_pelanggan	= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
$npwp			= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
$no_telepon		= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
//$no_hp			= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
$alamat			= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';

$lokasi			= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
//$kategori		= (isset($_REQUEST['kategori'])) ? to_number($_REQUEST['kategori']) : '';
$uang_pangkal	= (isset($_REQUEST['uang_pangkal'])) ? to_number($_REQUEST['uang_pangkal']) : '';
$periode		= (isset($_REQUEST['periode'])) ? to_number($_REQUEST['periode']) : '';
$kode_blok 		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$luas 			= (isset($_REQUEST['luas'])) ? to_number($_REQUEST['luas']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$key_pkl 		= (isset($_REQUEST['key_pkl'])) ? clean($_REQUEST['key_pkl']) : '';
$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';
$id = 1 ;
$periode_awal			= (isset($_REQUEST['periode_awal-'.$id])) ? clean($_REQUEST['periode_awal-'.$id]) : '';
$t_periode = explode('-', $periode_awal);
$bln_periode = $t_periode[1];

$periode_akhir			= (isset($_REQUEST['periode_akhir-'.$id])) ? clean($_REQUEST['periode_akhir-'.$id]) : '';
$persen_nilai_tambah	= (isset($_REQUEST['persen_nilai_tambah-'.$id])) ? to_decimal($_REQUEST['persen_nilai_tambah-'.$id]) : '';
$nilai_tambah			= (isset($_REQUEST['nilai_tambah-'.$id])) ? to_decimal($_REQUEST['nilai_tambah-'.$id]) : '';
$persen_nilai_kurang	= (isset($_REQUEST['persen_nilai_kurang-'.$id])) ? to_decimal($_REQUEST['persen_nilai_kurang-'.$id]) : '';
$nilai_kurang			= (isset($_REQUEST['nilai_kurang-'.$id])) ? to_decimal($_REQUEST['nilai_kurang-'.$id]) : '';
$total					= (isset($_REQUEST['total-'.$id])) ? to_decimal($_REQUEST['total-'.$id]) : '';
$persen_ppn = 0;
$nilai_ppn				= ($total * $persen_ppn) / 100;
$total_bayar			= $total + $nilai_ppn;
$pembayaran 			=1;
//$pembayaran		= (isset($_REQUEST['pembayaran'])) ? to_number($_REQUEST['pembayaran']) : '';
//$max	= (isset($_REQUEST['max'])) ? to_number($_REQUEST['max']) : '';

//$tarif2			= (isset($_REQUEST['tarif2'])) ? to_number($_REQUEST['tarif2']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		/*
		ex_empty($no_ktp, 'No KTP harus diisi.');
		
		ex_empty($no_hp, 'No HP harus diisi.');
		*/
		ex_empty($alamat, 'Alamat harus diisi.'); 
		
		ex_empty($no_va, 'No VA harus diisi');
		ex_empty($lokasi, 'Lokasi harus diisi.');
		ex_empty($tarif, 'Tarif harus diisi.');
		ex_empty($pembayaran, 'Cara Pembayaran harus diisi.');
		ex_empty($periode_awal, 'Periode Awal harus diisi.');
		ex_empty($periode_akhir, 'Periode Akhir harus diisi.');
		ex_empty($total, 'Total harus diisi.');
		ex_empty($uang_pangkal, 'Total harus diisi.');
		ex_empty($periode, 'Total harus diisi.');
		ex_empty($uang_pangkal, 'Total harus diisi.');
		ex_empty($kode_blok, 'Total harus diisi.');
		ex_empty($uang_pangkal, 'Total harus diisi.');
		
		$query = "(SELECT CASE WHEN (SELECT MAX(ID_PEMBAYARAN) FROM PELANGGAN_PKL) IS NULL THEN '1' ELSE (SELECT ID_PEMBAYARAN = MAX(CASE WHEN ID_PEMBAYARAN IS NULL THEN 0 ELSE ID_PEMBAYARAN END)+1 FROM PELANGGAN_PKL)END AS ID_PEMBAYARAN)";
		$id_bayar = $conn->Execute($query);
		$id_pembayaran = $id_bayar->fields['ID_PEMBAYARAN'];

		$query = "
		UPDATE PELANGGAN_PKL SET STATUS = '0' WHERE NO_PELANGGAN = '$no_va'";
		ex_false($conn->Execute($query), $query);

		$query = "
		INSERT INTO PELANGGAN_PKL(
			NO_PELANGGAN, NAMA_PELANGGAN, NPWP, NO_TELEPON, ALAMAT, 
			KODE_LOKASI, TARIF, KETERANGAN, KASIR, 
			PEMBAYARAN, JUMLAH_PERIODE, KEY_PKL, UANG_PANGKAL,
			LUAS, KODE_BLOK, PERIODE_AWAL, PERIODE_AKHIR, 
			PERSEN_PPN, NILAI_PPN, PERSEN_NILAI_TAMBAH, NILAI_TAMBAH, 
			PERSEN_NILAI_KURANG, NILAI_KURANG, TOTAL, TOTAL_BAYAR,
			STATUS, ID_PEMBAYARAN,STATUS_BAYAR
			)
		VALUES (
			'$no_va', '$nama_pelanggan', '$npwp', '$no_telepon', '$alamat', 
			'$lokasi', $tarif, '$keterangan', '$kasir', 
			$bln_periode, $periode, '$key_pkl', $uang_pangkal,
			$luas, '$kode_blok', CONVERT(DATETIME,'$periode_awal',105), CONVERT(DATETIME,'$periode_akhir',105),
			$persen_ppn, $nilai_ppn, $persen_nilai_tambah, $nilai_tambah, 
			$persen_nilai_kurang, $nilai_kurang, $total, $total_bayar,
			'1',$id_pembayaran,0)
		";

		ex_false($conn->Execute($query), $query);
		for($a=1;$a<$periode;$a++){
			$bln_periode++;
			if($bln_periode>12){
				$bln_periode = 1;
			}
			$query = "
		INSERT INTO PELANGGAN_PKL(
			NO_PELANGGAN, NAMA_PELANGGAN, NPWP, NO_TELEPON, ALAMAT, 
			KODE_LOKASI, TARIF, KETERANGAN, KASIR, 
			PEMBAYARAN, JUMLAH_PERIODE, KEY_PKL, UANG_PANGKAL,
			LUAS, KODE_BLOK, PERIODE_AWAL, PERIODE_AKHIR, 
			PERSEN_PPN, NILAI_PPN, PERSEN_NILAI_TAMBAH, NILAI_TAMBAH, 
			PERSEN_NILAI_KURANG, NILAI_KURANG, TOTAL, TOTAL_BAYAR,
			STATUS, ID_PEMBAYARAN, STATUS_BAYAR
			)
		VALUES (
			'$no_va', '$nama_pelanggan', '$npwp', '$no_telepon', '$alamat', 
			'$lokasi', $tarif, '$keterangan', '$kasir', 
			$bln_periode, $periode, '$key_pkl', $uang_pangkal,
			$luas, '$kode_blok', CONVERT(DATETIME,'$periode_awal',105), CONVERT(DATETIME,'$periode_akhir',105),
			$persen_ppn, $nilai_ppn, $persen_nilai_tambah, $nilai_tambah, 
			$persen_nilai_kurang, $nilai_kurang, $total, $total_bayar,
			'1',$id_pembayaran,0
			)
		";

		ex_false($conn->Execute($query), $query);
		}
			
		$conn->committrans();	
		$msg = 'Data master pelanggan kaki lima berhasil disimpan.';
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
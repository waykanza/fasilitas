<?php
require_once('../../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$kasir = $_SESSION['ID_USER'];
	
	$nama_pelanggan	= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
	$no_ktp			= (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
	$npwp			= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
	$no_telepon		= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
	$no_hp			= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
	$alamat			= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';
	
	$jenis_bayar	= (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
	$kode_bank		= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
	$no_rekening	= (isset($_REQUEST['no_rekening'])) ? clean($_REQUEST['no_rekening']) : '';
	$no_trx			= (isset($_REQUEST['no_trx'])) ? clean($_REQUEST['no_trx']) : '';
	
	$kode_lokasi	= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
	$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
	$key_pkl		= (isset($_REQUEST['key_pkl'])) ? clean($_REQUEST['key_pkl']) : '';
	$uang_pangkal	= (isset($_REQUEST['uang_pangkal'])) ? to_number($_REQUEST['uang_pangkal']) : '';
	$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
	$satuan			= (isset($_REQUEST['satuan'])) ? to_number($_REQUEST['satuan']) : '';
	
	$luas			= (isset($_REQUEST['luas'])) ? to_decimal($_REQUEST['luas']) : '';
	$durasi			= (isset($_REQUEST['durasi'])) ? to_decimal($_REQUEST['durasi']) : '';
	
	$administrasi	= (isset($_REQUEST['administrasi'])) ? to_decimal($_REQUEST['administrasi']) : '';
	
	$persen_nilai_tambah	= (isset($_REQUEST['persen_nilai_tambah'])) ? to_decimal($_REQUEST['persen_nilai_tambah']) : '';
	$persen_nilai_kurang	= (isset($_REQUEST['persen_nilai_kurang'])) ? to_decimal($_REQUEST['persen_nilai_kurang']) : '';
	$nilai_tambah	= (isset($_REQUEST['nilai_tambah'])) ? to_decimal($_REQUEST['nilai_tambah']) : '';
	$nilai_kurang	= (isset($_REQUEST['nilai_kurang'])) ? to_decimal($_REQUEST['nilai_kurang']) : '';
	
	$jumlah_bayar	= (isset($_REQUEST['jumlah_bayar'])) ? to_decimal($_REQUEST['jumlah_bayar']) : '';
	
	$tgl_serahterima = (isset($_REQUEST['tgl_serahterima'])) ? clean($_REQUEST['tgl_serahterima']) : '';
	$tgl_pemutusan	= (isset($_REQUEST['tgl_pemutusan'])) ? clean($_REQUEST['tgl_pemutusan']) : '';
	$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
	
	
	
	$persen_ppn		= 10;
	$nilai_ppn		= $jumlah_bayar * ($persen_ppn / 100);
	
	try
	{
		$conn->begintrans();
		
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		/*
		ex_empty($no_ktp, 'No KTP harus diisi.');
		ex_empty($no_hp, 'No HP harus diisi.');
		ex_empty($alamat, 'Alamat harus diisi.');
		*/
		ex_empty($kode_lokasi, 'Lokasi harus diisi.');
		ex_empty($kode_tipe, 'Kategori harus diisi.');
		ex_empty($key_pkl, 'Kode tarif harus diisi.');
		ex_zero($uang_pangkal, 'Uang pangkal harus diisi.');
		ex_zero($luas, 'Luas harus diisi.');
		ex_zero($durasi, 'Durasi harus diisi.');
		ex_zero($administrasi, 'Administrasi harus diisi.');
		ex_zero($jumlah_bayar, 'Jumlah bayar harus diisi.');
		//ex_empty($tgl_serahterima, 'Tanggal serahterima harus diisi.');
		//ex_empty($tgl_pemutusan, 'Tanggal pemutusan harus diisi.');
		
		$query = "
		INSERT INTO KWT_PELANGGAN_PKL 
		(
			NO_KTP, NAMA_PELANGGAN, NPWP, ALAMAT, NO_TELEPON, NO_HP 
		)
		VALUES (
			'$no_ktp', '$nama_pelanggan', '$npwp', '$alamat', '$no_telepon', '$no_hp'
		)";
		ex_false($conn->Execute($query), $query);

		$query 	= "SELECT MAX(NO_PELANGGAN) AS NO_PELANGGAN FROM KWT_PELANGGAN_PKL";
		$obj 	= $conn->Execute($query);
		$no_pelanggan	= $obj->fields['NO_PELANGGAN'];
		
		$query = "
		INSERT INTO KWT_PEMBAYARAN_PKL 
		(
			NO_PELANGGAN,
			
			KEY_PKL, KODE_LOKASI, KODE_TIPE, 
			UANG_PANGKAL, TARIF, SATUAN, LUAS, DURASI, 
			TGL_SERAHTERIMA, 
			TGL_PEMUTUSAN, 
			KETERANGAN, 
			
			PERSEN_PPN, NILAI_PPN, ADMINISTRASI, PERSEN_NILAI_TAMBAH, NILAI_TAMBAH, PERSEN_NILAI_KURANG, NILAI_KURANG, JUMLAH_BAYAR,
			
			KASIR
		)
		VALUES (
			'$no_pelanggan',
			
			'$key_pkl', '$kode_lokasi', '$kode_tipe', 
			$uang_pangkal, $tarif, $satuan, $luas, $durasi, 
			CONVERT(DATETIME,'$tgl_serahterima',105),
			CONVERT(DATETIME,'$tgl_pemutusan',105),
			'$keterangan',

			$persen_ppn, $nilai_ppn, $administrasi, $persen_nilai_tambah, $nilai_tambah, $persen_nilai_kurang, $nilai_kurang, $jumlah_bayar,
			
			'$kasir'
		)";
		ex_false($conn->Execute($query), $query);
		
		/* GET NO KWITANSI */
		$id_pembayaran = $conn->Execute("SELECT @@IDENTITY AS ID_PEMBAYARAN")->fields['ID_PEMBAYARAN'];
		$no_kwitansi = $conn->Execute("SELECT NO_KWITANSI FROM KWT_PEMBAYARAN_PKL WHERE ID_PEMBAYARAN = '$id_pembayaran'")->fields['NO_KWITANSI'];
		
		//pelanggan_lookup(array('no_ktp' => $no_ktp,'nama_pelanggan' => $nama_pelanggan,'npwp' => $npwp,'alamat' => $alamat,'no_telepon' => $no_telepon,'no_hp' => $no_hp,'kode_bank' => $kode_bank,'no_rekening' => $no_rekening));
		
		$conn->committrans();
		
		$msg = "Data pembayaran pelanggan \"$nama_pelanggan\" berhasil disimpan.";
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
		
		$id_pembayaran = '';
		$no_kwitansi = '';
	}
	
	close($conn);
	$json = array('id_pembayaran' => $id_pembayaran, 'no_kwitansi' => $no_kwitansi, 'msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}
?>
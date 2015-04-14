<?php
require_once('../../../../config/config.php');
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
	$key_ksp		= (isset($_REQUEST['key_ksp'])) ? clean($_REQUEST['key_ksp']) : '';
	$save_deposit	= (isset($_REQUEST['save_deposit'])) ? to_number($_REQUEST['save_deposit']) : '';
	$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
	$durasi			= (isset($_REQUEST['durasi'])) ? to_decimal($_REQUEST['durasi']) : '';
	
	$administrasi	= (isset($_REQUEST['administrasi'])) ? to_decimal($_REQUEST['administrasi']) : '';
	
	$tgl_serahterima = (isset($_REQUEST['tgl_serahterima'])) ? clean($_REQUEST['tgl_serahterima']) : '';
	$tgl_pemutusan	= (isset($_REQUEST['tgl_pemutusan'])) ? clean($_REQUEST['tgl_pemutusan']) : '';
	$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
	
	$jumlah_bayar	= ($durasi * $tarif) + $administrasi + $save_deposit;
	$persen_ppn		= 10;
	$nilai_ppn		= ($jumlah_bayar - $administrasi) * ($persen_ppn / 100);
	
	try
	{
		$conn->begintrans();
		
		ex_empty($no_ktp, 'No KTP harus diisi.');
		ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
		ex_empty($no_hp, 'No HP harus diisi.');
		ex_empty($alamat, 'Alamat harus diisi.');
		
		if ($jenis_bayar != '1')
		{
			ex_empty($kode_bank, 'Kode bank harus diisi.');
			ex_empty($no_rekening, 'No rekening harus diisi.');
			ex_empty($no_trx, 'No rekening harus diisi.');
		}
		else
		{
			$kode_bank = '';
			$no_rekening = '';
			$no_trx = '';
		}
		
		ex_empty($kode_lokasi, 'Lokasi harus diisi.');
		ex_empty($kode_tipe, 'Kategori harus diisi.');
		ex_empty($key_ksp, 'Kode tarif harus diisi.');
		ex_zero($save_deposit, 'Uang pangkal harus diisi.');
		ex_zero($durasi, 'Durasi harus diisi.');
		ex_zero($administrasi, 'Administrasi harus diisi.');
		ex_zero($jumlah_bayar, 'Jumlah bayar harus diisi.');
		ex_empty($tgl_serahterima, 'Tanggal serahterima harus diisi.');
		ex_empty($tgl_pemutusan, 'Tanggal pemutusan harus diisi.');
		
		$query = "
		INSERT INTO KWT_PEMBAYARAN_KSP 
		(
			NO_KTP, NAMA_PELANGGAN, NPWP, ALAMAT, NO_TELEPON, NO_HP, 
			
			KEY_KSP, KODE_LOKASI, KODE_TIPE, 
			SAVE_DEPOSIT, TARIF, DURASI, 
			TGL_SERAHTERIMA, 
			TGL_PEMUTUSAN, 
			KETERANGAN, 
			
			PERSEN_PPN, NILAI_PPN, ADMINISTRASI, JUMLAH_BAYAR, 
			JENIS_BAYAR, KODE_BANK, NO_REKENING, NO_TRX, 
			
			KASIR
		)
		VALUES (
			'$no_ktp', '$nama_pelanggan', '$npwp', '$alamat', '$no_telepon', '$no_hp', 
			
			'$key_ksp', '$kode_lokasi', '$kode_tipe', 
			$save_deposit, $tarif, $durasi, 
			CONVERT(DATETIME,'$tgl_serahterima',105),
			CONVERT(DATETIME,'$tgl_pemutusan',105),
			'$keterangan',

			$persen_ppn, $nilai_ppn, $administrasi, $jumlah_bayar, 
			'$jenis_bayar', '$kode_bank', '$no_rekening', '$no_trx', 
			
			'$kasir'
		)";
		ex_false($conn->Execute($query), $query);
		
		/* GET NO KWITANSI */
		$id_pembayaran = $conn->Execute("SELECT @@IDENTITY AS ID_PEMBAYARAN")->fields['ID_PEMBAYARAN'];
		$no_kwitansi = $conn->Execute("SELECT NO_KWITANSI FROM KWT_PEMBAYARAN_KSP WHERE ID_PEMBAYARAN = '$id_pembayaran'")->fields['NO_KWITANSI'];
		
		pelanggan_lookup(array('no_ktp' => $no_ktp,'nama_pelanggan' => $nama_pelanggan,'npwp' => $npwp,'alamat' => $alamat,'no_telepon' => $no_telepon,'no_hp' => $no_hp,'kode_bank' => $kode_bank,'no_rekening' => $no_rekening));
		
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
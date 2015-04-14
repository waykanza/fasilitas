<?php
require_once('../../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		
		$conn->begintrans();
		/*
		$query="
			SELECT
			p.*,
			CONVERT(VARCHAR(11),p.TGL_SERAHTERIMA,106) AS TGL_SERAHTERIMA,
			CONVERT(VARCHAR(11),p.TGL_PEMUTUSAN,106) AS TGL_PEMUTUSAN,
			l.*,
			t.*,
			b.*,
			c.*
		FROM 
			KWT_PEMBAYARAN_PKL p
			LEFT JOIN KWT_LOKASI_PKL l ON p.KODE_LOKASI = l.KODE_LOKASI
			LEFT JOIN KWT_TIPE_PKL t ON p.KODE_TIPE = t.KODE_TIPE
			LEFT JOIN KWT_BANK b ON p.KODE_BANK = b.KODE_BANK
			LEFT JOIN KWT_PELANGGAN_PKL c ON p.NO_PELANGGAN = c.NO_PELANGGAN
		WHERE
			ID_PEMBAYARAN = '$id'
	
		";
		$obj = $conn->Execute($query);	
		$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
		$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
		$id_pembayaran		= $obj->fields['ID_PEMBAYARAN'];
		
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
		$kasir			= (isset($_REQUEST['kasir'])) ? clean($_REQUEST['kasir']) : '';
		$persen_ppn		= 10;
		$nilai_ppn		= $jumlah_bayar * ($persen_ppn / 100);	
		
		
		ex_empty($kode_lokasi, 'Pilih Lokasi');
		ex_empty($kode_tipe, 'Pilih Kategori');
		
		ex_zero($luas, 'Luas harus diisi.');
		ex_zero($durasi, 'Durasi harus diisi.');
		ex_zero($administrasi, 'Administrasi harus diisi.');
		ex_zero($jumlah_bayar, 'Jumlah bayar harus diisi.');
		ex_empty($tgl_serahterima, 'Tanggal serahterima harus diisi.');
		ex_empty($tgl_pemutusan, 'Tanggal pemutusan harus diisi.');
			
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
				$no_pelanggan,
			
				'$key_pkl', '$kode_lokasi', '$kode_tipe', 
				$uang_pangkal, $tarif, $satuan, $luas, $durasi, 
				CONVERT(DATETIME,'$tgl_serahterima',105),
				CONVERT(DATETIME,'$tgl_pemutusan',105),
				'$keterangan',

				$persen_ppn, $nilai_ppn, $administrasi, $persen_nilai_tambah, $nilai_tambah, $persen_nilai_kurang, $nilai_kurang, $jumlah_bayar, 
			
				'$kasir'
		)";
		ex_false($conn->Execute($query), $query);
		*/	
		/* GET NO KWITANSI */
		//$id_pembayaran = $conn->Execute("SELECT @@IDENTITY AS ID_PEMBAYARAN")->fields['ID_PEMBAYARAN'];
		//$no_kwitansi = $conn->Execute("SELECT NO_KWITANSI FROM KWT_PEMBAYARAN_PKL WHERE ID_PEMBAYARAN = '$id_pembayaran'")->fields['NO_KWITANSI'];
		
		$conn->committrans();
		
		$msg = "Data pembayaran PKL \"$nama_pelanggan\" berhasil diperpanjang.";
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
else
{
	$query = "
	SELECT
		p.*, c.*, l.*, t.*
	FROM 
		KWT_PEMBAYARAN_PKL p
		LEFT JOIN KWT_LOKASI_PKL l ON p.KODE_LOKASI = l.KODE_LOKASI
		LEFT JOIN KWT_TIPE_PKL t ON p.KODE_TIPE = t.KODE_TIPE
		LEFT JOIN KWT_PELANGGAN_PKL c ON p.NO_PELANGGAN = c.NO_PELANGGAN
	WHERE ID_PEMBAYARAN = '$id'";
	$obj = $conn->execute($query);
	$id_pembayaran 	= $obj->fields['ID_PEMBAYARAN'];
	
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$no_ktp				= $obj->fields['NO_KTP'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];	
	$alamat				= $obj->fields['ALAMAT'];
	$no_tlp				= $obj->fields['NO_TELEPON'];
	$no_hp				= $obj->fields['NO_HP'];
	$tipe_pkl			= $obj->fields['NAMA_TIPE'];
	$lokasi				= $obj->fields['DETAIL_LOKASI'];
	$nlokasi			= $obj->fields['NAMA_LOKASI'];
	$luas				= $obj->fields['LUAS'];
	
	$tanggal			= date("d M Y", strtotime($obj->fields['CREATED_DATE']));
	$no_kwitansi		= $obj->fields['NO_KWITANSI'];
	$durasi				= $obj->fields['DURASI'];
	$serahterima		= date("d M Y", strtotime($obj->fields['TGL_SERAHTERIMA']));
	$pemutusan			= date("d M Y", strtotime($obj->fields['TGL_PEMUTUSAN']));
	
	$satuan				= satuan($obj->fields['SATUAN']);
	$satuan12			= $obj->fields['SATUAN'];
	
	$tarif				= $obj->fields['TARIF'];
	$kasir				= $obj->fields['KASIR'];
}
?>
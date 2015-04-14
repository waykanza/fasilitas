<?php
require_once('../../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') /* Proses Simpan */
	{
		
		try
		{
			$conn->begintrans();
		
		
			$conn->committrans();
		
			$msg = "";
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'Perpanjang') /* Proses Perpanjang */
	{
		try
		{
		
			$luas					= (isset($_REQUEST['luas'])) ? to_decimal($_REQUEST['luas']) : '';
			$durasi					= (isset($_REQUEST['durasi'])) ? to_decimal($_REQUEST['durasi']) : '';
			$administrasi			= (isset($_REQUEST['administrasi'])) ? to_decimal($_REQUEST['administrasi']) : '';
			$persen_nilai_tambah	= (isset($_REQUEST['persen_nilai_tambah'])) ? to_decimal($_REQUEST['persen_nilai_tambah']) : '';
			$persen_nilai_kurang	= (isset($_REQUEST['persen_nilai_kurang'])) ? to_decimal($_REQUEST['persen_nilai_kurang']) : '';
			$nilai_tambah			= (isset($_REQUEST['nilai_tambah'])) ? to_decimal($_REQUEST['nilai_tambah']) : '';
			$nilai_kurang			= (isset($_REQUEST['nilai_kurang'])) ? to_decimal($_REQUEST['nilai_kurang']) : '';
			$jumlah_bayar			= (isset($_REQUEST['jumlah_bayar'])) ? to_decimal($_REQUEST['jumlah_bayar']) : '';
			$tgl_serahterima 		= (isset($_REQUEST['tgl_serahterima'])) ? clean($_REQUEST['tgl_serahterima']) : '';
			$tgl_pemutusan			= (isset($_REQUEST['tgl_pemutusan'])) ? clean($_REQUEST['tgl_pemutusan']) : '';
			$keterangan				= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
			$persen_ppn				= 10;
			$nilai_ppn				= $jumlah_bayar * ($persen_ppn / 100);
			$kasir = $_SESSION['ID_USER'];
		
		
			ex_zero($luas, 'Luas harus diisi.');
			ex_zero($durasi, 'Durasi harus diisi.');
			ex_zero($administrasi, 'Administrasi harus diisi.');
			ex_zero($jumlah_bayar, 'Jumlah bayar harus diisi.');
			ex_empty($tgl_serahterima, 'Tanggal serahterima harus diisi.');
			ex_empty($tgl_pemutusan, 'Tanggal pemutusan harus diisi.');
		
			$conn->begintrans();

			$query 	= "SELECT * FROM KWT_PEMBAYARAN_PKL WHERE ID_PEMBAYARAN = $id ";
			$obj 	= $conn->Execute($query);
			$no_pelanggan	= $obj->fields['NO_PELANGGAN'];
			$key_pkl		= $obj->fields['KEY_PKL'];
			$kode_lokasi	= $obj->fields['KODE_LOKASI'];
			$kode_tipe		= $obj->fields['KODE_TIPE'];
			$tarif			= $obj->fields['TARIF'];
			$satuan			= $obj->fields['SATUAN'];
			
			$query = "
			INSERT INTO KWT_PEMBAYARAN_PKL 
			(
				NO_PELANGGAN,
		
				KEY_PKL, KODE_LOKASI, KODE_TIPE, 
				TARIF, SATUAN, LUAS, DURASI, 
				TGL_SERAHTERIMA, 
				TGL_PEMUTUSAN, 
				KETERANGAN, 
			
				PERSEN_PPN, NILAI_PPN, ADMINISTRASI, PERSEN_NILAI_TAMBAH, NILAI_TAMBAH, PERSEN_NILAI_KURANG, NILAI_KURANG, JUMLAH_BAYAR, 
				
				KASIR
			)
			VALUES (
				$no_pelanggan,
			
				'$key_pkl', '$kode_lokasi', '$kode_tipe', 
				$tarif, $satuan, $luas, $durasi,  
				CONVERT(DATETIME,'$tgl_serahterima',105),
				CONVERT(DATETIME,'$tgl_pemutusan',105),
				'$keterangan',

				$persen_ppn, $nilai_ppn, $administrasi, $persen_nilai_tambah, $nilai_tambah, $persen_nilai_kurang, $nilai_kurang, $jumlah_bayar, 
			
				'$kasir'
			)";

			ex_false($conn->execute($query), $query);
			$id_pembayaran = $conn->Execute("SELECT @@IDENTITY AS ID_PEMBAYARAN")->fields['ID_PEMBAYARAN'];
			$conn->committrans();
			
			$msg = 'Data pembayaran pedagang kaki lima berhasil diperpanjang.';
			
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();			
			$id_pembayaran = '';
		}
	}
	elseif ($act == 'delete') /* Proses Delete */
	{
		$act = array();
		
		try
		{
			$conn->begintrans();
			/*
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data PKL dahulu!');
			
			foreach ($cb_data as $id_del)
			{
				$query 	= "SELECT NO_PELANGGAN FROM KWT_PEMBAYARAN_PKL WHERE ID_PELANGGAN = '$id_del'";
				$obj 	= $conn->Execute($query);
				$no_pelanggan	= $obj->fields['NO_PELANGGAN'];
			
				$query = "UPDATE KWT_PEMBAYARAN_PKL SET TGL_KELUAR=getdate() WHERE NO_PELANGGAN = $no_pelanggan";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			*/
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal.' : 'Pemutusan sewa lokasi PKL telah dilakukan.';
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}

	close($conn);
	$json = array('id_pembayaran' => $id_pembayaran,'act' => $act, 'msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}

if ($act == 'Perpanjang')
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
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$tipe_pkl			= $obj->fields['NAMA_TIPE'];
	$lokasi				= $obj->fields['DETAIL_LOKASI'];
	$nlokasi			= $obj->fields['NAMA_LOKASI'];
	$luas				= $obj->fields['LUAS'];
	
	$satuan				= satuan($obj->fields['SATUAN']);
	$satuan12			= $obj->fields['SATUAN'];
	$tarif				= $obj->fields['TARIF'];
}

?>
<?php
require_once('../../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;
error_reporting(0);
$id = (isset($_REQUEST['id'])) ? base64_decode(clean($_REQUEST['id'])) : '';

$tgl_bayar 		= (isset($_REQUEST['tgl_bayar'])) ? clean($_REQUEST['tgl_bayar']) : '';
$jenis_bayar	= (isset($_REQUEST['jenis_bayar'])) ? clean($_REQUEST['jenis_bayar']) : '';
$kode_bank		= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
$no_rekening	= (isset($_REQUEST['no_rekening'])) ? clean($_REQUEST['no_rekening']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$kasir = $_SESSION['ID_USER'];

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		ex_empty($tgl_bayar, 'Tanggal bayar harus diisi.');
		ex_empty($jenis_bayar, 'Jenis bayar harus diisi.');
		ex_empty($kode_bank, 'Kode bank harus diisi.');
		ex_empty($no_rekening, 'No rekening harus diisi.');
	
		$conn->begintrans();
		
		$query="
			UPDATE PELANGGAN_MP
			SET TANGGAL_BAYAR=CONVERT(DATETIME,'$tgl_bayar',105), JENIS_BAYAR ='$jenis_bayar', KODE_BANK='$kode_bank', NO_REKENING='$no_rekening', KETERANGAN='$keterangan', 
			CREATED_DATE=getdate(), STATUS_BAYAR=2, KASIR='$kasir'
			WHERE ID_PEMBAYARAN = '$id'
		";
		
		ex_false($conn->Execute($query), $query);
		
		
		$conn->committrans();
		
		$msg = "Data pembayaran berhasil disimpan.";
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
	$query="
		SELECT *
			FROM PELANGGAN_MP a	
			LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
			LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE
			LEFT JOIN KWT_BANK e ON a.KODE_BANK = e.KODE_BANK
		WHERE
			ID_PEMBAYARAN = '$id'
	
	";
	$obj = $conn->Execute($query);	
	
	$no_ktp				= $obj->fields['NO_KTP'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$npwp				= $obj->fields['NPWP'];
	$alamat				= $obj->fields['ALAMAT'];
	$no_tlp				= $obj->fields['NO_TELEPON'];
	$no_hp				= $obj->fields['NO_HP'];
	
	$pembayaran = $obj->fields['PEMBAYARAN'];
	if ($obj->fields['KODE_MP'] == 'A') {
			$mp = 'BILLBOARD / SIGN BOARD / PLAY SIGN';
			$tahun = ' / Tahun';
			$satuan ='Per ' .$pembayaran. ' Bulan';
			if ($pembayaran % 12 == 0){
				$xx = $pembayaran / 12;
				$satuan ='Per '.$xx.' Tahun';
				if ($pembayaran / 12 == 1){
					$satuan =' Tahunan';
				}
			}
		} else if ($obj->fields['KODE_MP'] == 'B') {
			$mp = 'NEON BOX / NEON SIGN';
			$tahun = ' / Tahun';
			$satuan ='Per ' .$pembayaran. ' Bulan';
			if ($pembayaran % 12 == 0){
				$xx = $pembayaran / 12;
				$satuan ='Per '.$xx.' Tahun';
				if ($pembayaran / 12 == 1){
					$satuan =' Tahunan';
				}
			}
		} else if ($obj->fields['KODE_MP'] == 'C') {
			$mp = 'SPANDUK / UMBUL-UMBUL / STANDING DISPLAY';
			$tahun = ' / Minggu';	
			$satuan ='Per ' .$pembayaran. ' Minggu';
			if ($pembayaran % 4 == 0){
				$bulan = $pembayaran / 4;
				$satuan ='Per ' .$bulan. ' Bulan';
				if ($pembayaran / 4 == 1){
					$satuan =' Bulanan';
				}
			} else if ($pembayaran == 1){
					$satuan =' Mingguan';
			}
		} else {
			$mp = 'BANNER / BALIHO';
			$tahun = ' / Hari';
			$satuan ='Per ' .$pembayaran. ' Hari';
			if ($pembayaran % 7 == 0){
				$minggu = $pembayaran / 7;
				$satuan ='Per ' .$minggu. ' Minggu';
				if ($pembayaran / 7 == 1){
					$satuan =' Mingguan';
				}
			}else if ($pembayaran == 1){
					$satuan =' Harian';
			}
		}
	
	$kategori			= $obj->fields['NAMA_TIPE'];
	$lokasi				= $obj->fields['NAMA_LOKASI'];
	
	$awal				= date("d M Y", strtotime($obj->fields['PERIODE_AWAL']));
	$akhir				= date("d M Y", strtotime($obj->fields['PERIODE_AKHIR']));
		
	$tarif				= to_money($obj->fields['TARIF']);
	$tarif2				= to_money($obj->fields['TARIF2']);
	$nilai_tambah		= to_money($obj->fields['NILAI_TAMBAH']);
	$persen_nilai_tambah		= to_decimal($obj->fields['PERSEN_NILAI_TAMBAH']);
	$nilai_kurang		= to_money($obj->fields['NILAI_KURANG']);
	$persen_nilai_kurang		= to_decimal($obj->fields['PERSEN_NILAI_KURANG']);
	$nilai_ppn			= to_money($obj->fields['NILAI_PPN']);
	$persen_ppn			= to_decimal($obj->fields['PERSEN_PPN']);
	$total				= to_money($obj->fields['TOTAL']);
	$total_bayar		= to_money($obj->fields['TOTAL_BAYAR']);
	$total_bayar2		= $obj->fields['TOTAL_BAYAR'];
	
	$status_bayar			= $obj->fields['STATUS_BAYAR'];
	$tanggal_bayar			= date("d-m-Y", strtotime($obj->fields['TANGGAL_BAYAR']));
	$jenis_bayar			= jenis_bayar($obj->fields['JENIS_BAYAR']);
	$kode_bank				= $obj->fields['NAMA_BANK'].' ('.$obj->fields['KODE_BANK'].')';
	$no_rekening			= $obj->fields['NO_REKENING'];
	$no_kwitansi			= $obj->fields['ID_PEMBAYARAN'];
	$keterangan				= $obj->fields['KETERANGAN'];
}

$id = base64_encode($id);
?>
<?php 
require_once('../../../config/config.php');

$conn = conn();
$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$single_blok	= (isset($_REQUEST['single_blok'])) ? clean($_REQUEST['single_blok']) : '';
	$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
	$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
	$periode_awal	= (isset($_REQUEST['periode_awal'])) ? to_periode($_REQUEST['periode_awal']) : '';
	$jumlah_periode	= (isset($_REQUEST['jumlah_periode'])) ? to_number($_REQUEST['jumlah_periode']) : '';
	
	$where_single_blok = '';
	$diskon_rupiah_ipl = 0;
	$tgl_diskon_ipl = 'NULL';
	$user_diskon_ipl = 'NULL';
	
	if ($single_blok == '1') {
		$diskon_rupiah_ipl = (isset($_REQUEST['diskon_rupiah_ipl'])) ? to_number($_REQUEST['diskon_rupiah_ipl']) : '';
		$where_single_blok = " AND p.KODE_BLOK = '$kode_blok' ";
		
		if ($diskon_rupiah_ipl > 0) {
			$tgl_diskon_ipl	= 'GETDATE()';
			$user_diskon_ipl = "'" . $_SESSION['ID_USER'] . "'";
		}
	}
	
	try
	{
		$conn->begintrans();
		
		ex_empty($periode, 'Masukkan periode tagihan.');
		ex_zero($jumlah_periode, 'Jumlah periode harus > 0');
		
		# UNTUK AIR
		$periode_prev = date('Ym', strtotime('-1 months', strtotime($periode.'01')));
		
		$periode_awal = $periode;
		$periode_akhir = date('Ym', strtotime('+' . ($jumlah_periode - 1) . ' months', strtotime($periode_awal.'01')));
		
		# CEK PERIODE SEBELUMNYA
		# $query = "SELECT COUNT(ID_PEMBAYARAN) AS TOTAL FROM KWT_PEMBAYARAN_AI p WHERE TRX IN ('1', '2', '4', '5') AND PERIODE = '$periode_prev' $where_single_blok";
		# ex_found($conn->Execute($query)->fields['TOTAL'], "Data periode $periode_prev belum diproses!");
		
		# CEK DATA SUDAH ADA ATAU BELUM
		# $query = "SELECT COUNT(ID_PEMBAYARAN) AS TOTAL FROM KWT_PEMBAYARAN_AI p WHERE TRX IN ('1', '2', '4', '5') AND PERIODE = '$periode' $where_single_blok";
		# ex_found($conn->Execute($query)->fields['TOTAL'], "Data periode $periode sudah ada!");
		
		# JUST IN CASE!
		$conn->Execute("DELETE FROM KWT_PEMBAYARAN_AI WHERE PERIODE = '$periode'");
		$conn->Execute("UPDATE KWT_PERIODE_DEPOSIT SET STATUS_PROSES = NULL WHERE PERIODE_AWAL = '$periode'");
		
		require_once('proses_2_5.php'); # IPL & AIR (BG, RV)
		require_once('proses_1_2_4_5.php'); # IPL & AIR (KV, BG, HN, RV)
		require_once('proses_3_6.php'); # DEPOSIT (BG, RV)
		
		$msg = 'Periode berhasil diproses!';
		
		$conn->committrans();
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$conn->rollbacktrans();
	}
}

close($conn);
$result = array('msg' => $msg);
echo json_encode($result);					
?>	
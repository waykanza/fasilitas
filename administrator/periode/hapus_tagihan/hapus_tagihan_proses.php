<?php
require_once('../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;
$list_id = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		$user_del = $_SESSION['ID_USER'];
		$cb_data = $_REQUEST['cb_data'];
		$cb_ket = $_REQUEST['cb_ket'];
		ex_empty($cb_data, 'Pilih data yang akan dihapus.');
		ex_empty($cb_ket, 'Masukkan alasan penghapusan tagihan.');
		
		foreach ($cb_data as $i => $x)
		{
			$id_del = base64_decode($cb_data[$i]);
			$keterangan_del = $cb_ket[$i];
			
			$query = "
			INSERT INTO KWT_PEMBAYARAN_AI_DEL
			(ID_PEMBAYARAN, TRX, NO_INVOICE, PERIODE, PERIODE_AWAL, PERIODE_AKHIR, JUMLAH_PERIODE, NO_PELANGGAN, KODE_SEKTOR, KODE_CLUSTER, KODE_BLOK, STATUS_BLOK, KODE_ZONA, KEY_AIR, KEY_IPL, STAND_LALU, STAND_ANGKAT, STAND_AKHIR, BLOK1, BLOK2, BLOK3, BLOK4, STAND_MIN_PAKAI, TARIF1, TARIF2, TARIF3, TARIF4, TARIF_MIN_PAKAI, LUAS_KAVLING, TARIF_IPL, JUMLAH_AIR, ABONEMEN, JUMLAH_IPL, DENDA, ADMINISTRASI, JUMLAH_BAYAR, DISKON_PERSEN_AIR, DISKON_RUPIAH_AIR, TGL_DISKON_AIR, USER_DISKON_AIR, DISKON_PERSEN_IPL, DISKON_RUPIAH_IPL, TGL_DISKON_IPL, USER_DISKON_IPL, KETERANGAN_DISKON, PERSEN_PPN, NILAI_PPN, STATUS_BAYAR, NO_KWITANSI, BAYAR_MELALUI, JENIS_BAYAR, TGL_BAYAR, TGL_TERIMA_BANK, KASIR, KETERANGAN_BAYAR, STATUS_CETAK_KWT, STATUS_CETAK_IVC, NO_FAKTUR_PAJAK, TGL_FAKTUR_PAJAK, TGL_POST_FP, STATUS_POST_PB, STATUS_EDIT, USER_EDIT, TGL_EDIT, STATUS_BATAL, USER_BATAL, TGL_BATAL, KETERANGAN_BATAL, USER_DEL, KETERANGAN_DEL)
			SELECT 
				*, 
				'$user_del' AS USER_DEL, 
				'$keterangan_del' AS KETERANGAN_DEL
			FROM KWT_PEMBAYARAN_AI 
			WHERE ID_PEMBAYARAN = '$id_del' AND STATUS_BAYAR IS NULL
			
			DELETE FROM KWT_PEMBAYARAN_AI 
			WHERE ID_PEMBAYARAN = '$id_del' AND STATUS_BAYAR IS NULL
			";
			if ($conn->Execute($query)) {
				$list_id[] = base64_encode($id_del);
			} else {
				$error = TRUE;
			}
		}
		
		$conn->committrans();
		
		$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data tagihan berhasil dihapus.';
	}
	catch(Exception $e)
	{
		$msg = $e->getMessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}
	
	close($conn);
	$json = array('msg' => $msg, 'error'=> $error, 'list_id' => $list_id);
	echo json_encode($json);
	exit;
}
?>
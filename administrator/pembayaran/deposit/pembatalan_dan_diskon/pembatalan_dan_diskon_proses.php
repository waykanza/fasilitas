<?php
require_once('../../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;

$id = (isset($_REQUEST['id'])) ? base64_decode(clean($_REQUEST['id'])) : '';
$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'diskon')
	{
		try
		{
			$conn->begintrans();
			
			$query = "
			SELECT
				STATUS_POST_PB,
				STATUS_BAYAR,
				DISKON_RUPIAH_IPL
			FROM KWT_PEMBAYARAN_AI
			WHERE 
				ID_PEMBAYARAN = '$id'";
			
			$obj = $conn->Execute($query);
			
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Data sudah di-posting!');
			ex_equal($obj->fields['STATUS_BAYAR'], '2', 'Data sudah dibayar!');
			$old_diskon_rupiah_ipl = $obj->fields['DISKON_RUPIAH_IPL'];
			
			$user_diskon = $_SESSION['ID_USER'];
			$diskon_rupiah_ipl = (isset($_REQUEST['diskon_rupiah_ipl'])) ? to_number($_REQUEST['diskon_rupiah_ipl']) : '';
			$keterangan_diskon = (isset($_REQUEST['keterangan_diskon'])) ? clean($_REQUEST['keterangan_diskon']) : '';
			
			if ($diskon_rupiah_ipl != $old_diskon_rupiah_ipl)
			{
				$query = "
				UPDATE KWT_PEMBAYARAN_AI 
				SET 
					USER_DISKON_IPL = '$user_diskon',
					TGL_DISKON_IPL = GETDATE(),
					DISKON_RUPIAH_IPL = '$diskon_rupiah_ipl',
					KETERANGAN_DISKON = '$keterangan_diskon'
				WHERE
					ID_PEMBAYARAN = '$id'
				";
				
				ex_false($conn->Execute($query), $query);
			}			
			
			$conn->committrans();
			
			$msg = 'Data diskon berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'pembatalan')
	{
		try
		{
			$conn->begintrans();
			
			$query = "SELECT STATUS_BAYAR, STATUS_POST_PB FROM KWT_PEMBAYARAN_AI WHERE ID_PEMBAYARAN = '$id'";
			$obj = $conn->Execute($query);
			ex_equal($obj->fields['STATUS_BAYAR'], '', 'Data belum dibayar!');
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Data sudah diposting!');
			
			$user_batal = $_SESSION['ID_USER'];
			
			$query = "
			UPDATE KWT_PEMBAYARAN_AI 
			SET 
				TGL_BAYAR = NULL,
				STATUS_BAYAR = NULL,
				BAYAR_MELALUI = NULL,
				KASIR = NULL,
				JENIS_BAYAR = NULL,
				NO_KWITANSI = NULL,
				STATUS_CETAK_KWT = NULL,
				
				ADMINISTRASI = 0,
				JUMLAH_BAYAR = 0,
				
				STATUS_BATAL = '1',
				TGL_BATAL = GETDATE(),
				USER_BATAL = '$user_batal'
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Data pembayaran berhasil dibatalkan.';
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
}
else
{
	$query = "
	DECLARE 
	@adm_db INT,
	@adm_dr INT
	
	SELECT TOP 1 
	@adm_db = ISNULL(ADMINISTRASI_DB, 0) ,
	@adm_dr = ISNULL(ADMINISTRASI_DR, 0)
	FROM KWT_PARAMETER
	
	SELECT
		(
			SELECT COUNT(NO_PELANGGAN)
			FROM KWT_PEMBAYARAN_AI
			WHERE $where_trx_deposit AND STATUS_BAYAR IS NULL AND NO_PELANGGAN = b.NO_PELANGGAN
		) AS JUMLAH_PIUTANG,
		
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.PERIODE,
		b.PERIODE_AWAL,
		b.PERIODE_AKHIR,
		b.JUMLAH_PERIODE,
		b.KODE_BLOK,
		s.NAMA_SEKTOR + ' (' + b.KODE_SEKTOR + ')' AS NAMA_SEKTOR,
		c.NAMA_CLUSTER + ' (' + b.KODE_CLUSTER + ')' AS NAMA_CLUSTER,
		b.STATUS_BLOK,
		
		b.AKTIF_AIR,
		b.AKTIF_IPL,
	
		b.KEY_AIR,
		b.KEY_IPL,
		
		b.STAND_LALU,
		b.STAND_ANGKAT,
		b.STAND_AKHIR,
		b.BLOK1,
		b.BLOK2,
		b.BLOK3,
		b.BLOK4,
		b.STAND_MIN_PAKAI,
		b.TARIF1,
		b.TARIF2,
		b.TARIF3,
		b.TARIF4,
		b.TARIF_MIN_PAKAI,
		
		b.LUAS_KAVLING,
		b.TARIF_IPL,
		
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.JUMLAH_IPL,
		b.DENDA,
		(
			CASE WHEN STATUS_BAYAR = '2' 
			THEN ISNULL(b.ADMINISTRASI,0) 
			ELSE 
				CASE TRX
					WHEN '3' THEN @adm_db
					WHEN '6' THEN @adm_dr 
				END
			END 
		) AS ADMINISTRASI,
		
		b.USER_DISKON_AIR,
		CONVERT(VARCHAR(10),b.TGL_DISKON_AIR,105) AS TGL_DISKON_AIR,
		b.DISKON_RUPIAH_AIR,
		
		b.USER_DISKON_IPL,
		CONVERT(VARCHAR(10),b.TGL_DISKON_IPL,105) AS TGL_DISKON_IPL,
		b.DISKON_RUPIAH_IPL,
		
		b.KETERANGAN_DISKON,
		
		b.JUMLAH_BAYAR,		
		
		b.STATUS_BAYAR,
		b.JENIS_BAYAR,
		b.BAYAR_MELALUI,
		CONVERT(VARCHAR(10),b.TGL_BAYAR,105) AS TGL_BAYAR,
		b.NO_KWITANSI,
		b.KETERANGAN_BAYAR,
		b.KASIR,
		b.STATUS_CETAK_KWT,
		b.NO_FAKTUR_PAJAK,
		
		b.USER_BATAL,
		CONVERT(VARCHAR(10),b.TGL_BATAL,105) AS TGL_BATAL
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
	WHERE 
		ID_PEMBAYARAN = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$jumlah_piutang		= $obj->fields['JUMLAH_PIUTANG'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode			= $obj->fields['PERIODE'];
	$periode_awal		= $obj->fields['PERIODE_AWAL'];
	$periode_akhir		= $obj->fields['PERIODE_AKHIR'];
	$jumlah_periode		= $obj->fields['JUMLAH_PERIODE'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$nama_sektor		= $obj->fields['NAMA_SEKTOR'];
	$nama_cluster		= $obj->fields['NAMA_CLUSTER'];
	$status_blok		= $obj->fields['STATUS_BLOK'];
	
	$aktif_air			= $obj->fields['AKTIF_AIR'];
	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	
	$key_air			= $obj->fields['KEY_AIR'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	
	$stand_lalu			= $obj->fields['STAND_LALU'];
	$stand_angkat		= $obj->fields['STAND_ANGKAT'];
	$stand_akhir		= $obj->fields['STAND_AKHIR'];
	$real_pemakaian		= ($stand_angkat + $stand_akhir) - $stand_lalu;

	$blok1				= $obj->fields['BLOK1'];
	$blok2				= $obj->fields['BLOK2'];
	$blok3				= $obj->fields['BLOK3'];
	$blok4				= $obj->fields['BLOK4'];
	$stand_min_pakai	= $obj->fields['STAND_MIN_PAKAI'];
	$tarif1				= $obj->fields['TARIF1'];
	$tarif2				= $obj->fields['TARIF2'];
	$tarif3				= $obj->fields['TARIF3'];
	$tarif4				= $obj->fields['TARIF4'];
	$tarif_min_pakai	= $obj->fields['TARIF_MIN_PAKAI'];
	
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$tarif_ipl			= $obj->fields['TARIF_IPL'];
	
	$jumlah_air			= $obj->fields['JUMLAH_AIR'];
	$abonemen			= $obj->fields['ABONEMEN'];
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$administrasi		= $obj->fields['ADMINISTRASI'];
	
	$user_diskon_air	= $obj->fields['USER_DISKON_AIR'];
	$tgl_diskon_air		= $obj->fields['TGL_DISKON_AIR'];
	$diskon_rupiah_air	= $obj->fields['DISKON_RUPIAH_AIR'];

	$user_diskon_ipl	= $obj->fields['USER_DISKON_IPL'];
	$tgl_diskon_ipl		= $obj->fields['TGL_DISKON_IPL'];
	$diskon_rupiah_ipl	= $obj->fields['DISKON_RUPIAH_IPL'];
	
	$keterangan_diskon	= $obj->fields['KETERANGAN_DISKON'];
	
	$total				= $jumlah_air + $abonemen + $jumlah_ipl + $denda + $administrasi;
	$diskon				= $diskon_rupiah_air + $diskon_rupiah_ipl;
	
	$jumlah_bayar		= $obj->fields['JUMLAH_BAYAR'];
	
	$status_bayar		= $obj->fields['STATUS_BAYAR'];
	$jenis_bayar		= $obj->fields['JENIS_BAYAR'];
	$bayar_melalui		= $obj->fields['BAYAR_MELALUI'];
	$tgl_bayar			= $obj->fields['TGL_BAYAR'];
	$no_kwitansi		= $obj->fields['NO_KWITANSI'];
	$keterangan_bayar	= $obj->fields['KETERANGAN_BAYAR'];
	$kasir				= $obj->fields['KASIR'];
	$status_cetak_kwt	= $obj->fields['STATUS_CETAK_KWT'];
	$no_faktur_pajak	= $obj->fields['NO_FAKTUR_PAJAK'];
	
	$user_batal			= $obj->fields['USER_BATAL'];
	$tgl_batal			= $obj->fields['TGL_BATAL'];
}

$id = base64_encode($id);
?>
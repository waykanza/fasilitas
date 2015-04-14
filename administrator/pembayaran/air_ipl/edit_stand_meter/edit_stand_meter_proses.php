<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$id = (isset($_REQUEST['id'])) ? base64_decode(clean($_REQUEST['id'])) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$user_edit = $_SESSION['ID_USER'];
		
		$conn->begintrans();
		
		$query = "
		SELECT 
			STATUS_POST_PB,
			STATUS_BAYAR,
			KEY_AIR			
		FROM KWT_PEMBAYARAN_AI 
		WHERE 
			ID_PEMBAYARAN = '$id'
		";
		
		$obj = $conn->Execute($query);
		
		ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Data sudah di-posting!');
		ex_equal($obj->fields['STATUS_BAYAR'], '2', 'Data sudah dibayar!');
		
		$stand_lalu		= (isset($_REQUEST['stand_lalu'])) ? to_number($_REQUEST['stand_lalu']) : '';
		$stand_angkat	= (isset($_REQUEST['stand_angkat'])) ? to_number($_REQUEST['stand_angkat']) : '';
		$stand_akhir	= (isset($_REQUEST['stand_akhir'])) ? to_number($_REQUEST['stand_akhir']) : '';
		
		$pemakaian		= (($stand_akhir + $stand_angkat) - $stand_lalu);
		ex_less_then($pemakaian, 0, 'Pemakaian tidak boleh minus!');
		
		$key_air = $obj->fields['KEY_AIR'];
		
		$query = "
		SELECT 
			KEY_AIR,
			BLOK1,
			BLOK2,
			BLOK3,
			BLOK4,
			STAND_MIN_PAKAI, 
			TARIF1,
			TARIF2,
			TARIF3,
			TARIF4,
			ABONEMEN
		FROM KWT_TARIF_AIR 
		WHERE KEY_AIR = '$key_air'";
		
		$obj = $conn->Execute($query);
		
		$key_air = $obj->fields['KEY_AIR'];
		ex_empty($key_air, "Kode tarif air \"$key_air\" tidak terdaftar di Master Air -> Tarif.");
		
		$abonemen = (int) $obj->fields['ABONEMEN'];
		$limit_blok1 = (int) $obj->fields['BLOK1'];
		$limit_blok2 = (int) $obj->fields['BLOK2'];
		$limit_blok3 = (int) $obj->fields['BLOK3'];
		$limit_blok4 = (int) $obj->fields['BLOK4'];
		$limit_stand_min_pakai = (int) $obj->fields['STAND_MIN_PAKAI'];
		$tarif1 = (int) $obj->fields['TARIF1'];
		$tarif2 = (int) $obj->fields['TARIF2'];
		$tarif3 = (int) $obj->fields['TARIF3'];
		$tarif4 = (int) $obj->fields['TARIF4'];

		$blok1 = 0;
		$blok2 = 0;
		$blok3 = 0;
		$blok4 = 0;
		$stand_min_pakai = 0;
		$tarif_min_pakai = 0;
		
		if ($pemakaian < $limit_stand_min_pakai)
		{
			$blok1 = $pemakaian;
			$stand_min_pakai = $limit_stand_min_pakai - $blok1;
			$tarif_min_pakai = $tarif1;
		}
		else
		{
			if ($pemakaian > $limit_blok1) { $blok1 = $limit_blok1; $pemakaian -= $blok1;
				
				if ($pemakaian > $limit_blok2) { $blok2 = $limit_blok2; $pemakaian -= $blok2;
				
					if ($pemakaian > $limit_blok3) { $blok3 = $limit_blok3; $pemakaian -= $blok3;
				
						$blok4 = max(0, $pemakaian);
						
					} else { $blok3 = max(0, $pemakaian); }
				} else { $blok2 = max(0, $pemakaian); }
			} else { $blok1 = max(0, $pemakaian); }
		}
		
		$jumlah_air = ($blok1 * $tarif1) + ($blok2 * $tarif2) + ($blok3 * $tarif3) + ($blok4 * $tarif4) + ($stand_min_pakai * $tarif_min_pakai);
		
		$query = "
		UPDATE KWT_PEMBAYARAN_AI 
		SET 
			STAND_LALU = $stand_lalu,
			STAND_ANGKAT = $stand_angkat,
			STAND_AKHIR = $stand_akhir,
			BLOK1 = $blok1,
			BLOK2 = $blok2,
			BLOK3 = $blok3,
			BLOK4 = $blok4,
			STAND_MIN_PAKAI = $stand_min_pakai,
			TARIF1 = $tarif1,
			TARIF2 = $tarif2,
			TARIF3 = $tarif3,
			TARIF4 = $tarif4,
			TARIF_MIN_PAKAI = $tarif_min_pakai,

			JUMLAH_AIR = $jumlah_air,
			ABONEMEN = $abonemen,
			JUMLAH_BAYAR = 0,
			
			STATUS_EDIT = '1',
			USER_EDIT = '$user_edit',
			TGL_EDIT = GETDATE()
		WHERE
			ID_PEMBAYARAN = '$id'
		";
		
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Data stand meter berhasil diubah.';
	}
	catch (Exception $e)
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
	DECLARE 
	@adm_kv INT,
	@adm_bg INT,
	@adm_hn INT,
	@adm_rv INT
	
	SELECT TOP 1 
	@adm_kv = ISNULL(ADMINISTRASI_KV, 0) ,
	@adm_bg = ISNULL(ADMINISTRASI_BG, 0) ,
	@adm_hn = ISNULL(ADMINISTRASI_HN, 0) ,
	@adm_rv = ISNULL(ADMINISTRASI_RV, 0)
	FROM KWT_PARAMETER
	
	SELECT
		(
			SELECT COUNT(NO_PELANGGAN)
			FROM KWT_PEMBAYARAN_AI
			WHERE $where_trx_air_ipl AND  STATUS_BAYAR IS NULL AND NO_PELANGGAN = b.NO_PELANGGAN
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
					WHEN '1' THEN @adm_kv
					WHEN '2' THEN @adm_bg 
					WHEN '4' THEN @adm_hn
					WHEN '5' THEN @adm_rv
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
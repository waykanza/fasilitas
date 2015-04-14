<?php
require_once('../../../config/config.php');
die_login();
die_mod('PA1');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;

$id = (isset($_REQUEST['id'])) ? base64_decode(clean($_REQUEST['id'])) : '';
$act = (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'pembayaran')
	{
		try
		{
			$conn->begintrans();
			
			$cara_bayar		= (isset($_REQUEST['cara_bayar'])) ? clean($_REQUEST['cara_bayar']) : '';
			$bayar_via		= (isset($_REQUEST['bayar_via'])) ? clean($_REQUEST['bayar_via']) : '';
			$tgl_bayar_bank	= (isset($_REQUEST['tgl_bayar_bank'])) ? to_date($_REQUEST['tgl_bayar_bank']) : '';
			$ket_bayar		= (isset($_REQUEST['ket_bayar'])) ? clean($_REQUEST['ket_bayar']) : '';

			ex_empty($cara_bayar, 'Pilih jenis pembayaran.');
			ex_empty($bayar_via, 'Pilih kode bank.');
			ex_empty($tgl_bayar_bank, 'Masukkan tanggal bayar.');
			
			$obj = $conn->Execute("SELECT STATUS_BAYAR FROM KWT_PEMBAYARAN_AI WHERE ID_PEMBAYARAN = '$id'");
			ex_equal($obj->fields['STATUS_BAYAR'], '1', 'Tagihan sudah dibayar!');
			
			$query = "
			DECLARE 
			@adm_kv INT,
			@adm_bg INT,
			@adm_hn INT,
			@adm_rv INT
			
			SELECT TOP 1 
			@adm_kv = ADM_KV ,
			@adm_bg = ADM_BG ,
			@adm_hn = ADM_HN ,
			@adm_rv = ADM_RV
			FROM KWT_PARAMETER
			
			UPDATE KWT_PEMBAYARAN_AI 
			SET 
				STATUS_BAYAR = 1, 
				CARA_BAYAR = $cara_bayar,
				BAYAR_VIA = '$bayar_via', 
				
				TGL_BAYAR_BANK = '$tgl_bayar_bank',
				TGL_BAYAR_SYS = GETDATE(), 
				USER_BAYAR = '$sess_id_user',
				KET_BAYAR = '$ket_bayar',
				
				ADM = 
				(
					CASE TRX
						WHEN $trx_kv THEN @adm_kv
						WHEN $trx_bg THEN @adm_bg 
						WHEN $trx_hn THEN @adm_hn
						WHEN $trx_rv THEN @adm_rv
						ELSE 0 
					END
				),
				JUMLAH_BAYAR = 
				(
					JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA + 
					CASE TRX
						WHEN $trx_kv THEN @adm_kv
						WHEN $trx_bg THEN @adm_bg 
						WHEN $trx_hn THEN @adm_hn
						WHEN $trx_rv THEN @adm_rv
						ELSE 0 
					END - 
					DISKON_AIR - DISKON_IPL
				),
				
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tagihan berhasil dibayar.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'diskon_air' || $act == 'diskon_ipl')
	{
		try
		{
			$conn->begintrans();
			
			$obj = $conn->Execute("
			SELECT
				STATUS_POST_PB,
				STATUS_BAYAR,
				DISKON_AIR,
				DISKON_IPL,
				KET_DISKON_AIR,
				KET_DISKON_IPL
			FROM KWT_PEMBAYARAN_AI
			WHERE 
				ID_PEMBAYARAN = '$id'
			");
			
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Tagihan sudah di-posting!');
			ex_equal($obj->fields['STATUS_BAYAR'], '1', 'Tagihan sudah dibayar!');
			
			$old_diskon_air		= $obj->fields['DISKON_AIR'];
			$old_diskon_ipl		= $obj->fields['DISKON_IPL'];
			$old_ket_diskon_air	= $obj->fields['KET_DISKON_AIR'];
			$old_ket_diskon_ipl	= $obj->fields['KET_DISKON_IPL'];
			
			$diskon_air		= (isset($_REQUEST['diskon_air'])) ? to_number($_REQUEST['diskon_air']) : '0';
			$diskon_ipl		= (isset($_REQUEST['diskon_ipl'])) ? to_number($_REQUEST['diskon_ipl']) : '0';
			$ket_diskon_air	= (isset($_REQUEST['ket_diskon_air'])) ? clean($_REQUEST['ket_diskon_air']) : '';
			$ket_diskon_ipl	= (isset($_REQUEST['ket_diskon_ipl'])) ? clean($_REQUEST['ket_diskon_ipl']) : '';
			
			# AIR
			if ($act == 'diskon_air' AND (
					$diskon_air != $old_diskon_air || 
					strtoupper($ket_diskon_air) != strtoupper($old_ket_diskon_air)
				))
			{
				
				$query = "
				UPDATE KWT_PEMBAYARAN_AI 
				SET 
					USER_DISKON_AIR = '$sess_id_user'
					,TGL_DISKON_AIR = GETDATE()
					,DISKON_AIR = '$diskon_air'
					,KET_DISKON_AIR = '$ket_diskon_air'
					,USER_MODIFIED = '$sess_id_user' 
					,MODIFIED_DATE = GETDATE() 
				WHERE
					ID_PEMBAYARAN = '$id'
				";
				
				ex_false($conn->Execute($query), $query);
				
				$msg = 'Diskon berhasil ditambahkan.';
			}
				
			# IPL
			if ($act == 'diskon_ipl' AND (
					$diskon_ipl != $old_diskon_ipl || 
					strtoupper($ket_diskon_ipl) != strtoupper($old_ket_diskon_ipl)
				))
			{
				$query = "
				UPDATE KWT_PEMBAYARAN_AI 
				SET 
					USER_DISKON_IPL = '$sess_id_user'
					,TGL_DISKON_IPL = GETDATE() 
					,DISKON_IPL = '$diskon_ipl'
					,KET_DISKON_IPL = '$ket_diskon_ipl'
					,USER_MODIFIED = '$sess_id_user' 
					,MODIFIED_DATE = GETDATE() 
				WHERE
					ID_PEMBAYARAN = '$id'
				";
				
				ex_false($conn->Execute($query), $query);
				
				$msg = 'Diskon berhasil ditambahkan.';
			}
			
			$conn->committrans();
			
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
			
			$ket_batal = (isset($_REQUEST['ket_batal'])) ? clean($_REQUEST['ket_batal']) : '';
			
			ex_empty($ket_batal, 'Masukkan keterangan pembatalan.');
			
			$obj = $conn->Execute("SELECT STATUS_BAYAR, STATUS_POST_PB FROM KWT_PEMBAYARAN_AI WHERE ID_PEMBAYARAN = '$id'");
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Tagihan sudah di-posting!');
			ex_equal($obj->fields['STATUS_BAYAR'], '0', 'Tagihan belum dibayar!');
			
			$query = "
			UPDATE KWT_PEMBAYARAN_AI 
			SET 
				STATUS_BAYAR = 0,
				CARA_BAYAR = 0,
				BAYAR_VIA = NULL,
				
				TGL_BAYAR_BANK = NULL,
				TGL_BAYAR_SYS = NULL,
				USER_BAYAR = NULL,
				KET_BAYAR = NULL,
				
				ADM = 0,
				JUMLAH_BAYAR = 0,
				
				NO_KWITANSI = NULL,
				
				STATUS_CETAK_KWT = 0, 
				USER_CETAK_KWT = NULL, 
				TGL_CETAK_KWT = NULL, 
				
				STATUS_BATAL = 1, 
				USER_BATAL = '$sess_id_user', 
				TGL_BATAL = GETDATE(), 
				KET_BATAL = '$ket_batal', 
					
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tagihan pembayaran berhasil dibatalkan.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'stand_meter')
	{
		try
		{
			
			$conn->begintrans();
			
			$obj = $conn->Execute("
			SELECT 
				b.STATUS_POST_PB, 
				b.STATUS_BAYAR, 
				b.KEY_AIR, 
				
				t.KEY_AIR AS KEY_AIR_LOOKUP, 
				t.BLOK1, 
				t.BLOK2,
				t.BLOK3, 
				t.BLOK4,
				t.STAND_MIN_PAKAI, 
				t.TARIF1, 
				t.TARIF2,
				t.TARIF3, 
				t.TARIF4,
				t.ABONEMEN
			FROM 
				KWT_PEMBAYARAN_AI b 
				LEFT JOIN KWT_TARIF_AIR t ON b.KEY_AIR = t.KEY_AIR 
			WHERE
				ID_PEMBAYARAN = '$id'
			");
			
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Tagihan sudah di-posting!');
			ex_equal($obj->fields['STATUS_BAYAR'], '1', 'Tagihan sudah dibayar!');
			
			$key_air		= $obj->fields['KEY_AIR'];
			
			ex_empty($obj->fields['KEY_AIR_LOOKUP'], "Kode tarif air \"$key_air\" tidak terdaftar di Master Air -> Tarif.");
			
			$stand_lalu		= (isset($_REQUEST['stand_lalu'])) ? to_number($_REQUEST['stand_lalu']) : '0';
			$stand_angkat	= (isset($_REQUEST['stand_angkat'])) ? to_number($_REQUEST['stand_angkat']) : '0';
			$stand_akhir	= (isset($_REQUEST['stand_akhir'])) ? to_number($_REQUEST['stand_akhir']) : '0';
			$pemakaian		= (($stand_akhir + $stand_angkat) - $stand_lalu);
			
			ex_less($pemakaian, 0, 'Pemakaian tidak boleh minus!');
			
			$limit_blok1 = (int) $obj->fields['BLOK1'];
			$limit_blok2 = (int) $obj->fields['BLOK2'];
			$limit_blok3 = (int) $obj->fields['BLOK3'];
			$limit_blok4 = (int) $obj->fields['BLOK4'];
			$limit_stand_min_pakai = (int) $obj->fields['STAND_MIN_PAKAI'];
			$tarif1 = (int) $obj->fields['TARIF1'];
			$tarif2 = (int) $obj->fields['TARIF2'];
			$tarif3 = (int) $obj->fields['TARIF3'];
			$tarif4 = (int) $obj->fields['TARIF4'];
			$abonemen = (int) $obj->fields['ABONEMEN'];

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
				if ($pemakaian > $limit_blok1) { 
					$blok1 = $limit_blok1; 
					$pemakaian -= $blok1;
					
					if ($pemakaian > ($limit_blok2 - $limit_blok1)) { 
						$blok2 = ($limit_blok2 - $limit_blok1); 
						$pemakaian -= ($limit_blok2 - $limit_blok1);
					
						if ($pemakaian > ($limit_blok3 - $limit_blok2)) { 
							$blok3 = ($limit_blok3 - $limit_blok2); 
							$pemakaian -= ($limit_blok3 - $limit_blok2);
					
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
					
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				ID_PEMBAYARAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Data stand meter berhasil diubah.';
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
	@adm_kv INT,
	@adm_bg INT,
	@adm_hn INT,
	@adm_rv INT
	
	SELECT TOP 1 
	@adm_kv = ADM_KV ,
	@adm_bg = ADM_BG ,
	@adm_hn = ADM_HN ,
	@adm_rv = ADM_RV
	FROM KWT_PARAMETER
	
	SELECT
		(
			SELECT COUNT(NO_PELANGGAN)
			FROM KWT_PEMBAYARAN_AI
			WHERE $where_trx_air_ipl AND STATUS_BAYAR = 0 AND NO_PELANGGAN = b.NO_PELANGGAN
		) AS JUMLAH_PIUTANG,
		
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.PERIODE_TAG,
		b.PERIODE_AIR,
		b.PERIODE_IPL_AWAL,
		b.PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
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
			CASE WHEN STATUS_BAYAR = 1 
			THEN b.ADM 
			ELSE 
				CASE TRX
					WHEN $trx_kv THEN @adm_kv
					WHEN $trx_bg THEN @adm_bg 
					WHEN $trx_hn THEN @adm_hn
					WHEN $trx_rv THEN @adm_rv
					ELSE 0 
				END
			END 
		) AS ADM,
		
		ua.NAMA_USER AS USER_DISKON_AIR,
		CONVERT(VARCHAR(10),b.TGL_DISKON_AIR,105) AS TGL_DISKON_AIR,
		b.DISKON_AIR,
		b.KET_DISKON_AIR,
		
		ui.NAMA_USER AS USER_DISKON_IPL,
		CONVERT(VARCHAR(10),b.TGL_DISKON_IPL,105) AS TGL_DISKON_IPL,
		b.DISKON_IPL,
		b.KET_DISKON_IPL,
		
		b.JUMLAH_BAYAR,		
		
		b.STATUS_BAYAR,
		b.CARA_BAYAR,
		b.BAYAR_VIA,
		CONVERT(VARCHAR(19),b.TGL_BAYAR_BANK,120) AS TGL_BAYAR_BANK,
		CONVERT(VARCHAR(19),b.TGL_BAYAR_SYS,120) AS TGL_BAYAR_SYS,
		b.NO_INVOICE,
		b.NO_KWITANSI,
		b.KET_IVC,
		b.KET_BAYAR,
		uk.NAMA_USER AS USER_BAYAR,
		
		b.NO_FP,
		
		b.STATUS_CETAK_KWT,
		uc.NAMA_USER AS USER_CETAK_KWT,
		CONVERT(VARCHAR(19),b.TGL_CETAK_KWT,120) AS TGL_CETAK_KWT,
		
		b.STATUS_BATAL,
		ub.NAMA_USER AS USER_BATAL,
		CONVERT(VARCHAR(19),b.TGL_BATAL,120) AS TGL_BATAL,
		b.KET_BATAL
	FROM 
		KWT_PEMBAYARAN_AI b
		LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
		LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR
		LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER
		
		LEFT JOIN KWT_USER uk ON b.USER_BAYAR = uk.ID_USER 
		LEFT JOIN KWT_USER uc ON b.USER_CETAK_KWT = uc.ID_USER 
		LEFT JOIN KWT_USER ua ON b.USER_DISKON_AIR = ua.ID_USER 
		LEFT JOIN KWT_USER ui ON b.USER_DISKON_IPL = ui.ID_USER 
		LEFT JOIN KWT_USER ub ON b.USER_BATAL = ub.ID_USER 
	WHERE 
		ID_PEMBAYARAN = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$jumlah_piutang		= $obj->fields['JUMLAH_PIUTANG'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode_air			= $obj->fields['PERIODE_AIR'];
	$periode_tag			= $obj->fields['PERIODE_TAG'];
	$periode_ipl_awal		= $obj->fields['PERIODE_IPL_AWAL'];
	$periode_ipl_akhir		= $obj->fields['PERIODE_IPL_AKHIR'];
	$jumlah_periode_ipl		= $obj->fields['JUMLAH_PERIODE_IPL'];
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
	$adm		= $obj->fields['ADM'];
	
	$user_diskon_air	= $obj->fields['USER_DISKON_AIR'];
	$tgl_diskon_air		= $obj->fields['TGL_DISKON_AIR'];
	$diskon_air			= $obj->fields['DISKON_AIR'];
	$ket_diskon_air		= $obj->fields['KET_DISKON_AIR'];

	$user_diskon_ipl	= $obj->fields['USER_DISKON_IPL'];
	$tgl_diskon_ipl		= $obj->fields['TGL_DISKON_IPL'];
	$diskon_ipl			= $obj->fields['DISKON_IPL'];
	$ket_diskon_ipl		= $obj->fields['KET_DISKON_IPL'];
	
	$ket_ivc			= $obj->fields['KET_IVC'];
	
	$total				= $jumlah_air + $abonemen + $jumlah_ipl + $denda + $adm;
	$diskon				= $diskon_air + $diskon_ipl;
	
	$jumlah_bayar		= $obj->fields['JUMLAH_BAYAR'];
	
	$status_bayar		= $obj->fields['STATUS_BAYAR'];
	$cara_bayar			= $obj->fields['CARA_BAYAR'];
	$bayar_via			= $obj->fields['BAYAR_VIA'];
	$tgl_bayar_bank		= $obj->fields['TGL_BAYAR_BANK'];
	$tgl_bayar_sys		= $obj->fields['TGL_BAYAR_SYS'];
	$no_invoice			= $obj->fields['NO_INVOICE'];
	$no_kwitansi		= $obj->fields['NO_KWITANSI'];
	$ket_bayar			= $obj->fields['KET_BAYAR'];
	$user_bayar			= $obj->fields['USER_BAYAR'];
	
	$no_fp				= $obj->fields['NO_FP'];
	
	$status_cetak_kwt	= $obj->fields['STATUS_CETAK_KWT'];
	$user_cetak_kwt		= $obj->fields['USER_CETAK_KWT'];
	$tgl_cetak_kwt		= $obj->fields['TGL_CETAK_KWT'];
	
	$status_batal		= $obj->fields['STATUS_BATAL'];
	$user_batal			= $obj->fields['USER_BATAL'];
	$tgl_batal			= $obj->fields['TGL_BATAL'];
	$ket_batal			= $obj->fields['KET_BATAL'];
	
	$diskon_air_persen = ($jumlah_air != 0) ? (($diskon_air / $jumlah_air) * 100) : 0;
	$diskon_ipl_persen = ($jumlah_ipl != 0) ? (($diskon_ipl / $jumlah_ipl) * 100) : 0;
}

$id = base64_encode($id);
?>
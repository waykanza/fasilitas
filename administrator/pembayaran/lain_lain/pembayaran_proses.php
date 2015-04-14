<?php
require_once('../../../config/config.php');
die_login();
die_mod('PL1');
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
			@adm_lbg INT,
			@adm_lrv INT
			
			SELECT TOP 1 
			@adm_lbg = ADM_DBG ,
			@adm_lrv = ADM_DRV 
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
						WHEN $trx_lbg THEN @adm_lbg
						WHEN $trx_lrv THEN @adm_lrv 
						ELSE 0 
					END
				),
				JUMLAH_BAYAR = 
				(
					JUMLAH_IPL + DENDA + 
					CASE TRX
						WHEN $trx_lbg THEN @adm_lbg
						WHEN $trx_lrv THEN @adm_lrv 
						ELSE 0 
					END - 
					DISKON_IPL
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
	elseif ($act == 'diskon_ipl')
	{
		try
		{
			$conn->begintrans();
			
			$obj = $conn->Execute("
			SELECT
				STATUS_POST_PB,
				STATUS_BAYAR,
				DISKON_IPL,
				KET_DISKON_IPL
			FROM KWT_PEMBAYARAN_AI
			WHERE 
				ID_PEMBAYARAN = '$id'
			");
			
			ex_equal($obj->fields['STATUS_POST_PB'], '1', 'Tagihan sudah di-posting!');
			ex_equal($obj->fields['STATUS_BAYAR'], '1', 'Tagihan sudah dibayar!');
			
			$old_diskon_ipl		= $obj->fields['DISKON_IPL'];
			$old_ket_diskon_ipl	= $obj->fields['KET_DISKON_IPL'];
			
			$diskon_ipl		= (isset($_REQUEST['diskon_ipl'])) ? to_number($_REQUEST['diskon_ipl']) : '0';
			$ket_diskon_ipl	= (isset($_REQUEST['ket_diskon_ipl'])) ? clean($_REQUEST['ket_diskon_ipl']) : '';
			
			if (
				$diskon_ipl != $old_diskon_ipl || 
				strtoupper($ket_diskon_ipl) != strtoupper($old_ket_diskon_ipl) 
			)
			{				
				$query_diskon = "
				,USER_DISKON_IPL = '$sess_id_user'
				,TGL_DISKON_IPL = GETDATE()
				,DISKON_IPL = '$diskon_ipl'
				,KET_DISKON_IPL = '$ket_diskon_ipl'";
				
				$query = "
				UPDATE KWT_PEMBAYARAN_AI 
				SET 
					USER_MODIFIED = '$sess_id_user' 
					, MODIFIED_DATE = GETDATE() 
					$query_diskon
				WHERE
					ID_PEMBAYARAN = '$id'
				";
				
				ex_false($conn->Execute($query), $query);
			}
			
			$conn->committrans();
			
			$msg = 'Diskon berhasil ditambahkan.';
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
	
	close($conn);
	$json = array('msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}
else
{
	$query = "
	DECLARE 
	@adm_lbg INT,
	@adm_lrv INT
			
	SELECT TOP 1 
	@adm_lbg = ADM_DBG ,
	@adm_lrv = ADM_DRV 
	FROM KWT_PARAMETER
	
	SELECT
		(
			SELECT COUNT(NO_PELANGGAN)
			FROM KWT_PEMBAYARAN_AI
			WHERE $where_trx_lain_lain AND STATUS_BAYAR = 0 AND NO_PELANGGAN = b.NO_PELANGGAN
		) AS JUMLAH_PIUTANG,
		
		b.NO_PELANGGAN,
		p.NAMA_PELANGGAN,
		b.PERIODE_TAG,
		b.PERIODE_IPL_AWAL,
		b.PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
		b.KODE_BLOK,
		s.NAMA_SEKTOR + ' (' + b.KODE_SEKTOR + ')' AS NAMA_SEKTOR,
		c.NAMA_CLUSTER + ' (' + b.KODE_CLUSTER + ')' AS NAMA_CLUSTER,
		b.STATUS_BLOK,
	
		b.AKTIF_IPL,
		b.KEY_IPL,
		
		b.LUAS_KAVLING,
		
		b.JUMLAH_IPL,
		b.DENDA,
		(
			CASE WHEN STATUS_BAYAR = 1 
			THEN b.ADM 
			ELSE 
				CASE TRX
					WHEN $trx_lbg THEN @adm_lbg
					WHEN $trx_lrv THEN @adm_lrv 
					ELSE 0 
				END
			END 
		) AS ADM,
		
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
		LEFT JOIN KWT_USER ui ON b.USER_DISKON_IPL = ui.ID_USER 
		LEFT JOIN KWT_USER ub ON b.USER_BATAL = ub.ID_USER 
	WHERE 
		ID_PEMBAYARAN = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$jumlah_piutang		= $obj->fields['JUMLAH_PIUTANG'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$periode_tag		= $obj->fields['PERIODE_TAG'];
	$periode_ipl_awal	= $obj->fields['PERIODE_IPL_AWAL'];
	$periode_ipl_akhir	= $obj->fields['PERIODE_IPL_AKHIR'];
	$jumlah_periode_ipl	= $obj->fields['JUMLAH_PERIODE_IPL'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$nama_sektor		= $obj->fields['NAMA_SEKTOR'];
	$nama_cluster		= $obj->fields['NAMA_CLUSTER'];
	$status_blok		= $obj->fields['STATUS_BLOK'];
	
	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$denda				= $obj->fields['DENDA'];
	$adm		= $obj->fields['ADM'];

	$user_diskon_ipl	= $obj->fields['USER_DISKON_IPL'];
	$tgl_diskon_ipl		= $obj->fields['TGL_DISKON_IPL'];
	$diskon_ipl			= $obj->fields['DISKON_IPL'];
	$ket_diskon_ipl		= $obj->fields['KET_DISKON_IPL'];
	
	$ket_ivc			= $obj->fields['KET_IVC'];
	
	$total				= $jumlah_ipl + $denda + $adm;
	$diskon				= $diskon_ipl;
	
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
	
	$diskon_ipl_persen = ($jumlah_ipl != 0) ? (($diskon_ipl / $jumlah_ipl) * 100) : 0;
}

$id = base64_encode($id);
?>
<?php
require_once('../../../config/config.php');
die_login();
die_mod('M15');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

	$status_blok		= (isset($_REQUEST['status_blok'])) ? to_number($_REQUEST['status_blok']) : '0';
	$info_tagihan		= (isset($_REQUEST['info_tagihan'])) ? to_number($_REQUEST['info_tagihan']) : '0';

	$no_ktp				= (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
	$nama_pelanggan		= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
	$npwp				= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
	$alamat				= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';
	$no_telepon			= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
	$no_hp				= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
	
	$aktif_sm			= (isset($_REQUEST['aktif_sm'])) ? to_number($_REQUEST['aktif_sm']) : '0';
	$sm_no_ktp			= (isset($_REQUEST['sm_no_ktp'])) ? clean($_REQUEST['sm_no_ktp']) : '';
	$sm_nama_pelanggan	= (isset($_REQUEST['sm_nama_pelanggan'])) ? clean($_REQUEST['sm_nama_pelanggan']) : '';
	$sm_npwp			= (isset($_REQUEST['sm_npwp'])) ? clean($_REQUEST['sm_npwp']) : '';
	$sm_alamat			= (isset($_REQUEST['sm_alamat'])) ? clean($_REQUEST['sm_alamat']) : '';
	$sm_no_telepon		= (isset($_REQUEST['sm_no_telepon'])) ? clean($_REQUEST['sm_no_telepon']) : '';
	$sm_no_hp			= (isset($_REQUEST['sm_no_hp'])) ? clean($_REQUEST['sm_no_hp']) : '';

	$aktif_ad			= (isset($_REQUEST['aktif_ad'])) ? to_number($_REQUEST['aktif_ad']) : '0';
	$kode_bank			= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
	$no_rekening		= (isset($_REQUEST['no_rekening'])) ? clean($_REQUEST['no_rekening']) : '';

	$ket				= (isset($_REQUEST['ket'])) ? clean($_REQUEST['ket']) : '';

	$periode_putus		= (isset($_REQUEST['periode_putus'])) ? to_periode($_REQUEST['periode_putus']) : '';
	$tgl_pemutusan		= (isset($_REQUEST['tgl_pemutusan'])) ? to_date($_REQUEST['tgl_pemutusan']) : '';
	$petugas			= (isset($_REQUEST['petugas'])) ? clean($_REQUEST['petugas']) : '';

	$aktif_air			= (isset($_REQUEST['aktif_air'])) ? to_number($_REQUEST['aktif_air']) : '0';
	$kode_zona			= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
	$tipe_air			= (isset($_REQUEST['tipe_air'])) ? clean($_REQUEST['tipe_air']) : '';
	$key_air			= (isset($_REQUEST['key_air'])) ? clean($_REQUEST['key_air']) : '';

	$aktif_ipl			= (isset($_REQUEST['aktif_ipl'])) ? to_number($_REQUEST['aktif_ipl']) : '0';
	$tipe_ipl			= (isset($_REQUEST['tipe_ipl'])) ? clean($_REQUEST['tipe_ipl']) : '';
	$key_ipl			= (isset($_REQUEST['key_ipl'])) ? clean($_REQUEST['key_ipl']) : '';
	
	$golongan			= (isset($_REQUEST['golongan'])) ? to_number($_REQUEST['golongan']) : '0';
	$tipe_denda			= (isset($_REQUEST['tipe_denda'])) ? to_number($_REQUEST['tipe_denda']) : '0';
	
	# ++
	$kode_blok			= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
	$luas_kavling		= (isset($_REQUEST['luas_kavling'])) ? to_decimal($_REQUEST['luas_kavling']) : '';
	$luas_bangunan		= (isset($_REQUEST['luas_bangunan'])) ? to_decimal($_REQUEST['luas_bangunan']) : '';
	$kode_sektor		= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
	$kode_cluster		= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
	$tgl_ppjb			= (isset($_REQUEST['tgl_ppjb'])) ? to_date($_REQUEST['tgl_ppjb']) : '';
	$no_pelanggan		= (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
	
	$jumlah_piutang_ai	= 0; $nilai_piutang_ai = 0;
	$jumlah_piutang_dp	= 0; $nilai_piutang_dp = 0;
	$jumlah_piutang_ll	= 0; $nilai_piutang_ll = 0;
	
	$join_to = '';
	$status_join = '';
	
	$split_from = '';
	$status_split = '';
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($tgl_ppjb, 'Tanggal PPJB tidak boleh kosong.');
			ex_empty($kode_blok, 'Kode blok tidak boleh kosong.');
			ex_empty($luas_kavling, 'Luas kavling tidak boleh kosong.');
			ex_empty($kode_sektor, 'Pilih kode sektor.');
			ex_empty($kode_cluster, 'Pilih kode cluster.');
			ex_empty($status_blok, 'Pilih status blok.');
			ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
			
			if ($aktif_air != 0) {
				ex_empty($key_air, 'Pilih kode tarif air.');
			}
			if ($aktif_ipl != 0) {
				ex_empty($key_ipl, 'Pilih kode tarif IPL.');
			}
			
			$query = "SELECT COUNT(KODE_BLOK) AS TOTAL FROM KWT_PELANGGAN WHERE KODE_BLOK = '$kode_blok'";
			ex_found($conn->Execute($query)->fields['TOTAL'], "Kode blok \"$kode_blok\" telah terdaftar.");
			
			$query = "
			DECLARE @np VARCHAR(15) = 
			( 
				SELECT
					ISNULL(KODE_PEL,'00') + 
					RIGHT(YEAR(GETDATE()),2) + '0' + 
					(SELECT RIGHT('0000000' + CAST(COU_NP AS VARCHAR), 7) FROM KWT_PARAMETER)
				FROM KWT_SEKTOR 
				WHERE KODE_SEKTOR = '$kode_sektor'
			) 
			
			INSERT INTO KWT_PELANGGAN
			(
				NO_PELANGGAN,
				
				KODE_BLOK, LUAS_KAVLING, LUAS_BANGUNAN, KODE_SEKTOR, KODE_CLUSTER, TGL_PPJB,
				STATUS_BLOK, INFO_TAGIHAN, NO_KTP, NAMA_PELANGGAN, NPWP, ALAMAT, NO_TELEPON, NO_HP,
				AKTIF_SM, SM_NAMA_PELANGGAN, SM_NO_KTP, SM_NPWP, SM_NO_HP, SM_NO_TELEPON, SM_ALAMAT, 
				AKTIF_AD, KODE_BANK, NO_REKENING, KET, AKTIF_AIR, KODE_ZONA, TIPE_AIR, KEY_AIR, 
				AKTIF_IPL, TIPE_IPL, KEY_IPL, 
				
				GOLONGAN, TIPE_DENDA, 
				
				USER_CREATED
			)
			VALUES 
			(
				@np,
				
				dbo.ETN('$kode_blok'), $luas_kavling, $luas_bangunan, dbo.ETN('$kode_sektor'), dbo.ETN('$kode_cluster'), CONVERT(DATETIME,'$tgl_ppjb',105),
				
				$status_blok, $info_tagihan, dbo.ETN('$no_ktp'), dbo.ETN('$nama_pelanggan'),
				dbo.ETN('$npwp'), dbo.ETN('$alamat'), dbo.ETN('$no_telepon'), dbo.ETN('$no_hp'),
				
				$aktif_sm, dbo.ETN('$sm_nama_pelanggan'), dbo.ETN('$sm_no_ktp'), dbo.ETN('$sm_npwp'), dbo.ETN('$sm_no_hp'),
				dbo.ETN('$sm_no_telepon'), dbo.ETN('$sm_alamat'), $aktif_ad, dbo.ETN('$kode_bank'), dbo.ETN('$no_rekening'), dbo.ETN('$ket'),
				
				$aktif_air, dbo.ETN('$kode_zona'), dbo.ETN('$tipe_air'), dbo.ETN('$key_air'),
	
				$aktif_ipl, dbo.ETN('$tipe_ipl'), dbo.ETN('$key_ipl'), 
				
				$golongan, $tipe_denda, 
				
				'$sess_id_user'
			)
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("UPDATE KWT_PARAMETER SET COU_NP = COU_NP + 1");
			
			$conn->committrans();
			
			$msg = "Data pelanggan \"$kode_blok\" berhasil disimpan.";
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'Ubah') # Proses Ubah
	{
		try
		{
			$conn->begintrans();

			ex_empty($tgl_ppjb, 'Tanggal PPJB tidak boleh kosong.');
			ex_empty($kode_blok, 'Kode blok tidak boleh kosong.');
			ex_empty($luas_kavling, 'Luas kavling tidak boleh kosong.');
			ex_empty($kode_sektor, 'Pilih kode sektor.');
			ex_empty($kode_cluster, 'Pilih kode cluster.');
			ex_empty($status_blok, 'Pilih status blok.');
			ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
			
			if ($aktif_air != 0) {
				ex_empty($key_air, 'Pilih kode tarif air.');
			}
			if ($aktif_ipl != 0) {
				ex_empty($key_ipl, 'Pilih kode tarif IPL.');
			}
			
			$query = "
			UPDATE KWT_PELANGGAN 
			SET 			
				KODE_BLOK = dbo.ETN('$kode_blok'), 
				LUAS_KAVLING = $luas_kavling, 
				LUAS_BANGUNAN = $luas_bangunan, 
				KODE_SEKTOR = dbo.ETN('$kode_sektor'), 
				KODE_CLUSTER = dbo.ETN('$kode_cluster'), 
				TGL_PPJB = CONVERT(DATETIME,'$tgl_ppjb',105),
				
				STATUS_BLOK = $status_blok, 
				INFO_TAGIHAN = $info_tagihan,
				
				NO_KTP = dbo.ETN('$no_ktp'),
				NAMA_PELANGGAN = dbo.ETN('$nama_pelanggan'),
				NPWP = dbo.ETN('$npwp'),
				ALAMAT = dbo.ETN('$alamat'),
				NO_TELEPON = dbo.ETN('$no_telepon'),
				NO_HP = dbo.ETN('$no_hp'),
				
				AKTIF_SM = $aktif_sm,
				SM_NAMA_PELANGGAN = dbo.ETN('$sm_nama_pelanggan'),
				SM_NO_KTP = dbo.ETN('$sm_no_ktp'),
				SM_NPWP = dbo.ETN('$sm_npwp'),
				SM_NO_HP = dbo.ETN('$sm_no_hp'),
				SM_NO_TELEPON = dbo.ETN('$sm_no_telepon'),
				SM_ALAMAT = dbo.ETN('$sm_alamat'),
				
				AKTIF_AD = $aktif_ad,
				KODE_BANK = dbo.ETN('$kode_bank'),
				NO_REKENING = dbo.ETN('$no_rekening'),
				
				KET = dbo.ETN('$ket'),
				
				AKTIF_AIR = $aktif_air,
				KODE_ZONA = dbo.ETN('$kode_zona'),
				TIPE_AIR = dbo.ETN('$tipe_air'),
				KEY_AIR = dbo.ETN('$key_air'),
	
				AKTIF_IPL = $aktif_ipl,
				TIPE_IPL = dbo.ETN('$tipe_ipl'),
				KEY_IPL = dbo.ETN('$key_ipl'), 
				
				GOLONGAN = $golongan, 
				TIPE_DENDA = $tipe_denda,
				
				USER_MODIFIED = '$sess_id_user', 
				MODIFIED_DATE = GETDATE() 
			WHERE
				NO_PELANGGAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$pelanggan_lookup = array(
				'no_ktp' => $no_ktp, 'nama_pelanggan' => $nama_pelanggan, 'npwp' => $npwp, 'alamat' => $alamat, 'no_telepon' => $no_telepon, 'no_hp' => $no_hp, 
				'sm_no_ktp' => $sm_no_ktp, 'sm_nama_pelanggan' => $sm_nama_pelanggan, 'sm_npwp' => $sm_npwp, 'sm_alamat' => $sm_alamat, 'sm_no_telepon' => $sm_no_telepon, 'sm_no_hp' => $sm_no_hp, 
				'kode_bank' => $kode_bank, 'no_rekening' => $no_rekening 
			);
			pelanggan_lookup($pelanggan_lookup);
			
			$conn->committrans();
			
			$msg = 'Data pelanggan berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	
	if ($act == 'Putus') # Proses Ubah
	{
		try
		{
			$conn->begintrans();

			ex_empty($periode_putus, 'Periode pemutusan harus diisi.');
			ex_empty($tgl_pemutusan, 'Tanggal pemutusan harus diisi.');
			ex_empty($petugas, 'Petugas harus diisi.');
			
			$query = "
			INSERT INTO KWT_PEMUTUSAN_AIR (NO_PELANGGAN, PERIODE_PUTUS, TGL_PEMUTUSAN, PETUGAS, USER_CREATED)
			VALUES ($id, $periode_putus, '$tgl_pemutusan', '$petugas', '$sess_id_user')
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Pemutusan berhasil diproses.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	
	close($conn);
	$json = array('act' => $act, 'msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}

if ($act == 'Ubah')
{
	$query = "
	SELECT 
		p.*, 
		CONVERT(VARCHAR(10), p.TGL_PPJB, 105) AS TGL_PPJB, 
		CONVERT(VARCHAR(10), p.TGL_PEMUTUSAN, 105) AS TGL_PEMUTUSAN, 
		dbo.PTPS(p.PERIODE_PUTUS) AS PERIODE_PUTUS, 
		(
			SELECT
			(
				CAST(SUM(CASE WHEN $where_trx_air_ipl THEN (JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA + ADM - DISKON_IPL - DISKON_AIR) ELSE 0 END) AS VARCHAR) + '|' + 
				CAST(SUM(CASE WHEN $where_trx_air_ipl THEN 1 ELSE 0 END) AS VARCHAR)
				+ '#' + 
				CAST(SUM(CASE WHEN $where_trx_deposit THEN (JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA + ADM - DISKON_IPL - DISKON_AIR) ELSE 0 END) AS VARCHAR) + '|' + 
				CAST(SUM(CASE WHEN $where_trx_deposit THEN 1 ELSE 0 END) AS VARCHAR)
				+ '#' + 
				CAST(SUM(CASE WHEN $where_trx_lain_lain THEN (JUMLAH_AIR + ABONEMEN + JUMLAH_IPL + DENDA + ADM - DISKON_IPL - DISKON_AIR) ELSE 0 END) AS VARCHAR) + '|' + 
				CAST(SUM(CASE WHEN $where_trx_lain_lain THEN 1 ELSE 0 END) AS VARCHAR)
			)
			FROM KWT_PEMBAYARAN_AI 
			WHERE STATUS_BAYAR = 0 AND NO_PELANGGAN = p.NO_PELANGGAN
		) AS PIUTANG
		
	FROM KWT_PELANGGAN p 
	WHERE NO_PELANGGAN = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	$kode_cluster		= $obj->fields['KODE_CLUSTER'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
	
	$no_pelanggan		= fm_nopel($obj->fields['NO_PELANGGAN']);
	$status_blok		= $obj->fields['STATUS_BLOK'];
	$info_tagihan		= $obj->fields['INFO_TAGIHAN'];
	$tgl_ppjb			= $obj->fields['TGL_PPJB'];
	
	$no_ktp				= $obj->fields['NO_KTP'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$npwp				= $obj->fields['NPWP'];
	$alamat				= $obj->fields['ALAMAT'];
	$no_telepon			= $obj->fields['NO_TELEPON'];
	$no_hp				= $obj->fields['NO_HP'];
	
	$aktif_sm			= $obj->fields['AKTIF_SM'];
	$sm_nama_pelanggan	= $obj->fields['SM_NAMA_PELANGGAN'];
	$sm_no_ktp			= $obj->fields['SM_NO_KTP'];
	$sm_npwp			= $obj->fields['SM_NPWP'];
	$sm_no_hp			= $obj->fields['SM_NO_HP'];
	$sm_no_telepon		= $obj->fields['SM_NO_TELEPON'];
	$sm_alamat			= $obj->fields['SM_ALAMAT'];
	
	$aktif_ad			= $obj->fields['AKTIF_AD'];
	$kode_bank			= $obj->fields['KODE_BANK'];
	$no_rekening		= $obj->fields['NO_REKENING'];
	
	$ket			= $obj->fields['KET'];
	
	$piutang			= explode('#', $obj->fields['PIUTANG']);
	$piutang_ai			= explode('|', $piutang[0]);
	$piutang_dp			= explode('|', $piutang[1]);
	$piutang_ll			= explode('|', $piutang[2]);
	
	$jumlah_piutang_ai	= $piutang_ai[1]; $nilai_piutang_ai = $piutang_ai[0];
	$jumlah_piutang_dp	= $piutang_dp[1]; $nilai_piutang_dp	= $piutang_dp[0];
	$jumlah_piutang_ll	= $piutang_ll[1]; $nilai_piutang_ll	= $piutang_ll[0];


	$periode_putus		= $obj->fields['PERIODE_PUTUS'];
	$tgl_pemutusan		= $obj->fields['TGL_PEMUTUSAN'];
	$petugas			= $obj->fields['PETUGAS'];
	
	$aktif_air			= $obj->fields['AKTIF_AIR'];
	$kode_zona			= $obj->fields['KODE_ZONA'];
	$tipe_air			= $obj->fields['TIPE_AIR'];
	$key_air			= $obj->fields['KEY_AIR'];

	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	$tipe_ipl			= $obj->fields['TIPE_IPL'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	
	$disabled			= $obj->fields['DISABLED'];
	
	$status_join		= $obj->fields['STATUS_JOIN'];
	$join_to			= $obj->fields['JOIN_TO'];
	
	$status_split		= $obj->fields['STATUS_SPLIT'];
	$split_from			= $obj->fields['SPLIT_FROM'];
	
	$golongan			= $obj->fields['GOLONGAN'];
	$tipe_denda			= $obj->fields['TIPE_DENDA'];
}
?>
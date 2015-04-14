<?php
require_once('../../../config/config.php');
die_login();
die_mod('M14');
$conn = conn();
die_conn($conn);

$msg = '';
$error = FALSE;

$act_proses	= (isset($_REQUEST['act_proses'])) ? clean($_REQUEST['act_proses']) : '';
$act		= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id			= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$kode_sektor		= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
	$kode_cluster		= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
	
	$status_blok		= (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';
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
	
	$aktif_ad			= (isset($_REQUEST['aktif_ad'])) ? clean($_REQUEST['aktif_ad']) : '0';
	$kode_bank			= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
	$no_rekening		= (isset($_REQUEST['no_rekening'])) ? clean($_REQUEST['no_rekening']) : '';

	$ket				= (isset($_REQUEST['ket'])) ? clean($_REQUEST['ket']) : '';

	$aktif_air			= (isset($_REQUEST['aktif_air'])) ? to_number($_REQUEST['aktif_air']) : '0';
	$kode_zona			= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
	$tipe_air			= (isset($_REQUEST['tipe_air'])) ? clean($_REQUEST['tipe_air']) : '';
	$key_air			= (isset($_REQUEST['key_air'])) ? clean($_REQUEST['key_air']) : '';

	$aktif_ipl			= (isset($_REQUEST['aktif_ipl'])) ? to_number($_REQUEST['aktif_ipl']) : '0';
	$tipe_ipl			= (isset($_REQUEST['tipe_ipl'])) ? clean($_REQUEST['tipe_ipl']) : '';
	$key_ipl			= (isset($_REQUEST['key_ipl'])) ? clean($_REQUEST['key_ipl']) : '';
	
	$golongan			= (isset($_REQUEST['golongan'])) ? to_number($_REQUEST['golongan']) : '0';
	$tipe_denda			= (isset($_REQUEST['tipe_denda'])) ? to_number($_REQUEST['tipe_denda']) : '0';

	if ($act == 'Ubah') # Proses Ubah
	{
		try
		{
			$conn->begintrans();
			
			$obj = $conn->Execute("
			SELECT 
				(SELECT STATUS_PROSES FROM KWT_PELANGGAN_IMP WHERE KODE_BLOK = '$id') AS STATUS_PROSES,
				(SELECT COUNT(KODE_BLOK) FROM KWT_PELANGGAN WHERE KODE_BLOK = '$id') AS FOUND_MASTER
			"); 
			$status_proses = $obj->fields['STATUS_PROSES']; 
			$found_master = $obj->fields['FOUND_MASTER']; 
			
			if ($status_proses == '1') {
				throw new Exception("Kode blok \"$id\" telah diproses ke master pelanggan.");
			}
			
			ex_empty($kode_sektor, 'Pilih sektor.');
			ex_empty($kode_cluster, 'Pilih cluster.');
			ex_empty($status_blok, 'Pilih status blok.');
			ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
			
			if ($aktif_air != 0) {
				ex_empty($key_air, 'Pilih kode tarif air.');
			}
			if ($aktif_ipl != 0) {
				ex_empty($key_ipl, 'Pilih kode tarif IPL.');
			}
			
			$query = "
			UPDATE KWT_PELANGGAN_IMP 
			SET 
				KODE_SEKTOR = dbo.ETN('$kode_sektor'),
				KODE_CLUSTER = dbo.ETN('$kode_cluster'),
				
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
				KODE_BLOK = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$pelanggan_lookup = array(
				'no_ktp' => $no_ktp, 'nama_pelanggan' => $nama_pelanggan, 'npwp' => $npwp, 'alamat' => $alamat, 'no_telepon' => $no_telepon, 'no_hp' => $no_hp, 
				'sm_no_ktp' => $sm_no_ktp, 'sm_nama_pelanggan' => $sm_nama_pelanggan, 'sm_npwp' => $sm_npwp, 'sm_alamat' => $sm_alamat, 'sm_no_telepon' => $sm_no_telepon, 'sm_no_hp' => $sm_no_hp, 
				'kode_bank' => $kode_bank, 'no_rekening' => $no_rekening 
			);
			pelanggan_lookup($pelanggan_lookup);
			
			$msg = 'Data pelanggan berhasil diubah.';
			
			# PROSES SIMPAN
			if ($act_proses == 'YES') 
			{
				if ($found_master > 0) {
					throw new Exception("Kode blok \"$id\" telah terdaftar di master pelanggan.");
				}
				
				$query = "
				DECLARE @np VARCHAR(15) = 
				( 
					SELECT
						ISNULL(KODE_PEL,'00') + 
						RIGHT(YEAR(GETDATE()),2) + '0' + 
						(SELECT RIGHT('0000000' + CAST(COU_NP AS VARCHAR), 7) FROM KWT_PARAMETER)
					FROM KWT_SEKTOR 
					WHERE KODE_SEKTOR = (SELECT KODE_SEKTOR FROM KWT_PELANGGAN_IMP WHERE KODE_BLOK = '$id')
				) 
				
				INSERT INTO KWT_PELANGGAN
				(
					NO_PELANGGAN,
					
					KODE_BLOK,
					LUAS_KAVLING,
					LUAS_BANGUNAN,
					
					KODE_SEKTOR,
					KODE_CLUSTER,
					
					STATUS_BLOK,
					INFO_TAGIHAN,
					
					NO_KTP,
					NAMA_PELANGGAN,
					NPWP,
					ALAMAT,
					NO_TELEPON,
					NO_HP,
					
					AKTIF_SM,
					SM_NAMA_PELANGGAN,
					SM_NO_KTP,
					SM_NPWP,
					SM_NO_HP,
					SM_NO_TELEPON,
					SM_ALAMAT,
					
					AKTIF_AD,
					KODE_BANK,
					NO_REKENING,
					
					KET,
					
					AKTIF_AIR, 
					KODE_ZONA, 
					TIPE_AIR, 
					KEY_AIR, 
		
					AKTIF_IPL, 
					TIPE_IPL, 
					KEY_IPL, 
					
					GOLONGAN,
					TIPE_DENDA,
				
					USER_CREATED
				) 
				SELECT
					@np,
					
					KODE_BLOK,
					LUAS_KAVLING,
					LUAS_BANGUNAN,
					
					KODE_SEKTOR,
					KODE_CLUSTER,
					
					STATUS_BLOK,
					INFO_TAGIHAN,
					
					NO_KTP,
					NAMA_PELANGGAN,
					NPWP,
					ALAMAT,
					NO_TELEPON,
					NO_HP,
					
					AKTIF_SM,
					SM_NAMA_PELANGGAN,
					SM_NO_KTP,
					SM_NPWP,
					SM_NO_HP,
					SM_NO_TELEPON,
					SM_ALAMAT,
					
					AKTIF_AD,
					KODE_BANK,
					NO_REKENING,
					
					KET,
					
					AKTIF_AIR, 
					KODE_ZONA, 
					TIPE_AIR, 
					KEY_AIR, 
		
					AKTIF_IPL, 
					TIPE_IPL, 
					KEY_IPL, 
					
					GOLONGAN,
					TIPE_DENDA,
					
					'$sess_id_user'
				FROM
					KWT_PELANGGAN_IMP
				WHERE
					KODE_BLOK = '$id'
				";
				
				ex_false($conn->Execute($query), $query);
				
				ex_false($conn->Execute("UPDATE KWT_PARAMETER SET COU_NP = COU_NP + 1"), "Gagal update increment no pelanggan.");
				
				$query = "
				UPDATE KWT_PELANGGAN_IMP 
				SET 
					STATUS_PROSES = 1,
					NO_PELANGGAN = (SELECT NO_PELANGGAN FROM KWT_PELANGGAN WHERE KODE_BLOK = '$id')
				WHERE 
					KODE_BLOK = '$id'
				";
				ex_false($conn->Execute($query), "Gagal Update status proses.");
				
				$msg = 'Data pelanggan berhasil dismpan.';
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
		CONVERT(VARCHAR(10), p.TGL_PPJB, 105) AS TGL_PPJB
	FROM KWT_PELANGGAN_IMP p 
	WHERE KODE_BLOK = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$status_proses		= $obj->fields['STATUS_PROSES'];
	
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	$kode_cluster		= $obj->fields['KODE_CLUSTER'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
	
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
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
	
	$ket				= $obj->fields['KET'];
	
	$aktif_air			= $obj->fields['AKTIF_AIR'];
	$kode_zona			= $obj->fields['KODE_ZONA'];
	$tipe_air			= $obj->fields['TIPE_AIR'];
	$key_air			= $obj->fields['KEY_AIR'];

	$aktif_ipl			= $obj->fields['AKTIF_IPL'];
	$tipe_ipl			= $obj->fields['TIPE_IPL'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	
	$golongan			= $obj->fields['GOLONGAN'];
	$tipe_denda			= $obj->fields['TIPE_DENDA'];
}
?>
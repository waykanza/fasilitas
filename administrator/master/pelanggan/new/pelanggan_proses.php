<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$kode_sektor		= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
	$kode_cluster		= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
	
	$status_blok		= (isset($_REQUEST['status_blok'])) ? clean($_REQUEST['status_blok']) : '';
	$info_tagihan		= (isset($_REQUEST['info_tagihan'])) ? clean($_REQUEST['info_tagihan']) : '';

	$no_ktp				= (isset($_REQUEST['no_ktp'])) ? clean($_REQUEST['no_ktp']) : '';
	$nama_pelanggan		= (isset($_REQUEST['nama_pelanggan'])) ? clean($_REQUEST['nama_pelanggan']) : '';
	$npwp				= (isset($_REQUEST['npwp'])) ? clean($_REQUEST['npwp']) : '';
	$alamat				= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';
	$no_telepon			= (isset($_REQUEST['no_telepon'])) ? clean($_REQUEST['no_telepon']) : '';
	$no_hp				= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';

	$pakai_sm			= (isset($_REQUEST['pakai_sm'])) ? clean($_REQUEST['pakai_sm']) : '';
	$sm_nama_pelanggan	= (isset($_REQUEST['sm_nama_pelanggan'])) ? clean($_REQUEST['sm_nama_pelanggan']) : '';
	$sm_alamat			= (isset($_REQUEST['sm_alamat'])) ? clean($_REQUEST['sm_alamat']) : '';
	$sm_no_telepon		= (isset($_REQUEST['sm_no_telepon'])) ? clean($_REQUEST['sm_no_telepon']) : '';
	$sm_no_hp			= (isset($_REQUEST['sm_no_hp'])) ? clean($_REQUEST['sm_no_hp']) : '';
	
	$debet_bank			= (isset($_REQUEST['debet_bank'])) ? clean($_REQUEST['debet_bank']) : '';
	$kode_bank			= (isset($_REQUEST['kode_bank'])) ? clean($_REQUEST['kode_bank']) : '';
	$no_rekening		= (isset($_REQUEST['no_rekening'])) ? clean($_REQUEST['no_rekening']) : '';

	$keterangan			= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';

	$periode_putus		= (isset($_REQUEST['periode_putus'])) ? to_periode($_REQUEST['periode_putus']) : '';
	$tgl_pemutusan		= (isset($_REQUEST['tgl_pemutusan'])) ? to_date($_REQUEST['tgl_pemutusan']) : '';
	$petugas			= '';

	$aktif_air			= (isset($_REQUEST['aktif_air'])) ? clean($_REQUEST['aktif_air']) : '';
	$kode_zona			= (isset($_REQUEST['kode_zona'])) ? clean($_REQUEST['kode_zona']) : '';
	$tipe_air			= (isset($_REQUEST['tipe_air'])) ? clean($_REQUEST['tipe_air']) : '';
	$key_air			= (isset($_REQUEST['key_air'])) ? clean($_REQUEST['key_air']) : '';

	$aktif_ipl			= (isset($_REQUEST['aktif_ipl'])) ? clean($_REQUEST['aktif_ipl']) : '';
	$tipe_ipl			= (isset($_REQUEST['tipe_ipl'])) ? clean($_REQUEST['tipe_ipl']) : '';
	$key_ipl			= (isset($_REQUEST['key_ipl'])) ? clean($_REQUEST['key_ipl']) : '';

	if ($act == 'Ubah') # Proses Ubah
	{
		try
		{
			$conn->begintrans();

			ex_empty($kode_sektor, 'Pilih sektor.');
			ex_empty($kode_cluster, 'Pilih cluster.');
			ex_empty($status_blok, 'Pilih status blok.');
			ex_empty($nama_pelanggan, 'Nama pelanggan harus diisi.');
			
			if ($aktif_air != '') {
				ex_empty($key_air, 'Pilih kode tarif air.');
			}
			if ($aktif_ipl != '') {
				ex_empty($key_ipl, 'Pilih kode tarif IPL.');
			}
			
			$query = "
			UPDATE KWT_PELANGGAN_IMP 
			SET 
				STATUS_PROSES = '1',
				
				KODE_SEKTOR = dbo.ETN('$kode_sektor'),
				KODE_CLUSTER = dbo.ETN('$kode_cluster'),
				
				STATUS_BLOK = dbo.ETN('$status_blok'),
				INFO_TAGIHAN = dbo.ETN('$info_tagihan'),
				
				NO_KTP = dbo.ETN('$no_ktp'),
				NAMA_PELANGGAN = dbo.ETN('$nama_pelanggan'),
				NPWP = dbo.ETN('$npwp'),
				ALAMAT = dbo.ETN('$alamat'),
				NO_TELEPON = dbo.ETN('$no_telepon'),
				NO_HP = dbo.ETN('$no_hp'),
				
				PAKAI_SM = dbo.ETN('$pakai_sm'),
				SM_NAMA_PELANGGAN = dbo.ETN('$sm_nama_pelanggan'),
				SM_ALAMAT = dbo.ETN('$sm_alamat'),
				SM_NO_TELEPON = dbo.ETN('$sm_no_telepon'),
				SM_NO_HP = dbo.ETN('$sm_no_hp'),
				
				DEBET_BANK = dbo.ETN('$debet_bank'),
				KODE_BANK = dbo.ETN('$kode_bank'),
				NO_REKENING = dbo.ETN('$no_rekening'),
				
				KETERANGAN = dbo.ETN('$keterangan'),
				
				AKTIF_AIR = dbo.ETN('$aktif_air'),
				KODE_ZONA = dbo.ETN('$kode_zona'),
				TIPE_AIR = dbo.ETN('$tipe_air'),
				KEY_AIR = dbo.ETN('$key_air'),
	
				AKTIF_IPL = dbo.ETN('$aktif_ipl'),
				TIPE_IPL = dbo.ETN('$tipe_ipl'),
				KEY_IPL = dbo.ETN('$key_ipl')
			WHERE
				NO_PELANGGAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$query = "
			INSERT INTO KWT_PELANGGAN
			(
				KODE_BLOK,
				
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
				
				DEBET_BANK,
				KODE_BANK,
				NO_REKENING,
				
				KETERANGAN,
				
				AKTIF_AIR, 
				KODE_ZONA, 
				TIPE_AIR, 
				KEY_AIR, 
	
				AKTIF_IPL, 
				TIPE_IPL, 
				KEY_IPL
			) 
			SELECT
				KODE_BLOK,
				
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
				
				DEBET_BANK,
				KODE_BANK,
				NO_REKENING,
				
				KETERANGAN,
				
				AKTIF_AIR, 
				KODE_ZONA, 
				TIPE_AIR, 
				KEY_AIR, 
	
				AKTIF_IPL, 
				TIPE_IPL, 
				KEY_IPL
			FROM
				KWT_PELANGGAN_IMP
			WHERE
				NO_PELANGGAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$pelanggan_lookup = array(
				'no_ktp' => $no_ktp, 'nama_pelanggan' => $nama_pelanggan,
				'npwp' => $npwp, 'alamat' => $alamat, 'no_telepon' => $no_telepon,
				'no_hp' => $no_hp, 'kode_bank' => $kode_bank, 'no_rekening' => $no_rekening
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
		dbo.PTPS(p.PERIODE_PUTUS) AS PERIODE_PUTUS
	FROM KWT_PELANGGAN_IMP p 
	WHERE NO_PELANGGAN = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$status_proses = $obj->fields['STATUS_PROSES'];
	
	if ($status_proses == '1') {
		echo "<script>alert('Blok ini telah masuk kedalam master pelanggan!');parent.loadData();</script>";
	}
	
	$kode_sektor		= $obj->fields['KODE_SEKTOR'];
	$kode_cluster		= $obj->fields['KODE_CLUSTER'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$luas_kavling		= $obj->fields['LUAS_KAVLING'];
	$luas_bangunan 		= $obj->fields['LUAS_BANGUNAN'];
	
	$no_pelanggan		= no_pelanggan($obj->fields['NO_PELANGGAN']);
	$status_blok		= $obj->fields['STATUS_BLOK'];
	$info_tagihan		= $obj->fields['INFO_TAGIHAN'];
	$tgl_ppjb			= $obj->fields['TGL_PPJB'];
	
	$no_ktp				= $obj->fields['NO_KTP'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$npwp				= $obj->fields['NPWP'];
	$alamat				= $obj->fields['ALAMAT'];
	$no_telepon			= $obj->fields['NO_TELEPON'];
	$no_hp				= $obj->fields['NO_HP'];
	
	$pakai_sm			= $obj->fields['PAKAI_SM'];
	$sm_nama_pelanggan	= $obj->fields['SM_NAMA_PELANGGAN'];
	$sm_alamat			= $obj->fields['SM_ALAMAT'];
	$sm_no_telepon		= $obj->fields['SM_NO_TELEPON'];
	$sm_no_hp			= $obj->fields['SM_NO_HP'];
	
	$debet_bank			= $obj->fields['DEBET_BANK'];
	$kode_bank			= $obj->fields['KODE_BANK'];
	$no_rekening		= $obj->fields['NO_REKENING'];
	
	$keterangan			= $obj->fields['KETERANGAN'];

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
}
?>
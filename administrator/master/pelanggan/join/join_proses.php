<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_blok	= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$luas_kavling = (isset($_REQUEST['luas_kavling'])) ? to_decimal($_REQUEST['luas_kavling']) : '';
$luas_bangunan = (isset($_REQUEST['luas_bangunan'])) ? to_decimal($_REQUEST['luas_bangunan']) : '';

$ref_kode_blok_ary = (isset($_REQUEST['ref_kode_blok'])) ? $_REQUEST['ref_kode_blok'] : array();

$ref_kode_blok_ary[1]	= (isset($ref_kode_blok_ary[1])) ? $ref_kode_blok_ary[1] : '';
$ref_kode_blok_ary[2]	= (isset($ref_kode_blok_ary[2])) ? $ref_kode_blok_ary[2] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		ex_empty($kode_blok, 'Massukan kode blok baru.');
		ex_empty($luas_kavling, 'Masukkan luas kavling.');
		if (empty($ref_kode_blok_ary) || $ref_kode_blok_ary[1] == '' || $ref_kode_blok_ary[2] == '')
		{
			throw new Exception("Pilih blok yang akan digabung.");
		}
		
		$query = "SELECT TOP 1 NO_PELANGGAN, NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE KODE_BLOK = '$kode_blok'";
		$obj = $conn->Execute($query);
		ex_found($obj->recordcount(), "
		Kode blok \"$kode_blok\" telah terdaftar sebelumnya 
		oleh " .$obj->fields['NAMA_PELANGGAN'] . " dengan no. pelanggan \"" . $obj->fields['NO_PELANGGAN'] . "\"");
		
		$valid = explode('-',$kode_blok);
		$valid = $valid[0];
		
		foreach ($ref_kode_blok_ary as $ref_kode_blok)
		{
			$cek_valid = explode('-',clean($ref_kode_blok));
			$cek_valid = $cek_valid[0];
			
			if ($cek_valid != $valid) { throw new Exception("\"$cek_valid\" Tidak sama dengan \"$valid\"."); }
		}
		
		$ref_kode_blok_in = implode("' ,'", $ref_kode_blok_ary);
		
		# UPDATE OLD KODE_BLOK
		$query = "
		UPDATE KWT_PELANGGAN 
		SET 
			JOIN_TO = '$kode_blok',
			DISABLED = '1'
		WHERE KODE_BLOK IN ('$ref_kode_blok_in')";
		ex_false($conn->Execute($query), $query);
		
		# INSERT NEW KODE_BLOK
		$user_join = $_SESSION['ID_USER'];
		$query = "
		INSERT INTO KWT_PELANGGAN
		(
			TGL_PPJB,
			INFO_TAGIHAN, 
			
			KODE_SEKTOR, 
			KODE_CLUSTER, 
			KODE_BLOK, 
			LUAS_KAVLING, 
			LUAS_BANGUNAN, 
			STATUS_BLOK, 
			
			NAMA_PELANGGAN, 
			NO_KTP, 
			NPWP, 
			ALAMAT, 
			NO_TELEPON, 
			NO_HP, 
			
			DEBET_BANK, 
			KODE_BANK, 
			NO_REKENING, 
			
			AKTIF_AIR,
			KODE_ZONA, 
			TIPE_AIR, 
			KEY_AIR, 
			TGL_PEMUTUSAN, 
			PETUGAS, 
			PERIODE_PUTUS,
			
			AKTIF_IPL, 
			TIPE_IPL, 
			KEY_IPL,
			KETERANGAN,
			
			STATUS_JOIN,
			USER_JOIN,
			JOIN_DATE
		)
		SELECT 
			TGL_PPJB,
			INFO_TAGIHAN, 
			
			KODE_SEKTOR, 
			KODE_CLUSTER, 
			'$kode_blok' AS KODE_BLOK, 
			$luas_kavling AS LUAS_KAVLING, 
			$luas_bangunan AS LUAS_BANGUNAN, 
			STATUS_BLOK,
				
			NAMA_PELANGGAN, 
			NO_KTP, 
			NPWP, 
			ALAMAT, 
			NO_TELEPON, 
			NO_HP, 
			
			DEBET_BANK, 
			KODE_BANK, 
			NO_REKENING, 
			
			AKTIF_AIR,
			KODE_ZONA, 
			TIPE_AIR, 
			KEY_AIR, 
			TGL_PEMUTUSAN, 
			PETUGAS, 
			PERIODE_PUTUS,
			
			AKTIF_IPL, 
			TIPE_IPL, 
			KEY_IPL,
			CAST(KETERANGAN AS VARCHAR) + ' [PENGGABUNGAN BLOK]' AS KETERANGAN,
			
			STATUS_JOIN = '1',
			USER_JOIN = '$user_join',
			JOIN_DATE = GETDATE()
		FROM
			KWT_PELANGGAN
		WHERE 
			KODE_BLOK = '" . $ref_kode_blok_ary[1] . "'
		";
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
			
		$msg = 'Data blok berhasil digabung.';
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
?>
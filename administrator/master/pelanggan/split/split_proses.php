<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$ref_kode_blok	= (isset($_REQUEST['ref_kode_blok'])) ? clean($_REQUEST['ref_kode_blok']) : '';

$kode_blok_ary	= (isset($_REQUEST['kode_blok'])) ? $_REQUEST['kode_blok'] : array();
$luas_kavling_ary = (isset($_REQUEST['luas_kavling'])) ? $_REQUEST['luas_kavling'] : array();
$luas_bangunan_ary = (isset($_REQUEST['luas_bangunan'])) ? $_REQUEST['luas_bangunan'] : array();

$kode_blok_ary[1]	= (isset($kode_blok_ary[1])) ? $kode_blok_ary[1] : '';
$kode_blok_ary[2]	= (isset($kode_blok_ary[2])) ? $kode_blok_ary[2] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		ex_empty($ref_kode_blok, 'Massukan kode blok baru.');
		if (empty($kode_blok_ary) || $kode_blok_ary[1] == '' || $kode_blok_ary[2] == '')
		{
			throw new Exception("Massukan kode blok baru.");
		}
		
		$kode_blok_in = array();
		foreach ($kode_blok_ary as $i => $v)
		{
			$kode_blok = $ref_kode_blok . $v;
			$luas_kavling = to_decimal($luas_kavling_ary[$i]);
			$luas_bangunan = to_decimal($luas_bangunan_ary[$i]);
			
			ex_empty($luas_kavling, "Masukkan luas kavling \"$kode_blok\".");
			
			$query = "SELECT TOP 1 NO_PELANGGAN, NAMA_PELANGGAN FROM KWT_PELANGGAN WHERE KODE_BLOK = '$kode_blok'";
			$obj = $conn->Execute($query);
			ex_found($obj->recordcount(), "
			Kode blok \"$kode_blok\" telah terdaftar sebelumnya 
			oleh " .$obj->fields['NAMA_PELANGGAN'] . " dengan no. pelanggan \"" . $obj->fields['NO_PELANGGAN'] . "\"");
			
			# INSERT NEW KODE_BLOK
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
				
				SPLIT_FROM
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
				CAST(KETERANGAN AS VARCHAR) + ' [PEMISAHAN DARI BLOK $ref_kode_blok]' AS KETERANGAN,
				
				'$ref_kode_blok' AS SPLIT_FROM
			FROM
				KWT_PELANGGAN
			WHERE 
				KODE_BLOK = '$ref_kode_blok'
			";
			ex_false($conn->Execute($query), $query);
		}
		
		# UPDATE OLD KODE_BLOK
		$user_split = $_SESSION['ID_USER'];
		$query = "
		UPDATE KWT_PELANGGAN 
		SET 
			STATUS_SPLIT = '1',
			USER_SPLIT = '$user_split',
			SPLIT_DATE = GETDATE(),
			DISABLED = '1'
		WHERE KODE_BLOK = '$ref_kode_blok'";
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
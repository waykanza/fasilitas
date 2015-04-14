<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_lokasi = (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$key_ksp		= (isset($_REQUEST['key_ksp'])) ? clean($_REQUEST['key_ksp']) : '';
$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
$save_deposit	= (isset($_REQUEST['save_deposit'])) ? to_number($_REQUEST['save_deposit']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_tipe, 'Kategori harus diisi.');
			$key_ksp = $kode_lokasi . '-' . $kode_tipe;
			
			$query = "SELECT KEY_KSP FROM KWT_TARIF_KSP WHERE KEY_KSP = '$key_ksp'";
			ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_ksp\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_KSP (KEY_KSP, KODE_LOKASI, KODE_TIPE,TARIF, SAVE_DEPOSIT)
			VALUES(
				'$key_ksp', 
				'$kode_lokasi', 
				'$kode_tipe',
				$tarif,
				$save_deposit
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Tarif kegiatan shooting / pemotretan \"$key_ksp\" berhasil disimpan.";
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'Ubah') /* Proses Ubah */
	{
		try
		{
			$conn->begintrans();

			ex_empty($kode_tipe, 'Kategori harus diisi.');
			$key_ksp = $kode_lokasi . '-' . $kode_tipe;
			
			if ($key_ksp != $id)
			{
				$query = "SELECT KEY_KSP FROM KWT_TARIF_KSP WHERE KEY_KSP = '$key_ksp'";
				ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_ksp\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_KSP 
			SET 
				KEY_KSP = '$key_ksp', 
				KODE_LOKASI = '$kode_lokasi', 
				KODE_TIPE = '$kode_tipe',
				TARIF = $tarif,
				SAVE_DEPOSIT = $save_deposit
			WHERE
				KEY_KSP = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tarif kegiatan shooting / pemotretan berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'delete') /* Proses Delete */
	{
		$act = array();
		$id_error = array();
		
		try
		{
			$conn->begintrans();
			
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{
				$query = "DELETE FROM KWT_TARIF_KSP WHERE KEY_KSP = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data tarif kegiatan shooting / pemotretan berhasil dihapus.';
		}
		catch(Exception $e)
		{
			$msg = $e->getMessage();
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
	SELECT *
	FROM KWT_TARIF_KSP
	WHERE KEY_KSP = '$id'";
	
	$obj = $conn->execute($query);
	$key_ksp		= $obj->fields['KEY_KSP'];
	$kode_lokasi	= $obj->fields['KODE_LOKASI'];
	$kode_tipe		= $obj->fields['KODE_TIPE'];
	$tarif			= $obj->fields['TARIF'];
	$save_deposit	= $obj->fields['SAVE_DEPOSIT'];
}
?>
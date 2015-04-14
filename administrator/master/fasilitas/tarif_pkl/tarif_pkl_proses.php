<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_lokasi = (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$key_pkl		= (isset($_REQUEST['key_pkl'])) ? clean($_REQUEST['key_pkl']) : '';
$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$uang_pangkal	= (isset($_REQUEST['uang_pangkal'])) ? to_number($_REQUEST['uang_pangkal']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
$satuan			= (isset($_REQUEST['satuan'])) ? to_number($_REQUEST['satuan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_tipe, 'Kategori harus diisi.');
			$key_pkl = $kode_lokasi . '-' . $key_pkl;
			
			$query = "SELECT KEY_PKL FROM KWT_TARIF_PKL WHERE KEY_PKL = '$key_pkl'";
			ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_pkl\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_PKL (
				KEY_PKL, KODE_LOKASI, KODE_TIPE,
				UANG_PANGKAL, TARIF, SATUAN)
			VALUES(
				'$key_pkl', 
				'$kode_lokasi', 
				'$kode_tipe',
				$uang_pangkal,
				$tarif,
				'$satuan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Tarif pedagang kaki lima \"$kode_lokasi\" berhasil disimpan.";
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
			$key_pkl = $kode_lokasi . '-' . $key_pkl;
			
			if ($key_pkl != $id)
			{
				$query = "SELECT KEY_PKL FROM KWT_TARIF_PKL WHERE KEY_PKL = '$key_pkl'";
				ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_pkl\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_PKL 
			SET 
				KEY_PKL = '$key_pkl', 
				KODE_LOKASI = '$kode_lokasi', 
				KODE_TIPE = '$kode_tipe',
				UANG_PANGKAL = $uang_pangkal,
				TARIF = $tarif,
				SATUAN = '$satuan'
			WHERE
				KEY_PKL = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tarif pedagang kaki lima berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_PKL WHERE KEY_PKL = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data tarif pedagang kaki lima berhasil dihapus.';
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
	FROM KWT_TARIF_PKL
	WHERE KEY_PKL = '$id'";
	
	$obj = $conn->execute($query);
	$key_pkl		= explode('-', $obj->fields['KEY_PKL']);
	$key_pkl		= $key_pkl[2];
	$kode_lokasi	= $obj->fields['KODE_LOKASI'];
	$kode_tipe		= $obj->fields['KODE_TIPE'];
	$uang_pangkal	= $obj->fields['UANG_PANGKAL'];
	$tarif			= $obj->fields['TARIF'];
	$satuan			= $obj->fields['SATUAN'];
}
?>
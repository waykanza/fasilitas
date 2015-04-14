<?php
require_once('../../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$key_mp = (isset($_REQUEST['key_mp'])) ? clean($_REQUEST['key_mp']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$key_mpd		= (isset($_REQUEST['key_mpd'])) ? clean($_REQUEST['key_mpd']) : '';
$kode_lokasi	= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($key_mpd, 'Key harus diisi.');
			ex_empty($kode_lokasi, 'Kategori harus diisi.');
			$key_mpd = $key_mp . '-' . $kode_lokasi;
			
			$query = "SELECT KEY_MPD FROM KWT_TARIF_MPD WHERE KEY_MPD = '$key_mpd'";
			ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_mpd\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_MPD (
				KEY_MPD, KEY_MP, KODE_LOKASI, TARIF)
			VALUES(
				'$key_mpd', 
				'$key_mp', 
				'$kode_lokasi',
				$tarif
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Data detail tarif \"$key_mpd\" berhasil disimpan.";
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

			ex_empty($key_mpd, 'Key harus diisi.');
			ex_empty($kode_lokasi, 'Kategori harus diisi.');
			$key_mpd = $key_mp . '-' . $kode_lokasi;
			
			if ($key_mpd != $id)
			{
				$query = "SELECT KEY_MPD FROM KWT_TARIF_MPD WHERE KEY_MPD = '$key_mpd'";
				ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_mpd\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_MPD 
			SET 
				KEY_MPD = '$key_mpd', 
				KEY_MP = '$key_mp', 
				KODE_LOKASI = '$kode_lokasi', 
				TARIF = $tarif
			WHERE
				KEY_MPD = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Data detail tarif berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_MPD WHERE KEY_MPD = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data detail tarif berhasil dihapus.';
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
	FROM KWT_TARIF_MPD
	WHERE KEY_MPD = '$id'";
	
	$obj = $conn->execute($query);
	$key_mp		= $obj->fields['KEY_MP'];
	$key_mpd	= $obj->fields['KEY_MPD'];
	$kode_lokasi = $obj->fields['KODE_LOKASI'];
	$tarif		= $obj->fields['TARIF'];
}
?>
<?php
require_once('../../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_mp = 'F';
$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$key_mp		= (isset($_REQUEST['key_mp'])) ? clean($_REQUEST['key_mp']) : '';
$kode_tipe	= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$tarif		= (isset($_REQUEST['tarif'])) ? to_decimal($_REQUEST['tarif']) : '';
$keterangan = (isset($_REQUEST['tarif'])) ? clean($_REQUEST['keterangan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($key_mp, 'Key harus diisi.');
			ex_empty($kode_tipe, 'Kategori harus diisi.');
			$key_mp = $kode_sk . '-' . $kode_tipe;
			
			$query = "SELECT KEY_MP FROM KWT_TARIF_MP WHERE KEY_MP = '$key_mp'";
			ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_mp\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_MP (KEY_MP, KODE_SK, KODE_MP, KODE_TIPE, TARIF, KETERANGAN)
			VALUES(
				'$key_mp', 
				'$kode_sk', 
				'$kode_mp',
				'$kode_tipe',
				$tarif,
				'$keterangan'
			)";
			ex_false($conn->Execute($query), $query);			
			
			$conn->committrans();
			
			$msg = "Tarif bus trans bintaro jaya \"$key_mp\" berhasil disimpan.";
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

			ex_empty($key_mp, 'Key harus diisi.');
			ex_empty($kode_tipe, 'Kategori harus diisi.');
			$key_mp = $kode_sk . '-' . $kode_tipe;
			
			if ($key_mp != $id)
			{
				$query = "SELECT KEY_MP FROM KWT_TARIF_MP WHERE KEY_MP = '$key_mp'";
				ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_mp\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_MP 
			SET 
				KEY_MP = '$key_mp', 
				KODE_SK = '$kode_sk', 
				KODE_MP = '$kode_mp',
				KODE_TIPE = '$kode_tipe',
				TARIF = $tarif,
				KETERANGAN = '$keterangan'
			WHERE
				KEY_MP = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
				
			$conn->committrans();
			
			$msg = 'Tarif bus trans bintaro jaya berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_MP WHERE KEY_MP = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data tarif bus trans bintaro jaya berhasil dihapus.';
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
	FROM KWT_TARIF_MP
	WHERE KEY_MP = '$id'";
	
	$obj = $conn->execute($query);
	$key_mp		= $obj->fields['KEY_MP'];
	$kode_sk	= $obj->fields['KODE_SK'];
	$kode_mp	= $obj->fields['KODE_MP'];
	$kode_tipe	= $obj->fields['KODE_TIPE'];
	$tarif		= $obj->fields['TARIF'];
	$keterangan = $obj->fields['KETERANGAN'];
}
?>
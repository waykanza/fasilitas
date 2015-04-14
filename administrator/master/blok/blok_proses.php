<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';
$luas_kavling	= (isset($_REQUEST['luas_kavling'])) ? to_decimal($_REQUEST['luas_kavling']) : '';
$luas_bangunan	= (isset($_REQUEST['luas_bangunan'])) ? to_decimal($_REQUEST['luas_bangunan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_blok, 'Kode blok harus diisi.');
			ex_empty($kode_sektor, 'Pilih sektor.');
			ex_empty($kode_cluster, 'Pilih cluster.');
			ex_empty($luas_kavling, 'Masukkan luas kavling.');
			
			$query = "SELECT KODE_BLOK FROM KWT_BLOK WHERE KODE_BLOK = '$kode_blok'";
			ex_found($conn->Execute($query)->recordcount(), "Kode blok \"$kode_blok\" telah terdaftar.");
				
			$query = "INSERT INTO KWT_BLOK (
				KODE_BLOK, KODE_SEKTOR, KODE_CLUSTER, LUAS_KAVLING, LUAS_BANGUNAN
			)
			VALUES(
				'$kode_blok', 
				'$kode_sektor', 
				'$kode_cluster',
				$luas_kavling,
				$luas_bangunan
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("
				UPDATE KWT_PELANGGAN SET 
					KODE_SEKTOR = '$kode_sektor', 
					KODE_CLUSTER = '$kode_cluster', 
					LUAS_KAVLING = $luas_kavling, 
					LUAS_BANGUNAN = $luas_bangunan
				WHERE KODE_BLOK = '$kode_blok'
			");
			
			$conn->committrans();
			
			$msg = "Kode blok \"$kode_blok\" berhasil disimpan.";
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

			ex_empty($kode_blok, 'Kode blok harus diisi.');
			ex_empty($kode_sektor, 'Pilih sektor.');
			ex_empty($kode_cluster, 'Pilih cluster.');
			ex_empty($luas_kavling, 'Masukkan luas kavling.');
			
			if ($kode_blok != $id)
			{
				$query = "SELECT KODE_BLOK FROM KWT_BLOK WHERE KODE_BLOK = '$kode_blok'";
				ex_found($conn->Execute($query)->recordcount(), "Kode blok \"$kode_blok\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_BLOK 
			SET 
				KODE_BLOK = '$kode_blok',
				KODE_SEKTOR = '$kode_sektor',
				KODE_CLUSTER = '$kode_cluster',
				LUAS_KAVLING = $luas_kavling,
				LUAS_BANGUNAN = $luas_bangunan
			WHERE
				KODE_BLOK = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->Execute("
				UPDATE KWT_PELANGGAN SET 
					KODE_SEKTOR = '$kode_sektor', 
					KODE_CLUSTER = '$kode_cluster', 
					LUAS_KAVLING = $luas_kavling, 
					LUAS_BANGUNAN = $luas_bangunan
				WHERE KODE_BLOK = '$kode_blok'
			");
			
			$conn->committrans();
			
			$msg = 'Blok berhasil diubah.';
		}
		catch(Exception $e)
		{
			$msg = $e->getmessage();
			$error = TRUE;
			$conn->rollbacktrans();
		}
	}
	elseif ($act == 'delete') # Proses Delete
	{
		$act = array();
		
		try
		{
			$conn->begintrans();
			
			$cb_data = $_REQUEST['cb_data'];
			ex_empty($cb_data, 'Pilih data yang akan dihapus.');
			
			foreach ($cb_data as $id_del)
			{
				$query = "DELETE FROM KWT_BLOK WHERE KODE_BLOK = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data blok berhasil dihapus.';
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
	$query = "SELECT * FROM KWT_BLOK WHERE KODE_BLOK = '$id'";
	$obj = $conn->Execute($query);
	
	$kode_blok		= $obj->fields['KODE_BLOK'];
	$kode_sektor	= $obj->fields['KODE_SEKTOR'];
	$kode_cluster	= $obj->fields['KODE_CLUSTER'];
	$luas_kavling	= $obj->fields['LUAS_KAVLING'];
	$luas_bangunan	= $obj->fields['LUAS_BANGUNAN'];
	$status_blok	= $obj->fields['STATUS_BLOK'];
}
?>
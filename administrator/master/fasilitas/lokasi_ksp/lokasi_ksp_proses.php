<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_lokasi	= (isset($_REQUEST['kode_lokasi'])) ? clean($_REQUEST['kode_lokasi']) : '';
$tipe_lokasi	= (isset($_REQUEST['tipe_lokasi'])) ? clean($_REQUEST['tipe_lokasi']) : '';
$detail_lokasi	= (isset($_REQUEST['detail_lokasi'])) ? clean($_REQUEST['detail_lokasi']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_lokasi, 'Kode harus diisi.');
			ex_empty($tipe_lokasi, 'Tipe lokasi harus diisi.');
			ex_empty($detail_lokasi, 'Detail lokasi harus diisi.');
			$kode_lokasi = $kode_sk . '-' . $tipe_lokasi;
			
			$query = "SELECT KODE_LOKASI FROM KWT_LOKASI_KSP WHERE KODE_LOKASI = '$kode_lokasi'";
			ex_found($conn->Execute($query)->recordcount(), "Kode lokasi \"$kode_lokasi\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_LOKASI_KSP (
				KODE_LOKASI, KODE_SK, TIPE_LOKASI,
				DETAIL_LOKASI)
			VALUES(
				'$kode_lokasi', 
				'$kode_sk', 
				'$tipe_lokasi',
				'$detail_lokasi'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Lokasi kegiatan shooting / pemotretan \"$kode_lokasi\" berhasil disimpan.";
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

			ex_empty($kode_lokasi, 'Kode harus diisi.');
			ex_empty($tipe_lokasi, 'Tipe lokasi harus diisi.');
			ex_empty($detail_lokasi, 'Detail lokasi harus diisi.');
			$kode_lokasi = $kode_sk . '-' . $tipe_lokasi;
			
			if ($kode_lokasi != $id)
			{
				$query = "SELECT KODE_LOKASI FROM KWT_LOKASI_KSP WHERE KODE_LOKASI = '$kode_lokasi'";
				ex_found($conn->Execute($query)->recordcount(), "Kode lokasi \"$kode_lokasi\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_LOKASI_KSP 
			SET 
				KODE_LOKASI = '$kode_lokasi',
				KODE_SK = '$kode_sk',
				TIPE_LOKASI = '$tipe_lokasi',
				DETAIL_LOKASI = '$detail_lokasi'
			WHERE
				KODE_LOKASI = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Lokasi kegiatan shooting / pemotretan berhasil diubah.';
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
				$query = "DELETE FROM KWT_LOKASI_KSP WHERE KODE_LOKASI = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data lokasi kegiatan shooting / pemotretan berhasil dihapus.';
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
	FROM KWT_LOKASI_KSP
	WHERE KODE_LOKASI = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_lokasi	= $obj->fields['KODE_LOKASI'];
	$kode_sk		= $obj->fields['KODE_SK'];
	$tipe_lokasi	= $obj->fields['TIPE_LOKASI'];
	$detail_lokasi	= $obj->fields['DETAIL_LOKASI'];
}
?>
<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_sk		= (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$no_sk			= (isset($_REQUEST['no_sk'])) ? clean($_REQUEST['no_sk']) : '';
$tgl_sk			= (isset($_REQUEST['tgl_sk'])) ? clean($_REQUEST['tgl_sk']) : '';
$tgl_berlaku	= (isset($_REQUEST['tgl_berlaku'])) ? clean($_REQUEST['tgl_berlaku']) : '';
$pembuat		= (isset($_REQUEST['pembuat'])) ? clean($_REQUEST['pembuat']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$status_sk		= (isset($_REQUEST['status_sk'])) ? clean($_REQUEST['status_sk']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_sk, 'Kode SK harus diisi.');
			ex_empty($no_sk, 'No SK harus diisi.');
			ex_empty($tgl_sk, 'Tanggal SK harus diisi.');
			ex_empty($tgl_berlaku, 'Tanggal berlaku harus diisi.');
			ex_empty($status_sk, 'Pilih status SK.');
			
			if ($status_sk == '1')
			{
				$conn->Execute("UPDATE KWT_SK_PSP SET STATUS_SK = 0");
			}
			
			$query = "SELECT KODE_SK FROM KWT_SK_PSP WHERE KODE_SK = '$kode_sk'";
			ex_found($conn->Execute($query)->recordcount(), "Kode SK \"$kode_sk\" telah terdaftar.");
			
			$query = "SELECT NO_SK FROM KWT_SK_PSP WHERE NO_SK = '$no_sk'";
			ex_found($conn->Execute($query)->recordcount(), "No SK \"$no_sk\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_SK_PSP (KODE_SK, NO_SK, TGL_SK, TGL_BERLAKU, PEMBUAT, KETERANGAN, STATUS_SK)
			VALUES(
				'$kode_sk', 
				'$no_sk', 
				CONVERT(DATE,'$tgl_sk',105), 
				CONVERT(DATE,'$tgl_berlaku',105),
				'$pembuat',
				'$keterangan',
				'$status_sk'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "SK tarif pembukaan sarana prasaranan lingkungan \"$no_sk\" berhasil disimpan.";
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

			ex_empty($kode_sk, 'Kode SK harus diisi.');
			ex_empty($no_sk, 'No SK harus diisi.');
			ex_empty($tgl_sk, 'Tanggal SK harus diisi.');
			ex_empty($tgl_berlaku, 'Tanggal berlaku harus diisi.');
			ex_empty($status_sk, 'Pilih status SK.');
			
			if ($status_sk == '1')
			{
				$conn->Execute("UPDATE KWT_SK_PSP SET STATUS_SK = 0");
			}
			
			if ($kode_sk != $id)
			{
				$query = "SELECT KODE_SK FROM KWT_SK_PSP WHERE KODE_SK = '$kode_sk'";
				ex_found($conn->Execute($query)->recordcount(), "Kode SK \"$kode_sk\" telah terdaftar.");
			}
			
			$old_no_sk = $conn->Execute("SELECT NO_SK FROM KWT_SK_PSP WHERE KODE_SK = '$id'")->fields['NO_SK'];
			if ($no_sk != $old_no_sk)
			{
				$query = "SELECT NO_SK FROM KWT_SK_PSP WHERE NO_SK = '$no_sk'";
				ex_found($conn->Execute($query)->recordcount(), "No SK \"$no_sk\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_SK_PSP 
			SET 
				KODE_SK = '$kode_sk', 
				NO_SK = '$no_sk',
				TGL_SK = CONVERT(DATE,'$tgl_sk',105), 
				TGL_BERLAKU = CONVERT(DATE,'$tgl_berlaku',105), 
				PEMBUAT = '$pembuat',
				KETERANGAN = '$keterangan',
				STATUS_SK = '$status_sk'
			WHERE
				KODE_SK = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'SK tarif pembukaan sarana prasaranan lingkungan berhasil diubah.';
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
				$query = "DELETE FROM KWT_SK_PSP WHERE KODE_SK = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$id_error[] = $id_del;
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data sk tarif pembukaan sarana prasaranan lingkungan berhasil dihapus.';
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
	SELECT 
		KODE_SK, 
		NO_SK, 
		CONVERT(VARCHAR(10),TGL_SK,105) AS TGL_SK,
		CONVERT(VARCHAR(10),TGL_BERLAKU,105) AS TGL_BERLAKU,
		PEMBUAT,
		KETERANGAN,
		STATUS_SK
	FROM KWT_SK_PSP 
	WHERE KODE_SK = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_sk		= $obj->fields['KODE_SK'];
	$no_sk			= $obj->fields['NO_SK'];
	$tgl_sk			= $obj->fields['TGL_SK'];
	$tgl_berlaku	= $obj->fields['TGL_BERLAKU'];
	$pembuat		= $obj->fields['PEMBUAT'];
	$keterangan		= $obj->fields['KETERANGAN'];
	$status_sk		= $obj->fields['STATUS_SK'];
}
?>
<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_sk	= (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$no_sk		= (isset($_REQUEST['no_sk'])) ? clean($_REQUEST['no_sk']) : '';
$status_sk		= (isset($_REQUEST['status_sk'])) ? clean($_REQUEST['status_sk']) : '';
$tgl_sk			= (isset($_REQUEST['tgl_sk'])) ? clean($_REQUEST['tgl_sk']) : '';
$tgl_berlaku	= (isset($_REQUEST['tgl_berlaku'])) ? clean($_REQUEST['tgl_berlaku']) : '';
$pembuat		= (isset($_REQUEST['pembuat'])) ? clean($_REQUEST['pembuat']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_sk, 'Kode SK harus diisi.');
			ex_empty($no_sk, 'No SK harus diisi.');
			ex_empty($status_sk, 'Pilih status SK.');
			ex_empty($tgl_sk, 'Tanggal SK harus diisi.');
			ex_empty($tgl_berlaku, 'Tanggal berlaku harus diisi.');
			
			if ( ! is_numeric($kode_sk)) {
				throw new Exception('Kode SK harus berupa angka.');
			}
			
			$query = "SELECT KODE_SK FROM KWT_SK_IPL WHERE KODE_SK = '$kode_sk'";
			ex_found($conn->Execute($query)->recordcount(), "Kode SK \"$kode_sk\" telah terdaftar.");
			
			$query = "SELECT NO_SK FROM KWT_SK_IPL WHERE NO_SK = '$no_sk'";
			ex_found($conn->Execute($query)->recordcount(), "No SK \"$no_sk\" telah terdaftar.");
			
			if ($status_sk == '1')
			{
				$conn->Execute("UPDATE KWT_SK_IPL SET STATUS_SK = '0'");
			}
			
			$query = "INSERT INTO KWT_SK_IPL (KODE_SK, NO_SK, STATUS_SK, TGL_SK, TGL_BERLAKU, PEMBUAT, KETERANGAN)
			VALUES(
				'$kode_sk', 
				'$no_sk', 
				'$status_sk', 
				CONVERT(DATETIME,'$tgl_sk',105), 
				CONVERT(DATETIME,'$tgl_berlaku',105),
				'$pembuat',
				'$keterangan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "SK IPL \"$no_sk\" berhasil disimpan.";
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

			ex_empty($kode_sk, 'Kode SK harus diisi.');
			ex_empty($no_sk, 'No SK harus diisi.');
			ex_empty($status_sk, 'Pilih status SK.');
			ex_empty($tgl_sk, 'Tanggal SK harus diisi.');
			ex_empty($tgl_berlaku, 'Tanggal berlaku harus diisi.');
			
			if ( ! is_numeric($kode_sk)) {
				throw new Exception('Kode SK harus berupa angka.');
			}
			
			if ($kode_sk != $id)
			{
				$query = "SELECT KODE_SK FROM KWT_SK_IPL WHERE KODE_SK = '$kode_sk'";
				ex_found($conn->Execute($query)->recordcount(), "Kode SK \"$kode_sk\" telah terdaftar.");
			}
			
			$old_no_sk = $conn->Execute("SELECT NO_SK FROM KWT_SK_IPL WHERE KODE_SK = '$id'")->fields['NO_SK'];
			if ($no_sk != $old_no_sk)
			{
				$query = "SELECT NO_SK FROM KWT_SK_IPL WHERE NO_SK = '$no_sk'";
				ex_found($conn->Execute($query)->recordcount(), "No SK \"$no_sk\" telah terdaftar.");
			}
			
			if ($status_sk == '1')
			{
				$conn->Execute("UPDATE KWT_SK_IPL SET STATUS_SK = '0'");
			}
			
			$query = "
			UPDATE KWT_SK_IPL 
			SET 
				KODE_SK = '$kode_sk', 
				NO_SK = '$no_sk',
				STATUS_SK = '$status_sk',
				TGL_SK = CONVERT(DATETIME,'$tgl_sk',105), 
				TGL_BERLAKU = CONVERT(DATETIME,'$tgl_berlaku',105), 
				PEMBUAT = '$pembuat',
				KETERANGAN = '$keterangan'
			WHERE
				KODE_SK = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'SK IPL berhasil diubah.';
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
				$query = "DELETE FROM KWT_SK_IPL WHERE KODE_SK = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data sk tarif IPL berhasil dihapus.';
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
		STATUS_SK,
		CONVERT(VARCHAR(10),TGL_SK,105) AS TGL_SK,
		CONVERT(VARCHAR(10),TGL_BERLAKU,105) AS TGL_BERLAKU,
		PEMBUAT,
		KETERANGAN
	FROM KWT_SK_IPL 
	WHERE KODE_SK = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_sk	= $obj->fields['KODE_SK'];
	$no_sk		= $obj->fields['NO_SK'];
	$status_sk		= $obj->fields['STATUS_SK'];
	$tgl_sk			= $obj->fields['TGL_SK'];
	$tgl_berlaku	= $obj->fields['TGL_BERLAKU'];
	$pembuat		= $obj->fields['PEMBUAT'];
	$keterangan		= $obj->fields['KETERANGAN'];
}
?>
<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_sk	= (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$act		= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id			= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$tarif_ipl			= (isset($_REQUEST['tarif_ipl'])) ? to_number($_REQUEST['tarif_ipl']) : '';
$denda_standar_ipl	= (isset($_REQUEST['denda_standar_ipl'])) ? to_decimal($_REQUEST['denda_standar_ipl']) : '';
$denda_bisnis_ipl	= (isset($_REQUEST['denda_bisnis_ipl'])) ? to_decimal($_REQUEST['denda_bisnis_ipl']) : '';
$nilai_deposit		= (isset($_REQUEST['nilai_deposit'])) ? to_number($_REQUEST['nilai_deposit']) : '';
$kode_tipe			= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$keterangan			= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$key_ipl			= (isset($_REQUEST['key_ipl'])) ? clean($_REQUEST['key_ipl']) : '';
$key_ipl			= (empty($key_ipl)) ? $kode_sk.'-'.$kode_tipe : $key_ipl;

$status_blok = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_tipe, 'Pilih kategori.');
			
			$query = "SELECT KEY_IPL FROM KWT_TARIF_IPL WHERE KEY_IPL = '$key_ipl'";
			ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_ipl\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_IPL (
				KEY_IPL,
				KODE_SK,
				KODE_TIPE,
				TARIF_IPL,
				DENDA_BISNIS_IPL,
				DENDA_STANDAR_IPL,
				NILAI_DEPOSIT,
				KETERANGAN)
			VALUES(
				'$key_ipl',
				'$kode_sk',
				'$kode_tipe',
				$tarif_ipl,
				$denda_bisnis_ipl,
				$denda_standar_ipl,
				$nilai_deposit,
				'$keterangan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Tarif IPL \"$key_ipl\" berhasil disimpan.";
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

			ex_empty($kode_tipe, 'Pilih kategori.');
			
			if ($key_ipl != $id)
			{
				$query = "SELECT KEY_IPL FROM KWT_TARIF_IPL WHERE KEY_IPL = '$key_ipl'";
				ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_ipl\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_IPL 
			SET 
				KEY_IPL = '$key_ipl',
				KODE_SK = '$kode_sk',
				KODE_TIPE = '$kode_tipe',
				TARIF_IPL = $tarif_ipl,
				DENDA_BISNIS_IPL = $denda_bisnis_ipl,
				DENDA_STANDAR_IPL = $denda_standar_ipl,
				NILAI_DEPOSIT = $nilai_deposit,
				KETERANGAN = '$keterangan'
			WHERE
				KEY_IPL = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tarif IPL berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_IPL WHERE KEY_IPL = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data tarif IPL berhasil dihapus.';
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
		d.*, 
		t.STATUS_BLOK
	FROM
		KWT_TARIF_IPL d
		LEFT JOIN KWT_TIPE_IPL t ON d.KODE_TIPE = t.KODE_TIPE
	WHERE 
		d.KEY_IPL = '$id'
	";
	
	$obj = $conn->Execute($query);
	
	$status_blok		= $obj->fields['STATUS_BLOK'];
	$key_ipl			= $obj->fields['KEY_IPL'];
	$kode_sk			= $obj->fields['KODE_SK'];
	$kode_tipe			= $obj->fields['KODE_TIPE'];
	$tarif_ipl			= $obj->fields['TARIF_IPL'];
	$denda_bisnis_ipl	= $obj->fields['DENDA_BISNIS_IPL'];
	$denda_standar_ipl	= $obj->fields['DENDA_STANDAR_IPL'];
	$nilai_deposit		= $obj->fields['NILAI_DEPOSIT'];
	$keterangan			= $obj->fields['KETERANGAN'];
	
}
?>
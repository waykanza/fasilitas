<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_tipe	= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$blok1		= (isset($_REQUEST['blok1'])) ? to_number($_REQUEST['blok1']) : '';
$blok2		= (isset($_REQUEST['blok2'])) ? to_number($_REQUEST['blok2']) : '';
$blok3		= (isset($_REQUEST['blok3'])) ? to_number($_REQUEST['blok3']) : '';
$blok4		= (isset($_REQUEST['blok4'])) ? to_number($_REQUEST['blok4']) : '';
$stand_min_pakai = (isset($_REQUEST['stand_min_pakai'])) ? to_number($_REQUEST['stand_min_pakai']) : '';
$tarif1		= (isset($_REQUEST['tarif1'])) ? to_decimal($_REQUEST['tarif1']) : '';
$tarif2		= (isset($_REQUEST['tarif2'])) ? to_decimal($_REQUEST['tarif2']) : '';
$tarif3		= (isset($_REQUEST['tarif3'])) ? to_decimal($_REQUEST['tarif3']) : '';
$tarif4		= (isset($_REQUEST['tarif4'])) ? to_decimal($_REQUEST['tarif4']) : '';
$abonemen	= (isset($_REQUEST['abonemen'])) ? to_number($_REQUEST['abonemen']) : '';
$denda_standar_air = (isset($_REQUEST['denda_standar_air'])) ? to_decimal($_REQUEST['denda_standar_air']) : '';
$denda_bisnis_air = (isset($_REQUEST['denda_bisnis_air'])) ? to_decimal($_REQUEST['denda_bisnis_air']) : '';
$keterangan	= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';
$key_air	= (isset($_REQUEST['key_air'])) ? clean($_REQUEST['key_air']) : '';
$key_air	= (empty($key_air)) ? $kode_sk.'-'.$kode_tipe : $key_air;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
		
			ex_empty($kode_tipe, 'Pilih kategori.');
			
			$query = "SELECT KEY_AIR FROM KWT_TARIF_AIR WHERE KEY_AIR = '$key_air'";
			ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_air\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_AIR (
				KODE_SK, 
				ABONEMEN,
				DENDA_BISNIS_AIR,
				DENDA_STANDAR_AIR,
				KETERANGAN, 
				KEY_AIR, 
				KODE_TIPE,
				BLOK1, BLOK2, BLOK3, BLOK4, STAND_MIN_PAKAI,
				TARIF1, TARIF2, TARIF3, TARIF4)
			VALUES(
				'$kode_sk',
				$abonemen,
				$denda_bisnis_air,
				$denda_standar_air,
				'$keterangan',
				'$key_air',
				'$kode_tipe',
				$blok1,
				$blok2,
				$blok3,
				$blok4,
				$stand_min_pakai,
				$tarif1,
				$tarif2,
				$tarif3,
				$tarif4
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Tarif air \"$key_air\" berhasil disimpan.";
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
			
			if ($key_air != $id)
			{
				$query = "SELECT KEY_AIR FROM KWT_TARIF_AIR WHERE KEY_AIR = '$key_air'";
				ex_found($conn->Execute($query)->recordcount(), "Key# \"$key_air\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_AIR 
			SET 
				KODE_SK = '$kode_sk',
				ABONEMEN = $abonemen,
				DENDA_BISNIS_AIR = $denda_bisnis_air,
				DENDA_STANDAR_AIR = $denda_standar_air,
				KETERANGAN = '$keterangan',
				KEY_AIR = '$key_air',
				KODE_TIPE = '$kode_tipe',
				BLOK1 = $blok1,
				BLOK2 = $blok2,
				BLOK3 = $blok3,
				BLOK4 = $blok4,
				STAND_MIN_PAKAI = $stand_min_pakai,
				TARIF1 = $tarif1,
				TARIF2 = $tarif2,
				TARIF3 = $tarif3,
				TARIF4 = $tarif4
			WHERE
				KEY_AIR = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tarif air berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_AIR WHERE KEY_AIR = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data tarif air berhasil dihapus.';
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
	FROM KWT_TARIF_AIR 
	WHERE KEY_AIR = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_sk = $obj->fields['KODE_SK'];
	$abonemen = $obj->fields['ABONEMEN'];
	$denda_bisnis_air = $obj->fields['DENDA_BISNIS_AIR'];
	$denda_standar_air = $obj->fields['DENDA_STANDAR_AIR'];
	$keterangan = $obj->fields['KETERANGAN'];
	$key_air = $obj->fields['KEY_AIR'];
	$kode_tipe = $obj->fields['KODE_TIPE'];
	$blok1 = $obj->fields['BLOK1'];
	$blok2 = $obj->fields['BLOK2'];
	$blok3 = $obj->fields['BLOK3'];
	$blok4 = $obj->fields['BLOK4'];
	$stand_min_pakai = $obj->fields['STAND_MIN_PAKAI'];
	$tarif1 = $obj->fields['TARIF1'];
	$tarif2 = $obj->fields['TARIF2'];
	$tarif3 = $obj->fields['TARIF3'];
	$tarif4 = $obj->fields['TARIF4'];
}
?>
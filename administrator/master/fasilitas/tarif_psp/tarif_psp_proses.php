<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$kode_sk = (isset($_REQUEST['kode_sk'])) ? clean($_REQUEST['kode_sk']) : '';
$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$key_psp		= (isset($_REQUEST['key_psp'])) ? clean($_REQUEST['key_psp']) : '';
$kode_tipe		= (isset($_REQUEST['kode_tipe'])) ? clean($_REQUEST['kode_tipe']) : '';
$kode_fungsi	= (isset($_REQUEST['kode_fungsi'])) ? clean($_REQUEST['kode_fungsi']) : '';
$tarif			= (isset($_REQUEST['tarif'])) ? to_number($_REQUEST['tarif']) : '';
$lokasi			= (isset($_REQUEST['lokasi'])) ? clean($_REQUEST['lokasi']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	
	if ($act == 'Simpan') /* Proses Simpan */
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_tipe, 'Kategori harus diisi.');
			ex_empty($kode_fungsi, 'Fungsi harus diisi.');
			ex_empty($lokasi, 'Lokasi harus diisi.');
			$key_psp = $kode_sk . '-' . $kode_tipe . '-' . $kode_fungsi;
			
			$query = "SELECT KEY_PSP FROM KWT_TARIF_PSP WHERE KEY_PSP = '$key_psp'";
			ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_psp\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_TARIF_PSP (KEY_PSP, KODE_SK, KODE_TIPE,KODE_FUNGSI, TARIF, LOKASI)
			VALUES(
				'$key_psp', 
				'$kode_sk', 
				'$kode_tipe',
				'$kode_fungsi',
				$tarif,
				'$lokasi'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Tarif pembukaan sarana prasarana lingkungan \"$key_psp\" berhasil disimpan.";
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
			ex_empty($kode_fungsi, 'Fungsi harus diisi.');
			ex_empty($lokasi, 'Lokasi harus diisi.');
			$key_psp = $kode_sk . '-' . $kode_tipe . '-' . $kode_fungsi;
			
			if ($key_psp != $id)
			{
				$query = "SELECT KEY_PSP FROM KWT_TARIF_PSP WHERE KEY_PSP = '$key_psp'";
				ex_found($conn->Execute($query)->recordcount(), "Key# tarif \"$key_psp\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_TARIF_PSP 
			SET 
				KEY_PSP = '$key_psp', 
				KODE_SK = '$kode_sk', 
				KODE_TIPE = '$kode_tipe',
				KODE_FUNGSI = '$kode_fungsi',
				TARIF = $tarif,
				LOKASI = '$lokasi'
			WHERE
				KEY_PSP = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Tarif pembukaan sarana prasarana lingkungan berhasil diubah.';
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
				$query = "DELETE FROM KWT_TARIF_PSP WHERE KEY_PSP = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus. Kode: '.implode(', ', $id_error) : 'Data tarif pembukaan sarana prasarana lingkungan berhasil dihapus.';
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
	FROM KWT_TARIF_PSP
	WHERE KEY_PSP = '$id'";
	
	$obj = $conn->execute($query);
	$key_psp		= $obj->fields['KEY_PSP'];
	$kode_sk		= $obj->fields['KODE_SK'];
	$kode_tipe		= $obj->fields['KODE_TIPE'];
	$kode_fungsi	= $obj->fields['KODE_FUNGSI'];
	$tarif			= $obj->fields['TARIF'];
	$lokasi			= $obj->fields['LOKASI'];
}
?>
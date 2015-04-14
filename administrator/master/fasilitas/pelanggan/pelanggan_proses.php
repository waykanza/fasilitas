<?php
require_once('../../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$no_va		= (isset($_REQUEST['no_va'])) ? clean($_REQUEST['no_va']) : '';
$nama		= (isset($_REQUEST['nama'])) ? clean($_REQUEST['nama']) : '';
$kode_blok	= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$no_telp	= (isset($_REQUEST['no_telp'])) ? clean($_REQUEST['no_telp']) : '';
$no_hp		= (isset($_REQUEST['no_hp'])) ? clean($_REQUEST['no_hp']) : '';
$alamat		= (isset($_REQUEST['alamat'])) ? clean($_REQUEST['alamat']) : '';
$keterangan	= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($no_va, 'No. virtual account harus diisi.');
			ex_empty($nama, 'Nama  harus diisi.');
			ex_empty($kode_blok, 'Kode blok harus diisi.');
			ex_empty($no_telp, 'No. telepon harus diisi.');
			ex_empty($no_hp, 'No. Hp harus diisi.');
			ex_empty($alamat, 'Alamat harus diisi.');
			
			$query = "SELECT NO_PELANGGAN FROM FSL_PELANGGAN WHERE NO_PELANGGAN = '$no_va'";
			ex_found($conn->Execute($query)->recordcount(), "NO PELANGGAN \"$no_va\" telah terdaftar.");
			
			$query = "INSERT INTO FSL_PELANGGAN (NO_PELANGGAN, NAMA_PELANGGAN, KODE_BLOK, NO_HP, NO_TELEPON, ALAMAT, KETERANGAN)
			VALUES(
				'$no_va', 
				'$nama', 
				'$kode_blok', 
				'$no_telp', 
				'$no_hp', 
				'$alamat',
				'$keterangan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Pelanggan \"$nama\" berhasil disimpan.";
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

			ex_empty($no_va, 'No. virtual account harus diisi.');
			ex_empty($nama, 'Nama  harus diisi.');
			ex_empty($kode_blok, 'Kode blok harus diisi.');
			ex_empty($no_telp, 'No. telepon harus diisi.');
			ex_empty($no_hp, 'No. Hp harus diisi.');
			ex_empty($alamat, 'Alamat harus diisi.');
			
			if ($no_va != $id)
			{
				$query = "SELECT NO_PELANGGAN FROM FSL_PELANGGAN WHERE NO_PELANGGAN = '$no_va'";
				ex_found($conn->Execute($query)->recordcount(), "NO PELANGGAN \"$no_va\" telah terdaftar.");
			}
			
			$query = "
			UPDATE FSL_PELANGGAN 
			SET 
				NO_PELANGGAN = '$no_va',
				NAMA_PELANGGAN = '$nama',
				KODE_BLOK = '$kode_blok',
				NO_HP = '$no_hp',
				NO_TELEPON = '$no_telp',
				ALAMAT = '$alamat',
				KETERANGAN = '$keterangan'
			WHERE
				NO_PELANGGAN = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Pelanggan berhasil diubah.';
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
				$query = "DELETE FROM FSL_PELANGGAN WHERE NO_PELANGGAN = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data cluster berhasil dihapus.';
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
	$query = "SELECT * FROM FSL_PELANGGAN WHERE NO_PELANGGAN = '$id'";
	$obj = $conn->Execute($query);
	$no_va	= $obj->fields['NO_PELANGGAN'];
	$nama	= $obj->fields['NAMA_PELANGGAN'];
	$kode_blok	= $obj->fields['KODE_BLOK'];
	$no_hp	= $obj->fields['NO_HP'];
	$no_telp	= $obj->fields['NO_TELEPON'];
	$alamat	= $obj->fields['ALAMAT'];
	$keterangan	= $obj->fields['KETERANGAN'];
}
?>
<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$nama_cluster	= (isset($_REQUEST['nama_cluster'])) ? clean($_REQUEST['nama_cluster']) : '';
$kode_sektor	= (isset($_REQUEST['kode_sektor'])) ? clean($_REQUEST['kode_sektor']) : '';
$kode_cluster	= (isset($_REQUEST['kode_cluster'])) ? clean($_REQUEST['kode_cluster']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_cluster, 'Kode cluster harus diisi.');
			ex_empty($nama_cluster, 'Nama cluster harus diisi.');
			ex_empty($kode_sektor, 'Kode sektor harus dipilih.');
			
			$query = "SELECT KODE_CLUSTER FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$kode_cluster'";
			ex_found($conn->Execute($query)->recordcount(), "Kode cluster \"$kode_cluster\" telah terdaftar.");
			
			$query = "SELECT NAMA_CLUSTER FROM KWT_CLUSTER WHERE NAMA_CLUSTER = '$nama_cluster'";
			ex_found($conn->Execute($query)->recordcount(), "Nama cluster \"$nama_cluster\" telah terdaftar.");
			
			$query = "INSERT INTO KWT_CLUSTER (KODE_CLUSTER, NAMA_CLUSTER, KODE_SEKTOR)
			VALUES(
				'$kode_cluster', 
				'$nama_cluster', 
				'$kode_sektor'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Cluster \"$nama_cluster\" berhasil disimpan.";
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

			ex_empty($kode_cluster, 'Kode cluster harus diisi.');
			ex_empty($nama_cluster, 'Nama cluster harus diisi.');
			ex_empty($kode_sektor, 'Kode sektor harus dipilih.');
			
			if ($kode_cluster != $id)
			{
				$query = "SELECT KODE_CLUSTER FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$kode_cluster'";
				ex_found($conn->Execute($query)->recordcount(), "Kode cluster \"$kode_cluster\" telah terdaftar.");
			}
			
			$old_nama_cluster = $conn->Execute("SELECT NAMA_CLUSTER FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$id'")->fields['NAMA_CLUSTER'];
			if ($nama_cluster != $old_nama_cluster)
			{
				$query = "SELECT NAMA_CLUSTER FROM KWT_CLUSTER WHERE NAMA_CLUSTER = '$nama_cluster'";
				ex_found($conn->Execute($query)->recordcount(), "Nama cluster \"$nama_cluster\" telah terdaftar.");
			}
			
			$query = "
			UPDATE KWT_CLUSTER 
			SET 
				KODE_CLUSTER = '$kode_cluster',
				NAMA_CLUSTER = '$nama_cluster',
				KODE_SEKTOR = '$kode_sektor'
			WHERE
				KODE_CLUSTER = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Cluster berhasil diubah.';
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
				$query = "DELETE FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$id_del'";
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
	$query = "SELECT * FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$id'";
	$obj = $conn->Execute($query);
	$kode_cluster	= $obj->fields['KODE_CLUSTER'];
	$nama_cluster	= $obj->fields['NAMA_CLUSTER'];
	$kode_sektor	= $obj->fields['KODE_SEKTOR'];
}
?>
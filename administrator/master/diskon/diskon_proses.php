<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act	= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$id		= (isset($_REQUEST['id'])) ? clean($_REQUEST['id']) : '';

$kode_blok			= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';
$periode_awal		= (isset($_REQUEST['periode_awal'])) ? to_periode($_REQUEST['periode_awal']) : '';
$periode_akhir		= (isset($_REQUEST['periode_akhir'])) ? to_periode($_REQUEST['periode_akhir']) : '';
$diskon_air_nilai	= (isset($_REQUEST['diskon_air_nilai'])) ? to_number($_REQUEST['diskon_air_nilai']) : '';
$diskon_ipl_nilai	= (isset($_REQUEST['diskon_ipl_nilai'])) ? to_number($_REQUEST['diskon_ipl_nilai']) : '';
$diskon_air_persen	= (isset($_REQUEST['diskon_air_persen'])) ? to_decimal($_REQUEST['diskon_air_persen']) : '';
$diskon_ipl_persen	= (isset($_REQUEST['diskon_ipl_persen'])) ? to_decimal($_REQUEST['diskon_ipl_persen']) : '';
$keterangan			= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($kode_blok, 'Kode harus diisi.');
			ex_empty($periode_awal, 'Periode awal harus diisi.');
			ex_empty($periode_akhir, 'Periode akhir harus diisi.');
			
			$query = "INSERT INTO KWT_DISKON_KHUSUS (KODE_BLOK, PERIODE_AWAL, PERIODE_AKHIR, DISKON_AIR_NILAI, DISKON_IPL_NILAI, DISKON_AIR_PERSEN, DISKON_IPL_PERSEN, KETERANGAN)
			VALUES(
				'$kode_blok', 
				'$periode_awal',
				'$periode_akhir',
				$diskon_air_nilai,
				$diskon_ipl_nilai,
				$diskon_air_persen,
				$diskon_ipl_persen, 
				'$keterangan'
			)";
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Diskon Khusus \"$kode_blok\" berhasil disimpan.";
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

			ex_empty($kode_blok, 'Kode harus diisi.');
			ex_empty($periode_awal, 'Periode awal harus diisi.');
			ex_empty($periode_akhir, 'Periode akhir harus diisi.');
			
			$query = "
			UPDATE KWT_DISKON_KHUSUS 
			SET 
				KODE_BLOK = '$kode_blok', 
				PERIODE_AWAL = '$periode_awal', 
				PERIODE_AKHIR = '$periode_akhir', 
				DISKON_AIR_NILAI = $diskon_air_nilai,
				DISKON_IPL_NILAI = $diskon_ipl_nilai,
				DISKON_AIR_PERSEN = $diskon_air_persen,
				DISKON_IPL_PERSEN = $diskon_ipl_persen,
				KETERANGAN = '$keterangan'
			WHERE
				ID_DISKON = '$id'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Diskon Khusus berhasil diubah.';
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
				$query = "DELETE FROM KWT_DISKON_KHUSUS WHERE ID_DISKON = '$id_del'";
				if ($conn->Execute($query)) {
					$act[] = $id_del;
				} else {
					$error = TRUE;
				}
			}
			
			$conn->committrans();
			
			$msg = ($error) ? 'Sebagian data gagal dihapus.' : 'Data diskon berhasil dihapus.';
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
		KODE_BLOK,
		dbo.PTPS(PERIODE_AWAL) AS PERIODE_AWAL,
		dbo.PTPS(PERIODE_AKHIR) AS PERIODE_AKHIR,
		DISKON_AIR_NILAI,
		DISKON_IPL_NILAI,
		DISKON_AIR_PERSEN,
		DISKON_IPL_PERSEN,
		KETERANGAN
	FROM KWT_DISKON_KHUSUS 
	WHERE ID_DISKON = '$id'";
	
	$obj = $conn->Execute($query);
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$periode_awal		= $obj->fields['PERIODE_AWAL'];
	$periode_akhir		= $obj->fields['PERIODE_AKHIR'];
	$diskon_air_nilai	= $obj->fields['DISKON_AIR_NILAI'];
	$diskon_ipl_nilai	= $obj->fields['DISKON_IPL_NILAI'];
	$diskon_air_persen	= $obj->fields['DISKON_AIR_PERSEN'];
	$diskon_ipl_persen	= $obj->fields['DISKON_IPL_PERSEN'];
	$keterangan			= $obj->fields['KETERANGAN'];
}
?>
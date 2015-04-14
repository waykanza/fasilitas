<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

$act			= (isset($_REQUEST['act'])) ? clean($_REQUEST['act']) : '';
$idd			= (isset($_REQUEST['idd'])) ? base64_decode(clean($_REQUEST['idd'])) : '';
$kode_blok		= (isset($_REQUEST['kode_blok'])) ? clean($_REQUEST['kode_blok']) : '';

$periode_awal	= (isset($_REQUEST['periode_awal'])) ? to_periode($_REQUEST['periode_awal']) : '';
$jumlah_periode	= (isset($_REQUEST['jumlah_periode'])) ? to_number($_REQUEST['jumlah_periode']) : '';
$nilai_deposit	= (isset($_REQUEST['nilai_deposit'])) ? to_number($_REQUEST['nilai_deposit']) : '';
$keterangan		= (isset($_REQUEST['keterangan'])) ? clean($_REQUEST['keterangan']) : '';

$opt_deposit	= (isset($_REQUEST['opt_deposit'])) ? clean($_REQUEST['opt_deposit']) : '';

$master_nilai_deposit = $conn->Execute("
SELECT i.NILAI_DEPOSIT AS MASTER_NILAI_DEPOSIT
FROM KWT_PELANGGAN p LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
WHERE p.kode_blok = '$kode_blok'")->fields['MASTER_NILAI_DEPOSIT'];

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	if ($act == 'Simpan') # Proses Tambah
	{
		try
		{
			$conn->begintrans();
			
			ex_empty($periode_awal, 'Masukkan periode mulai.');
			ex_zero($jumlah_periode, 'Jumlah periode harus > 0');
			if ($opt_deposit == '') { throw new Exception('Pilih option deposit.'); }
			
			$jumlah_periode--;
			$periode_akhir = date('Ym', strtotime("+$jumlah_periode months", strtotime($periode_awal.'01')));
			$jumlah_periode++;
			
			$query = "
			SELECT TOP 1
				p.STATUS_BLOK,
				d.STATUS_PROSES,
				d.PERIODE_AWAL,
				d.PERIODE_AKHIR
			FROM 
				KWT_PELANGGAN p
				LEFT JOIN KWT_PERIODE_DEPOSIT d ON p.KODE_BLOK = d.KODE_BLOK AND d.TRX = '6'
			WHERE 
				p.KODE_BLOK = '$kode_blok'
			ORDER BY CAST(d.PERIODE_AKHIR AS INT) DESC
			";
			
			$obj = $conn->Execute($query);
			
			$prev_status_blok = $obj->fields['STATUS_BLOK'];
			$prev_status_proses = $obj->fields['STATUS_PROSES'];
			$prev_periode_awal = $obj->fields['PERIODE_AWAL'];
			$prev_periode_akhir = $obj->fields['PERIODE_AKHIR'];
			
			if ($prev_status_blok != '5')
			{
				throw new Exception('Status blok tidak sama dengan "Renovasi".');
			}
			if ($prev_periode_akhir != '')
			{
				if ($prev_status_proses != '1')
				{
					throw new Exception('Periode sebelumnya belum diproses.');
				}
				if ($periode_awal <= $prev_periode_akhir)
				{
					throw new Exception("Periode mulai harus lebih besar dari \"$prev_periode_akhir\".");
				}
			}
			
			$id_deposit = '6#' . $periode_awal . '#' . $kode_blok;
			
			$query = "INSERT INTO KWT_PERIODE_DEPOSIT
			(
				ID_DEPOSIT, 
				TRX, 
				KODE_BLOK, 
				PERIODE_AWAL, 
				PERIODE_AKHIR, 
				JUMLAH_PERIODE, 
				NILAI_DEPOSIT, 
				KETERANGAN
			)
			VALUES
			(
				'$id_deposit', 
				'6', 
				'$kode_blok', 
				'$periode_awal', 
				'$periode_akhir',
				$jumlah_periode,
				$nilai_deposit,
				'$keterangan'
			)";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = "Periode renovasi berhasil disimpan.";
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

			$user_edit = $_SESSION['ID_USER'];
			
			ex_empty($periode_awal, 'Masukkan periode mulai.');
			ex_zero($jumlah_periode, 'Jumlah periode harus > 0');
			if ($opt_deposit == '') { throw new Exception('Pilih option deposit.'); }
			
			$jumlah_periode--;
			$periode_akhir = date('Ym', strtotime("+$jumlah_periode months", strtotime($periode_awal.'01')));
			$jumlah_periode++;
			
			$query = "
			DECLARE @idd VARCHAR(20) = '$idd'
			
			SELECT TOP 1
				p.STATUS_BLOK,
				d.STATUS_PROSES,
				d.PERIODE_AWAL,
				d.PERIODE_AKHIR,
				(SELECT STATUS_PROSES FROM KWT_PERIODE_DEPOSIT WHERE ID_DEPOSIT = @idd) AS THIS_STATUS_PROSES
			FROM 
				KWT_PELANGGAN p
				LEFT JOIN KWT_PERIODE_DEPOSIT d ON 
					p.KODE_BLOK = d.KODE_BLOK AND
					d.ID_DEPOSIT != @idd AND 
					CAST(d.PERIODE_AKHIR AS INT) < 
					(
						SELECT CAST(PERIODE_AWAL AS INT) 
						FROM KWT_PERIODE_DEPOSIT 
						WHERE ID_DEPOSIT = @idd
					)			
			WHERE 
				p.KODE_BLOK = '$kode_blok'
			ORDER BY CAST(d.PERIODE_AKHIR AS INT) DESC
			";
			
			$obj = $conn->Execute($query);
			
			$this_status_proses = $obj->fields['THIS_STATUS_PROSES'];
			if ($this_status_proses == '1')
			{
				throw new Exception('Periode sedang diproses.');
			}
			
			$prev_status_blok	= $obj->fields['STATUS_BLOK'];
			$prev_status_proses	= $obj->fields['STATUS_PROSES'];
			$prev_periode_awal	= $obj->fields['PERIODE_AWAL'];
			$prev_periode_akhir	= $obj->fields['PERIODE_AKHIR'];
			
			if ($prev_status_blok != '5')
			{
				throw new Exception('Status blok tidak sama dengan "Renovasi".'.$query);
			}
			if ($prev_periode_akhir != '')
			{
				if ($prev_status_proses != '1')
				{
					throw new Exception('Periode sebelumnya belum diproses.');
				}
				if ($periode_awal <= $prev_periode_akhir)
				{
					throw new Exception("Periode mulai harus lebih besar dari \"$prev_periode_akhir\".");
				}
			}
			
			$id_deposit = '6#' . $periode_awal . '#' . $kode_blok;
			
			$query = "
			UPDATE KWT_PERIODE_DEPOSIT 
			SET 
				ID_DEPOSIT = '$id_deposit', 
				KODE_BLOK = '$kode_blok',  
				PERIODE_AWAL = '$periode_awal',  
				PERIODE_AKHIR = '$periode_akhir',  
				JUMLAH_PERIODE = '$jumlah_periode',  
				NILAI_DEPOSIT = '$nilai_deposit',  
				KETERANGAN = '$keterangan',
				
				STATUS_EDIT = '1',
				USER_EDIT = '$user_edit',
				TGL_EDIT = GETDATE()
			WHERE
				ID_DEPOSIT = '$idd'
			";
			
			ex_false($conn->Execute($query), $query);
			
			$conn->committrans();
			
			$msg = 'Periode renovasi berhasil diubah.';
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
			
			$query = "
			SELECT STATUS_PROSES 
			FROM KWT_PERIODE_DEPOSIT
			WHERE ID_DEPOSIT = '$idd'";
			
			$status_proses = $conn->Execute($query)->fields['STATUS_PROSES'];
			if ($status_proses == '1')
			{
				throw new Exception("Periode sedang diproses.");
			}
			
			$conn->Execute("DELETE FROM KWT_PERIODE_DEPOSIT WHERE ID_DEPOSIT = '$idd'");
			
			$conn->committrans();
			
			$msg = 'Periode renovasi berhasil dihapus.';
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
		dbo.PTPS(d.PERIODE_AWAL) AS PERIODE_AWAL,
		d.JUMLAH_PERIODE,
		d.NILAI_DEPOSIT,
		d.KETERANGAN
	FROM 
		KWT_PERIODE_DEPOSIT d
	WHERE
		d.ID_DEPOSIT = '$idd'";
	
	$obj = $conn->Execute($query);
	
	$periode_awal		= $obj->fields['PERIODE_AWAL'];
	$jumlah_periode		= $obj->fields['JUMLAH_PERIODE'];
	$nilai_deposit		= $obj->fields['NILAI_DEPOSIT'];
	$keterangan			= $obj->fields['KETERANGAN'];
}

$idd		= base64_encode($idd);
$kode_blok	= $kode_blok;
?>
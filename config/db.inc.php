<?php
function get_parameter($f)
{
	Global $conn;
	
	if ($f != '')
	{
		return $conn->execute("SELECT TOP 1 $f FROM KWT_PARAMETER");
	}
}

# GET LOOKUP * TABLE
function get_nama($t, $k)
{
	Global $conn;
	
	switch ($t)
	{
		case 'user': 
			return $conn->execute("SELECT NAMA_USER FROM KWT_USER WHERE ID_USER = '$k'")->fields['NAMA_USER'];
			break;
		case 'bank': 
			return $conn->execute("SELECT NAMA_BANK FROM KWT_BANK WHERE KODE_BANK = '$k'")->fields['NAMA_BANK'];
			break;
		case 'sektor': 
			return $conn->execute("SELECT NAMA_SEKTOR FROM KWT_SEKTOR WHERE KODE_SEKTOR = '$k'")->fields['NAMA_SEKTOR'];
			break;
		case 'cluster': 
			return $conn->execute("SELECT NAMA_CLUSTER FROM KWT_CLUSTER WHERE KODE_CLUSTER = '$k'")->fields['NAMA_CLUSTER'];
			break;
		case 'zona_meter': 
			return $conn->execute("SELECT NAMA_ZONA FROM KWT_ZONA_METER_BALANCE WHERE KODE_ZONA = '$k'")->fields['NAMA_ZONA'];
			break;
		default : 
			return '';
			break;
	}
}

# UPDATE LOOKUP PELANGGAN
function pelanggan_lookup($v = array())
{
	Global $conn;
	
	$found = $conn->execute("SELECT COUNT(NO_KTP) AS TOTAL FROM KWT_PELANGGAN_LOOKUP WHERE NO_KTP = '" . $v['no_ktp'] . "'")->fields['TOTAL'];
	
	if ($found < 1)
	{
		$query = "INSERT INTO KWT_PELANGGAN_LOOKUP (NO_KTP, NAMA_PELANGGAN, NPWP, ALAMAT, NO_TELEPON, NO_HP, KODE_BANK, NO_REKENING)
		VALUES (
			'".$v['no_ktp']."',
			'".$v['nama_pelanggan']."',
			'".$v['npwp']."',
			'".$v['alamat']."',
			'".$v['no_telepon']."',
			'".$v['no_hp']."',
			'".$v['kode_bank']."',
			'".$v['no_rekening']."'
		)";
	}
	else
	{
		$query = "
		UPDATE KWT_PELANGGAN_LOOKUP
		SET NAMA_PELANGGAN = '".$v['nama_pelanggan']."',
			NPWP = '".$v['npwp']."',
			ALAMAT = '".$v['alamat']."',
			NO_TELEPON = '".$v['no_telepon']."',
			NO_HP = '".$v['no_hp']."',
			KODE_BANK = '".$v['kode_bank']."',
			NO_REKENING = '".$v['no_rekening']."'
		WHERE
			NO_KTP = '".$v['no_ktp']."'
		";
	}
	
	$conn->execute($query);
}
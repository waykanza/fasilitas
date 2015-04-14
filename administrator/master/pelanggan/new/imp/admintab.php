<?php
include "oracle.php";
// DB ORACLE
if(empty($db_oracle)){
	$user_db	= "admintab";
	$pass_db	= "systabadmin";
	$host_db	= "localhost"; //128.128.1.1
	$service	= "orcl.it.jrpnet";

	$db_oracle	= new oracle();
	$db_oracle->oracon($user_db,$pass_db,$host_db,$service) or die("Database oracle connection failled!!");
}

function Get_data() {
	global $db_oracle;

	$sql = "
	SELECT 
		to_char(B.TANGGAL_SERAH_TERIMA,'DD/MM/YYYY') AS TGL_PPJB,
		D.LOKASI AS KODE_SEKTOR,
		D.LOKASI AS KODE_CLUSTER,
		C.KODE_BLOK,
		C.LUAS_TANAH AS LUAS_KAVLING,
		C.LUAS_BANGUNAN,
		A.NAMA_PEMBELI AS NAMA_PELANGGAN,
		A.NO_IDENTITAS AS NO_KTP,
		A.NPWP,
		A.ALAMAT_RUMAH,
		A.ALAMAT_SURAT,
		A.TELP_RUMAH AS NO_TELEPON,
		A.TELP_LAIN AS NO_HP,
		E.KODE_BANK,
		E.NAMA_BANK
	FROM 
		SPP A, 
		SERAH_TERIMA B, 
		STOK C, 
		LOKASI D, 
		BANK E
	WHERE 
		A.KODE_BLOK=B.KODE_BLOK
		AND C.KODE_LOKASI=D.KODE_LOKASI
		AND A.KODE_BLOK=C.KODE_BLOK
		AND A.KODE_BANK=E.KODE_BANK
		AND B.TANGGAL_SERAH_TERIMA IS NOT NULL
		AND B.TANGGAL_SERAH_TERIMA >= to_date('01/03/2014','DD/MM/YYYY')
	";
	
	//echo $sql;
	$data = $db_oracle->oraselect($sql);
	
	return $data;
}

$data = Get_data();

for($l=0; $l<count($data); $l++){

	echo 
	$data[$l]['TGL_PPJB'].'|'.
	$data[$l]['KODE_SEKTOR'].'|'.
	$data[$l]['KODE_CLUSTER'].'|'.
	$data[$l]['KODE_BLOK'].'|'.
	$data[$l]['LUAS_KAVLING'].'|'.
	$data[$l]['LUAS_BANGUNAN'].'|'.
	$data[$l]['NAMA_PELANGGAN'].'|'.
	$data[$l]['NO_KTP'].'|'.
	$data[$l]['NPWP'].'|'.
	$data[$l]['ALAMAT_RUMAH'].'|'.
	$data[$l]['ALAMAT_SURAT'].'|'.
	$data[$l]['NO_TELEPON'].'|'.
	$data[$l]['NO_HP'].'|'.
	$data[$l]['KODE_BANK'].'|'.
	$data[$l]['NAMA_BANK'].'|:::|';

}


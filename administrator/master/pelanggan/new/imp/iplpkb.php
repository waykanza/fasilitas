<?php
include "oracle.php";
// DB ORACLE
if(empty($db_oracle)){
	$user_db	= "iplpkb";
	$pass_db	= "iplpkb";
	$host_db	= "localhost"; //192.168.30.18
	$service	= "jaya5";

	$db_oracle	= new oracle();
	$db_oracle->oracon($user_db,$pass_db,$host_db,$service) or die("Database oracle connection failled!!");
}

function Get_data(){
	global $db_oracle;

	$sql = "
		SELECT 
			TO_CHAR(C.IUAO_PPJB_DATE,'DD/MM/YYYY') AS TGL_PPJB,
			TO_CHAR(G.IUBP_START_PERIOD,'DD/MM/YYYY') AS PERIODE_AWAL_BUILD,
			TO_CHAR(G.IUBP_END_PERIOD,'DD/MM/YYYY') AS PERIODE_AKHIR_BUILD,
			A.IUC_CODE AS KODE_BLOK, 
			D.IUA_CODE AS KODE_BLOK2, 
			D.ID_PELANGGAN AS NO_PELANGGAN,
			E.IUCLS_CODE AS KODE_CLUSTER,
			E.IUCLS_NAME AS NAMA_CLUSTER,
			F.IUSEC_CODE AS KODE_SEKTOR,
			F.IUSEC_NAME AS NAMA_SEKTOR,
			G.IUBP_DESC AS NAMA_SEKTOR
		FROM 
			IPL_UNIT_CUSTOMER A, 
			CLASP_UNIT B, 
			IPL_UNIT_AREA_OCCUPIED C, 
			IPL_UNIT_AREA D, 
			IPL_UNIT_CLUSTER E, 
			IPL_UNIT_SECTOR F, 
			IPL_UNIT_BUILD_PERIOD G
		WHERE 
			A.IUC_UNIT_ID=B.CU_ID
			AND A.IUC_ID=C.IUAO_CUSTOMER_ID
			AND C.IUAO_AREA_ID=D.IUA_ID
			AND D.IUA_CLUSTER_ID=E.IUCLS_ID
			AND D.IUA_SECTOR_ID=F.IUSEC_ID
			AND C.IUAO_ID=G.IUBP_AREA_OCCUPIED_ID
			AND C.IUAO_PPJB_DATE >= TO_DATE('01/01/2013','DD/MM/YYYY')
		";

	//echo $sql;
	$data = $db_oracle->oraselect($sql);
	
	return $data;
}

$data = Get_data();

for($l=0; $l<count($data); $l++){

	echo 
	$data[$l]['TGL_PPJB'].'|'.
	$data[$l]['PERIODE_AWAL_BUILD'].'|'.
	$data[$l]['PERIODE_AKHIR_BUILD'].'|'.
	$data[$l]['KODE_BLOK'].'|'.
	$data[$l]['KODE_BLOK2'].'|'.
	$data[$l]['NO_PELANGGAN'].'|'.
	$data[$l]['KODE_CLUSTER'].'|'.
	$data[$l]['NAMA_CLUSTER'].'|'.
	$data[$l]['KODE_SEKTOR'].'|'.
	$data[$l]['NAMA_SEKTOR'].'|'.
	$data[$l]['NAMA_SEKTOR'].'|:::|';

}


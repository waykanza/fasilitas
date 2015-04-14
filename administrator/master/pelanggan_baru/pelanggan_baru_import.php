<?php
require_once('../../../config/config.php');
die_login();
die_mod('M14');
$conn = conn();
die_conn($conn);

$i = 1;
$i_success = 0;
$i_error = 0;

$file = fopen(ORCL . 'serah_terima.txt', 'r');

while ( ! feof($file))
{
	$line = trim(fgets($file));
	if ($line != '') {
		
		$exp = explode('|', $line);
		
		$kode_blok		= (isset($exp[0])) ? clean($exp[0]) : '';
		$kode_sektor	= (isset($exp[1])) ? clean($exp[1]) : '';
		$kode_cluster	= (isset($exp[2])) ? clean($exp[2]) : '';
		$luas_kavling	= (isset($exp[3])) ? to_decimal($exp[3]) : '0';
		$luas_bangunan	= (isset($exp[4])) ? to_decimal($exp[4]) : '0';
		
		$nama_pelanggan	= (isset($exp[5])) ? clean($exp[5]) : '';
		$no_ktp			= (isset($exp[6])) ? clean($exp[6]) : '';
		$npwp			= (isset($exp[7])) ? clean($exp[7]) : '';
		$alamat			= (isset($exp[8])) ? clean($exp[8]) : '';
		$no_telepon		= (isset($exp[9])) ? clean($exp[9]) : '';
		$no_hp			= (isset($exp[10])) ? clean($exp[10]) : '';
		
		$sm_nama_pelanggan	= $nama_pelanggan;
		$sm_no_ktp			= $no_ktp;
		$sm_npwp			= $npwp;
		$sm_alamat			= (isset($exp[11])) ? clean($exp[11]) : '';
		$sm_no_telepon		= (isset($exp[12])) ? clean($exp[12]) : '';
		$sm_no_hp			= (isset($exp[13])) ? clean($exp[13]) : '';
		
		$tgl_ppjb			= (isset($exp[14])) ? clean($exp[14]) : '';
		
		#$conn->Execute("DELETE FROM KWT_PELANGGAN WHERE KODE_BLOK = '$kode_blok'");
		#$conn->Execute("DELETE FROM KWT_PELANGGAN_IMP WHERE KODE_BLOK = '$kode_blok'");
		
		$obj = $conn->Execute("
		SELECT 
			(SELECT COUNT(KODE_BLOK) FROM KWT_PELANGGAN WHERE KODE_BLOK = '$kode_blok') AS FOUND_MASTER,
			(SELECT COUNT(KODE_BLOK) FROM KWT_PELANGGAN_IMP WHERE KODE_BLOK = '$kode_blok') AS FOUND_IMP
		");
		
		$found_master = $obj->fields['FOUND_MASTER'];
		$found_imp = $obj->fields['FOUND_IMP'];
		
		if ($kode_blok == '') {
			$i_error++;
			echo "<font color='red'>$i | Format line error.</font><br>";
		} else if ($found_master > 0) {
			$i_error++;
			echo "<font color='blue'>$i | Blok <b>$kode_blok</b> sudah terdaftar di master pelanggan.</font><br>";
		} else if ($found_imp > 0) {
			$i_error++;
			echo "<font color='red'>$i | Blok <b>$kode_blok</b> sudah terdaftar di master pelanggan baru.</font><br>";
		} else {
		
			$query = "INSERT INTO KWT_PELANGGAN_IMP 
			(
				NO_PELANGGAN,
				KODE_BLOK,
				KODE_SEKTOR,
				KODE_CLUSTER,
				LUAS_KAVLING,
				LUAS_BANGUNAN,
				
				NAMA_PELANGGAN,
				NO_KTP,
				NPWP,
				ALAMAT,
				NO_TELEPON,
				NO_HP,
				
				SM_NAMA_PELANGGAN,
				SM_NO_KTP,
				SM_NPWP,
				SM_ALAMAT,
				SM_NO_TELEPON,
				SM_NO_HP,
				
				STATUS_BLOK,
				TGL_PPJB, 
				
				USER_CREATED
			)
			VALUES
			(
				'$kode_blok',
				'$kode_blok',
				'$kode_sektor',
				'$kode_cluster',
				'$luas_kavling',
				'$luas_bangunan',
				
				'$nama_pelanggan',
				'$no_ktp',
				'$npwp',
				'$alamat',
				'$no_telepon',
				'$no_hp',
				
				'$sm_nama_pelanggan',
				'$sm_no_ktp',
				'$sm_npwp',
				'$sm_alamat',
				'$sm_no_telepon',
				'$sm_no_hp',
				
				'8',
				'$tgl_ppjb', 
				
				'$sess_id_user'
			)";
			
			if ( ! $conn->Execute($query)) {
				$i_error++;
				echo "<font color='blue'>$i | Blok <b>$kode_blok</b> error $query.</font><br>";
			} else {
				$i_success++;
				echo "<font color='blue'>$i | Blok <b>$kode_blok</b> berhasil ditambahkan.</font><br>";
			}
		}
		
		$i++;
	}
}
fclose($file);

$i--;
echo "<br><b>
Total data : $i Blok<br>
Total data sukses : $i_success Blok<br>
Total data error : $i_error Blok<br></b>
";

?>
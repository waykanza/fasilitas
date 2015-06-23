<?php
require_once('../../../config/config.php');
$conn = conn();
$bulan = $_GET['bln']; if($bulan==''){unset($bulan);}
$tahun = $_GET['thn'];	if($tahun==''){unset($tahun);}
$bulan2 = $_GET['bln2']; if($bulan2==''){unset($bulan2);}
$tahun2 = $_GET['thn2']; if($tahun2==''){unset($tahun2);}
$sektor= urldecode($_GET['sektor']); if($sektor==''){unset($sektor);}
$status= urldecode($_GET['status_bayar']); if($status==''){unset($status);}
$no_va = urldecode($_GET['no_va']); if($no_va==''){unset($no_va);}
$nama  = urldecode($_GET['nama']); if($nama==''){unset($nama);}
$kode_blok= urldecode($_GET['kode_blok']); if($kode_blok==''){unset($kode_blok);}
$jenis_usaha = urldecode($_GET['jenis_usaha']); if($jenis_usaha==''){unset($jenis_usaha);}
$query = "SELECT * FROM PELANGGAN_MP a	
				LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
				LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE";
if((isset($bulan)&&isset($tahun))||isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_usaha)){
	$query .= " where ";
	if((isset($bulan)&&isset($tahun))&&(isset($bulan2)&&isset($tahun2))) {
		$query	.=" MONTH(PERIODE_AWAL) >= ".$bulan."  AND YEAR(PERIODE_AWAL) >= ".$tahun." AND MONTH(PERIODE_AWAL) <= ".$bulan2."  AND YEAR(PERIODE_AWAL) <= ".$tahun2;
		if(isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	else if((isset($bulan)&&isset($tahun))){
		$query	.=" MONTH(PERIODE_AWAL) = ".$bulan."  AND YEAR(PERIODE_AWAL) = ".$tahun;
		if(isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($sektor)){
		$query .=" KODE_SEKTOR like '%$sektor%'";
		if(isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($status)){
		$query .=" STATUS_BAYAR = $status";
		if(isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($no_va)){
		$query .=" NO_PELANGGAN like '%$no_va%'";
		if(isset($nama)||isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($nama)){
		$query .=" NAMA_PELANGGAN like '%$nama%'";
		if(isset($kode_blok)||isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($kode_blok)){
		$query .=" KODE_BLOK like '%$kode_blok%'";
		if(isset($jenis_usaha))
			$query .= " AND ";
	}

	if(isset($jenis_usaha)){
		$query .= " JENIS_USAHA = '$jenis_usaha'";
	}
}
echo $query;
?>

<head>
	<title>Print Laporan Media Promosi</title>
	<style type="text/css">
	@page {
			size: A4;
			margin: 30px 15px 0px 15px;
		}
	@media screen, print{
			body{
				font-family: 'Arial';
				text-align: left;
			}
			.table_header{
				padding: 5px;
				border: 1px solid;
			}
			.isi_table{
				border: 1px solid;
				padding: 5px;
			}
		}
	</style>
</head>
<body onload="window.print()">
	<table cellspacing="0">
		<tr>
			<td class="table_header">NO</td>
			<td class="table_header">NAMA PELANGGAN</td>
			<td class="table_header">JENIS</td>
			<td class="table_header">KATEGORI</td>
			<td class="table_header">CLUSTER</td>
			<td class="table_header">KODE BLOK</td>
			<td class="table_header">HARGA SEWA</td>
		</tr>
		<?php 
		$no = 1;
		while (!$data->EOF) {
			
		?>
		<tr>
			<td class="isi_table"><?php echo $no;?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_PELANGGAN']; ?></td>
			<td class="isi_table"><?php echo $data->fields['KODE_MP']; ?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_TIPE']; ?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_LOKASI']; ?></td>
			<td class="isi_table"><?php echo $data->fields['KODE_BLOK']; ?></td>
			<td class="isi_table"><?php echo $data->fields['TOTAL_BAYAR']; ?></td>
		</tr>
		<?php 
		$data->movenext();
		}
	
		?>
		</table>
</body>
</html>
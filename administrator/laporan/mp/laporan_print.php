<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
$data_terbilang = new Terbilang;
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
$jenis_sewa = urldecode($_GET['jenis_sewa']); if($jenis_sewa==''){unset($jenis_sewa);}
$query = "SELECT * FROM PELANGGAN_MP a	
				LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
				LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE";
if((isset($bulan)&&isset($tahun))||isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa)){
	$query .= " where ";
	if((isset($bulan)&&isset($tahun))&&(isset($bulan2)&&isset($tahun2))) {
		$bln = intval($bulan);
		$kata_bln = $data_terbilang->nama_bln($bln);
		$bln2 = intval($bulan2);
		$kata_bln2 = $data_terbilang->nama_bln($bln2);
		$query	.=" MONTH(PERIODE_AWAL) >= ".$bulan."  AND YEAR(PERIODE_AWAL) >= ".$tahun." AND MONTH(PERIODE_AWAL) <= ".$bulan2."  AND YEAR(PERIODE_AWAL) <= ".$tahun2;
		$kata_tanggal = "Bulan  $kata_bln $tahun s/d $kata_bln2 $tahun2";
		if(isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	else if((isset($bulan)&&isset($tahun))){
		$query	.=" MONTH(PERIODE_AWAL) = ".$bulan."  AND YEAR(PERIODE_AWAL) = ".$tahun;
		$bln = intval($bulan);
		$kata_bln = $data_terbilang->nama_bln($bln);
		$kata_tanggal = "Bulan $kata_bln $tahun";
		
		if(isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($sektor)){
		$query .=" KODE_SEKTOR like '%$sektor%'";
		if(isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($status)){
		$query .=" STATUS_BAYAR = $status";
		if(isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($no_va)){
		$query .=" NO_PELANGGAN like '%$no_va%'";
		if(isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($nama)){
		$query .=" NAMA_PELANGGAN like '%$nama%'";
		if(isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($kode_blok)){
		$query .=" KODE_BLOK like '%$kode_blok%'";
		if(isset($jenis_sewa))
			$query .= " AND ";
	}

	if(isset($jenis_sewa)){
		$query .= " KODE_MP = '$jenis_sewa'";
	}
}
$data = $conn->Execute($query);
if(isset($status)){
	if($status == '2'){
		$kata =	"Realisasi";
	}
	else if($status == '0'){
		$kata = "Piutang";
	}
}
else {
	$kata = "Rencana";
}
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
				font-size: 11px;
				text-align: left;
			}
			.table_header{
				background-color: #abcdef;
				font-size: 11px;
				padding: 5px;
				border: 1px solid;
			}
			.isi_table{
				font-size: 11px;
				border: 1px solid;
				padding: 5px;
			}
			.uang{
				text-align: right;
			}
		}
	</style>
</head>
<body onload="window.print()">
	<center><h1>Laporan Data <?php echo $kata;?> Media Promosi<br><?php echo $kata_tanggal;?></h1></center>
	<table cellspacing="0">
		<tr>
			<td class="table_header">No</td>
			<td class="table_header">Nama Pelanggan</td>
			<td class="table_header">Jenis</td>
			<td class="table_header">Kategori</td>
			<td class="table_header">Cluster</td>
			<td class="table_header">Blok-No</td>
			<td class="table_header">Harga Sewa</td>
		</tr>
		<?php 
		$no = 1;
		$total = 0;
		while (!$data->EOF) {
			if($data->fields['KODE_MP']=='A'){
				$mp = "Billboard / Sign Board / Pylon Sign (A) ";
			}
			else if($data->fields['KODE_MP']=='B'){
				$mp = "Neon Box / Neon Sign (B) ";
			}
			else if($data->fields['KODE_MP']=='C'){
				$mp = "Spanduk / Umbul-Umbul / Standing Display (C)";
			}
			else if($data->fields['KODE_MP']=='D'){
				$mp = "Banner / Baliho (D) ";
			}

		?>
		<tr>
			<td class="isi_table"><?php echo $no;?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_PELANGGAN']; ?></td>
			<td class="isi_table"><?php echo $mp; ?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_TIPE']; ?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_LOKASI']; ?></td>
			<td class="isi_table"><?php echo $data->fields['KODE_BLOK']; ?></td>
			<td class="isi_table uang"><?php echo to_money($data->fields['TOTAL_BAYAR']); ?></td>
		</tr>
		<?php 
		$total += $data->fields['TOTAL_BAYAR'];
		$data->movenext();
		}
		?>
		<tr>
			<td class = "isi_table" colspan = "6">Total</td>
			<td class = "isi_table uang">Rp. <?php echo to_money($total);?></td>
		<?php
	
		?>
		</table>
</body>
</html>
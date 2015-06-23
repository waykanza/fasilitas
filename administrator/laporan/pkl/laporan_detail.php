<?php
require_once('../../../config/config.php');
error_reporting(0);
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
$query = "SELECT a.NAMA_PELANGGAN,d.DETAIL_LOKASI,a.NO_PELANGGAN,a.KODE_BLOK,c.NAMA_TIPE AS KATEGORI,MONTH(a.PERIODE_AWAL) as periode_ke,a.TOTAL_BAYAR,a.STATUS_BAYAR FROM PELANGGAN_PKL a 
LEFT JOIN KWT_LOKASI_PKL d ON a.KODE_LOKASI = d.KODE_LOKASI

LEFT JOIN  KWT_TARIF_PKL b ON a.KEY_PKL = b.KEY_PKL

LEFT JOIN KWT_TIPE_PKL c ON b.KODE_TIPE = c.KODE_TIPE

";
if((isset($bulan)&&isset($tahun))||isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa)){
	$query .= " where ";
	if((isset($bulan)&&isset($tahun))&&(isset($bulan2)&&isset($tahun2))) {
		$query	.=" MONTH(PERIODE_AWAL) >= ".$bulan."  AND YEAR(PERIODE_AWAL) >= ".$tahun." AND MONTH(PERIODE_AWAL) <= ".$bulan2."  AND YEAR(PERIODE_AWAL) <= ".$tahun2;
		if(isset($sektor)||isset($status)||isset($no_va)||isset($nama)||isset($kode_blok)||isset($jenis_sewa))
			$query .= " AND ";
	}

	else if((isset($bulan)&&isset($tahun))){
		$query	.=" MONTH(PERIODE_AWAL) = ".$bulan."  AND YEAR(PERIODE_AWAL) = ".$tahun;
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

}
$query.="ORDER BY a.KODE_LOKASI ASC, a.ID_PEMBAYARAN";
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
			size: A3 Landscape;
			margin: 30px 15px 0px 15px;
		}
	@media screen, print{
			body{
				font-family: 'Arial';
				text-align: left;
				font-size: 10px;
			}
			.table_header{
				background-color: #abcdef;
				font-size: 10px;
				padding: 5px;
				border: 1px solid;
			}
			.isi_table{

				font-size: 10px;
				border: 1px solid;
				padding: 5px;
			}
			.uang{
				text-align: right;
			}

		}

	</style>
</head>
<body onload="">
	<?php 


	?>
	<center><h1>Laporan Data <?php echo $kata;?> Pedagang Kaki Lima</h1></center>
	<table cellspacing="0">
		<tr>
			<td class="table_header">No</td>
			<td class="table_header">Nama Pelanggan</td>
			<td class="table_header">Lokasi Dagang</td>
			<td class="table_header">No Virtual</td>
			<td class="table_header">Blok-No</td>
			<td class="table_header">Jenis Usaha</td>
			<td class="table_header">Januari</td>
			<td class="table_header">Februari</td>
			<td class="table_header">Maret</td>
			<td class="table_header">April</td>
			<td class="table_header">Mei</td>
			<td class="table_header">Juni</td>
			<td class="table_header">Juli</td>
			<td class="table_header">Agustus</td>
			<td class="table_header">September</td>
			<td class="table_header">Oktober</td>
			<td class="table_header">November</td>
			<td class="table_header">Desember</td>
			<td class="table_header">TOTAL</td>

		</tr>
		<?php 
		$no = 1;
		$data_sebelumnya = "0";
		$wilayah_sebelumnya = "0";
		$total_wilayah = 0;
		for($a=0;$a<12;$a++){
				$total[$a] = 0;
				}
		for($a=0;$a<12;$a++){
				$pembayaran[$a] = '';
				}
		while (!$data->EOF) {
			if($wilayah_sebelumnya!= $data->fields['DETAIL_LOKASI']){
				$total_wilayah = 0;
				$wilayah_sebelumnya = $data->fields['DETAIL_LOKASI'];
				?>
				<tr>
					<td class="isi_table" colspan="19">Lokasi : <?php echo $wilayah_sebelumnya;?></td>
				</tr>
				<?php
			}
			if($data_sebelumnya!=$data->fields['NO_PELANGGAN']){
				$data_pembeli['no'] = $no;
				$data_pembeli['NAMA_PELANGGAN'] = $data->fields['NAMA_PELANGGAN'];
				$data_pembeli['LOKASI'] = $data->fields['DETAIL_LOKASI'];
				$data_pembeli['NO_PELANGGAN'] = $data->fields['NO_PELANGGAN'];
				$data_pembeli['KODE_BLOK'] = $data->fields['KODE_BLOK'];
				$data_pembeli['JENIS_USAHA'] = $data->fields['KATEGORI'];
				$pembayaran[$data->fields['periode_ke']-1] = $data->fields['TOTAL_BAYAR'];
				$data_sebelumnya = $data_pembeli['NO_PELANGGAN'];
				$no++;
				?>
				<?php
			}
			
			if ($data_sebelumnya==$data->fields['NO_PELANGGAN']){
				$pembayaran[$data->fields['periode_ke']-1] = $data->fields['TOTAL_BAYAR'];
			}

		$data->movenext();
		if($data->fields['NO_PELANGGAN']!=$data_sebelumnya){
			
			?>
				<tr>
				<td class="isi_table"><?php echo $data_pembeli['no'];?></td>
				<td class="isi_table"><?php echo $data_pembeli['NAMA_PELANGGAN'];?></td>
				<td class="isi_table"><?php echo $data_pembeli['LOKASI'];?></td>
				<td class="isi_table"><?php echo $data_pembeli['NO_PELANGGAN'];?></td>
				<td class="isi_table"><?php echo $data_pembeli['KODE_BLOK'];?></td>
				<td class="isi_table"><?php echo $data_pembeli['JENIS_USAHA'];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[0]);$total[0] += $pembayaran[0];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[1]);$total[1] += $pembayaran[1];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[2]);$total[2] += $pembayaran[2];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[3]);$total[3] += $pembayaran[3];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[4]);$total[4] += $pembayaran[4];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[5]);$total[5] += $pembayaran[5];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[6]);$total[6] += $pembayaran[6];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[7]);$total[7] += $pembayaran[7];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[8]);$total[8] += $pembayaran[8];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[9]);$total[9] += $pembayaran[9];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[10]);$total[10] += $pembayaran[10];?></td>
				<td class="isi_table uang"><?php echo to_money($pembayaran[11]);$total[11] += $pembayaran[11];?></td>
			<?php
			$total_perorang = 0;
			for($a=0;$a<12;$a++){
				$total_perorang +=$pembayaran[$a]; 
				$pembayaran[$a] = '';
				}	
			$total_wilayah += $total_perorang;
			?>
				<td class="isi_table uang"><?php echo to_money($total_perorang);?></td>
			<?php

			}
			if($wilayah_sebelumnya!= $data->fields['DETAIL_LOKASI']){
				?>
				<tr>
					<td class="isi_table" colspan="18">TOTAL Per Wilayah <td class = "isi_table uang">Rp. <?php echo to_money($total_wilayah);?></td>
				</tr>
				<?php
			}
		
		}
	
		?>
			<tr>
				<td colspan="6" class="isi_table"> TOTAL </td>
				<td class="isi_table uang"><?php echo to_money($total[0]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[1]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[2]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[3]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[4]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[5]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[6]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[7]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[8]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[9]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[10]);?></td>
				<td class="isi_table uang"><?php echo to_money($total[11]);?></td>
				<?php
				$total_seluruh = 0;
				for($a=0;$a<12;$a++){
					$total_seluruh +=$total[$a]; 
				}
				?>
				<td class="isi_table uang">Rp. <?php echo to_money($total_seluruh);?></td>
		</table>
</body>
</html>
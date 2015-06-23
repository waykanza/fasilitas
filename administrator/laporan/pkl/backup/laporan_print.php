<?php
require_once('../../../config/config.php');
$conn = conn();
$bulan = $_GET['bln'];
$tahun = $_GET['thn'];
if(isset($bulan)&&isset($tahun)){
	$query = "SELECT *
	FROM PELANGGAN_PKL a left join
	KWT_LOKASI_PKL b on a.KODE_LOKASI = b.KODE_LOKASI
	ORDER BY a.NO_PELANGGAN DESC
	";
	$data = $conn->Execute($query);
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
			<td class="table_header">NAMA PEDAGANG</td>
			<td class="table_header">LOKASI DAGANG</td>
			<td class="table_header">NO VA</td>
			<td class="table_header">BLOK-NO</td>
			<td class="table_header">JENIS USAHA</td>
			<td class="table_header">TARIF</td>
		</tr>
		<?php 
		$no = 1;
		while (!$data->EOF) {
			
		?>
		<tr>
			<td class="isi_table"><?php echo $no;?></td>
			<td class="isi_table"><?php echo $data->fields['NAMA_PELANGGAN']; ?></td>
			<td class="isi_table"><?php echo $data->fields['DETAIL_LOKASI']; ?></td>
			<td class="isi_table"><?php echo $data->fields['NO_PELANGGAN']; ?></td>
			<td class="isi_table"><?php echo $data->fields['KODE_BLOK']; ?></td>
			<?php 
				$tipe = explode('-', $data->fields['KEY_PKL']);
				$query ="SELECT * FROM " ;
			?>
			<td class="isi_table"><?php echo $data->fields['']; ?>ASDAS</td>
			<td class="isi_table"><?php echo $data->fields['TOTAL_BAYAR']; ?></td>
		</tr>
		<?php 
		$data->movenext();
		}
	}
		?>
		</table>
</body>
</html>
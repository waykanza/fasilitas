<?php
require_once('../../../config/config.php');
require_once('../../../config/Terbilang.php');
$conn = conn();
$id_pembayaran = $_GET['id_pembayaran'];
if(isset($id_pembayaran)){
	$query = "SELECT * FROM PELANGGAN_PKL WHERE ID_PEMBAYARAN = '$id_pembayaran'";
	$obj = $conn->Execute($query);
	while(!$obj->EOF)
	{
		?>
		<html>
		<head>
			<title>Print spp</title>
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
			#invoice_mp{
				height: 100%;
				width: 100%;
			}
			.separator{
				width: 100%;
				height: 5px;
			}
			.header,.ttd{
				text-align: center;
			}
			#t_terbilang{
				text-align: left;
				margin-left: 10px;
			}
			.tabel_pelanggan,#t_norek,#t_keterangan{
				text-align: left;
			}
		}
			</style>
		</head>
		<body onload="windows.print()">
			<div id = "invoice_mp">
				<div class ='header'><b>PT. JAYA REAL PROPERTY, Tbk.</b></div>
				<div class ='header'>UNIT PENGELOLA KAWASAN BINTARO</center>
				<hr/>
				<div class="separator">&nbsp;</div>
				<div class ='header'>INVOICE</div>
				<div class ='header'>No.        /JRP/PKB-FAS/INVOICE/IV/2015</div>
				<div class="separator">&nbsp;</div>
				<table class = "tabel_pelanggan">
					<tr>
						<td>Materi</td>
						<td>:</td>
						<td>Sewa Lokasi Pedagang Kaki Lima</td>
					</tr>
					<tr>
						<td>Periode</td>
						<td>:</td>
						<td><?php echo $obj->fields['PERIODE_AWAL']; ?> s/d <?php echo $obj->fields['PERIODE_AKHIR'];?></td>
					</tr>
					<tr>
						<td>Nama Penyewa</td>
						<td>:</td>
						<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
					</tr>
					<tr>
						<td>Alamat Penyewa</td>
						<td>:</td>
						<td><?php echo $obj->fields['ALAMAT']; ?></td>
					</tr>
					<tr>
						<td>No Kwitansi</td>
						<td>:</td>
						<td></td>
					</tr>
					<tr>
						<td>Tanggal Jatuh Tempo</td>
						<td>:</td>
						<td></td>
					</tr>
				</table>
				<div class="separator"></div>
				<table border="1" cellspacing="0" width="100%">
					<tr>
						<td width="5%">No</td>
						<td width="50%">Keterangan</td>
						<td width="45%">Total</td>
					</tr>
					<tr>
						<td>1</td>
						<td>Sewa Lokasi Pedagang Kaki Lima</td>
						<td> = Rp <?php echo $obj->fields['TOTAL']; ?></td>
					</tr>
					<tr>
						<td></td>
						<td><div id = 't_sub_total'>Sub Total </div><div id = 't_ppn'>PPN 10%</div><div id = 't_total'>Total</div></td>
						<td><div id = 'sub_total'> = Rp <?php echo $obj->fields['TOTAL'];?></div><div id = 'ppn'>= Rp <?php echo $obj->fields['NILAI_PPN'];?></div><div id = 'total'> = Rp <?php echo $obj->fields['TOTAL_BAYAR'];?></div></td>
					</tr>
				<tr>
					<td colspan = '3'>
						<div id="separator">&nbsp;</div>
						<div id = "t_terbilang" >Terbilang : <?php $bilangan = new Terbilang; echo $bilangan -> eja($obj->fields['TOTAL_BAYAR']);?></div>	
						<div id="separator">&nbsp;</div>
					</td>
				</tr>
				<tr>
					<td colspan = '3'>
						<div id="separator">&nbsp;</div>
						<div id = "t_norek" ><u><b>Pembayaran harus sesuai jumlah tersebut diatas </b></u> dan harap ditransfer ke Bank Negara Indonesia 1946 Capem Pasar Modern BTC Sektor 7 - Bintaro Jaya dengan No. Rekening : <b>166-88-99997</b> a/n PT. JAYA REAL PROPERTY, Tbk												
						</div>	
						<div id="separator">&nbsp;</div>
					</td>
				</tr>
				</table>
				<table id="t_keterangan" width="100%">
					<tr>
						<td colspan="2" width="60%"  >Keterangan :</td>
						<td width="40%" class= 'ttd'>Tangerang, Tanggal<br/>PT. JAYA REAL PROPERTY, Tbk.</td>
					</tr>
					<tr>
						<td valign="top">1.</td>
						<td>Invoice ini bukan merupakan bukti pembayaran yang sah.</td>
					</tr>
					<tr>
						<td valign="top">2.</td>
						<td>Kwitansi asli merupakan bukti pembayaran yang sah setelah dana diterima efektif di rekening PT. JAYA REAL PROPERTY, Tbk.</td>
					</tr>
					<tr>
						<td valign="top">3.</td>
						<td>Keterlambatan pembayaran akan dikenakan denda sesuai dengan peraturan yang berlaku.</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td class= 'ttd'><u><b>Winarso</b></u><br/>Manager Fasilitas</td>
					</tr>
				</table>
			</div>
		</body>
		</html>
 		<?php
	
		$obj->movenext();
	}

}
else{

}
close($conn);
exit;
?>
<!DOCTYPE html>
<?php
require_once('../../../config/config.php');
require_once('../../../config/Terbilang.php');
$conn = conn();
$id_pembayaran = $_GET['id_pembayaran'];
if(isset($id_pembayaran)){
	$query = "IF (SELECT NO_KWITANSI FROM PELANGGAN_PKL WHERE ID_PEMBAYARAN = $id_pembayaran) IS NULL BEGIN
			UPDATE PELANGGAN_PKL SET NO_KWITANSI = (SELECT CASE WHEN (SELECT MAX(NO_KWITANSI) FROM PELANGGAN_PKL) IS NULL THEN '1' ELSE (SELECT NO_KWITANSI = MAX(CASE WHEN NO_KWITANSI IS NULL THEN 0 ELSE NO_KWITANSI END)+1 FROM PELANGGAN_PKL)end)
			WHERE ID_PEMBAYARAN = $id_pembayaran END";
	$conn->Execute($query);
	$query = "SELECT * FROM PELANGGAN_PKL WHERE ID_PEMBAYARAN = '$id_pembayaran'";
	$obj = $conn->Execute($query);
	$query = "SELECT INVOICE_MP, MANAGER_FAS FROM KWT_PARAMETER";
	$param = $conn->Execute($query);
	while(!$param->EOF){
		$manager = $param->fields['MANAGER_FAS'];
		$no_invoice = $param->fields['INVOICE_MP'];
		$param->movenext();
	}
	while(!$obj->EOF)
	{
		$tanggal = explode('-', $obj->fields['PERIODE_AWAL']);
		$bilangan = new Terbilang;

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
				padding: 5px;
			}
		}
			</style>
		</head>
		<body onload="window.print()">
			<div id = "invoice_mp">
				<div class ='header'><b>PT. JAYA REAL PROPERTY, Tbk.</b></div>
				<div class ='header'>UNIT PENGELOLA KAWASAN BINTARO</center>
				<hr/>
				<div class="separator">&nbsp;</div>
				<div class ='header'>INVOICE</div>
				<div class ='header'>No.  <?php echo $obj->fields['NO_KWITANSI'];echo $no_invoice; echo $bilangan->romawi($tanggal[1]);?>/<?php echo $tanggal[0]; ?></div>
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
						<td><?php echo date("d F Y", strtotime($obj->fields['PERIODE_AWAL'])); ?> s/d <?php echo date("d F Y", strtotime($obj->fields['PERIODE_AKHIR']));?></td>
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
						<td><?php echo $obj->fields['NO_KWITANSI']; ?></td>
					</tr>
					<tr>
						<td>Tanggal Jatuh Tempo</td>
						<td>:</td>
						<td>25-<?php echo $tanggal[1].'-'.$tanggal[0];?></td>
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
						<div id = "t_terbilang" >Terbilang : <?php echo $bilangan -> eja($obj->fields['TOTAL_BAYAR']);?></div>	
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
						<td width="40%" class= 'ttd'>Tangerang, <?php echo date("d F Y", strtotime($obj->fields['PERIODE_AWAL'])); ?><br/>PT. JAYA REAL PROPERTY, Tbk.</td>
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
						<td class= 'ttd'><u><b><?php echo $manager; ?></b></u><br/>Manager Fasilitas</td>
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
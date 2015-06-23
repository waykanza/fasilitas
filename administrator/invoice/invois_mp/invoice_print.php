<?php
require_once('../../../config/config.php');
require_once('../../../config/Terbilang.php');
$conn = conn();
$id_pembayaran = $_GET['id_pembayaran'];
if(isset($id_pembayaran)){
	$query = "IF (SELECT NO_KWITANSI FROM PELANGGAN_MP WHERE ID_PEMBAYARAN = $id_pembayaran) IS NULL BEGIN
			UPDATE PELANGGAN_MP SET NO_KWITANSI = (SELECT CASE WHEN (SELECT MAX(NO_KWITANSI) FROM PELANGGAN_MP) IS NULL THEN '1' ELSE (SELECT NO_KWITANSI = MAX(CASE WHEN NO_KWITANSI IS NULL THEN 0 ELSE NO_KWITANSI END)+1 FROM PELANGGAN_MP)end)
			WHERE ID_PEMBAYARAN = $id_pembayaran END";
	$conn->Execute($query);
	$query = "SELECT * FROM PELANGGAN_MP WHERE ID_PEMBAYARAN = '$id_pembayaran'";
	$obj = $conn->Execute($query);
	$query = "SELECT INVOICE_MP, MANAGER_FAS, BANK, NO_REKENING FROM KWT_PARAMETER";
	$param = $conn->Execute($query);
	while(!$param->EOF){
		$manager = $param->fields['MANAGER_FAS'];
		$no_invoice = $param->fields['INVOICE_MP'];
		$bank = $param->fields['BANK'];
		$no_rekening = $param->fields['NO_REKENING'];
		$param->movenext();
	}
	while(!$obj->EOF)
	{
		if ($obj->fields['KODE_MP'] == 'A') {
			$kode_mp = 'BILLBOARD / SIGN BOARD / PLAY SIGN';
		} else if ($obj->fields['KODE_MP'] == 'B') {
			$kode_mp = 'NEON BOX / NEON SIGN';
		} else if ($obj->fields['KODE_MP'] == 'C') {
			$kode_mp = 'SPANDUK / UMBUL-UMBUL / STANDING DISPLAY';
		} else {
			$kode_mp = 'BANNER / BALIHO';
		}
		$tanggal = explode('-', $obj->fields['PERIODE_AWAL']);
		$bilangan = new Terbilang;
		$date = date('d/n/Y',time());
		$date = explode('/', $date);
		?>
		<html>
		<head>
			<title>Print Invoice Media Promosi</title>
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
			.right{
				margin-right: 10px;
				text-align: right;
			}
			.tabel_harga{
				padding: 10px;
			}
			.detail_harga{
				margin-top: 10px;
				text-align: right;
				margin-right: 10px;
			}
			.no_harga{
				margin-top: 10px;
				margin-left: 10px;	
			}
			.ket_harga{
				margin-top: 10px;
				margin-left: 10px;
			}
			.margin_bot{
				margin-bottom: 10px;
			}
			.ket_{
				margin-left: 10px;

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
						<td>Sewa Lokasi Penempatan <?php echo $kode_mp;?></td>
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
						<td>Kode Blok</td>
						<td>:</td>
						<td><?php echo $obj->fields['KODE_BLOK']; ?></td>
					</tr>
					<tr>
						<td>NO VA</td>
						<td>:</td>
						<td><?php echo $obj->fields['NO_PELANGGAN']; ?></td>
					</tr>
					<!--
					<tr>
						<td>No Kwitansi</td>
						<td>:</td>
						<td><?php echo $obj->fields['NO_KWITANSI']; ?></td>
					</tr>
					-->
					<tr>
						<td>Tanggal Jatuh Tempo</td>
						<td>:</td>
						<td>25 <?php echo $bilangan->nama_bln($tanggal[1]).' '.$tanggal[0];?></td>
					</tr>
				</table>
				<div class="separator"></div>
				<table border="1" cellspacing="0" width="100%" >
					<tr >
						<td width="5%" class="tabel_harga">No</td>
						<td width="50%" align="center">Keterangan</td>
						<td width="45%" align="center">Total</td>
					</tr>
					<tr>
						<td class="tabel_harga">1</td>
						<td><div class="ket_harga">Sewa Lokasi Penempatan <?php echo $kode_mp;?></div><br/><div class = "ket_ margin_bot">Periode : <?php echo date("d F Y", strtotime($obj->fields['PERIODE_AWAL'])); ?> s/d <?php echo date("d F Y", strtotime($obj->fields['PERIODE_AKHIR']));?><br/>Dilokasi : </div></div></td>
						<td><div class="ket_harga"> = Rp <?php echo number_format($obj->fields['TOTAL']); ?></div><br/><div class="ket_ margin_bot">&nbsp;<br/>&nbsp;</div></td>
					</tr>
					<tr class="tabel_harga">
						<td>&nbsp;</td>
						<td><div id = 't_sub_total' class="detail_harga right">Sub Total </div><div id = 't_ppn' class="detail_harga right">PPN 10%</div><div id = 't_total' class="detail_harga margin_bot right">Total</div></td>
						<td><div id = 'sub_total' class="no_harga right" > = Rp <?php echo number_format($obj->fields['TOTAL']);?></div><div id = 'ppn' class="no_harga right">= Rp <?php echo number_format($obj->fields['NILAI_PPN']);?></div><div id = 'total' class="no_harga margin_bot right"> = Rp <?php echo number_format($obj->fields['TOTAL_BAYAR']);?></div></td>
					</tr>
				<tr>
					<td colspan = '3' class="tabel_harga">
						<div id="separator">&nbsp;</div>
						<div id = "t_terbilang" >Terbilang : <b><?php echo strtoupper($bilangan -> eja($obj->fields['TOTAL_BAYAR']));?> RUPIAH</b></div>	
						<div id="separator">&nbsp;</div>
					</td>
				</tr>
				<tr>
					<td colspan = '3'>
						<div id="separator">&nbsp;</div>
						<div id = "t_norek" ><u><b>Pembayaran harus sesuai jumlah tersebut diatas </b></u> dan harap ditransfer ke <?php echo $bank;?> 1946 Capem Pasar Modern BTC Sektor 7 - Bintaro Jaya dengan No. Rekening : <b><?php echo $no_rekening;?></b> a/n PT. JAYA REAL PROPERTY, Tbk												
						</div>	
						<div id="separator">&nbsp;</div>
					</td>
				</tr>
				</table>
				<table id="t_keterangan" width="100%">
					<tr>
						<td colspan="2" width="60%"  >Keterangan :</td>
						<td width="40%" class= 'ttd'>Tangerang, <?php echo $date[0].' '.$bilangan->nama_bln($date[1]).' '.$date[2]  ?><br/><b>PT. JAYA REAL PROPERTY, Tbk.</b></td>
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
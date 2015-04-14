<?php
require_once('../../../../config/config.php');
require_once('../../../../config/terbilang.php');
$conn = conn();

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../../config/css/print.css" rel="stylesheet">
<!-- JS -->
<script type="text/javascript" src="../../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script>
$(function() {
	//window.print();
	//window.close();
});
</script>
</head>

<body style="width:500px; margin:5px;">
<?php
$terbilang = new Terbilang;
$id_pembayaran	= (isset($_REQUEST['id_pembayaran'])) ? clean($_REQUEST['id_pembayaran']) : '';

$query = "
SELECT
	p.*,
	CONVERT(VARCHAR(11),p.TGL_SERAHTERIMA,106) AS TGL_SERAHTERIMA,
	CONVERT(VARCHAR(11),p.TGL_PEMUTUSAN,106) AS TGL_PEMUTUSAN,
	l.TIPE_LOKASI,
	l.DETAIL_LOKASI,
	t.NAMA_TIPE,
	b.NAMA_BANK
FROM 
	KWT_PEMBAYARAN_KSP p
	LEFT JOIN KWT_LOKASI_KSP l ON p.KODE_LOKASI = l.KODE_LOKASI
	LEFT JOIN KWT_TIPE_KSP t ON p.KODE_TIPE = t.KODE_TIPE
	LEFT JOIN KWT_BANK b ON p.KODE_BANK = b.KODE_BANK
WHERE ID_PEMBAYARAN = '$id_pembayaran'";
$obj = $conn->Execute($query);

?>
<table class="kwitansi">
<tr>
	<td width="100">No. Bukti</td><td>:</td>
	<td><?php echo $obj->fields['NO_KWITANSI']; ?></td>
</tr>
<tr>
	<td>Tanggal Cetak</td><td>:</td>
	<td><?php echo date('d M Y h:i:s'); ?></td>
</tr>
<tr>
	<td>Kasir</td><td>:</td>
	<td><?php echo $obj->fields['KASIR']; ?></td>
</tr>
<tr>
	<td>Jenis Pembayaran</td><td>:</td>
	<td><?php echo jenis_bayar($obj->fields['JENIS_BAYAR']); ?></td>
</tr>
<tr>
	<td>Bank</td><td>:</td>
	<td><?php echo $obj->fields['NAMA_BANK']; ?></td>
</tr>
<tr>
	<td>No. Rekening</td><td>:</td>
	<td><?php echo $obj->fields['NO_REKENING']; ?></td>
</tr>

<tr>
	<td>Nama Pelanggan</td><td>:</td>
	<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
</tr>
<tr>
	<td>No. KTP</td><td>:</td>
	<td><?php echo $obj->fields['NO_KTP']; ?></td>
</tr>
<tr>
	<td>Lokasi</td><td>:</td>
	<td><?php echo tipe_lokasi($obj->fields['TIPE_LOKASI']); ?></td>
</tr>
<tr>
	<td></td><td></td>
	<td><?php echo $obj->fields['DETAIL_LOKASI']; ?></td>
</tr>
<tr>
	<td>Kategori</td><td>:</td>
	<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
</tr>
<tr>
	<td>Tarif</td><td>:</td>
	<td>Rp. <?php echo to_money($obj->fields['TARIF']); ?> / Hari</td>
</tr>
<tr>
	<td>Durasi</td><td>:</td>
	<td><?php echo to_money($obj->fields['DURASI']); ?> Hari</td>
</tr>
<tr>
	<td>Tanggal Serahterima</td><td>:</td>
	<td><?php echo $obj->fields['TGL_SERAHTERIMA']; ?></td>
</tr>
<tr>
	<td>Tanggal Pemutusan</td><td>:</td>
	<td><?php echo $obj->fields['TGL_PEMUTUSAN']; ?></td>
</tr>
<tr>
	<td>Keterangan</td><td>:</td>
	<td><?php echo $obj->fields['KETERANGAN']; ?></td>
</tr>
</table>

<br>

<table class="kwitansi t-border">
<tr>
	<th>Keterangan</th>
	<th>Nilai</th>
</tr>
<tr>
	<td>Durasi * Tarif</td>
	<td align="right">Rp. <?php echo to_money($obj->fields['DURASI'] * $obj->fields['TARIF']); ?></td>
</tr>
<tr>
	<td>Administrasi</td>
	<td align="right">Rp. <?php echo to_money($obj->fields['ADMINISTRASI']); ?></td>
</tr>
<tr>
	<td><b>Total (Inc. PPN <?php echo to_money($obj->fields['PERSEN_PPN']); ?>%)</b></td>
	<td align="right"><b>Rp. <?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></b></td>
</tr>
</table>

<br>

<table class="kwitansi">
<tr>
	<td width="1" valign="top" class="nowrap">Terbilang : </td>
	<td align="left"><span class="tebilang"><?php echo ucfirst($terbilang->eja($obj->fields['JUMLAH_BAYAR'])); ?> rupiah.</span></td>
</tr>
</table>
</body>
</html>

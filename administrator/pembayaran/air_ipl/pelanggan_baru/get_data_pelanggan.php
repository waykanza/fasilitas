<?php
require_once('../../../../config/config.php');
$conn = conn();

$no_pelanggan = (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';

$query = "
SELECT 
	p.NO_PELANGGAN,
	p.STATUS_BLOK,
	p.NAMA_PELANGGAN,
	(s.NAMA_SEKTOR + ' (' + c.KODE_SEKTOR + ')') AS NAMA_SEKTOR,
	(c.NAMA_CLUSTER + ' (' + c.KODE_CLUSTER + ')') AS NAMA_CLUSTER,
	p.KODE_BLOK,
	p.KEY_AIR,
	p.KEY_IPL
FROM 
	KWT_PELANGGAN p
	LEFT JOIN KWT_SEKTOR s ON p.KODE_SEKTOR = s.KODE_SEKTOR
	LEFT JOIN KWT_CLUSTER c ON p.KODE_CLUSTER = c.KODE_CLUSTER
WHERE 
	p.DISABLED IS NULL AND 
	p.NO_PELANGGAN = '$no_pelanggan'
";

$opj = $conn->Execute($query);

$json = array(
	'no_pelanggan' => $opj->fields['NO_PELANGGAN'],
	'status_blok' => $opj->fields['STATUS_BLOK'],
	'nama_pelanggan' => $opj->fields['NAMA_PELANGGAN'],
	'nama_sektor' => $opj->fields['NAMA_SEKTOR'],
	'nama_cluster' => $opj->fields['NAMA_CLUSTER'],
	'kode_blok' => $opj->fields['KODE_BLOK'],
	'key_air' => $opj->fields['KEY_AIR'],
	'key_ipl' => $opj->fields['KEY_IPL']
);

close($conn);
echo json_encode($json);
exit;
?>
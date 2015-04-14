<?php
require_once('../../../config/config.php');

ini_set('auto_detect_line_endings', TRUE);

$status = '';
$respon = '';

$path = IMPORT_PATH . 'sm\\';
	
$status = read_file($path . 'status.txt');
$respon = read_lines_file($path . 'respon.txt');

echo json_encode(array('status' => $status, 'respon' => $respon));
exit;
?>
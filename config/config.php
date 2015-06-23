<?php
error_reporting(E_ALL);
setlocale(LC_TIME, 'Indonesian');

if ( ! session_start()) { session_start(); }

ini_set('date.timezone', 'Asia/Jakarta');
set_time_limit(0);

$trx_kv = '1'; #KAVLING KOSONG
$trx_bg = '2'; #MASA MEMBANGUN (AIR & IPL)
$trx_db = '3'; #MASA MEMBANGUN (DEPOSIT)
$trx_hn = '4'; #HUNIA
$trx_rv = '5'; #RENOVASI (AIR & IPL)
$trx_dr = '6'; #RENOVASI (DEPOSIT)

$where_trx_kv = "TRX = '$trx_kv' ";
$where_trx_bg = "TRX = '$trx_bg' ";
$where_trx_db = "TRX = '$trx_db' ";
$where_trx_hn = "TRX = '$trx_hn' ";
$where_trx_rv = "TRX = '$trx_rv' ";
$where_trx_dr = "TRX = '$trx_dr' ";

$where_trx_air_ipl = "TRX IN ('1', '2', '4', '5') ";
$where_trx_deposit = "TRX IN ('3', '6') ";

#================ INCLUDE ================
require_once('adodb/adodb.inc.php');
require_once('functions.php');

#============== APPLICATION ==============
define('BASE_URL', 'http://localhost/fasilitas/');
define('APP_DIR', 'D:\\xampp\\www\\pkb\\');
define('EXPORT_PATH', APP_DIR . 'vb\\export\\');
define('IMPORT_PATH', APP_DIR . 'vb\\import\\');

#=============== DATABASE ================
define('DNS', TRUE);

define('DRIVER', 'mssql');
define('HOST', 'IRVAN\SQLEXPRESS');
define('DB', 'dbfasilitas');
define('USR', '');
define('PWD', '');
?>
<?php
require_once('exception.inc.php');
require_once('status.inc.php');
require_once('db.inc.php');


/* ======== FILE ======== */
function read_file($p)
{
	$f = fopen($p, 'r');
	$r = trim(fgets($f));
	fclose($f);
	
	return $r;
}

function write_file($p, $c)
{
	$f = fopen($p,'w');
	fwrite($f, $c);
	fclose($f);
}

function read_lines_file($p)
{
	$r = implode('<br>', file($p));
	
	return $r;
}

/* ====== CLEAN STRING ====== */
function clean($v, $r = '')
{
	$v = str_replace(array("'","''"),array("`","``"),strip_tags(trim($v)));
	return ($v == '') ? $r : $v;
}

function clean_op($v, $r = '')
{
	$v = str_replace(array("'","''"),array("`","``"),strip_tags(trim($v)));
	return ($v == '') ? $r : $v;
}

# ====== CLEAN NUMBER & PERIODE ======
function to_number($v, $r = 0) # Old Regex'/\D/'
{
	$v = intval(preg_replace('/[^0-9\.]/', '', trim($v)));
	return ($v == 0) ? $r : $v;
}

function to_decimal($v, $l = 2, $r = 0)
{
	$v = round(floatval(preg_replace('/[^0-9\.]/', '', trim($v))), $l);
	return ($v == 0) ? $r : $v;
}

function to_periode($v, $r = '')
{
	$v = preg_replace('/\D/', '', trim($v));
	$p = substr($v,2,4) . substr($v,0,2);
	return (strlen($p) == 6) ? $p : $r;
}

function to_periode_prev($v, $r = '')
{
	return date('Ym', strtotime('-1 months', strtotime($v.'01')));
}

function to_date($v, $r = '')
{
	$v = preg_replace('/\D/', '', trim($v));
	$p = substr($v,4,4) . substr($v,2,2) . substr($v,0,2);
	return (strlen($p) == 8) ? $p : $r;
}

# ======== XLS ======== 
function xls_clean_number($v)
{
	if (strpos($v, ']* ')) {
		$r = explode(']* ', $v);
		return $r[1];
	}
	return $v;
}

# ====== FORMAT DATE ======
function to_money($v, $d = 0)
{
	return number_format($v, $d);
}

function to_mmyyyy($v, $d = '')
{
	$v = preg_replace('/\D/', '', trim($v));
	
	return (substr($v,0,2) . $d . substr($v,2,4));
}

function fm_date($d, $f = '%d %B %Y')
{
	return strftime($f, strtotime($d));
}

function fm_periode($p, $f = '%B %Y') #return date('F Y', strtotime($p.'01'));
{
	return strftime($f, strtotime($p.'01'));
}

function fm_periode_first($p, $f = '%d %B %Y')
{
	return fm_periode($p, $f);
}

function fm_periode_last($p, $f = '%d %B %Y')
{
	return strftime($f, strtotime(date('Ymt', strtotime($p.'01'))));
}

function fm_date_first($p, $f = 'Y-m-d')
{
	return date($f, strtotime($p.'01'));
}

function fm_date_last($p, $f = 'Y-m-t')
{
	return date($f, strtotime($p.'01'));
}

function get_int_bulan($v)
{
	switch (strtoupper($v))
	{
		case 'JAN' : return '01'; break;
		case 'FEB' : return '02'; break;
		case 'MAR' : return '03'; break;
		case 'APR' : return '04'; break;
		case 'MEI' : return '05'; break;
		case 'JUN' : return '06'; break;
		case 'JUL' : return '07'; break;
		case 'AGS' : return '08'; break;
		case 'SEP' : return '09'; break;
		case 'OKT' : return '10'; break;
		case 'NOV' : return '11'; break;
		case 'DES' : return '12'; break;
		default : return ''; break;
	}
}

# ====== CHECKING ====== 
function if_empty($v, $x = '', $y = '')
{
	return (empty($v)) ? $x : $y;
}

function if_zero($v, $x = '', $y = '')
{
	return ($v == 0) ? $x : $y;
}

function is_selected($a, $b)
{
	if ($a == $b)
	{
		return 'selected="selected"';
	}
	return '';
}

function is_checked($a, $b)
{
	if ($a == $b)
	{
		return 'checked="checked"';
	}
	return '';
}
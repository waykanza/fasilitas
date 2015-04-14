<?php
function no_pelanggan($v)
{
	return preg_replace('/(--)|(-$)/','', substr($v,0,2).'-'.substr($v,2,2).'-'.substr($v,4,1).'-'.substr($v,5));
}

function jenis_trx($v)
{
	if ($v == '1') return 'KAVLING KOSONG';
	elseif ($v == '2') return 'MASA MEMBANGUN';
	elseif ($v == '3') return 'DEPOSIT (MB)';
	elseif ($v == '4') return 'HUNIAN';
	elseif ($v == '5') return 'RENOVASI';
	elseif ($v == '6') return 'DEPOSIT (RV)';
	else return '-';
}

function status_blok($v)
{
	switch ($v)
	{
		case '1': return 'KAVLING KOSONG'; break;
		case '2': return 'MASA MEMBANGUN'; break;
		case '4': return 'HUNIAN'; break;
		case '5': return 'RENOVASI'; break;
		default : return '-'; break;
	}
}

function golongan($v)
{
	if ($v == '1') return 'BISNIS';
	elseif ($v == '0') return 'STANDAR';
	else return '-';
}

function status_bayar($v)
{
	return ($v == '2') ? '<i class="t"></i>' : '<i class="f"></i>';
}

function status_proses($v)
{
	return ($v == '1') ? '<i class="t"></i>' : '<i class="f"></i>';
}
function status_notproses($v)
{
	return ($v == '') ? '<i class="t"></i>' : '<i class="f"></i>';
}

function status_cetak_kwt($v)
{
	return ($v == '1') ? '<i class="t"></i>' : '<i class="f"></i>';
}

function status_cetak_ivc($v)
{
	return ($v == '1') ? '<i class="t"></i>' : '<i class="f"></i>';
}

function status_sk($v)
{
	return ($v == '1') ? '<i class="t"></i>' : '<i class="f"></i>';
}

function tipe_tarif_ipl($v)
{
	if ($v == '0') return 'TETAP';
	elseif ($v == '1') return 'PER METER';
	elseif ($v == '2') return 'PER PERIODE';
	else return '-';
}

function status_pelanggan($v)
{
	if ($v == '1') return 'AKTIF';
	elseif ($v == '0') return 'TDK. AKTIF';
	else return '-';
}

function status_pelanggan_xls($v)
{
	if ($v == '1') return 'PEL. AKTIF';
	elseif ($v == '0') return 'PEL. TIDAK AKTIF';
	else return '-';
}

function tipe_lokasi($v)
{
	if ($v == 'ID') return 'INDOOR';
	elseif ($v == 'OD') return 'OUTDOOR';
	else return '-';
}

function satuan($v)
{
	if ($v == '0') return ' / m&sup2';
	elseif ($v == '1') return ' / Bulan';
	else return '-';
}

function jenis_bayar($v, $b = '')
{
	if ($b != '') { $b = " ($b)"; }
	switch ($v)
	{
		case '1': return 'TUNAI'; break;
		case '2': return 'K. DEBIT'; break;
		case '3': return 'K. KREDIT'; break;
		case '4': return "T. BANK $b"; break;
		default : return '-'; break;
	}
}
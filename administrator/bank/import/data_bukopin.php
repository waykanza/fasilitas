<?php
$xls = new Spreadsheet_Excel_Reader($uploaded_file, false);
$baris = $xls->rowcount($sheet_index=0);

$x_tgl_bayar_bank = clean($xls->val(3, 2));

$spl = explode('-', $x_tgl_bayar_bank);
if ((count($spl) != 3) || ( ! checkdate($spl[1], $spl[0], $spl[2])))
{
	echo '<tr><td colspan="14">Error. Format tanggal tidak valid. '.$x_tgl_bayar_bank.'</td></tr>';
	exit;
}

$tbb = $spl[0].'-'.$spl[1].'-'.$spl[2].' 00:00:00';

for ($ix = 5; $ix <= $baris; $ix++)
{
	$x_no_pelanggan = (string) clean($xls->val($ix, 3));
	
	if ($x_no_pelanggan != "")
	{
		$list[$ix] = array(
			'np'	=> $x_no_pelanggan,
			'jair'	=> to_decimal(xls_clean_number($xls->val($ix, 5))),
			'jipl'	=> to_decimal(xls_clean_number($xls->val($ix, 6))),
			'ba'	=> to_decimal(xls_clean_number($xls->val($ix, 7)))
		);
	}
}

$in_no_pelanggan = array();
$data_imp = array();

foreach ($list as $x)
{
	$np				= (string) $x['np'];
	$jumlah_air		= (int) $x['jair'];
	$jumlah_ipl		= (int) $x['jipl'];
	$biaya_admin	= (int) $x['ba'];
	$jb				= $jumlah_air + $jumlah_ipl;

	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb'] = $tbb;
}

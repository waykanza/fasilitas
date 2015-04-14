<?php
$xls = new Spreadsheet_Excel_Reader($uploaded_file, false);
$baris = $xls->rowcount($sheet_index=0);
$list = array();
for ($ix = 1; $ix <= $baris; $ix++)
{
	$x_ket = (string) clean($xls->val($ix, 7));
	
	if (strpos('#' . $x_ket, 'UBP'))
	{
		$x_no_pelanggan = (string) clean(substr($x_ket, 22, 15));
		
		if ($x_no_pelanggan != '')
		{
			$list[$ix] = array(
				'np'	=> $x_no_pelanggan,
				'jb'	=> to_decimal(xls_clean_number($xls->val($ix, 5))),
				'tb'	=> clean($xls->val($ix, 1))
			);
		}
	}
}

$in_no_pelanggan = array();
$data_imp = array();

foreach ($list as $x)
{
	$np	= (string) $x['np'];
	$jb	= (int) $x['jb'];
	
	$spl = explode('/', $x['tb']);
	if ((count($spl) != 3) || ( ! checkdate(get_int_bulan($spl[1]), $spl[0], $spl[2])))
	{
		echo '
		<tr>
			<td colspan="2"></td>
			<td>'.$np.'</td>
			<td colspan="11">Error. Format tanggal tidak valid. '.$x['tb'].'</td>
		</tr>';
		continue;
	}
	
	$tbb = $spl[0].'-'.get_int_bulan($spl[1]).'-'.$spl[2].' 00:00:00';
	
	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb']	= $tbb;
}

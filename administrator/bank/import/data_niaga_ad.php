<?php
$l = 0;
$list = array();
$file = fopen($uploaded_file, 'r');
while ( ! feof($file))
{
	$l++;
	
	$line = fgets($file);
	if ($line == '')
	{
		echo '<tr><td colspan="14">Error. format baris : '.$l.'</td></tr>';
		continue;
	}
	$list[$l] = array(
		'np' => (string) clean(substr($line, 17, 13)),
		'jb' => to_decimal(substr($line, 101, 10)),
		'tb' => clean(substr($line, 49, 8))
	);
}
fclose($file);

$in_no_pelanggan = array();
$data_imp = array();

foreach ($list as $x)
{
	$np = (string) $x['np'];
	$jb = (int) $x['jb'];
	
	$hri = clean(substr($x['tb'], 0, 2));
	$bln = clean(substr($x['tb'], 2, 2));
	$thn = clean(substr($x['tb'], 6, 2));
	
	if ( ! checkdate($bln, $hri, $thn))
	{
		echo '
		<tr>
			<td colspan="2"></td>
			<td>'.$np.'</td>
			<td colspan="11">Error. Format tanggal tidak valid. '.$x['tb'].'</td>
		</tr>';
		continue;
	}
	
	$tbb = $hri.'-'.$bln.'-'.$thn.' 00:00:00';
	
	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb']	= $tbb;
}

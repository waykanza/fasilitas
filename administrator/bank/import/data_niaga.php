<?php
$l = 0;
$l_skip = array(1, 2, 3, 4);
$list = array();
$file = fopen($uploaded_file, 'r');
while ( ! feof($file))
{
	$l++;
	$line = fgets($file);
	if (in_array($l, $l_skip, TRUE))
	{
		continue;
	}
	elseif (strlen($line) < 255 || $line == '')
	{
		echo '<tr><td colspan="14">Error. format baris : '.$l.'</td></tr>';
		continue;
	}
	
	$list[$l] = array(
		'np' => (string) clean(substr($line, 47, 13)),
		'jb' => to_decimal(substr($line, 190, 10)),
		'db' => clean(substr($line, 0, 8)),
		'tb' => clean(substr($line, 9, 5))
	);
}
fclose($file);

$in_no_pelanggan = array();
$data_imp = array();

foreach ($list as $x)
{
	$np = (string) $x['np'];
	$jb = (int) $x['jb'];
	
	$spl = explode('/', $x['db']);
	if ((count($spl) != 3) || ( ! checkdate($spl[0], $spl[1], $spl[2])))
	{
		echo '
		<tr>
			<td colspan="2"></td>
			<td>'.$np.'</td>
			<td colspan="11">Error. Format tanggal tidak valid. '.$x['db'].'</td>
		</tr>';
		continue;
	}
	
	$tbb = $spl[1].'-'.$spl[0].'-'.$spl[2].' '.$x['tb'].':00';
	
	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb']	= $tbb;
}

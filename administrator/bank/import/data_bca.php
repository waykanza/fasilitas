<?php
$l = 0;
$list = array();
$file = fopen($uploaded_file, 'r');
while ( ! feof($file))
{
	$l++;
	
	$line = fgets($file);
	if (strlen($line) < 71 || $line == '')
	{
		echo '<tr><td colspan="14">Error. Format baris : '.$l.'</td></tr>';
		continue;
	}
	$list[$l] = array(
		'np' => (string) clean(substr($line, 8, 19)),
		'jb' => to_decimal(substr($line, 33, 21)),
		'db' => clean(substr($line, 57, 8)),
		'tb' => clean(substr($line, 67, 8))
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
	if ((count($spl) != 3) || ( ! checkdate($spl[1], $spl[0], $spl[2])))
	{
		echo '
		<tr>
			<td colspan="2"></td>
			<td>'.$np.'</td>
			<td colspan="11">Error. Format tanggal tidak valid. '.$x['db'].'</td>
		</tr>';
		continue;
	}
	
	$tbb = $spl[0].'-'.$spl[1].'-'.$spl[2].' '.$x['tb'];
	
	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb']	= $tbb;
}

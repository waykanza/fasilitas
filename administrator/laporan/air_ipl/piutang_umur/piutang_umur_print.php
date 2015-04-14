<?php
require_once('../../../../config/config.php');
$conn = conn();
$query_search = '';

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '1';

$desc_top = array();
$desc_bottom = array();

$desc_top[] = 'Laporan Umur Piutang';

$obj = get_parameter('JRP_PT, UNIT_NAMA, UNIT_ALAMAT_1, UNIT_ALAMAT_2, UNIT_KOTA, UNIT_KODE_POS');

$set_jrp = '
<tr><td colspan="17" class="nb"><b>' . $obj->fields['JRP_PT'] . '</b></td></tr>
<tr><td colspan="17" class="nb"><b>' . $obj->fields['UNIT_NAMA'] . '</b></td></tr>
<tr><td colspan="17" class="nb">' . $obj->fields['UNIT_ALAMAT_1'] . ' ' . $obj->fields['UNIT_ALAMAT_2'] . '</td></tr>
<tr><td colspan="17" class="nb">' . $obj->fields['UNIT_KOTA'] . ', ' . $obj->fields['UNIT_KODE_POS'] . '</td></tr>
<tr><td colspan="17" class="nb">&nbsp;</td></tr>
<tr>
	<td colspan="15" class="nb">
		' . implode(' | ', $desc_top) . '
	</td>
	<td colspan="2" align="right" class="nb text-right va-bottom">Halaman 1 dari 1</td>
</tr>

<tr>
	<th>NO.</th>
	<th>SEKTOR</th>
	<th>BELUM JT</th>
	<th>1 BLN</th>
	<th>2 BLN</th>
	<th>3 BLN</th>
	<th>4 BLN</th>
	<th>5 BLN</th>
	<th>6 BLN</th>
	<th>7 BLN</th>
	<th>8 BLN</th>
	<th>9 BLN</th>
	<th>10 BLN</th>
	<th>11 BLN</th>
	<th>12 BLN</th>
	<th>> 12 BLN</th>
	<th>TOTAL</th>
</tr>
';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $desc_top[0]; ?></title>
<style type="text/css">
@media print {
	@page {
		size:34.6cm 27.90cm;
	}
	.newpage {page-break-before:always;}
}

.newpage {margin-top:25px;}

table {
	font-family:Arial, Helvetica, sans-serif;
	width:100%;
	border-spacing:0;
	border-collapse:collapse;
}
table tr {
	font-size:11px;
	padding:2px;
}
table td {
	padding:2px;
	vertical-align:top;
}
table th.nb,
table td.nb {
	border:none !important;
}
table.data th {
	border:1px solid #000000;
}
table.data td {
	border-right:1px solid #000000;
	border-left:1px solid #000000;
}
tfoot tr {
	font-weight:bold;
	text-align:right;
	border:1px solid #000000;
}
.break { word-wrap:break-word; }
.nowrap { white-space:nowrap; }
.va-top { vertical-align:top; }
.va-bottom { vertical-align:bottom; }
.text-left { text-align:left; }
.text-center { text-align:center; }
.text-right { text-align:right; }
</style>
</head>
<body>

<table class="data">

<?php
echo $set_jrp;

	$query = "SELECT KODE_SEKTOR, NAMA_SEKTOR FROM KWT_SEKTOR";
	$obj = $conn->Execute($query);
	while( ! $obj->EOF)
	{
		$kks = $obj->fields['KODE_SEKTOR'];
		$ns[$kks] = $obj->fields['NAMA_SEKTOR'];
		$bjt[$kks] = 0;
		$b1[$kks] = 0;
		$b2[$kks] = 0;
		$b3[$kks] = 0;
		$b4[$kks] = 0;
		$b5[$kks] = 0;
		$b6[$kks] = 0;
		$b7[$kks] = 0;
		$b8[$kks] = 0;
		$b9[$kks] = 0;
		$b10[$kks] = 0;
		$b11[$kks] = 0;
		$b12[$kks] = 0;
		$b13[$kks] = 0;
		$total[$kks] = 0;
		
		$obj->movenext();
	}
	
	$thn = (int) substr($periode,0,4);
	$bln = (int) substr($periode,4,2);
	
	if ($bln == 1)
	{
		$bln = 12;
		$thn = $thn - 1;
	}
	else
	{
		$bln = $bln - 1;
	}

	$query_periode = ($thn * 100) + $bln;
	
	$query = "	
		SELECT 
			b.PERIODE,
			b.KODE_SEKTOR,
			SUM(b.JUMLAH_IPL + b.DENDA + b.ADMINISTRASI - b.DISKON_RUPIAH_IPL) AS JB
		FROM 
			KWT_PEMBAYARAN_AI b
		WHERE
			$where_trx_air_ipl AND 
			b.STATUS_BAYAR IS NULL AND
			CAST(PERIODE AS INT) <= $query_periode
		GROUP BY b.KODE_SEKTOR, b.PERIODE
	";
	
	$obj = $conn->Execute($query);
	
	$sum_bjt = 0;
	$sum_b1 = 0;
	$sum_b2 = 0;
	$sum_b3 = 0;
	$sum_b4 = 0;
	$sum_b5 = 0;
	$sum_b6 = 0;
	$sum_b7 = 0;
	$sum_b8 = 0;
	$sum_b9 = 0;
	$sum_b10 = 0;
	$sum_b11 = 0;
	$sum_b12 = 0;
	$sum_b13 = 0;
	$sum_total = 0;
	
	error_reporting(E_ALL ^ E_NOTICE);
	while( ! $obj->EOF)
	{
		$ks = $obj->fields['KODE_SEKTOR'];
		
		$xperiode = $obj->fields['PERIODE'];
		$xthn = (int) substr($xperiode,0,4);
		$xbln = (int) substr($xperiode,4,2);
		
		if ($xperiode == $periode) { $n = 0; }
		else
		{
			if ($xthn == $thn)
			{
				$n = $bln - $xbln;
				if ($bln >= $xbln)
				{
					$n = $bln - $xbln;
				}
				else
				{
					$n = 14;
				}
			}
			else
			{
				if (($thn - $xthn) == 1)
				{
					$n = $bln + (12 - $xbln);
					if ($n > 12)
					{
						$n = 13;
					}
				}
				else
				{
					$n = 13;
				}
			}
		}
		
		$jb = $obj->fields['JB'];
		
		switch ($n)
		{
			case 0 : $bjt[$ks] += $jb; $total[$ks] += $jb; break;
			case 1 : $b1[$ks] += $jb; $total[$ks] += $jb; break;
			case 2 : $b2[$ks] += $jb; $total[$ks] += $jb; break;
			case 3 : $b3[$ks] += $jb; $total[$ks] += $jb; break;
			case 4 : $b4[$ks] += $jb; $total[$ks] += $jb; break;
			case 5 : $b5[$ks] += $jb; $total[$ks] += $jb; break;
			case 6 : $b6[$ks] += $jb; $total[$ks] += $jb; break;
			case 7 : $b7[$ks] += $jb; $total[$ks] += $jb; break;
			case 8 : $b8[$ks] += $jb; $total[$ks] += $jb; break;
			case 9 : $b9[$ks] += $jb; $total[$ks] += $jb; break;
			case 10 : $b10[$ks] += $jb; $total[$ks] += $jb; break;
			case 11 : $b11[$ks] += $jb; $total[$ks] += $jb; break;
			case 12 : $b12[$ks] += $jb; $total[$ks] += $jb; break;
			case 13 : $b13[$ks] += $jb; $total[$ks] += $jb; break;
		}
		
		$obj->movenext();
	}

if ( ! empty($total))
{
	$i = 1;
	foreach ($total as $x => $v)
	{
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $ns[$x]; ?></td>
			<td class="text-right"><?php echo to_money($bjt[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b1[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b2[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b3[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b4[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b5[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b6[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b7[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b8[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b9[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b10[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b11[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b12[$x]); ?></td>
			<td class="text-right"><?php echo to_money($b13[$x]); ?></td>
			<td class="text-right"><?php echo to_money($total[$x]); ?></td>
		</tr>
		<?php
		$sum_bjt += $bjt[$x];
		$sum_b1 += $b1[$x];
		$sum_b2 += $b2[$x];
		$sum_b3 += $b3[$x];
		$sum_b4 += $b4[$x];
		$sum_b5 += $b5[$x];
		$sum_b6 += $b6[$x];
		$sum_b7 += $b7[$x];
		$sum_b8 += $b8[$x];
		$sum_b9 += $b9[$x];
		$sum_b10 += $b10[$x];
		$sum_b11 += $b11[$x];
		$sum_b12 += $b12[$x];
		$sum_b13 += $b13[$x];
		$sum_total += $total[$x];
		$i++;
	}
}
?>
<tfoot>
<tr>
	<td colspan="2">GRAND TOTAL .........</td>
	<td><?php echo to_money($sum_bjt); ?></td>
	<td><?php echo to_money($sum_b1); ?></td>
	<td><?php echo to_money($sum_b2); ?></td>
	<td><?php echo to_money($sum_b3); ?></td>
	<td><?php echo to_money($sum_b4); ?></td>
	<td><?php echo to_money($sum_b5); ?></td>
	<td><?php echo to_money($sum_b6); ?></td>
	<td><?php echo to_money($sum_b7); ?></td>
	<td><?php echo to_money($sum_b8); ?></td>
	<td><?php echo to_money($sum_b9); ?></td>
	<td><?php echo to_money($sum_b10); ?></td>
	<td><?php echo to_money($sum_b11); ?></td>
	<td><?php echo to_money($sum_b12); ?></td>
	<td><?php echo to_money($sum_b13); ?></td>
	<td><?php echo to_money($sum_total); ?></td>
</tr>
</tfoot>

</body>
</html>
<?php
close($conn);
exit;
?>
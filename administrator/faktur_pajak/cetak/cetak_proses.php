<?php
require_once('../../../config/config.php');
$conn = conn();

$cb_data = (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style type="text/css">
	@media print {
		@page {
			size: 8.5in 4in portrait;
			margin: 0;
		}
		.newpage {
			page-break-before: always;
		}
	}
	
	* {
		font-size: 14px;
		line-height: 1.5;
	}
	table {
		border-collapse: collapse;
	}
	table tr td {
		/* font-family: "New Century Schoolbook", Times, serif; */
		font-size: 14px;
	}
	.line-sum {
		border:none;
		border-top:1px solid #000;
		margin:0;
		padding:0 0 2px 0;
	}
	
	#wrap {
		position: relative;
		width: 750px;
	}
	
	.f-left { float: left; }
	.f-right { float: right; }
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.text-center { text-align: center; }
	.va-top { vertical-align:top; }
</style>
</head>

<body onload="window.print()">
<div id="wrap">
<?php

$obj			= $conn->Execute("SELECT JRP_PT, JRP_ALAMAT_1, JRP_ALAMAT_2, REG_NPWP, NAMA_PAJAK, JBT_PAJAK FROM KWT_PARAMETER");

$jrp_pt			= $obj->fields['JRP_PT'];
$jrp_alamat_1	= $obj->fields['JRP_ALAMAT_1'];
$jrp_alamat_2	= $obj->fields['JRP_ALAMAT_2'];
$reg_npwp		= $obj->fields['REG_NPWP'];
$nama_pajak		= $obj->fields['NAMA_PAJAK'];
$jbt_pajak		= $obj->fields['JBT_PAJAK'];

$id_pembayaran = array();
foreach ($cb_data as $x) { $id_pembayaran[] = base64_decode($x); }
$id_pembayaran = implode("' ,'", $id_pembayaran);

$query = "
SELECT
	b.TRX,
	b.NO_FAKTUR_PAJAK,
	CONVERT(VARCHAR(8),b.TGL_FAKTUR_PAJAK,112) AS TGL_FAKTUR_PAJAK,
	b.KODE_BLOK,
	p.NAMA_PELANGGAN,
	p.ALAMAT,
	p.NPWP,
	b.NO_PELANGGAN,
	b.PERIODE,
	b.NO_INVOICE,
	(b.NILAI_PPN * (100 / b.PERSEN_PPN)) AS NILAI_DPP,
	b.NILAI_PPN,
	b.AKTIF_AIR,
	b.AKTIF_IPL,
	b.ABONEMEN
FROM 
	KWT_PEMBAYARAN_AI b
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN
WHERE
	b.ID_PEMBAYARAN IN ('$id_pembayaran')
";
	
$obj = $conn->Execute($query);

while( ! $obj->EOF)
{
	$trx				= $obj->fields['TRX'];
	$no_faktur_pajak	= $obj->fields['NO_FAKTUR_PAJAK'];
	$tgl_faktur_pajak	= $obj->fields['TGL_FAKTUR_PAJAK'];
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$alamat				= $obj->fields['ALAMAT'];
	$npwp				= $obj->fields['NPWP'];
	$no_pelanggan		= $obj->fields['NO_PELANGGAN'];
	$periode			= $obj->fields['PERIODE'];
	$no_invoice			= $obj->fields['NO_INVOICE'];
	$kode_blok			= $obj->fields['KODE_BLOK'];
	$nilai_dpp			= to_money($obj->fields['NILAI_DPP']);
	$nilai_ppn			= to_money($obj->fields['NILAI_PPN']);

	$aktif_air			= $obj->fields['AKTIF_AIR'];
	$aktif_ipl			= $obj->fields['AKTIF_IPL'];

	$text = 'Pembayaran tagihan';
	
	if ($trx == '1' || $trx == '2' || $trx == '4' || $trx == '5')
	{
		if ($aktif_air == '1')
		{
			$text = $text . ' air';
			if ($aktif_ipl == '1')
			{
				$text = $text . ', IPL';
			}
			$text = $text . ' dan abonemen';
		}
		elseif ($aktif_ipl == '1')
		{
			$text = $text . ' IPL';
		}
		
		if ($trx == '1') { $text = $text . ' KAVLING KOSONG'; }
		elseif ($trx == '2') { $text = $text . ' MASA MEMBANGUN'; }
		elseif ($trx == '4') { $text = $text . ' HUNIAN'; }
		elseif ($trx == '5') { $text = $text . ' RENOVASI'; }
	}
	elseif ($trx == '3' || $trx == '6')
	{
		$text = $text . ' SAVE DEPOSIT ';
		
		if ($trx == '3') { $text = $text . ' MASA MEMBANGUN'; }
		elseif ($trx == '6') { $text = $text . ' RENOVASI'; }
	}

	$text = $text . ' periode '. fm_periode($periode) . ' atas blok/no. ' . $kode_blok;
	
	?>
	<div class="clear"></div>
	
		<div class="text-center" style="margin:96px 0 0 0;font-size:21px;"><?php echo $no_faktur_pajak; ?></div>
		
		<div style="margin:30px 0 0 200px;">
			<?php 
			echo 
			$jrp_pt . '<br>' . 
			$jrp_alamat_1 . '<br>' . 
			$jrp_alamat_2 . '<br>' .
			$reg_npwp;
			?>
		</div>
		
		<div style="margin:80px 0 0 200px;">
			<?php 
			echo 
			$nama_pelanggan . '<br>' . 
			$alamat . '<br>';
			?>
		</div>
	
	<div class="clear"></div>
	
		<div class="f-left" style="width:32px;margin:80px 0 0 0;">1.</div>
		
		<div class="f-left" style="width:350px;margin:80px 0 0 0;">
			<?php 
			echo
			$text .
			'<br><br><br><br>Invoice : ' . $no_invoice;
			?>
		</div>
	
		<div class="f-right text-right" style="width:150px;margin:80px 0 0 0;">
			<?php echo $nilai_dpp; ?>
		</div>
	
	<div class="clear"></div>
	
		<div class="f-left" style="margin:230px 0 0 0;">|||||||||||||||||&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|||||||||||||||||||||||||||||||||</div>
		
		<div class="f-right text-right" style="margin:230px 0 0 0;">
			<?php echo $nilai_dpp; ?>
		</div>
	
	<div class="clear"></div>
	
		<div class="text-right" style="margin:5px 0 0 0;">---</div>
		<div class="text-right" style="margin:5px 0 0 0;">---</div>
	
	<div class="clear"></div>
	
		<div class="f-right text-right" style="margin:5px 0 0 0;">
			<?php echo $nilai_dpp . '<br><br>' . $nilai_ppn; ?>
		</div>
	
	<div class="clear"></div>
	
		<div class="f-right text-right" style="margin:10px 90px 0 0;">
			<div class="text-center">Tangerang&emsp;&emsp;&emsp;&emsp;&emsp;<?php echo fm_date($tgl_faktur_pajak); ?></div>
			<div class="text-center" style="margin:100px 0 0 0;"><?php echo $jbt_pajak; ?></div>
			<div class="text-center" style="margin:0 0 0 0;"><?php echo $nama_pajak; ?></div>
		</div>
		
	<div class="newpage"></div>
	<?php
	$obj->movenext();
}
?>
</div>

</body>
</html>

<?php close($conn); ?>

<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
$conn = conn();

$terbilang = new Terbilang;
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
	
	table {
		border-collapse: collapse;
	}
	table tr td {
		/* font-family: "New Century Schoolbook", Times, serif; */
		font-size: 10px;
	}
	.line-sum {
		border:none;
		border-top:1px solid #000;
		margin:0;
		padding:0 0 2px 0;
	}
	
	.wrap {
		font-size: 11px;
		line-height:15px;
		position: relative;
		width: 575px;
	}
	
	.f-left { float: left; }
	.f-right { float: right; }
	
	.clear { clear: both; }
	.text-left { text-align: left; }
	.text-right { text-align: right; }
	.va-top { vertical-align:top; }
</style>
</head>

<body onload="window.print()">
<?php

$in_kode_blok = array();
foreach ($cb_data as $x) { $in_kode_blok[] = base64_decode($x); }
$in_kode_blok = implode("' ,'", $in_kode_blok);

$query = "
DECLARE 
@adm_db INT,
@adm_dr INT,
@now DATE = DATEADD(MONTH, -1, GETDATE())

SELECT TOP 1 
@adm_db = ISNULL(ADMINISTRASI_DB, 0) ,
@adm_dr = ISNULL(ADMINISTRASI_DR, 0)
FROM KWT_PARAMETER

SELECT 
	b.KODE_BLOK,
	(
		SELECT
			CASE WHEN PAKAI_SM IS NULL THEN
				NO_PELANGGAN + '||' + NAMA_PELANGGAN + '||' + NAMA_CLUSTER 
			ELSE
				NO_PELANGGAN + '||' + SM_NAMA_PELANGGAN + '||' + NAMA_CLUSTER 
			END
		FROM 
			KWT_PELANGGAN x 
			LEFT JOIN KWT_CLUSTER y ON x.KODE_CLUSTER = y.KODE_CLUSTER
		 WHERE x.KODE_BLOK = b.KODE_BLOK
	) AS DATA_PEL,
	
	SUM(b.JUMLAH_PERIODE) AS JUMLAH_PERIODE,
	
	MIN(b.PERIODE_AWAL) AS PERIODE_AWAL,
	MAX(b.PERIODE_AKHIR) AS PERIODE_AKHIR,
	
	SUM(b.JUMLAH_IPL - b.DISKON_RUPIAH_IPL) AS JUMLAH_IPL,
	SUM(b.DENDA) AS DENDA,
	SUM(
		CASE TRX
			WHEN '3' THEN @adm_db
			WHEN '6' THEN @adm_dr 
		END
	) AS ADMINISTRASI,
	
	SUM(
		b.JUMLAH_IPL + b.DENDA - b.DISKON_RUPIAH_IPL + 
		CASE TRX
			WHEN '3' THEN @adm_db
			WHEN '6' THEN @adm_dr 
		END
	) AS JUMLAH_BAYAR
FROM 
	KWT_PEMBAYARAN_AI b 
	LEFT JOIN KWT_PELANGGAN p ON b.KODE_BLOK = p.KODE_BLOK
WHERE 
	p.INFO_TAGIHAN = '1' AND 
	b.STATUS_BAYAR IS NULL AND
	(CAST(DATEDIFF(MONTH, dbo.PTDF(b.PERIODE), @now) AS INT)) >= 2 AND
	b.KODE_BLOK IN ('$in_kode_blok')
GROUP BY b.KODE_BLOK
ORDER BY b.KODE_BLOK
";
	
$obj = $conn->Execute($query);
$tgl_jt = date('Ym25');

while( ! $obj->EOF)
{
	$data_pel		= explode('||', $obj->fields['DATA_PEL']);
	$no_pelanggan	= $data_pel[0];
	$nama_pelanggan	= $data_pel[1];
	$nama_cluster	= $data_pel[2];
	
	$kode_blok		= $obj->fields['KODE_BLOK'];
	
	$jumlah_periode	= $obj->fields['JUMLAH_PERIODE'];
	
	$periode_awal	= $obj->fields['PERIODE_AWAL'];
	$periode_akhir	= $obj->fields['PERIODE_AKHIR'];
	
	$jumlah_ipl		= $obj->fields['JUMLAH_IPL'];
	$denda			= $obj->fields['DENDA'];
	$administrasi	= $obj->fields['ADMINISTRASI'];
	
	$jumlah_bayar	= $obj->fields['JUMLAH_BAYAR'];

	?>
	<div class="wrap">
		<div class="clear"></div>
		
			<div style="margin:101px 0 0 0;">&nbsp;</div>
		
		<div class="clear"></div>
		
			<div class="f-left" style="margin:0 0 0 45px;">
				<div style="margin:10px 0 0 0;"><?php echo $nama_pelanggan; ?></div>
				<div style="margin:20px 0 0 50px;"><?php echo $kode_blok; ?></div>
				<div style="margin:0 0 0 50px;"><?php echo $nama_cluster; ?></div>
			</div>
			
			<div class="f-right" style="margin:0 0 0 0;">
				<?php 
				echo 
				no_pelanggan($no_pelanggan) . '<br>' . 
				fm_periode($periode_akhir) . '<br>' . 
				fm_date($tgl_jt) . '<br>'  .
				$jumlah_periode . ' Bulan<br>';
				?>
			</div>
		
		<div class="clear"></div>

			<div style="margin:65px 0 0 0;"></div>
			
			<div class="text-right"><?php echo to_money($jumlah_ipl); ?></div>
			<div class="text-right"><?php echo to_money($denda); ?></div>
			<div class="text-right"><?php echo to_money($administrasi); ?></div>
			<div class="text-right"><?php echo to_money($jumlah_bayar); ?></div>
			
			<div class="" style="margin:15px 0 0 95px;"><i><?php echo ucfirst($terbilang->eja($jumlah_bayar)); ?> rupiah.</i></div>
		
		<div class="clear"></div>
		
	</div>

	<div class="newpage"></div>
	<?php
	$obj->movenext();
}
?>

</body>
</html>

<?php close($conn); ?>

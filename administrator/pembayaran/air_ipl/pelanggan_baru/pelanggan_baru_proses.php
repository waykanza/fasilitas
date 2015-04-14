<?php
require_once('../../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;

$periode		= (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';
$jumlah_periode	= (isset($_REQUEST['jumlah_periode'])) ? to_number($_REQUEST['jumlah_periode']) : '';
$no_pelanggan	= (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
$trx			= (isset($_REQUEST['trx'])) ? clean($_REQUEST['trx']) : '';
$stand_akhir	= (isset($_REQUEST['stand_akhir'])) ? to_number($_REQUEST['stand_akhir']) : '';
$stand_lalu		= (isset($_REQUEST['stand_lalu'])) ? to_number($_REQUEST['stand_lalu']) : '';
	
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($periode, 'Periode awal.');
		ex_zero($jumlah_periode, 'Jumlah periode harus > 0');
		ex_empty($no_pelanggan, 'Pilih pelanggan.');
		ex_empty($trx, 'Error Trx.');
		
		$periode_awal = $periode;
		
		$jumlah_periode--;
		$periode_akhir = date('Ym', strtotime("+$jumlah_periode months", strtotime($periode_awal.'01')));
		$jumlah_periode++;
		
		$pemakaian = (int) ($stand_akhir - $stand_lalu);
		ex_less_then($pemakaian, 0, 'Pemakaian tidak boleh minus!');
		
		$id_pembayaran = $trx . '#' . $periode . '#' . $no_pelanggan;
		$query = "
		SELECT ID_PEMBAYARAN 
		FROM KWT_PEMBAYARAN_AI p
		WHERE 
			ID_PEMBAYARAN = '$id_pembayaran' OR
			$periode_akhir < (
				CASE WHEN p.AKTIF_IPL = '1'
				THEN 
				(
					SELECT ISNULL(MAX(CAST(PERIODE_AKHIR AS INT)), 0)
					FROM KWT_PEMBAYARAN_AI
					WHERE 
						KODE_BLOK = p.KODE_BLOK AND
						TRX = p.STATUS_BLOK AND 
						AKTIF_IPL = '1'
				)
				ELSE 999999 END
			)
		";
		ex_found($conn->Execute($query)->recordcount(), "Nomor pelanggan \"$no_pelanggan\" telah terdaftar di tagihan periode $periode!");
		
		$query = "
		SELECT 
			p.NO_PELANGGAN,
			p.KODE_SEKTOR,
			p.KODE_CLUSTER,
			p.KODE_BLOK,
			p.KODE_ZONA,
			p.AKTIF_AIR,
			p.AKTIF_IPL,
			a.KEY_AIR,
			i.KEY_IPL,
			p.STATUS_BLOK,
			ISNULL(p.LUAS_KAVLING,0) AS LUAS_KAVLING,
			
			ISNULL(a.ABONEMEN,0) AS ABONEMEN,
			ISNULL(a.BLOK1,0) AS BLOK1, 
			ISNULL(a.BLOK2,0) AS BLOK2, 
			ISNULL(a.BLOK3,0) AS BLOK3, 
			ISNULL(a.BLOK4,0) AS BLOK4, 
			
			ISNULL(a.STAND_MIN_PAKAI,0) AS STAND_MIN_PAKAI,
			ISNULL(a.TARIF1,0) AS TARIF1, 
			ISNULL(a.TARIF2,0) AS TARIF2, 
			ISNULL(a.TARIF3,0) AS TARIF3, 
			ISNULL(a.TARIF4,0) AS TARIF4,
			
			ISNULL(i.TARIF_IPL,0) AS TARIF_IPL,
			i.TIPE_TARIF_IPL
		FROM 
			KWT_PELANGGAN p
			LEFT JOIN KWT_TARIF_AIR a ON p.KEY_AIR = a.KEY_AIR
			LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
		WHERE 
			p.DISABLED IS NULL AND 
			p.NO_PELANGGAN = '$no_pelanggan'
		";

		$obj = $conn->Execute($query);
		
		$aktif_air		= $obj->fields['AKTIF_AIR'];
		$aktif_ipl		= $obj->fields['AKTIF_IPL'];
		$key_air		= $obj->fields['KEY_AIR'];
		$key_ipl		= $obj->fields['KEY_IPL'];
		$tipe_tarif_ipl = $obj->fields['TIPE_TARIF_IPL'];
		
		if ($key_air == '' AND $key_ipl == '') {
			throw new Exception("Pelanggan tidak aktif air & IPL");
		}
		
		if ($aktif_air != '') {
			ex_empty($key_air, "Kode tarif air tidak terdaftar di Master Air -> Tarif.");
		}
		if ($aktif_ipl != '') {
			ex_empty($key_ipl, "Kode tarif IPL tidak terdaftar di Master IPL -> Tarif.");
			ex_empty($tipe_tarif_ipl, "Kategori IPL tidak terdaftar di Master IPL -> kategori.");
		}
		
		$luas_kavling	= $obj->fields['LUAS_KAVLING'];
		$tarif_ipl		= $obj->fields['TARIF_IPL'];
		$jumlah_ipl		= 0;
		
		if ($tipe_tarif_ipl == '1') {
			$jumlah_ipl = (int) ($luas_kavling * $tarif_ipl);
		} else {
			$jumlah_ipl = (int) $tarif_ipl;
		}
		
		$kode_sektor = $obj->fields['KODE_SEKTOR'];
		$kode_cluster = $obj->fields['KODE_CLUSTER'];
		$kode_blok = $obj->fields['KODE_BLOK'];
		$kode_zona = $obj->fields['KODE_ZONA'];
		$status_blok = $obj->fields['STATUS_BLOK'];
		
		$abonemen = (int) $obj->fields['ABONEMEN'];
		$limit_blok1 = (int) $obj->fields['BLOK1'];
		$limit_blok2 = (int) $obj->fields['BLOK2'];
		$limit_blok3 = (int) $obj->fields['BLOK3'];
		$limit_blok4 = (int) $obj->fields['BLOK4'];
		$limit_stand_min_pakai = (int) $obj->fields['STAND_MIN_PAKAI'];
		$tarif1 = (int) $obj->fields['TARIF1'];
		$tarif2 = (int) $obj->fields['TARIF2'];
		$tarif3 = (int) $obj->fields['TARIF3'];
		$tarif4 = (int) $obj->fields['TARIF4'];

		$blok1 = 0; 
		$blok2 = 0; 
		$blok3 = 0; 
		$blok4 = 0;
		$stand_min_pakai = 0;
		$tarif_min_pakai = 0;
		
		if ($aktif_air != '')
		{
			if ($pemakaian < $limit_stand_min_pakai)
			{
				$blok1 = $pemakaian;
				$stand_min_pakai = $limit_stand_min_pakai - $blok1;
				$tarif_min_pakai = $tarif1;
			}
			else
			{
				if ($pemakaian > $limit_blok1) { $blok1 = $limit_blok1; $pemakaian -= $blok1;
					
					if ($pemakaian > $limit_blok2) { $blok2 = $limit_blok2; $pemakaian -= $blok2;
					
						if ($pemakaian > $limit_blok3) { $blok3 = $limit_blok3; $pemakaian -= $blok3;
					
							$blok4 = max(0, $pemakaian);
							
						} else { $blok3 = max(0, $pemakaian); }
					} else { $blok2 = max(0, $pemakaian); }
				} else { $blok1 = max(0, $pemakaian); }
			}
		}
		
		$stand_angkat = 0;
		$jumlah_air = ($blok1 * $tarif1) + ($blok2 * $tarif2) + ($blok3 * $tarif3) + ($blok4 * $tarif4) + ($stand_min_pakai * $tarif_min_pakai);
		$keterangan_bayar = "PELANGGAN BARU PERIODE $periode";
		
		$query = "
		INSERT INTO KWT_PEMBAYARAN_AI
		(
			TRX,
			ID_PEMBAYARAN, 
			PERIODE, 
			PERIODE_AWAL, 
			PERIODE_AKHIR, 
			JUMLAH_PERIODE, 
			NO_PELANGGAN,
			AKTIF_AIR,
			AKTIF_IPL,
			KEY_AIR, 
			KEY_IPL,  
			KODE_SEKTOR, 
			KODE_CLUSTER, 
			KODE_BLOK, 
			STATUS_BLOK, 
			KODE_ZONA, 
			STAND_LALU, 
			STAND_ANGKAT, 
			STAND_AKHIR, 
			BLOK1, 
			BLOK2, 
			BLOK3, 
			BLOK4, 
			STAND_MIN_PAKAI, 
			TARIF1, 
			TARIF2, 
			TARIF3, 
			TARIF4, 
			TARIF_MIN_PAKAI, 
			JUMLAH_AIR, 
			ABONEMEN, 
			JUMLAH_IPL, 
			KETERANGAN_BAYAR
		)
		VALUES
		(
			'$trx',
			'$id_pembayaran', 
			'$periode', 
			'$periode_awal', 
			'$periode_akhir', 
			'$jumlah_periode', 
			'$no_pelanggan',
			'$aktif_air',
			'$aktif_ipl',
			'$key_air', 
			'$key_ipl', 
			'$kode_sektor', 
			'$kode_cluster', 
			'$kode_blok', 
			'$status_blok', 
			'$kode_zona', 
			$stand_lalu, 
			$stand_angkat, 
			$stand_akhir, 
			$blok1, 
			$blok2, 
			$blok3, 
			$blok4, 
			$stand_min_pakai, 
			$tarif1, 
			$tarif2, 
			$tarif3, 
			$tarif4, 
			$tarif_min_pakai, 
			$jumlah_air, 
			$abonemen, 
			$jumlah_ipl,
			'$keterangan_bayar'
		)";
		
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Data pelanggan berhasil ditambahkan.';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}
}

close($conn);
$json = array('msg' => $msg, 'error'=> $error);
echo json_encode($json);
exit;
?>
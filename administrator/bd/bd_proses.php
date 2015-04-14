<?php
require_once('../../config/config.php');
die_login();
die_mod('U4');
$conn = conn();
die_conn($conn);

$msg = '';

$bln_bayar = (isset($_REQUEST['bln_bayar'])) ? clean($_REQUEST['bln_bayar']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($bln_bayar, 'Masukkan tanggal posting!');
		
		switch (date('m'))
		{
			case '1': $mm = 'I';	break; case '2': $mm = 'II'; break;
			case '3': $mm = 'III';	break; case '4': $mm = 'IV'; break;
			case '5': $mm = 'V';	break; case '6': $mm = 'VI'; break;
			case '7': $mm = 'VII';	break; case '8': $mm = 'VIII'; break;
			case '9': $mm = 'IX';	break; case '10': $mm = 'X'; break;
			case '11': $mm = 'XI';	break; case '12': $mm = 'XII'; break;
			default: $mm = '-'; break;
		}
			
		$base_format = '/' . $mm . '/' . date('Y');
		
		$ary_bank = array();
		$obj = $conn->Execute("SELECT KODE_BANK, COU_BD, COU_BDT FROM KWT_BANK");
		while( ! $obj->EOF)
		{
			$kb = $obj->fields['KODE_BANK'];
			$ary_bank[$kb]['COU_BD'] = (int) $obj->fields['COU_BD'];
			$ary_bank[$kb]['COU_BDT'] = (int) $obj->fields['COU_BDT'];
			
			$obj->movenext(); 
		}
		
		$max_tgl = (int) date('t', strtotime(to_periode($bln_bayar) . '01'));
		for ($t = 1; $t <= $max_tgl; $t++) 
		{
			if ($t < 10) { $st = '0' . $t; } else { $st = $t; }
			$tgl_bayar_sys = $st . '-' . $bln_bayar;
			
			# JUST IN CASE
			$conn->Execute("DELETE FROM KWT_POST_BD WHERE CONVERT(VARCHAR(10), TGL_BD, 105) = '$tgl_bayar_sys'");
			
			foreach ($ary_bank as $kode_bank => $b)
			{
				$cou_bd		= $ary_bank[$kode_bank]['COU_BD'];
				$cou_bdt	= $ary_bank[$kode_bank]['COU_BDT'];
				
				$no_bd = $cou_bd . '/BD-' . $kode_bank . $base_format; 
				$no_bdt = $cou_bdt . '/BDT-' . $kode_bank . $base_format;
					
				$query = "INSERT INTO KWT_POST_BD 
				(
					USER_BD, 
					BANK_BD, 
					NO_BD, 
					NO_BDT, 
					JUMLAH_BD, 
					JUMLAH_BDT, 
					TGL_BD, 
					
					USER_CREATED 
				)
				VALUES
				(
					'$sess_id_user', 
					'$kode_bank', 
					'$no_bd',
					'$no_bdt',
					(	SELECT ISNULL(SUM(JUMLAH_BAYAR),0) 
						FROM KWT_PEMBAYARAN_AI 
						WHERE
							STATUS_BAYAR = 1 AND 
							BAYAR_VIA = '$kode_bank' AND
							CONVERT(VARCHAR(10), TGL_BAYAR_SYS, 105) = '$tgl_bayar_sys' AND 
							AKTIF_AIR = 1 
					),
					(	SELECT ISNULL(SUM(JUMLAH_BAYAR),0) 
						FROM KWT_PEMBAYARAN_AI 
						WHERE
							STATUS_BAYAR = 1 AND 
							BAYAR_VIA = '$kode_bank' AND 
							CONVERT(VARCHAR(10), TGL_BAYAR_SYS, 105) = '$tgl_bayar_sys' AND
							AKTIF_IPL = 1 AND 
							AKTIF_AIR = 0 
					),
					CONVERT(DATETIME, '$tgl_bayar_sys', 105), 
					
					'$sess_id_user'
				)
				";
				ex_false($conn->Execute($query), $query);
				
				$query = "
				UPDATE KWT_PEMBAYARAN_AI 
				SET 
					STATUS_POST_BD = 1, 
				
					USER_MODIFIED = '$sess_id_user', 
					MODIFIED_DATE = GETDATE() 
				WHERE 
					STATUS_BAYAR = 1 AND 
					BAYAR_VIA = '$kode_bank' AND
					CONVERT(VARCHAR(10), TGL_BAYAR_SYS, 105) = '$tgl_bayar_sys'
				";
				ex_false($conn->Execute($query), $query);
				
				$cou_bd++;
				$cou_bdt++;
				$ary_bank[$kode_bank]['COU_BD'] = $cou_bd;
				$ary_bank[$kode_bank]['COU_BDT'] = $cou_bdt;
				
				$query = "
				UPDATE KWT_BANK
				SET 
					COU_BD = '$cou_bd', 
					COU_BDT = '$cou_bdt', 
				
					USER_MODIFIED = '$sess_id_user', 
					MODIFIED_DATE = GETDATE() 
				WHERE 
					KODE_BANK = '$kode_bank'
				";
				ex_false($conn->Execute($query), $query);
			}
		}
		
		$conn->committrans();
		
		$msg = 'Proses posting selesai.';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$conn->rollbacktrans();
	}

	close($conn);
	echo $msg;
	exit;
}
?>
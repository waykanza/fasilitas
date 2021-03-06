<?php 
		$query = "
		DECLARE @persen_ppn NUMERIC(5,2) = (SELECT TOP 1 ISNULL(PERSEN_PPN, 0) FROM KWT_PARAMETER)
		
		INSERT INTO KWT_PEMBAYARAN_AI
		(
			ID_PEMBAYARAN,
			TRX,
			
			PERIODE,
			PERIODE_AWAL,
			PERIODE_AKHIR,
			JUMLAH_PERIODE,
			
			NO_PELANGGAN,
			
			KODE_SEKTOR,
			KODE_CLUSTER,
			KODE_BLOK,
			STATUS_BLOK,
			
			LUAS_KAVLING,
			
			JUMLAH_IPL,
			
			PERSEN_PPN
		)
		SELECT 
			((CASE WHEN p.STATUS_BLOK = '2' THEN '3' WHEN p.STATUS_BLOK = '5' THEN '6' END) + '#$periode#' + p.NO_PELANGGAN) AS ID_PEMBAYARAN,
			(CASE WHEN p.STATUS_BLOK = '2' THEN '3' WHEN p.STATUS_BLOK = '5' THEN '6' END) AS TRX,
			
			'$periode' AS PERIODE,
			d.PERIODE_AWAL AS PERIODE_AWAL,
			d.PERIODE_AKHIR AS PERIODE_AKHIR,
			ISNULL(d.JUMLAH_PERIODE, 1) AS JUMLAH_PERIODE,
			
			p.NO_PELANGGAN,
			
			p.KODE_SEKTOR,
			p.KODE_CLUSTER,
			p.KODE_BLOK,
			p.STATUS_BLOK,
			
			ISNULL(p.LUAS_KAVLING, 0) AS LUAS_KAVLING,
			
			d.NILAI_DEPOSIT AS JUMLAH_IPL,
			
			@persen_ppn AS PERSEN_PPN
		FROM 
			KWT_PELANGGAN p
			LEFT JOIN KWT_PERIODE_DEPOSIT d ON p.KODE_BLOK = d.KODE_BLOK 
			LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
			LEFT JOIN KWT_TIPE_IPL t ON i.KODE_TIPE = t.KODE_TIPE
		WHERE
			p.DISABLED IS NULL AND 
			d.STATUS_PROSES IS NULL AND
			d.PERIODE_AWAL = '$periode' AND 
			d.NILAI_DEPOSIT > 0 AND 
			p.STATUS_BLOK IN ('2', '5') AND
			
			((CASE WHEN p.STATUS_BLOK = '2' THEN '3' WHEN p.STATUS_BLOK = '5' THEN '6' END) + '#$periode#' + p.NO_PELANGGAN) NOT IN 
				(
					SELECT ID_PEMBAYARAN 
					FROM KWT_PEMBAYARAN_AI 
					WHERE 
						PERIODE = '$periode' AND 
						TRX = (CASE WHEN p.STATUS_BLOK = '2' THEN '3' WHEN p.STATUS_BLOK = '5' THEN '6' END)
				)
				
			$where_single_blok
		";
		
		ex_false($conn->Execute($query), "Error Insert Save Deposit, Hubungi Developer/MSI !!");
		
		# UPDATE STATUS PROSES
		$query = "
		UPDATE d 
			SET d.STATUS_PROSES = '1'
		FROM 
			KWT_PELANGGAN p
			LEFT JOIN KWT_PERIODE_DEPOSIT d ON p.KODE_BLOK = d.KODE_BLOK 
			LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
			LEFT JOIN KWT_TIPE_IPL t ON i.KODE_TIPE = t.KODE_TIPE
		WHERE
			p.DISABLED IS NULL AND 
			d.STATUS_PROSES IS NULL AND
			d.PERIODE_AWAL = '$periode' AND 
			p.STATUS_BLOK IN ('2', '5')
			
			$where_single_blok
		";
			
		ex_false($conn->Execute($query), "Error Insert Save Deposit, Hubungi Developer/MSI !!");				
?>	
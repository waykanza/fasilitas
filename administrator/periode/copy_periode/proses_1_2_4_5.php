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
			KODE_ZONA,
			
			AKTIF_AIR,
			AKTIF_IPL,
			
			KEY_AIR,
			KEY_IPL,
			
			STAND_AKHIR,
			STAND_LALU,
			
			LUAS_KAVLING,
			TARIF_IPL,
			JUMLAH_IPL,
			
			DISKON_RUPIAH_IPL,
			TGL_DISKON_IPL,
			USER_DISKON_IPL,
			
			PERSEN_PPN
		)
		SELECT 
			p.STATUS_BLOK + '#$periode#' + p.NO_PELANGGAN AS ID_PEMBAYARAN,
			p.STATUS_BLOK AS TRX,
			
			'$periode' AS PERIODE,
			'$periode_awal' AS PERIODE_AWAL,
			'$periode_akhir' AS PERIODE_AKHIR,
			'$jumlah_periode' AS JUMLAH_PERIODE,
			
			p.NO_PELANGGAN,
			
			p.KODE_SEKTOR,
			p.KODE_CLUSTER,
			p.KODE_BLOK,
			p.STATUS_BLOK,
			(CASE WHEN p.AKTIF_AIR = '1' THEN p.KODE_ZONA END) AS KODE_ZONA,
			
			p.AKTIF_AIR AS AKTIF_AIR,
			p.AKTIF_IPL AS AKTIF_IPL,
			
			(CASE WHEN p.AKTIF_AIR = '1' THEN p.KEY_AIR END) AS KEY_AIR,
			(CASE WHEN p.AKTIF_IPL = '1' THEN p.KEY_IPL END) AS KEY_IPL,
			
			0 AS STAND_AKHIR,
			(CASE WHEN p.AKTIF_AIR = '1' THEN ISNULL(b.STAND_AKHIR, 0) ELSE 0 END) AS STAND_LALU,
			
			ISNULL(p.LUAS_KAVLING, 0) AS LUAS_KAVLING,
			ISNULL(i.TARIF_IPL, 0) AS TARIF_IPL,
			(
				CASE WHEN p.AKTIF_IPL = '1'
				THEN
					CASE WHEN i.TIPE_TARIF_IPL = '1'
					THEN 
						(ISNULL(p.LUAS_KAVLING, 0) * ISNULL(i.TARIF_IPL, 0)) * $jumlah_periode
					ELSE 
						ISNULL(i.TARIF_IPL, 0) * $jumlah_periode
					END
				ELSE 0 END
			) AS JUMLAH_IPL,
			
			$diskon_rupiah_ipl AS DISKON_RUPIAH_IPL,
			$tgl_diskon_ipl AS TGL_DISKON_IPL,
			$user_diskon_ipl AS USER_DISKON_IPL,
			
			@persen_ppn AS PERSEN_PPN
		FROM 
			KWT_PELANGGAN p
			LEFT JOIN KWT_PEMBAYARAN_AI b ON p.NO_PELANGGAN = b.NO_PELANGGAN AND 
				b.TRX IN ('1', '2', '4', '5') AND 
				b.PERIODE = '$periode_prev' AND 
				b.AKTIF_AIR = '1'
			LEFT JOIN KWT_TARIF_IPL i ON p.KEY_IPL = i.KEY_IPL
		WHERE
			p.DISABLED IS NULL AND 
			(p.AKTIF_AIR = '1' OR p.AKTIF_IPL = '1') AND 
			
			p.STATUS_BLOK IN ('1', '2', '4', '5') AND 
			
			$periode_akhir > 
			(
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
				ELSE 0 END
			) AND
			(p.STATUS_BLOK + '#$periode#' + p.NO_PELANGGAN) NOT IN 
				(
					SELECT ID_PEMBAYARAN 
					FROM KWT_PEMBAYARAN_AI 
					WHERE 
						PERIODE = '$periode' AND 
						TRX = p.STATUS_BLOK
				)
			$where_single_blok
			
		ORDER BY p.KODE_BLOK ASC
		";
		
		ex_false($conn->Execute($query), "Error Insert Air & IPL 1,2,4,5, Hubungi Developer/MSI !!$query");
<?php
require_once('../../../config/config.php');

$conn = conn();
$msg = '';
$error = FALSE;

$periode = (isset($_REQUEST['periode'])) ? to_periode($_REQUEST['periode']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		ex_empty($periode, 'Masukkan periode!');
		
		$query = "
		SELECT 
			COUNT(b.ID_PEMBAYARAN) AS TOTAL
		FROM 
			KWT_PEMBAYARAN_AI b
		WHERE
			b.PERIODE = '$periode' AND 
			b.STATUS_BAYAR = '2' AND
			b.TGL_POST_FP IS NULL
		";
		ex_notfound($conn->Execute($query)->fields['TOTAL'], "Data pembayaran tidak tidemukan.");

		$tgl_faktur_pajak = date('m-d-Y');
		
		$query = "
		DECLARE 
		@reg_fp VARCHAR(20),
		@cou_fp INT,
		@persen_ppn NUMERIC(5,2),
		@tgl_faktur_pajak DATE = CAST('$tgl_faktur_pajak' AS DATE),
		@id_pembayaran VARCHAR(50)
		
		SELECT TOP 1 
			@reg_fp = ISNULL(REG_FP, ''),
			@cou_fp = ISNULL(COU_FP, 0),
			@persen_ppn = ISNULL(PERSEN_PPN, 0)
		FROM KWT_PARAMETER
		
		DECLARE curid CURSOR LOCAL FOR 
		(
			SELECT ID_PEMBAYARAN
			FROM KWT_PEMBAYARAN_AI 
			WHERE
				PERIODE = '$periode' AND 
				STATUS_BAYAR = '2' AND
				TGL_POST_FP IS NULL
		)

		OPEN curid
		FETCH NEXT FROM curid INTO @id_pembayaran
		WHILE @@FETCH_STATUS = 0
		BEGIN
			SET @cou_fp = @cou_fp + 1
			
			UPDATE KWT_PEMBAYARAN_AI
			SET
				NO_FAKTUR_PAJAK = @reg_fp + CAST(@cou_fp AS VARCHAR(10)), 
				TGL_FAKTUR_PAJAK = @tgl_faktur_pajak,
				PERSEN_PPN = @persen_ppn,
				NILAI_PPN = ((JUMLAH_BAYAR - ADMINISTRASI - DENDA) * (@persen_ppn / (100 + @persen_ppn)))
			WHERE
				ID_PEMBAYARAN = @id_pembayaran
				
			FETCH NEXT FROM curid INTO @id_pembayaran
		END

		CLOSE curid
		DEALLOCATE curid
			
		UPDATE KWT_PARAMETER SET COU_FP = @cou_fp";

		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'No faktur pajak berhasil diproses.'.$query;
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
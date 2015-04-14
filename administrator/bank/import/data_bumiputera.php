<?php
$xls = new Spreadsheet_Excel_Reader($uploaded_file, false);
$baris = $xls->rowcount($sheet_index=0);

$x_tgl_bayar_bank = clean($xls->val(2, 2));

$spl = explode('-', $x_tgl_bayar_bank);
if ((count($spl) != 3) || ( ! checkdate($spl[1], $spl[0], $spl[2])))
{
	echo '<tr><td colspan="14">Error. Format tanggal tidak valid. '.$x_tgl_bayar_bank.'</td></tr>';
	exit;
}

$tbb = $spl[0].'-'.$spl[1].'-'.$spl[2].' 00:00:00';

# LIST KODE_BLOK
$q_kode_blok = array();
for ($ix = 4; $ix <= $baris; $ix++)
{
	$x_kode_blok = (string) clean($xls->val($ix, 2));
	
	if ($x_kode_blok != "")
	{
		$q_kode_blok[] = $x_kode_blok;
	}
}

# LIST NO_PELANGGAN
$in_q_kode_blok = implode("', '", $q_kode_blok);
$query = "
SELECT 
	KODE_BLOK, NO_PELANGGAN
FROM
	KWT_PELANGGAN 
WHERE
	KODE_BLOK IN ('$in_q_kode_blok') 
";

$q_np = array();
$obj = $conn->Execute($query);
while( ! $obj->EOF)
{
	$q_kb = $obj->fields['KODE_BLOK'];
	$q_np["$q_kb"] = $obj->fields['NO_PELANGGAN'];
	
	$obj->movenext();
}

# GET VALUES
for ($ix = 4; $ix <= $baris; $ix++)
{
	$x_kode_blok = (string) clean($xls->val($ix, 2));
	
	if ($x_kode_blok != "")
	{
		$qg_np = (isset($q_np["$x_kode_blok"])) ? $q_np["$x_kode_blok"] : "-";
		$list[$ix] = array(
			'np' => $qg_np,
			'jb' => to_decimal(xls_clean_number($xls->val($ix, 6))),
		);
	}
}

$in_no_pelanggan = array();
$data_imp = array();

foreach ($list as $x)
{
	$np				= (string) $x['np'];
	$jb				= (int) $x['jb'];

	$in_no_pelanggan[]		= $np;
	$data_imp["$np"]['jb']	= $jb;
	$data_imp["$np"]['tbb'] = $tbb;
}

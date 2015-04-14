<?php
require_once('../../../config/config.php');
$conn = conn();
$msg = '';
$error = FALSE;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	try
	{
		$conn->begintrans();
		
		$jrp_pt					= (isset($_REQUEST['jrp_pt'])) ? clean($_REQUEST['jrp_pt']) : '';
		$jrp_alamat_1			= (isset($_REQUEST['jrp_alamat_1'])) ? clean($_REQUEST['jrp_alamat_1']) : '';
		$jrp_alamat_2			= (isset($_REQUEST['jrp_alamat_2'])) ? clean($_REQUEST['jrp_alamat_2']) : '';
		$jrp_kota				= (isset($_REQUEST['jrp_kota'])) ? clean($_REQUEST['jrp_kota']) : '';
		$jrp_telp				= (isset($_REQUEST['jrp_telp'])) ? clean($_REQUEST['jrp_telp']) : '';
		$jrp_kode_pos			= (isset($_REQUEST['jrp_kode_pos'])) ? clean($_REQUEST['jrp_kode_pos']) : '';
		$jrp_fax				= (isset($_REQUEST['jrp_fax'])) ? clean($_REQUEST['jrp_fax']) : '';
		$jrp_email				= (isset($_REQUEST['jrp_email'])) ? clean($_REQUEST['jrp_email']) : '';
		
		$unit_nama				= (isset($_REQUEST['unit_nama'])) ? clean($_REQUEST['unit_nama']) : '';
		$unit_alamat_1			= (isset($_REQUEST['unit_alamat_1'])) ? clean($_REQUEST['unit_alamat_1']) : '';
		$unit_alamat_2			= (isset($_REQUEST['unit_alamat_2'])) ? clean($_REQUEST['unit_alamat_2']) : '';
		$unit_kota				= (isset($_REQUEST['unit_kota'])) ? clean($_REQUEST['unit_kota']) : '';
		$unit_telp				= (isset($_REQUEST['unit_telp'])) ? clean($_REQUEST['unit_telp']) : '';
		$unit_kode_pos			= (isset($_REQUEST['unit_kode_pos'])) ? clean($_REQUEST['unit_kode_pos']) : '';
		$unit_fax				= (isset($_REQUEST['unit_fax'])) ? clean($_REQUEST['unit_fax']) : '';
		$unit_email				= (isset($_REQUEST['unit_email'])) ? clean($_REQUEST['unit_email']) : '';
		
		$nama_pimpinan			= (isset($_REQUEST['nama_pimpinan'])) ? clean($_REQUEST['nama_pimpinan']) : '';
		$jbt_pimpinan			= (isset($_REQUEST['jbt_pimpinan'])) ? clean($_REQUEST['jbt_pimpinan']) : '';
		$nama_pajak				= (isset($_REQUEST['nama_pajak'])) ? clean($_REQUEST['nama_pajak']) : '';
		$jbt_pajak				= (isset($_REQUEST['jbt_pajak'])) ? clean($_REQUEST['jbt_pajak']) : '';
		$nama_administrasi		= (isset($_REQUEST['nama_administrasi'])) ? clean($_REQUEST['nama_administrasi']) : '';
		$jbt_administrasi		= (isset($_REQUEST['jbt_administrasi'])) ? clean($_REQUEST['jbt_administrasi']) : '';
		
		$cou_np					= (isset($_REQUEST['cou_np'])) ? to_number($_REQUEST['cou_np']) : '';
		$reg_npwp				= (isset($_REQUEST['reg_npwp'])) ? clean($_REQUEST['reg_npwp']) : '';
		$persen_ppn				= (isset($_REQUEST['persen_ppn'])) ? to_decimal($_REQUEST['persen_ppn']) : '';
		$reg_fp					= (isset($_REQUEST['reg_fp'])) ? clean($_REQUEST['reg_fp']) : '';
		$cou_fp					= (isset($_REQUEST['cou_fp'])) ? to_number($_REQUEST['cou_fp']) : '';
		
		$administrasi_kv		= (isset($_REQUEST['administrasi_kv'])) ? to_number($_REQUEST['administrasi_kv']) : '';
		$reg_ivc_kv				= (isset($_REQUEST['reg_ivc_kv'])) ? clean($_REQUEST['reg_ivc_kv']) : '';
		$cou_ivc_kv				= (isset($_REQUEST['cou_ivc_kv'])) ? to_number($_REQUEST['cou_ivc_kv']) : '';
		$reg_kwt_kv				= (isset($_REQUEST['reg_kwt_kv'])) ? clean($_REQUEST['reg_kwt_kv']) : '';
		$cou_kwt_kv				= (isset($_REQUEST['cou_kwt_kv'])) ? to_number($_REQUEST['cou_kwt_kv']) : '';
		
		$administrasi_bg		= (isset($_REQUEST['administrasi_bg'])) ? to_number($_REQUEST['administrasi_bg']) : '';
		$reg_ivc_bg				= (isset($_REQUEST['reg_ivc_bg'])) ? clean($_REQUEST['reg_ivc_bg']) : '';
		$cou_ivc_bg				= (isset($_REQUEST['cou_ivc_bg'])) ? to_number($_REQUEST['cou_ivc_bg']) : '';
		$reg_kwt_bg				= (isset($_REQUEST['reg_kwt_bg'])) ? clean($_REQUEST['reg_kwt_bg']) : '';
		$cou_kwt_bg				= (isset($_REQUEST['cou_kwt_bg'])) ? to_number($_REQUEST['cou_kwt_bg']) : '';
		
		$administrasi_db		= (isset($_REQUEST['administrasi_db'])) ? to_number($_REQUEST['administrasi_db']) : '';
		$reg_ivc_db				= (isset($_REQUEST['reg_ivc_db'])) ? clean($_REQUEST['reg_ivc_db']) : '';
		$cou_ivc_db				= (isset($_REQUEST['cou_ivc_db'])) ? to_number($_REQUEST['cou_ivc_db']) : '';
		$reg_kwt_db				= (isset($_REQUEST['reg_kwt_db'])) ? clean($_REQUEST['reg_kwt_db']) : '';
		$cou_kwt_db				= (isset($_REQUEST['cou_kwt_db'])) ? to_number($_REQUEST['cou_kwt_db']) : '';
		
		$administrasi_hn		= (isset($_REQUEST['administrasi_hn'])) ? to_number($_REQUEST['administrasi_hn']) : '';
		$cou_ivc_hn				= (isset($_REQUEST['cou_ivc_hn'])) ? to_number($_REQUEST['cou_ivc_hn']) : '';
		$reg_ivc_hn				= (isset($_REQUEST['reg_ivc_hn'])) ? clean($_REQUEST['reg_ivc_hn']) : '';
		$cou_kwt_hn				= (isset($_REQUEST['cou_kwt_hn'])) ? to_number($_REQUEST['cou_kwt_hn']) : '';
		$reg_kwt_hn				= (isset($_REQUEST['reg_kwt_hn'])) ? clean($_REQUEST['reg_kwt_hn']) : '';
		
		$administrasi_rv		= (isset($_REQUEST['administrasi_rv'])) ? to_number($_REQUEST['administrasi_rv']) : '';
		$reg_ivc_rv				= (isset($_REQUEST['reg_ivc_rv'])) ? clean($_REQUEST['reg_ivc_rv']) : '';
		$cou_ivc_rv				= (isset($_REQUEST['cou_ivc_rv'])) ? to_number($_REQUEST['cou_ivc_rv']) : '';
		$reg_kwt_rv				= (isset($_REQUEST['reg_kwt_rv'])) ? clean($_REQUEST['reg_kwt_rv']) : '';
		$cou_kwt_rv				= (isset($_REQUEST['cou_kwt_rv'])) ? to_number($_REQUEST['cou_kwt_rv']) : '';
		
		$administrasi_dr		= (isset($_REQUEST['administrasi_dr'])) ? to_number($_REQUEST['administrasi_dr']) : '';
		$reg_ivc_dr				= (isset($_REQUEST['reg_ivc_dr'])) ? clean($_REQUEST['reg_ivc_dr']) : '';
		$cou_ivc_dr				= (isset($_REQUEST['cou_ivc_dr'])) ? to_number($_REQUEST['cou_ivc_dr']) : '';
		$reg_kwt_dr				= (isset($_REQUEST['reg_kwt_dr'])) ? clean($_REQUEST['reg_kwt_dr']) : '';
		$cou_kwt_dr				= (isset($_REQUEST['cou_kwt_dr'])) ? to_number($_REQUEST['cou_kwt_dr']) : '';
		
		$conn->Execute("DELETE FROM KWT_PARAMETER");
		
		$query = "
		INSERT INTO KWT_PARAMETER (
			JRP_PT,
			JRP_ALAMAT_1,
			JRP_ALAMAT_2,
			JRP_KOTA,
			JRP_KODE_POS,
			JRP_TELP,
			JRP_FAX,
			JRP_EMAIL,
			
			UNIT_NAMA,
			UNIT_ALAMAT_1,
			UNIT_ALAMAT_2,
			UNIT_KOTA,
			UNIT_KODE_POS,
			UNIT_TELP,
			UNIT_FAX,
			UNIT_EMAIL,
			
			NAMA_PIMPINAN, JBT_PIMPINAN, 
			NAMA_PAJAK, JBT_PAJAK, 
			NAMA_ADMINISTRASI, JBT_ADMINISTRASI, 
			
			COU_NP,
			REG_NPWP,
			PERSEN_PPN,
			REG_FP,
			COU_FP,
			
			ADMINISTRASI_KV,
			REG_IVC_KV,
			COU_IVC_KV,
			REG_KWT_KV,
			COU_KWT_KV,
			
			ADMINISTRASI_BG,
			REG_IVC_BG,
			COU_IVC_BG,
			REG_KWT_BG,
			COU_KWT_BG,
			
			ADMINISTRASI_DB,
			REG_IVC_DB,
			COU_IVC_DB,
			REG_KWT_DB,
			COU_KWT_DB,
			
			ADMINISTRASI_HN,
			REG_IVC_HN,
			COU_IVC_HN,
			REG_KWT_HN,
			COU_KWT_HN,
			
			ADMINISTRASI_RV,
			REG_IVC_RV,
			COU_IVC_RV,
			REG_KWT_RV,
			COU_KWT_RV,
			
			ADMINISTRASI_DR,
			REG_IVC_DR,
			COU_IVC_DR,
			REG_KWT_DR,
			COU_KWT_DR
		)
		VALUES (
			'$jrp_pt',
			'$jrp_alamat_1',
			'$jrp_alamat_2',
			'$jrp_kota',
			'$jrp_kode_pos',
			'$jrp_telp',
			'$jrp_fax',
			'$jrp_email',
			
			'$unit_nama',
			'$unit_alamat_1',
			'$unit_alamat_2',
			'$unit_kota',
			'$unit_kode_pos',
			'$unit_telp',
			'$unit_fax',
			'$unit_email',
			
			'$nama_pimpinan', '$jbt_pimpinan', 
			'$nama_pajak', '$jbt_pajak', 
			'$nama_administrasi', '$jbt_administrasi', 
			
			'$cou_np',
			'$reg_npwp',
			'$persen_ppn',
			'$reg_fp',
			'$cou_fp',	
			
			'$administrasi_kv',
			'$reg_ivc_kv',
			'$cou_ivc_kv',
			'$reg_kwt_kv',
			'$cou_kwt_kv',
			
			'$administrasi_bg',
			'$reg_ivc_bg',
			'$cou_ivc_bg',
			'$reg_kwt_bg',
			'$cou_kwt_bg',
			
			'$administrasi_db',
			'$reg_ivc_db',
			'$cou_ivc_db',
			'$reg_kwt_db',
			'$cou_kwt_db',
			
			'$administrasi_hn',
			'$reg_ivc_hn',
			'$cou_ivc_hn',
			'$reg_kwt_hn',
			'$cou_kwt_hn',
			
			'$administrasi_rv',
			'$reg_ivc_rv',
			'$cou_ivc_rv',
			'$reg_kwt_rv',
			'$cou_kwt_rv',
			
			'$administrasi_dr',
			'$reg_ivc_dr',
			'$cou_ivc_dr',
			'$reg_kwt_dr',
			'$cou_kwt_dr'
		)
		";
		
		ex_false($conn->Execute($query), $query);
		
		$conn->committrans();
		
		$msg = 'Parameter berhasil diubah.';
	}
	catch(Exception $e)
	{
		$msg = $e->getmessage();
		$error = TRUE;
		$conn->rollbacktrans();
	}

	close($conn);
	$json = array('msg' => $msg, 'error'=> $error);
	echo json_encode($json);
	exit;
}
?>
<?php
require_once('user_management_proses.php');
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="../../plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="../../plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="../../plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	key('alt+s', function(e) { e.preventDefault(); $('#save').trigger('click'); });
	key('alt+r', function(e) { e.preventDefault(); $('#reset').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#id_user').inputmask('varchar', { repeat: '6' });
	$('#nama_user').inputmask('varchar', { repeat: '40' });
	$('#aktif_user').inputmask('varchar', { repeat: '2' });
	
	$('#close').on('click', function(e) {
		e.preventDefault();
		return parent.loadData();
	});
	
	$('#save').on('click', function(e) {
		e.preventDefault();
		var url		= base_adm + 'user_management/user_management_proses.php',
			data	= $('#form').serialize();
			
		$.post(url, data, function(data) {
			
			alert(data.msg);
			if (data.error == false) {
				if (data.act == 'Simpan') {
					$('#reset').click();
				} else if (data.act == 'Ubah') {
					parent.loadData();
				}
			}
		}, 'json');
		
		return false;
	});
});
</script>
</head>
<body class="popup">


<form name="form" id="form" method="post">
<table class="t-popup">
<tr>
	<td width="130">ID</td>
	<td><input type="text" name="id_user" id="id_user" size="8" value="<?php echo $id_user; ?>"></td>
</tr>
<tr>
	<td>PASSWORD</td>
	<td><input type="password" name="pass_user" id="pass_user" size="25" value=""></td>
</tr>
<tr>
	<td>CONF. PASSWORD</td>
	<td><input type="password" name="conf_pass_user" id="conf_pass_user" size="25" value=""></td>
</tr>
<tr>
	<td>NAMA</td>
	<td><input type="text" name="nama_user" id="nama_user" size="50" value="<?php echo $nama_user; ?>"></td>
</tr>
<tr>
	<td></td>
	<td>
		<input type="radio" name="aktif_user" id="auy" <?php echo is_checked('1', $aktif_user); ?> value="1"> <label for="auy">AKTIF</label>
		<input type="radio" name="aktif_user" id="aun" <?php echo is_checked('0', $aktif_user); ?> value="0"> <label for="aun">TIDAK AKTIF</label>
	</td>
</tr>

<tr>
	<td class="va-top"><br><br>MODUL</td>
	<td class="va-top"><br>
		<?php 
		$list_modul = array(
			'Master' => array(
				'101' => 'Parameter',
				'102' => 'Sektor',
				'103' => 'Cluster',
				'104' => 'Blok',
				'105' => 'Bank',
				'106' => 'Diskon Khusus',
				'107' => 'Rumah Pompa',
				
				'111' => 'SK Air',
				'112' => 'Katerori Air',
				'113' => 'Tarif Air',
				
				'121' => 'SK IPL',
				'122' => 'Katerori IPL',
				'123' => 'Tarif IPL',
				
				'131' => 'Pelanggan',
				'132' => 'Pelanggan Contact',
				'133' => 'Pelanggan Aktif Air & IPL',
				'134' => 'Pelanggan Baru'
			),
			'Proses Tagihan' => array(
				'201' => 'Periode Masa Membangun',
				'202' => 'Periode Renovasi',
				'203' => 'Prores Tagihan',
				'204' => 'Export Stand Meter',
				'205' => 'Import Stand Meter',
				'206' => 'Hitung Denda'
			),
			'Transaksi Bank' => array(
				'301' => 'Export',
				'302' => 'Import'
			),
			'Pembayaran' => array(
				'401' => 'Pembayaran (AI)',
				'402' => 'Pembatalan (AI)',
				'403' => 'Discount (AI)',
				'404' => 'Pelanggan Baru (AI)',
				'405' => 'Edit Stand Meter',
				'411' => 'Pembayaran (DP)',
				'412' => 'Pembatalan (DP)',
				'413' => 'Discount (DP)'
			),
			'Laporan' => array(
				'501' => 'Rincian Rencana (AI)',
				'502' => 'Rencana Rekap (AI)',
				'503' => 'Penerimaan Rincian (AI)',
				'504' => 'Penerimaan Rekap (AI)',
				'505' => 'Piutang Rincian (AI)',
				'506' => 'Piutang Rekap (AI)',
				'507' => 'Umur Piutang (AI)',
				'508' => 'Pemutusan Air',
				'509' => 'Pemakaian Air',
				
				'511' => 'Rencana Rincian (DP)',
				'512' => 'Rencana Rekap (DP)',
				'513' => 'Penerimaan Rincian (DP)',
				'514' => 'Penerimaan Rekap (DP)',
				'515' => 'Piutang Rincian (DP)',
				'516' => 'Piutang Rekap (DP)',
				'517' => 'Umur Piutang (DP)',
				
				'521' => 'Rincian Pelanggan',
				'522' => 'Rekap Pelanggan',
				'523' => 'Daftar Pelanggan',
				'524' => 'Daftar Faktur Pajak'
			),
			
			'Utilitas' => array(
				'601' => 'Invoice Air & IPL',
				'602' => 'Invoice Deposit',
				
				'611' => 'Posting Pembayaran',
				'612' => 'Faktur Pajak Penomoran',
				'613' => 'Faktur Pajak Cetak',
				'614' => 'Faktur Pajak Posting',
				
				'621' => 'User Management',
				'622' => 'User Log'
			)
		);
		
		echo '<table class="wauto">';
		foreach ($list_modul AS $mdl => $mn)
		{
			echo '<tr><td><br><b>' . $mdl . '</b><hr></td></tr>';
			echo '<tr><td class="va-top">';
			
			$t = 0;
			foreach ($mn AS $idm => $nmm)
			{
				if ($t % 4 == 0 AND $t != 0) {
					echo '</td><td class="va-top">';
				}
				
				$chk = '';
				if (in_array($idm, $list_aktif_modul)) {
					$chk = 'checked="checked"';
				}
				
				echo "<input type='checkbox' name='id_modul_ary[]' value='$idm' id='$idm' $chk > <label for='$idm'>$nmm</label>&nbsp;<br>";
				
				$t++;
			}
			echo '</td></tr>';
		}
		echo '</table>';
		?>
	</td>
</tr>

<tr>
	<td></td>
	<td class="td-action">
		<input type="submit" id="save" value=" <?php echo $act; ?> (Alt+S) ">
		<input type="reset" id="reset" value=" Reset (Alt+R) ">
		<input type="button" id="close" value=" Tutup (Esc) "></td>
	</td>
</tr>
</table>

<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
<input type="hidden" name="act" id="act" value="<?php echo $act; ?>">
</form>

</body>
</html>
<?php close($conn); ?>
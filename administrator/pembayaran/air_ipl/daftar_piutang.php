<?php
require_once('../../../config/config.php');
die_login();
$conn = conn();
die_conn($conn);


$no_pelanggan = (isset($_REQUEST['no_pelanggan'])) ? clean($_REQUEST['no_pelanggan']) : '';
$from_master = (isset($_REQUEST['from_master'])) ? clean($_REQUEST['from_master']) : '';
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<!-- CSS -->
<link type="text/css" href="../../../config/css/style.css" rel="stylesheet">
<link type="text/css" href="../../../plugin/css/zebra/default.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="../../../plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="../../../plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="../../../plugin/js/keymaster.js"></script>
<script type="text/javascript" src="../../../plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="../../../config/js/main.js"></script>
<script type="text/javascript">
jQuery(function($) {
	
	function to_decimal(v) {
		v = v.replace(/[^0-9.]/g, '');
		v = (v == '') ? 0 : parseFloat(v);
		return v;
	}
	
	/* -- SHORTCUT -- */
	$('.diskon_air_rupiah, .diskon_ipl_rupiah').inputmask('numeric', { repeat: '10' });
	$('.diskon_air_persen, .diskon_ipl_persen, #all_cb_diskon_air_persen, #all_cb_diskon_ipl_persen').inputmask('percent100');
	
	// Change One Persen
	$('.diskon_air_persen').on('change', function(e) {
		var jumlah_air = 0,
			diskon_air_persen = 0,
			diskon_air_rupiah = 0,
			closest;
		
		closest = $(this).closest('tr').find('.diskon_air_rupiah');
			
		diskon_air_persen = to_decimal($(this).val());
		jumlah_air = closest.data('jumlah_air');
		diskon_air_rupiah = (jumlah_air * (diskon_air_persen / 100).toFixed(2)).toFixed(0);
		
		if (diskon_air_rupiah > jumlah_air) {
			$(this).val('0');
			closest.val('0');
		} else {
			closest.val(diskon_air_rupiah);
		}
	});
	
	$('.diskon_ipl_persen').on('change', function(e) {
		var jumlah_ipl = 0,
			diskon_ipl_persen = 0,
			diskon_ipl_rupiah = 0,
			closest;
		
		closest = $(this).closest('tr').find('.diskon_ipl_rupiah');
			
		diskon_ipl_persen = to_decimal($(this).val());
		jumlah_ipl = closest.data('jumlah_ipl');
		diskon_ipl_rupiah = (jumlah_ipl * (diskon_ipl_persen / 100).toFixed(2)).toFixed(0);
		
		if (diskon_ipl_rupiah > jumlah_ipl) {
			$(this).val('0');
			closest.val('0');
		} else {
			closest.val(diskon_ipl_rupiah);
		}
	});
	
	// Change One Rupiah
	$('.diskon_air_rupiah').on('change', function(e) {
		var jumlah_air = 0,
			diskon_air_persen = 0,
			diskon_air_rupiah = 0,
			closest;
		
		closest = $(this).closest('tr').find('.diskon_air_persen');
			
		jumlah_air = $(this).data('jumlah_air');
		diskon_air_rupiah = to_decimal($(this).val());
		diskon_air_persen = 100 * (diskon_air_rupiah / jumlah_air);
		
		if (diskon_air_rupiah > jumlah_air) {
			$(this).val('0');
			closest.val('0');
		} else {
			closest.val(diskon_air_persen);
		}
	});
	
	$('.diskon_ipl_rupiah').on('change', function(e) {
		var jumlah_ipl = 0,
			diskon_ipl_persen = 0,
			diskon_ipl_rupiah = 0,
			closest;
		
		closest = $(this).closest('tr').find('.diskon_ipl_persen');
			
		jumlah_ipl = $(this).data('jumlah_ipl');
		diskon_ipl_rupiah = to_decimal($(this).val());
		diskon_ipl_persen = 100 * (diskon_ipl_rupiah / jumlah_ipl);
		
		if (diskon_ipl_rupiah > jumlah_ipl) {
			$(this).val('0');
			closest.val('0');
		} else {
			closest.val(diskon_ipl_persen);
		}
	});
	
	
	// Change All
	$('#all_cb_diskon_air_persen').on('change', function(e) {
		$('.diskon_air_persen').val($(this).val());
		
		all_cb_diskon_air_persen();
	});
	$('#all_cb_diskon_ipl_persen').on('change', function(e) {
		$('.diskon_ipl_persen').val($(this).val());
		
		all_cb_diskon_ipl_persen();
	});
	
	// Change All Function
	function all_cb_diskon_air_persen() {
		var jumlah_air = 0,
			diskon_air_persen = 0,
			diskon_air_rupiah = 0,
			closest;
		
		$('.diskon_air_persen').each(function() {
			closest = $(this).closest('tr').find('.diskon_air_rupiah');
			
			diskon_air_persen = to_decimal($(this).val());
			jumlah_air = closest.data('jumlah_air');
			diskon_air_rupiah = (jumlah_air * (diskon_air_persen / 100).toFixed(2)).toFixed(0);
			
			if (diskon_air_rupiah > jumlah_air) {
				$(this).val('0');
				closest.val('0');
			} else {
				closest.val(diskon_air_rupiah);
			}
			
		});
	}
	
	function all_cb_diskon_ipl_persen() {
		var jumlah_ipl = 0,
			diskon_ipl_persen = 0,
			diskon_ipl_rupiah = 0,
			closest;
		
		$('.diskon_ipl_persen').each(function() {
			closest = $(this).closest('tr').find('.diskon_ipl_rupiah');
			
			diskon_ipl_persen = to_decimal($(this).val());
			jumlah_ipl = closest.data('jumlah_ipl');
			diskon_ipl_rupiah = (jumlah_ipl * (diskon_ipl_persen / 100).toFixed(2)).toFixed(0);
			
			if (diskon_ipl_rupiah > jumlah_ipl) {
				$(this).val('0');
				closest.val('0');
			} else {
				closest.val(diskon_ipl_rupiah);
			}
			
		});
	}
	
	key('alt+p', function(e) { e.preventDefault(); $('#print').trigger('click'); });
	key('esc', function(e) { e.preventDefault(); $('#close').trigger('click'); });
	
	$('#print').on('click', function(e) {
		e.preventDefault();
		var url = base_pembayaran + 'air_ipl/daftar_piutang_cetak.php?id=<?php echo base64_encode($no_pelanggan); ?>';
		open_print(url, '2');
		
		return false;
	});
	
	$('#close').on('click', function(e) {
		parent.window.focus();
		parent.window.popup.close();
	});
	
	$('#save_diskon').on('click', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length,
			cara_bayar	= $('#cara_bayar').val();
			
		if (checked < 1) {
			alert('Pilih tagihan yang akan diproses.');
			return false;
		}
		
		$('#act').val('save_diskon');
		do_process();
		
		return false;
	});
	
	$('#bayar').on('click', function(e) {
		e.preventDefault();
		var checked = $(".cb_data:checked").length,
			cara_bayar	= $('#cara_bayar').val();
			
		if (checked < 1) {
			alert('Pilih tagihan yang akan dibayar.');
			return false;
		} else if (cara_bayar == '') {
			alert('Pilih jenis pembayaran.');
			return false;
		}
		
		$('#act').val('pembayaran');
		do_process();
		
		return false;
	});
	
	function do_process() {
		var url		= base_pembayaran + 'air_ipl/pembayaran_proses_bulk.php',
			data	= $('#form').serialize(),
			cek		= false;
		
		$.post(url, data, function(data) {
			
			alert(data.msg);
			
			if (data.error == false) {
				if (data.act == 'pembayaran') {
					var url = base_pembayaran + 'air_ipl/cetak_kwitansi.php?idp=' + data.list_idp;
					open_print(url, '2');
					location.reload();
				} else if (data.act == 'save_diskon') {
					location.reload();
				}
			}
			
		}, 'json');
	}
	
	t_strip('.t-data');
	
});
</script>
</head>
<body class="popup" style="padding-left:5px;padding-right:5px">

<form name="form" id="form" method="post">

<table class="t-data">
<tr>
	<th rowspan="3">NO.</th>
	<th rowspan="3">STATUS</th>
	<th colspan="4">PERIODE</th>
	<th colspan="5">STAND METER</th>
	<th rowspan="3">AIR</th>
	<th rowspan="3">ABONEMEN</th>
	<th rowspan="3">IPL</th>
	<th rowspan="3">DENDA</th>
	<th rowspan="3">ADM</th>
	<th colspan="2">DISKON</th>
	<th rowspan="3">JUMLAH</th>
	<?php if ($from_master == '') { ?>
	<th colspan="4">DISKON</th>
	<th rowspan="3">KET.</th>
	<th rowspan="3"><input type="checkbox" id="cb_all"></th>
	<?php } ?>
</tr>
<tr>
	<th rowspan="2">TAG.</th>
	<th rowspan="2">AWAL</th>
	<th rowspan="2">AKHIR</th>
	<th rowspan="2">JML.</th>
	<th rowspan="2">AKHIR</th>
	<th rowspan="2">LALU</th>
	<th rowspan="2">GANTI</th>
	<th rowspan="2">PAKAI</th>
	<th rowspan="2">MIN.</th>
	<th rowspan="2">AIR</th>
	<th rowspan="2">IPL</th>
	<?php if ($from_master == '') { ?>
	<th colspan="2">AIR</th>
	<th colspan="2">IPL</th>
	<?php } ?>
</tr>
<?php if ($from_master == '') { ?>
<tr>
	<th>Rp.</th>
	<th>%<input type="text" id="all_cb_diskon_air_persen" size="3"></th>
	<th>Rp.</th>
	<th>%<input type="text" id="all_cb_diskon_ipl_persen" size="3"></th>
</tr>
<?php } else { ?>
<tr>
	<th colspan="4" class="hidden"></th>
</tr>
<?php } ?>

<?php
	$query = "
	DECLARE 
	@adm_kv INT,
	@adm_bg INT,
	@adm_hn INT,
	@adm_rv INT
	
	SELECT TOP 1 
	@adm_kv = ADM_KV ,
	@adm_bg = ADM_BG ,
	@adm_hn = ADM_HN ,
	@adm_rv = ADM_RV
	FROM KWT_PARAMETER
	
	SELECT 
		b.ID_PEMBAYARAN,
		b.STATUS_BLOK,
		dbo.PTPS(b.PERIODE_TAG) AS PERIODE_TAG,
		dbo.PTPS(b.PERIODE_IPL_AWAL) AS PERIODE_IPL_AWAL,
		dbo.PTPS(b.PERIODE_IPL_AKHIR) AS PERIODE_IPL_AKHIR,
		b.JUMLAH_PERIODE_IPL,
		b.STAND_AKHIR, 
		b.STAND_LALU,
		b.STAND_ANGKAT,
		(b.STAND_AKHIR + b.STAND_ANGKAT - b.STAND_LALU) AS PAKAI,
		b.STAND_MIN_PAKAI,
		b.JUMLAH_AIR,
		b.ABONEMEN,
		b.JUMLAH_IPL,
		b.DENDA,
		(
			CASE TRX
				WHEN $trx_kv THEN @adm_kv
				WHEN $trx_bg THEN @adm_bg 
				WHEN $trx_hn THEN @adm_hn
				WHEN $trx_rv THEN @adm_rv
			END
		) AS ADM,
		b.DISKON_AIR,
		b.DISKON_IPL, 
		(
			b.JUMLAH_AIR + b.ABONEMEN + b.JUMLAH_IPL + b.DENDA + 
			CASE TRX
				WHEN $trx_kv THEN @adm_kv
				WHEN $trx_bg THEN @adm_bg 
				WHEN $trx_hn THEN @adm_hn
				WHEN $trx_rv THEN @adm_rv
			END
			- b.DISKON_AIR - b.DISKON_IPL 
		) AS JUMLAH
	FROM 
		KWT_PEMBAYARAN_AI b
	WHERE
		$where_trx_air_ipl AND 
		b.STATUS_BAYAR = 0 AND
		b.NO_PELANGGAN = '$no_pelanggan'
	ORDER BY CAST(PERIODE_TAG AS INT) ASC
	";
	$obj = $conn->Execute($query);

	$i						= 1;
	$sum_jumlah_air			= 0;
	$sum_abonemen			= 0;
	$sum_jumlah_ipl			= 0;
	$sum_denda				= 0;
	$sum_adm				= 0;
	$sum_diskon_air			= 0;
	$sum_diskon_ipl			= 0;
	$sum_jumlah				= 0;
	
	while( ! $obj->EOF)
	{
		$id = base64_encode($obj->fields['ID_PEMBAYARAN']);
		
		$diskon_air_persen = ($obj->fields['JUMLAH_AIR'] != 0) ? (($obj->fields['DISKON_AIR'] / $obj->fields['JUMLAH_AIR']) * 100) : 0;
		$diskon_ipl_persen = ($obj->fields['JUMLAH_IPL'] != 0) ? (($obj->fields['DISKON_IPL'] / $obj->fields['JUMLAH_IPL']) * 100) : 0;
		
		?>
		<tr> 
			<td class="text-center"><?php echo $i; ?></td>
			<td class="text-center"><?php echo status_blok($obj->fields['STATUS_BLOK']); ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_TAG']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AWAL']; ?></td>
			<td class="text-center nowrap"><?php echo $obj->fields['PERIODE_IPL_AKHIR']; ?></td>
			<td class="text-center"><?php echo $obj->fields['JUMLAH_PERIODE_IPL']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_AKHIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_LALU']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_ANGKAT']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['PAKAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['STAND_MIN_PAKAI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ABONEMEN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DENDA']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['ADM']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_AIR']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['DISKON_IPL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH']); ?></td>
			<?php if ($from_master == '') { ?>
			<td class="text-center"><input type="text" value="<?php echo $obj->fields['DISKON_AIR']; ?>" name="cb_diskon_air_rupiah[<?php echo $i; ?>]" size="6" class="diskon_air_rupiah" data-jumlah_air="<?php echo $obj->fields['JUMLAH_AIR']; ?>"></td>
			<td class="text-center"><input type="text" value="<?php echo $diskon_air_persen; ?>" name="cb_diskon_air_persen[<?php echo $i; ?>]" size="3" class="diskon_air_persen"></td>
			
			<td class="text-center"><input type="text" value="<?php echo $obj->fields['DISKON_IPL']; ?>" name="cb_diskon_ipl_rupiah[<?php echo $i; ?>]" size="6" class="diskon_ipl_rupiah" data-jumlah_ipl="<?php echo $obj->fields['JUMLAH_IPL']; ?>"></td>
			<td class="text-center"><input type="text" value="<?php echo $diskon_ipl_persen; ?>" name="cb_diskon_ipl_persen[<?php echo $i; ?>]" size="3" class="diskon_ipl_persen"></td>
			
			<td class="text-center"><textarea name="cb_ket_bayar[<?php echo $i; ?>]" rows="1"></textarea></td>
			<td class="text-center"><input type="checkbox" name="cb_data[<?php echo $i; ?>]" class="cb_data" value="<?php echo $id; ?>"></td>
			<?php } ?>
		</tr>
		<?php
		
		$sum_jumlah_air		+= $obj->fields['JUMLAH_AIR'];
		$sum_abonemen		+= $obj->fields['ABONEMEN'];
		$sum_jumlah_ipl		+= $obj->fields['JUMLAH_IPL'];
		$sum_denda			+= $obj->fields['DENDA'];
		$sum_adm			+= $obj->fields['ADM'];
		$sum_diskon_air		+= $obj->fields['DISKON_AIR'];
		$sum_diskon_ipl		+= $obj->fields['DISKON_IPL'];
		$sum_jumlah			+= $obj->fields['JUMLAH'];
		
		$i++;
		$obj->movenext();
	}
	?>
	<tfoot>
	<tr>
		<td colspan="11">TOTAL .......</td>
		<td><?php echo to_money($sum_jumlah_air); ?></td>
		<td><?php echo to_money($sum_abonemen); ?></td>
		<td><?php echo to_money($sum_jumlah_ipl); ?></td>
		<td><?php echo to_money($sum_denda); ?></td>
		<td><?php echo to_money($sum_adm); ?></td>
		<td><?php echo to_money($sum_diskon_air); ?></td>
		<td><?php echo to_money($sum_diskon_ipl); ?></td>
		<td><?php echo to_money($sum_jumlah); ?></td>
		<?php if ($from_master == '') { ?>
		<td colspan="6"></td>
		<?php } ?>
	</tr>
	</tfoot>
</table>

<table class="t-popup">
<tr>
	<td class="td-action va-top">
		<input type="button" id="print" value=" Cetak Daftar Piutang (Alt+P) ">
		<input type="button" id="close" value=" Tutup (Esc) ">
	</td>
	
	<?php if ($from_master == '') { ?>
	<td class="text-right">
	<br>
		<table class="wauto f-right">
		<tr>
			<td colspan="3">
				KET. DISKON AIR : <br><textarea name="ket_diskon_air" id="ket_diskon_air" class="w90"></textarea><br>
				KET. DISKON IPL : <br><textarea name="ket_diskon_ipl" id="ket_diskon_ipl" class="w90"></textarea><br>
				<input type="button" id="save_diskon" value=" Proses Diskon "><br><br> 
			</td>
		</tr>
		
		<tr>
			<td width="120">CARA BAYAR</td><td width="10">:</td>
			<td width="190">
				<select name="cara_bayar" id="cara_bayar">
					<option value=""> -- CARA BAYAR -- </option>
					<option value="2"> K. DEBET </option>
					<option value="3"> K. KREDIT </option>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>PEMBAYARAN VIA</td><td>:</td>
			<td>
				<select name="bayar_via" id="bayar_via">
					<option value=""> -- KODE BANK -- </option>
					<?php
					$obj = $conn->Execute("SELECT KODE_BANK, NAMA_BANK FROM KWT_BANK ORDER BY NAMA_BANK ASC");
					while( ! $obj->EOF)
					{
						$ov = $obj->fields['KODE_BANK'];
						$on = $obj->fields['NAMA_BANK'];
						echo "<option value='$ov'> $on ($ov) </option>";
						$obj->movenext();
					}
					?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td>TGL. BAYAR (BANK)</td><td>:</td>
			<td><input type="text" name="tgl_bayar_bank" id="tgl_bayar_bank" class="dd-mm-yyyy" size="13" value=""></td>
		</tr> 
		
		<tr>
			<td colspan="3"><input type="button" id="bayar" value=" Cetak Kwitansi "></td>
		</tr>
		</table>
	</td> 
	<?php } ?>
</tr>
</table>

<input type="hidden" name="act" id="act" value="">
</form>
</body>
</html>
<?php close($conn); ?>
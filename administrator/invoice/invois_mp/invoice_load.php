<?php
require_once('../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field		= (isset($_REQUEST['field'])) ? clean($_REQUEST['field']) : '';
$search	= (isset($_REQUEST['search'])) ? clean($_REQUEST['search']) : '';
$periode = (isset($_REQUEST['periode'])) ? clean($_REQUEST['periode']) : '';
if ($search != '')
{
	$query_search = " WHERE $field LIKE '%$search%' ";
	if($periode!=''){
		$period = explode('-', $periode);
		$query_search .= "and MONTH(PERIODE_AWAL) = $period[0] AND YEAR(PERIODE_AWAL) = $period[1]";
	}
}
else if($periode!=''){
	$period = explode('-', $periode);
	$query_search .= "where  MONTH(PERIODE_AWAL) = $period[0] AND YEAR(PERIODE_AWAL) = $period[1]";
}
# Pagination
$query = "
SELECT 
	COUNT(ID_PEMBAYARAN) AS TOTAL

FROM 
	PELANGGAN_MP a	
	LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
	LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE
$query_search

";
$total_data = $conn->Execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
# End Pagination
?>

<table class="t-control" id="pagging-1">
<tr>
	
	<td class="text-right">
		<input type="button" id="prev_page" value=" < (Alt+Left) ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" (Alt+Right) > ">
	</td>
</tr>
</table>


<table class="t-data wm100">
<tr>
	<th rowspan="2">NO.</th>
	<th rowspan="2">NO VA</th>
	<th rowspan="2">NAMA <br> PELANGGAN</th>
	<th colspan="3">MEDIA PROMOSI</th>
	<th colspan="2">PERIODE</th>
	<th rowspan="2">TARIF</th>
	<th colspan="2">BIAYA<br>STRATEGIS</th>
	<th colspan="2">DISCOUNT</th>
	<th colspan="3">NILAI TAGIHAN</th>
	<th rowspan="2">STATUS</th>
</tr>
<tr>
	<th colspan="1">JENIS</th>
	<th colspan="1">KATEGORI</th>
	<th colspan="1">LOKASI</th>
	<th colspan="1">AWAL</th>
	<th colspan="1">AKHIR</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
	<th colspan="1">JUMLAH</th>
	<th colspan="1">PPN</th>
	<th colspan="1">TOTAL<br>BAYAR</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT *
	FROM PELANGGAN_MP a	
	LEFT JOIN KWT_LOKASI_MP c ON a.KODE_LOKASI = c.KODE_LOKASI
	LEFT JOIN KWT_TIPE_MP d ON a.KODE_TIPE = d.KODE_TIPE
	$query_search
	ORDER BY a.PERIODE_AWAL DESC
	";
	
	$obj = $conn->SelectLimit($query, $per_page, $page_start);
		
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = $obj->fields['ID_PEMBAYARAN'];
		$pembayaran = $obj->fields['PEMBAYARAN'];
		
		if ($obj->fields['KODE_MP'] == 'A') {
			$kode_mp = 'BILLBOARD / SIGN BOARD / PLAY SIGN';
			
		} else if ($obj->fields['KODE_MP'] == 'B') {
			$kode_mp = 'NEON BOX / NEON SIGN';
			
		} else if ($obj->fields['KODE_MP'] == 'C') {
			$kode_mp = 'SPANDUK / UMBUL-UMBUL / STANDING DISPLAY';
		} else {
			$kode_mp = 'BANNER / BALIHO';
		}
		
		
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NO_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $kode_mp; ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td><?php echo $obj->fields['NAMA_LOKASI']; ?></td>
			<td class="text-center"><?php echo date("d M Y", strtotime($obj->fields['PERIODE_AWAL'])); ?></td>
			<td class="text-center"><?php echo date("d M Y", strtotime($obj->fields['PERIODE_AKHIR'])); ?></td>
			<td class="text-right"><?php echo to_decimal($obj->fields['TARIF']); ?></td>
			<td class="text-right"><?php echo to_decimal($obj->fields['PERSEN_NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_decimal($obj->fields['PERSEN_NILAI_KURANG']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_KURANG']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TOTAL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_PPN']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TOTAL_BAYAR']); ?></td>	
			<td class="text-right">
				<?php if($obj->fields['STATUS']==0){
					echo '<a style = "color : red;">non-aktif</a>';
				} else{
					echo '<a style = "color : green;">aktif</a>';
			}
				?>
			</td>
			
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
}
?>
</table>


<table class="t-control" id="pagging-2"></table>

<script type="text/javascript">

jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	
	$('#total-data').html('<?php echo $total_data; ?>');
	$('#per_page').val('<?php echo $per_page; ?>');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');

});

function showPrint(id)
{
	var url = base_invoice + 'invois_mp/invoice_print.php?id_pembayaran='+id;
	window.open(url,'_blank');
	return false;
}

</script>
<?php
close($conn);
exit;
?>
<?php
require_once('../../../../../config/config.php');
$conn = conn();
$query_search = '';

$per_page	= (isset($_REQUEST['per_page']) AND is_numeric($_REQUEST['per_page']) AND $_REQUEST['per_page'] > 0) ? $_REQUEST['per_page'] : 20;
$page_num	= (isset($_REQUEST['page_num']) AND is_numeric($_REQUEST['page_num']) AND $_REQUEST['page_num'] > 0) ? $_REQUEST['page_num'] : 1;

$field1		= (isset($_REQUEST['field1'])) ? clean($_REQUEST['field1']) : '';
$search1	= (isset($_REQUEST['search1'])) ? clean($_REQUEST['search1']) : '';

if ($search1 != '')
{
	$query_search .= " AND $field1 LIKE '%$search1%' ";
}

/* Pagination */
$query = "
	SELECT
		COUNT(p.ID_PEMBAYARAN) AS TOTAL
	FROM 
		KWT_PEMBAYARAN_PKL p
		LEFT JOIN KWT_LOKASI_PKL l ON p.KODE_LOKASI = l.KODE_LOKASI
		LEFT JOIN KWT_TIPE_PKL t ON p.KODE_TIPE = t.KODE_TIPE
		LEFT JOIN KWT_BANK b ON p.KODE_BANK = b.KODE_BANK
		LEFT JOIN KWT_PELANGGAN_PKL c ON p.NO_PELANGGAN = c.NO_PELANGGAN
	WHERE ID_REFERENSI IS NULL AND TGL_KELUAR IS NULL
	AND CREATED_DATE IN 
		(
		SELECT MAX(CREATED_DATE)
		FROM KWT_PEMBAYARAN_PKL
		GROUP BY NO_PELANGGAN
		)
	$query_search
	";
$total_data = $conn->execute($query)->fields['TOTAL'];
$total_page = ceil($total_data/$per_page);

$page_num = ($page_num > $total_page) ? $total_page : $page_num;
$page_start = (($page_num-1) * $per_page);
/* End Pagination */
?>

<table id="pagging-1" class="t-control">
<tr>
	<td>
		<!-- <input type="button" id="tambah" value=" Tambah (Alt+N) "> 
		<input type="button" id="hapus" value=" Pemutusan Sewa"> -->
	</td>
	
	<td class="text-right">
		<input type="button" id="prev_page" value=" < (Alt+Left) ">
		Hal : <input type="text" name="page_num" size="5" class="page_num apply text-center" value="<?php echo $page_num; ?>">
		Dari <?php echo $total_page ?> 
		<input type="hidden" id="total_page" value="<?php echo $total_page; ?>">
		<input type="button" id="next_page" value=" (Alt+Right) > ">
	</td>
</tr>
</table>

<table class="t-nowrap t-data wm100">
<tr>
	<!--<th rowspan="2" class="w2"><input type="checkbox" id="cb_all"></th>-->
	<th rowspan="2">NO.</th>
	<th rowspan="2">NAMA PELANGGAN</th>
	<th rowspan="2">TIPE PKL</th>
	<th rowspan="2">LOKASI</th>
	<th colspan="2">Harga Sewa</th>
	<th colspan="2">Biaya<br>Strategis</th>
	<th colspan="2">Discount</th>
	<th rowspan="2">LUAS<br>(m&sup2;)</th>	
	<th rowspan="2">Durasi<br>(Bulan)</th>
	<th rowspan="2">JUMLAH<br>BAYAR</th>
	<th rowspan="2">TANGGAL<br>BAYAR</th>
</tr>
<tr>
	<th colspan="1">UANG<br>PANGKAL</th>
	<th colspan="1">TARIF</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
	<th colspan="1">%</th>
	<th colspan="1">Rp.</th>
</tr>

<?php
if ($total_data > 0)
{
	$query = "
	SELECT
		p.*,
		CONVERT(VARCHAR(11),p.TGL_SERAHTERIMA,106) AS TGL_SERAHTERIMA,
		CONVERT(VARCHAR(11),p.TGL_PEMUTUSAN,106) AS TGL_PEMUTUSAN,
		l.NAMA_LOKASI,
		l.DETAIL_LOKASI,
		t.NAMA_TIPE,
		b.NAMA_BANK,
		c.NAMA_PELANGGAN
	FROM 
		KWT_PEMBAYARAN_PKL p
		LEFT JOIN KWT_LOKASI_PKL l ON p.KODE_LOKASI = l.KODE_LOKASI
		LEFT JOIN KWT_TIPE_PKL t ON p.KODE_TIPE = t.KODE_TIPE
		LEFT JOIN KWT_BANK b ON p.KODE_BANK = b.KODE_BANK
		LEFT JOIN KWT_PELANGGAN_PKL c ON p.NO_PELANGGAN = c.NO_PELANGGAN
	WHERE 
		ID_REFERENSI IS NULL AND TGL_KELUAR IS NULL 
		AND CREATED_DATE IN 
		(
		SELECT MAX(CREATED_DATE)
		FROM KWT_PEMBAYARAN_PKL
		GROUP BY NO_PELANGGAN
		)
	$query_search
	";
	$obj = $conn->selectlimit($query, $per_page, $page_start);
	$i = 1 + $page_start;
	while( ! $obj->EOF)
	{
		$id = $obj->fields['ID_PEMBAYARAN'];
		$satuan	= ($obj->fields['SATUAN'] == 0) ? 'm&sup2;' : 'Bln';
		?>
		<tr class="onclick" id="<?php echo $id; ?>"> 
			<!--<td width="30" class="notclick text-center"><input type="checkbox" name="cb_data[]" class="cb_data" value="<?php echo $id; ?>"></td>-->
			<td class="text-center"><?php echo $i; ?></td>
			<td><?php echo $obj->fields['NAMA_PELANGGAN']; ?></td>
			<td><?php echo $obj->fields['NAMA_TIPE']; ?></td>
			<td><?php echo $obj->fields['DETAIL_LOKASI']; ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['UANG_PANGKAL']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['TARIF']); ?><?php echo ' / '; echo $satuan; ?></td>
			<td class="text-right"><?php echo to_decimal($obj->fields['PERSEN_NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_TAMBAH']); ?></td>
			<td class="text-right"><?php echo to_decimal($obj->fields['PERSEN_NILAI_KURANG']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['NILAI_KURANG']); ?></td>
			<td class="text-center"><?php echo $obj->fields['LUAS']; ?></td>
			<td class="text-center"><?php echo to_money($obj->fields['DURASI']); ?></td>
			<td class="text-right"><?php echo to_money($obj->fields['JUMLAH_BAYAR']); ?></td>
			<td class="text-center"><?php echo date("d M Y", strtotime($obj->fields['CREATED_DATE'])); ?></td>
		</tr>
		<?php
		$i++;
		$obj->movenext();
	}
}
?>
</table>

<table id="pagging-2" class="t-control"></table>

<script type="text/javascript">
jQuery(function($) {
	$('#pagging-2').html($('#pagging-1').html());
	
	$('#total-data').html('<?php echo $total_data; ?>');
	$('#per_page').val('<?php echo $per_page; ?>');
	$('.page_num').inputmask('integer');
	t_strip('.t-data');
});
</script>

<?php
close($conn);
exit;
?>
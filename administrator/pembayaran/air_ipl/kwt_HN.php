	<table class="kwt" width="960" cellpadding="1" cellspacing="1">
	<tbody>
	<tr>
		<td colspan="8">&nbsp;</td>
		<td style="padding:0 0 10px 40px;" width="245">
			<table class="kwt" border="0" width="100%" cellpadding="1" cellspacing="1">
			<tbody>
			<tr><td width="23%">No. Dok.</td>
				<td width="3%">:</td>
				<td width="74%">082/F/KEU/JRP/06</td>
			</tr>
			<tr>
				<td>Rev.</td>
				<td>:</td>
				<td>1</td>
			</tr>
			</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td width="97">No. Bukti</td>
		<td width="3">:</td>
		<td width="177"><?php echo $no_kwitansi; ?></td>
		<td width="88">Bulan</td>
		<td width="3">:</td>
		<td width="210"><?php echo ucfirst(fm_periode($periode_air)); ?></td>
		<td width="101">No. Bukti</td>
		<td width="6">:</td>
		<td><?php echo $no_kwitansi; ?></td>
	</tr>
	<tr>
		<td>No. Pelanggan</td>
		<td>:</td>
		<td><?php echo fm_nopel($no_pelanggan); ?></td>
		<td>Stand Meter</td>
		<td>:</td>
		<td><?php echo to_money($stand_lalu) . ' - ' . to_money($stand_akhir); ?></td>
		<td>No. Pelanggan</td>
		<td>:</td>
		<td><?php echo fm_nopel($no_pelanggan); ?></td>
	</tr>
	<tr>
		<td>N a m a</td>
		<td>:</td>
		<td><?php echo $nama_pelanggan; ?></td>
		<td>Ganti Meter</td>
		<td>:</td>
		<td><?php echo to_money($stand_angkat); ?></td>
		<td>N a m a</td>
		<td>:</td>
		<td><?php echo $nama_pelanggan; ?></td>
	</tr>
	<tr>
		<td>Blok / Sektor</td>
		<td>:</td>
		<td><?php echo $kode_blok . ' / ' . $kode_sektor; ?></td>
		<td>Pemakaian</td>
		<td>:</td>
		<td><?php echo to_money($pemakaian); ?></td>
		<td>Blok / Sektor</td>
		<td>:</td>
		<td><?php echo $kode_blok . ' / ' . $kode_sektor; ?></td>
	</tr>
	<tr>
		<td>Tgl. Byr. / Via</td>
		<td>:</td>
		<td><?php echo $tgl_bayar_bank . ' / '. $bayar_via; ?></td>
		<td>Golongan</td>
		<td>:</td>
		<td><?php echo $key_air . ' / ' . $key_ipl; ?></td>
		<td>Tgl. Bayar</td>
		<td>:</td>
		<td><?php echo $tgl_bayar_bank . ' / '. $bayar_via; ?></td>
	</tr>
	<tr>
		<td colspan="6" style="padding:8px 0 8px 0;">
			<table class="kwt" border="0" width="74%" cellpadding="1" cellspacing="1">
			<tbody>
			<tr>
				<td style="padding-right:25px;" align="right" width="22%"><?php echo $l_blok1; ?> m&sup3;</td>
				<td width="5%">Rp.</td>
				<td align="right" width="22%"><?php echo to_money($blok1 * $tarif1); ?></td>
				<td style="padding-left:15px;" width="24%">Air Bersih</td>
				<td width="5%">Rp.</td>
				<td align="right" width="22%"><?php echo to_money($jumlah_air); ?></td>
			</tr>
			<tr>
				<td style="padding-right:25px;" align="right"><?php echo $l_blok2; ?> m&sup3;</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($blok2 * $tarif2); ?></td>
				<td style="padding-left:15px;">IPL</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($jumlah_ipl); ?></td>
			</tr>
			<tr>
				<td style="padding-right:25px;" align="right"><?php echo $l_blok3; ?> m&sup3;</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($blok3 * $tarif3); ?></td>
				<td style="padding-left:15px;">Abonemen</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($abonemen); ?></td>
			</tr>
			<tr>
				<td style="padding-right:25px;" align="right">&gt; <?php echo $l_blok4; ?> m&sup3;</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($blok4 * $tarif4); ?></td>
				<td style="padding-left:15px;">Denda</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($denda); ?></td>
			</tr>
			<tr>
				<td>Pakai Minimal</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($stand_min_pakai * $tarif_min_pakai); ?></td>
				<td style="padding-left:15px;">Administrasi</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($adm); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($jumlah_air); ?></td>
				<td style="padding-left:15px; font-weight:bold;">TOTAL</td>
				<td style="font-weight:bold;">Rp.</td>
				<td style="font-weight:bold;" align="right"><?php echo to_money($total); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="padding-left:15px;">Diskon</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($diskon); ?></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td style="font-weight:bold;">TOTAL BAYAR</td>
				<td style="font-weight:bold;">Rp.</td>
				<td style="font-weight:bold;" align="right"><?php echo to_money($jumlah_bayar); ?></td>
			</tr>
			</tbody>
			</table>
		</td>
		<td colspan="3" rowspan="2">
			<table class="kwt" border="0" width="100%" cellpadding="1" cellspacing="1">
			<tbody>
			<tr>
				<td width="29%">Bulan</td>
				<td colspan="2">: <?php echo ucfirst(fm_periode($periode_air)); ?></td>
				<td width="11%">Gol</td>
				<td width="2%">:</td>
				<td width="32%"><?php echo $key_air . ' / ' . $key_ipl; ?></td>
			</tr>
			<tr>
				<td>Pemakaian</td>
				<td colspan="2">: <?php echo to_money($pemakaian); ?></td>
				<td>Via</td>
				<td>:</td>
				<td><?php echo $bayar_via; ?></td>
			</tr>
			<tr>
				<td></td>
				<td width="6%"></td>
				<td width="20%"></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Air Bersih</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($jumlah_air); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>IPL</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($jumlah_ipl); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Abonemen</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($abonemen); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Denda</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($denda); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Administrasi</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($adm); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-weight:bold;">TOTAL</td>
				<td style="font-weight:bold;">Rp.</td>
				<td style="font-weight:bold;" align="right"><?php echo to_money($total); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Diskon</td>
				<td>Rp.</td>
				<td align="right"><?php echo to_money($diskon); ?></td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td style="font-weight:bold;">TOTAL BAYAR</td>
				<td style="font-weight:bold;">Rp.</td>
				<td style="font-weight:bold;" align="right"><?php echo to_money($jumlah_bayar); ?></td>
				<td>*)</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td colspan="6" style="padding-top:10px; font-style:italic;">Kasir&nbsp;&nbsp;:<?php echo $user_bayar; ?> <font style="font-style:normal; font-weight:bold;">/ - / COPY KWITANSI</font></td>
			</tr>
			<tr>
				<td colspan="6" style="font-style:italic;">*) Termasuk PPN <?php echo to_money($persen_ppn); ?>%</td>
			</tr>
			</tbody>
			</table>
		</td>
	</tr>
	<tr>
		<td colspan="6">
			<table class="kwt" style="font-style:italic;" border="0" width="100%" cellpadding="1" cellspacing="1">
			<tbody>
			<tr>
				<td style="padding-bottom:20px;" width="15%">Terbilang</td>
				<td style="padding-bottom:20px;" width="1%">:</td>
				<td style="padding-bottom:20px;" width="84%"><?php echo ucfirst($terbilang->eja($jumlah_bayar)); ?></td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td>:</td>
				<td><?php echo $ket_bayar; ?></td>
			</tr>
			<tr>
				<td>Kasir</td>
				<td>:</td>
				<td><?php echo $user_bayar; ?> <font style="font-style:normal; font-weight:bold;">/ - / COPY KWITANSI</font></td>
			</tr>
			<tr><td colspan="3">*) Termasuk PPN <?php echo to_money($persen_ppn); ?>%</td></tr>
			</tbody>
			</table>
		</td>
	</tr>
	</tbody>
	</table>

	<div class="newpage"></div>
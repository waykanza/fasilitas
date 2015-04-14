
<div>
<table width="800">
<tbody>
<tr>
<td width="55%">
<table width="100%">
<tbody>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td width="20%"></td>
<td width="5%"></td>
<td width="35%"></td>
<td width="15%"></td>
<td width="5%"></td>
<td width="20%"></td>
</tr>
<tr>
<td colspan="6">&nbsp;</td>
</tr>
<tr>
<td colspan="6">&nbsp;</td>
</tr>
<tr>
<td colspan="6">&nbsp;</td>
</tr>
<tr>
<td>No. Bukti</td>
<td align="center">:</td>
<td><span><?php echo $no_kwitansi; ?></span></td>
<td>Bulan</td>
<td align="center">:</td>
<td><span><?php echo ucfirst(fm_periode($periode_tag)); ?></span></td>
</tr>
<tr>
<td>No. Pelanggan</td>
<td align="center">:</td>
<td><span><?php echo fm_nopel($no_pelanggan); ?></span></td>
<td>Awal</td>
<td align="center">:</td>
<td><span><?php echo fm_periode_first($periode_tag); ?></span></td>
</tr>
<tr>
<td>N a m a</td>
<td align="center">:</td>
<td><span><?php echo $nama_pelanggan; ?></span></td>
<td>Akhir</td>
<td align="center">:</td>
<td><span><?php echo fm_periode_last($periode_tag); ?></span></td>
</tr>
<tr>
<td>Blok / Sektor</td>
<td align="center">:</td>
<td><span><?php echo $kode_blok; ?></span>&nbsp; / &nbsp;<span><?php echo $kode_sektor; ?></span></td>
<td>Bayar via</td>
<td align="center">:</td>
<td><span><?php echo $bayar_via; ?></span></td>
</tr>
<tr>
<td>Luas / Tarif</td>
<td align="center">:</td>
<td><span><?php echo to_money($luas_kavling); ?></span>&nbsp; / &nbsp;<span><?php echo to_money($tarif_ipl); ?></span> (M<sup>2</sup>)</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Tgl. Bayar</td>
<td align="center">:</td>
<td><span><?php echo $tgl_bayar_bank; ?></span></td>
<td>Tgl. Kwit</td>
<td><div align="center">:</div></td>
<td><span><?php echo $tgl_bayar_sys; ?></span></td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td width="45%"></td>
<td width="5%"></td>
<td width="10%"></td>
<td width="30%"></td>
<td width="10%"></td>
</tr>
<tr>
<td>IPL&nbsp;<span>Renovasi</span></td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($jumlah_ipl); ?></span></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Denda</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($denda); ?></span></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Total</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($total); ?></span></td>
<td>&nbsp;</td>
</tr>
<tr>
<td>Discount</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($diskon); ?></span></td>
<td>&nbsp;</td>
</tr>
<tr height="25" valign="bottom">
<td><b>TOTAL BAYAR</b></td>
<td align="center">:</td>
<td class="top-enabled"><b>Rp.</b></td>
<td class="top-enabled" align="right"><b><span><?php echo to_money($jumlah_bayar); ?></span></b></td>
<td>&nbsp;</td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td>
<table width="100%">
<tbody>
<tr>
<td width="25%"></td>
<td width="5%"></td>
<td width="70%"></td>
</tr>
<tr valign="top" height="30">
<td>Terbilang</td>
<td align="center">:</td>
<td><span><?php echo strtoupper($terbilang->eja($jumlah_bayar)); ?></span></td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td>
<table width="100%">
<tbody><tr valign="top">
<td width="25%">Keterangan</td>
<td width="5%" align="center">:</td>
<td width="70%"><span><?php echo $ket_bayar; ?></span></td>
</tr>
<tr>
<td>Kasir</td>
<td align="center">:</td>
<td><span><?php echo $user_bayar; ?></span></td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table></td>
<td width="3%">&nbsp;</td>
<td width="42%">
<table width="100%" align="right">
<tbody>
<tr>
<td><table width="100%" align="right">
<tbody>
<tr>
<td width="40%"></td>
<td width="5%"></td>
<td width="55%"></td>
</tr>
<tr>
<td colspan="3">&nbsp;</td>
</tr>
<tr>
<td colspan="3" align="right"><table width="100%" align="right">
<tbody>
<tr>
<td width="45%">&nbsp;</td>
<td align="left" width="20%">No. Dok</td>
<td width="10%" align="center">:</td>
<td align="left">082/F/KEU/JRP/06</td>
</tr>
<tr>
<td>&nbsp;</td>
<td align="left">Rev.</td>
<td align="center">:</td>
<td align="left">0</td>
</tr>
<tr>
<td colspan="3">&nbsp;</td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td>No. Bukti</td>
<td>:</td>
<td><span><?php echo $no_kwitansi; ?></span></td>
</tr>
<tr>
<td>No. Pelanggan</td>
<td>:</td>
<td><span><?php echo fm_nopel($no_pelanggan); ?></span></td>
</tr>
<tr>
<td>N a m a</td>
<td>:</td>
<td><span><?php echo $nama_pelanggan; ?></span></td>
</tr>
<tr>
<td>Blok / Sektor</td>
<td>:</td>
<td><span><?php echo $kode_blok; ?></span>&nbsp; / &nbsp;<span><?php echo $kode_sektor; ?></span></td>
</tr>
<tr>
<td>Luas / Tarif</td>
<td>:</td>
<td><span><?php echo to_money($luas_kavling); ?></span>&nbsp; / &nbsp;<span><?php echo to_money($tarif_ipl); ?></span> (M<sup>2</sup>)</td>
</tr>
<tr>
<td>Tgl. Bayar</td>
<td>:</td>
<td><span><?php echo $tgl_bayar_bank; ?></span></td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
<td><table width="100%">
<tbody>
<tr>
<td width="10%"></td>
<td width="5%"></td>
<td width="35%"></td>
<td width="10%"></td>
<td width="5%"></td>
<td width="35%"></td>
</tr>
<tr>
<td>Bulan</td>
<td>:</td>
<td><span><?php echo ucfirst(fm_periode($periode_tag)); ?></span></td>
<td>Via</td>
<td>:</td>
<td><span><?php echo $bayar_via; ?></span></td>
</tr>
<tr>
<td>Awal</td>
<td>:</td>
<td><span><?php echo fm_periode_first($periode_tag); ?></span></td>
<td>Akhir</td>
<td>:</td>
<td><span><?php echo fm_periode_last($periode_tag); ?></span></td>
</tr>
</tbody>
</table></td>
</tr>
<tr>
<td><table width="100%">
<tbody>
<tr>
<td width="45%"></td>
<td width="5%"></td>
<td width="10%"></td>
<td width="30%"></td>
</tr>
<tr>
<td>IPL&nbsp;<span>Renovasi</span></td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($jumlah_ipl); ?></span></td>
</tr>
<tr>
<td>Denda</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($denda); ?></span></td>
</tr>
<tr>
<td>Total</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($total); ?></span></td>
</tr>
<tr>
<td>Discount</td>
<td align="center">:</td>
<td>Rp.</td>
<td align="right"><span><?php echo to_money($diskon); ?></span></td>
</tr>
<tr height="25" valign="bottom">
<td><b>TOTAL BAYAR</b></td>
<td align="center">:</td>
<td class="top-enabled"><b>Rp.</b></td>
<td class="top-enabled" align="right"><b><span><?php echo to_money($jumlah_bayar); ?></span></b></td>
</tr>
<tr height="25" valign="bottom">
<td>(Include ppn <?php echo to_money($persen_ppn); ?> %)</td>
<td align="center">&nbsp;</td>
<td class="top-enabled">&nbsp;</td>
<td class="top-enabled" align="right"><b></b></td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table></td>
</tr>
</tbody>
</table>
</div>

<div class="newpage"></div>

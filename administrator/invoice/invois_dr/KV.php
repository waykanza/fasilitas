
<table border=0 cellpadding=0 cellspacing=0 width=759 style='border-collapse:collapse;table-layout:fixed;width:569pt'>
<col width=15 style='mso-width-source:userset;mso-width-alt:548;width:11pt'>
<col width=32 style='mso-width-source:userset;mso-width-alt:1170;width:24pt'>
<col width=64 span=2 style='width:48pt'>
<col width=11 style='mso-width-source:userset;mso-width-alt:402;width:8pt'>
<col width=64 span=6 style='width:48pt'>
<col width=18 style='mso-width-source:userset;mso-width-alt:658;width:14pt'>
<col width=32 style='mso-width-source:userset;mso-width-alt:1170;width:24pt'>
<col width=139 style='mso-width-source:userset;mso-width-alt:5083;width:104pt'>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 width=15 style='height:12.75pt;width:11pt'></td>
<td colspan=13 class=xl117 width=744 style='width:558pt'></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl117></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl117></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl117></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl117></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl117></td>
</tr>
<tr height=20 style='height:15.0pt'>
<td height=20 class=xl15 style='height:15.0pt'></td>
<td colspan=13 class=xl128>PT. JAYA REAL PROPERTY, Tbk.</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl94>UNIT PENGELOLA KAWASAN BINTARO</td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td colspan=13 class=xl127>&nbsp;</td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td colspan=13 class=xl94></td>
</tr>
<tr height=24 style='height:18.0pt'>
<td height=24 class=xl15 style='height:18.0pt'></td>
<td colspan=13 class=xl93>INVOICE</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl94>No.<span style='mso-spacerun:yes'></span><?php echo $no_invoice; ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=3 class=xl84>Materi</td>
<td class=xl63>:</td>
<td colspan=9 class=xl84>IURAN PEMELIHARAAN KAVELING KOSONG</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=3 class=xl84>Periode</td>
<td class=xl63>:</td>
<td colspan=9 class=xl83><?php echo fm_periode_first($periode_ipl_awal); ?> s/d <?php echo fm_periode_last($periode_ipl_akhir); ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=3 class=xl84>Nama Penyewa</td>
<td class=xl63>:</td>
<td colspan=9 class=xl84><?php echo $nama_pelanggan; ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=3 class=xl84>Blok</td>
<td class=xl63>:</td>
<td colspan=9 class=xl86><?php echo $kode_blok; ?></td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td colspan=13 class=xl94></td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td class=xl65>NO.</td>
<td colspan=9 class=xl98>KETERANGAN</td>
<td colspan=3 class=xl95 style='border-right:1.0pt solid black'>TOTAL</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl67>&nbsp;</td>
<td colspan=9 class=xl123>&nbsp;</td>
<td class=xl74 style='border-top:none'>&nbsp;</td>
<td class=xl68 style='border-top:none'>&nbsp;</td>
<td class=xl69 style='border-top:none'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>1</td>
<td colspan=9 class=xl84>Iuran Pemeliharaan Lingkungan Kavling Kosong</td>
<td class=xl75>=</td>
<td class=xl63>Rp.</td>
<td class=xl70 style="text-align:right;"><span style='mso-spacerun:yes'></span><?php echo to_money($jumlah_bayar); ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl71>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl76>&nbsp;</td>
<td class=xl15></td>
<td class=xl72>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl104></td>
<td class=xl76>&nbsp;</td>
<td class=xl15></td>
<td class=xl72>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl75>&nbsp;</td>
<td class=xl63></td>
<td class=xl70>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl75>&nbsp;</td>
<td class=xl63></td>
<td class=xl70>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl75>&nbsp;</td>
<td class=xl63></td>
<td class=xl70>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl77>&nbsp;</td>
<td class=xl63></td>
<td class=xl73>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl77>&nbsp;</td>
<td class=xl63></td>
<td class=xl70>&nbsp;</td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td class=xl66>&nbsp;</td>
<td colspan=9 class=xl84></td>
<td class=xl77>&nbsp;</td>
<td class=xl63></td>
<td class=xl70>&nbsp;</td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td class=xl81>&nbsp;</td>
<td colspan=7 class=xl125 style='border-left:none'>&nbsp;</td>
<td colspan=2 class=xl91 style='border-right:1.0pt solid black'>TOTAL</td>
<td class=xl78 style='border-left:none'>=</td>
<td class=xl79>Rp.</td>
<td class=xl80 style="text-align:right;"><span style='mso-spacerun:yes'></span><?php echo to_money($jumlah_bayar); ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl122 style='border-right:1.0pt solid black'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl82 colspan=2>Terbilang :<span style='mso-spacerun:yes'></span></td>
<td colspan=11 class=xl105 style='border-right:1.0pt solid black'># <?php echo ucfirst($terbilang->eja($jumlah_bayar + $prev_jumlah_bayar)); ?> #</td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td colspan=13 class=xl119 style='border-right:1.0pt solid black'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl101 style='border-right:1.0pt solid black'>Pembayaran harap ditujukan ke PT. JAYA REAL PROPERTY, Tbk dengan rekening :</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl75 style='border-right:1.0pt solid black'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl85 style='border-right:1.0pt solid black'>BANK BNI No. Rekening : 166 - 88 - 99997<span style='mso-spacerun:yes'></span></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl107 style='border-right:1.0pt solid black'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl88 style='border-right:1.0pt solid black'>Bagi yang membayar melalui Bank harap bukti pembayaran di kirimkan ke kami<span style='mso-spacerun:yes'></span>via Fax ke nomor (021) 7486 4002<spanstyle='mso-spacerun:yes'></span></td>
</tr>
<tr height=18 style='height:13.5pt'>
<td height=18 class=xl15 style='height:13.5pt'></td>
<td colspan=13 class=xl113 style='border-right:1.0pt solid black'>&nbsp;</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=8 class=xl111>Keterangan :</td>
<td colspan=5 class=xl84>Tangerang,<span style='mso-spacerun:yes'></span><?php echo fm_date($tgl_ivc); ?></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64>1.</td>
<td colspan=7 class=xl112>Invoice ini bukan merupakan bukti pembayaran yang sah.</td>
<td colspan=5 class=xl116>PT. JAYA REAL PROPERTY, Tbk</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64>2.</td>
<td colspan=7 class=xl112>Kwitansi asli merupakan bukti pembayaran yang sah setelah</td>
<td colspan=5 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64></td>
<td colspan=7 class=xl112>dana diterima efektif di rekening PT. JAYA REAL PROPERTY, Tbk.</td>
<td colspan=5 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64>3.</td>
<td colspan=7 class=xl112>Keterlambatan pembayaran akan dikenakan denda sesuai dengan</td>
<td colspan=5 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64></td>
<td colspan=7 class=xl112>peraturan yang berlaku.</td>
<td colspan=5 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td class=xl64>4.</td>
<td colspan=7 class=xl112>Surat ini resmi tanpa tanda tangan dan cap perusahaan.</td>
<td colspan=5 class=xl94></td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=8 class=xl94></td>
<td colspan=5 class=xl110>Wahid Utomo,SE</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=8 class=xl94></td>
<td colspan=5 class=xl84>Manager Tata Lingkungan</td>
</tr>
<tr height=17 style='height:12.75pt'>
<td height=17 class=xl15 style='height:12.75pt'></td>
<td colspan=13 class=xl94></td>
</tr>
<![if supportMisalignedColumns]>
<tr height=0 style='display:none'>
<td width=15 style='width:11pt'></td>
<td width=32 style='width:24pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=11 style='width:8pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=64 style='width:48pt'></td>
<td width=18 style='width:14pt'></td>
<td width=32 style='width:24pt'></td>
<td width=139 style='width:104pt'></td>
</tr>
<![endif]>
</table>


		<div class="text-center"><b>PEMBERITAHUAN</b></div>
		<div style="margin:5px 0 10px 0;">
			Bersama ini kami selaku pengembang kawasan mengucapkan terimakasih telah menjadikan wilayah Bintaro Jaya sebagai tempat hunian Bapak/Ibu. Sesuai dengan ketentuan pembayaran tagihan Air Bersih dan IPL Bintaro Jaya Bapak/Ibu diharapkan untuk dapat segera melakukan pelunasan pembayaran sesuai tanggal jatuh tempo sebagai berikut:
		</div>
		
		<br>
		
		<table class="tb-top bd-none">
		<tr><td width="160">Materi</td><td width="5"> : </td><td>AIR BERSIH DAN IURAN PEMELIHARAAN LINGKUNGAN</td></tr>
		<tr><td>Nama Penyewa</td><td> : </td><td><?php echo $nama_pelanggan; ?></td></tr>
		<tr>
			<td>Alamat Penyewa</td><td> : </td>
			<td>
				<?php echo $nama_cluster; ?> Sektor <?php echo $nama_sektor; ?><br>
				<?php echo $alamat; ?>
			</td>
		</tr>
		<tr><td>Blok</td><td> : </td><td><?php echo $kode_blok; ?></td></tr>
		<tr><td>Nomor Pelanggan</td><td> : </td><td><?php echo fm_nopel($no_pelanggan); ?></td></tr>
		<tr><td><b>Tanggal Jatuh Tempo</b></td><td><b> : </b></td><td><b><?php echo fm_date($tgl_jatuh_tempo); ?></b></td></tr>
		</table>
		
		<br>
		
		<table border="1" style="width:100%;">
		<!-- INVOICE -->
		<tr>
			<td width="1" class="text-center bd-right bd-bottom">NO.</td>
			<td class="text-center bd-right bd-bottom">KETERANGAN</td>
			<td class="text-center bd-bottom" colspan="2">TOTAL</td>
		</tr>
		<tr>
			<td class="text-center bd-right">1</td>
			<td class="bd-right"><b>Air Bersih dan Iuran Pemeliharaan Lingkungan Periode <?php echo fm_periode($periode_air); ?></b></td>
			<td width="40" class="text-left"></td>
			<td class="text-right"></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Iuran Pemeliharaan Lingkungan</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($jumlah_ipl); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Air Bersih</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($jumlah_air); ?> </td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Abonemen</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($abonemen); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Discount</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($diskon_ipl + $diskon_air); ?></td>
		</tr>
		<tr class="tr-sum">
			<td class="bd-right"></td>
			<td class="bd-right"></td>
			<td></td>
			<td><hr class="hr-sum"></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="text-right bd-right"><b>Total</b></td>
			<td class="text-left"><b>= Rp.</b></td>
			<td class="text-right"><b><?php echo to_money($jumlah_bayar); ?></b></td>
		</tr>
		
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right"></td>
			<td class="text-left"></td>
			<td class="text-right"></td>
		</tr>
		
		<tr>
			<td class="bd-right"></td>
			<td class="text-right bd-right">
				<div class="f-left">Angka Stan meter air</div>
				<div class="f-right"><b><?php echo to_money($stand_lalu) . ' m3 - ' . to_money($stand_akhir) . ' m3'; ?></b></div>
			</td>
			<td class="text-left"></td>
			<td class="text-right"><b><?php echo to_money($stand_akhir - $stand_lalu) . ' m3'; ?></b></td>
		</tr>
		
		<!-- AKUMULASI PIUTANG -->
		<tr>
			<td class="text-center bd-right">2</td>
			<td class="bd-right"><b>Total Tunggakan yang belum dibayar s / d <?php echo fm_periode(periode_mod('-1', $periode_air)); ?></b></td>
			<td class="text-left"></td>
			<td class="text-right"></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Iuran Pemeliharaan Lingkungan</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($prev_jumlah_ipl); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Air Bersih</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($prev_jumlah_air); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Abonemen</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($prev_abonemen); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Discount</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($prev_diskon_ipl + $prev_diskon_air); ?></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right">Denda</td>
			<td class="text-left">= Rp.</td>
			<td class="text-right"><?php echo to_money($prev_denda); ?></td>
		</tr>
		<tr class="tr-sum">
			<td class="bd-right"></td>
			<td class="bd-right"></td>
			<td></td>
			<td><hr class="hr-sum"></td>
		</tr>
		<tr>
			<td class="bd-right"></td>
			<td class="text-right bd-right"><b>Total</b></td>
			<td class="text-left"><b>= Rp.</b></td>
			<td class="text-right"><b><?php echo to_money($prev_jumlah_bayar); ?></b></td>
		</tr>
		
		<!-- TOTAL -->
		<tr>
			<td class="bd-right"></td>
			<td class="bd-right"><b>Total Tagihan belum dilakukan pembayaran</b></td>
			<td class="text-left"><b>= Rp.</b></td>
			<td class="text-right"><b><?php echo to_money($jumlah_bayar + $prev_jumlah_bayar); ?></b></td>
		</tr>
		
		<tr>
			<td colspan="4" class="bd-top bd-bottom">
				Terbilang : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<b>
					<i># <?php echo ucfirst($terbilang->eja($jumlah_bayar + $prev_jumlah_bayar)); ?> #</i><br><br>
					<?php echo 'Nilai tersebut diatas sudah termasuk termasuk PPn ' . to_money($persen_ppn) . '%'; ?>
				</b>
			</td>
		</tr>
		<tr>
			<td colspan="4">Untuk tertib administrasi, kami mohon agar melakukan tagihan sesuai tagihan yang telah kami sampaikan,<br>melalui :</td>
		</tr>
		<tr>
			<td colspan="4"><table class="bd-none">
				<tr>
					<td>
						<b>01. Bank Mandiri</b><br>
						- Mandiri ATM (Kode Bank 50201)<br>
						- Mandiri Internet Banking<br>
						- Teller (Semua Cabang di Indonesia)
					</td>
					<td>
						<b>03. Bank Bukopin</b><br>
						- Teller (Cabang Rukan 3 Bintaro Jaya)<br>
						- Autodebet
					</td>
					<td>
						<b>05. Bank CIMB Niaga</b><br>
						- CIMB Niaga ATM (Kode Bank 5759 atau 9769)<br>
						- Autodebet
					</td>
				</tr>
				<tr>
					<td>
						<b>02. Bank Permata</b><br>
						- Permata ATM (Kode Bank 045)<br>
						- Teller (Semua Cabang di Indonesia)
					</td>
					<td>
						<b>04. Bank BCA</b><br>
						- BCA ATM (Kode Bank 00704)<br>
						- BCA Internet Banking<br>
						- Teller (Semua Cabang di Indonesia)
					</td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td colspan="4" class="text-center "><b>
				Keterangan lebih lanjut dapat menghubungi Kantor Unit Pengelola Kawasan Bintaro Jaya Trade Center Blok H4 No m1-33 Jl Jend. Sudirman, Pusat Kawasan Niaga Sektor 7 - Bintaro Jaya telp. 021-7486 4001 ext. 201/ 100
			</b></td>
		</tr>
		<tr>
			<td colspan="4" class="text-center bd-top"><b>
				Abaikan informasi ini, apabila anda sudah melakukan pembayaran
			</b></td>
		</tr>
		</table>
		
		<br>
		
		<div class="clear"></div>

		<div class="f-left" style="font-size: 12px;">
			Keterangan : 
			<ol>
				<li>Invoice ini bukan merupakan bukti pembayaran yang sah.</li>
				<li>
					Kwitansi asli merupakan bukti pembayaran yang sah setelah<br>
					dana diterima efektif di rekening PT. JAYA REAL PROPERTY, Tbk.
				</li>
				<li>
					<b>
						Keterlambatan pembayaran akan dikenakan denda sebesar<br>
						<?php echo 'Rp. ' . to_money($denda_rupiah); ?>,- per bulan/sesuai ketentuan yang berlaku
					</b>
				</li>
				<li>Surat ini resmi tanpa tanda tangan dan cap perusahaan.</li>
			</ol>
		</div>
		
		<div class="f-right">
			<div class="text-center">Tangerang, <?php echo fm_date($tgl_ivc); ?></div>
			<div class="text-center" style="margin:0 0 80px 0;">PT. JAYA REAL PROPERTY, Tbk.</div>
			<div class="text-center" style="margin:0 0 0 0;"><b><u>M. Panji Manggala</u></b></div>
			<div class="text-center" style="margin:0 0 0 0;">Manager Air Bersih</div>
		</div>
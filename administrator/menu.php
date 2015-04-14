<script type="text/javascript">
jQuery(function($) {
	$('#nav a').each(function() {
		var link = $(this).attr('href');
		link = (link == '') ? 'javascript:void(0)' : base_adm + '?cmd=' + link;
		$(this).attr('href', link);
	});
});
</script>
<div class="clear"></div>
<ul id="nav">
	<li><a href="">Master</a>
		<ul>
			<li><a href="<?php echo base64_encode('parameter'); ?>">Parameter</a></li>
			<li><a href="<?php echo base64_encode('sektor'); ?>">Sektor</a></li>
			<li><a href="<?php echo base64_encode('cluster'); ?>">Cluster</a></li>
			<li><a href="<?php echo base64_encode('blok'); ?>">Blok</a></li>
			<li><a href="<?php echo base64_encode('bank'); ?>">Bank</a></li>
			
			<!-- FASILITAS -->
			<li class="separator"> Fasilitas </li>
			<li><a href="">SK <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('sk_sewa'); ?>">Sewa</a></li>
					<li><a href="<?php echo base64_encode('sk_psp'); ?>">Pembukaan Sarana Prasarana Lingkungan</a></li>
				</ul>
			</li>
			<li><a href="">Media Promosi <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('kategori_mp'); ?>">Kategori</a></li>
					<li><a href="<?php echo base64_encode('lokasi_mp'); ?>">Lokasi</a></li>
					<li><a href="">Tarif <span></span></a>
						<ul>
							<li><a href="">Billboard / Signboard / Pylon Sign <span></span></a>
								<ul>
									<li><a href="<?php echo base64_encode('kategori_tarif_mp_a'); ?>">Kategori Tarif</a></li>
									<li><a href="<?php echo base64_encode('detail_tarif_mp_a'); ?>">Detail Tarif</a></li>
								</ul>
							</li>
							<li><a href="">Neon box / Neon Sign <span></span></a>
								<ul>
									<li><a href="<?php echo base64_encode('kategori_tarif_mp_b'); ?>">Kategori Tarif</a></li>
									<li><a href="<?php echo base64_encode('detail_tarif_mp_b'); ?>">Detail Tarif</a></li>
								</ul>
							</li>
							<li><a href="">Spanduk / Umbul-Umbul / Standing Display <span></span></a>
								<ul>
									<li><a href="<?php echo base64_encode('kategori_tarif_mp_c'); ?>">Kategori Tarif</a></li>
									<li><a href="<?php echo base64_encode('detail_tarif_mp_c'); ?>">Detail Tarif</a></li>
								</ul>
							</li>
							<li><a href="">Banner / Baliho <span></span></a>
								<ul>
									<li><a href="<?php echo base64_encode('kategori_tarif_mp_d'); ?>">Kategori Tarif</a></li>
									<li><a href="<?php echo base64_encode('detail_tarif_mp_d'); ?>">Detail Tarif</a></li>
								</ul>
							</li>
							<li><a href="<?php echo base64_encode('tarif_mp_e'); ?>">Bus Trans Bintaro Jaya</a></li>
							<li><a href="<?php echo base64_encode('tarif_mp_f'); ?>">Halte Bus Bintaro Jaya dan Trans Bintaro Jaya</a></li>
						</ul>
					</li>
				</ul>
			</li>
			<li><a href="">Pedagang Kaki Lima <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('kategori_pkl'); ?>">Kategori</a></li>
					<li><a href="<?php echo base64_encode('lokasi_pkl'); ?>">Lokasi</a></li>
					<li><a href="<?php echo base64_encode('tarif_pkl'); ?>">Tarif</a></li>
				</ul>
			</li>
			<li><a href="<?php echo base64_encode('pelanggan'); ?>">Pelanggan</a></li>
			
			<!-- 
			<li><a href="">Kegiatan Shooting / Pemotretan <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('kategori_ksp'); ?>">Kategori</a></li>
					<li><a href="<?php echo base64_encode('lokasi_ksp'); ?>">Lokasi</a></li>
					<li><a href="<?php echo base64_encode('tarif_ksp'); ?>">Tarif</a></li>
				</ul>
			</li>
			<li><a href="">Pembukaan Sarana Prasarana Lingkungan <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('kategori_psp'); ?>">Kategori</a></li>
					<li><a href="<?php echo base64_encode('fungsi_psp'); ?>">Fungsi</a></li>
					<li><a href="<?php echo base64_encode('tarif_psp'); ?>">Tarif</a></li>
				</ul>
			</li>
			-->
		</ul>
	</li>
	
	<li><a href="">Proses Tagihan</a>
		<ul>
            <li><a href="<?php echo base64_encode('periode_mp'); ?>">Periode Media Promosi</a></li>
			<li><a href="<?php echo base64_encode('periode_sl'); ?>">Periode Sewa Lahan</a></li>
		</ul>
	</li>
	
	<li><a href="">Bank</a>
		<ul>
			<li><a href="<?php echo base64_encode('export_bank'); ?>">Export</a></li>
			<li><a href="<?php echo base64_encode('import_bank'); ?>">Import</a></li>
		</ul>
	</li>
	
	<li><a href="">Pembayaran</a>
		<ul>
            <li><a href="">Fasilitas <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('pembayaran_mp'); ?>">Pembayaran Media Promosi</a></li>
                    <li><a href="">Pembayaran Pedagang Kaki Lima <span></span></a>
                    	<ul>
                        	<li><a href="<?php echo base64_encode('pembayaran_pkl_baru'); ?>">Pelanggan Baru PKL</a></li>
                            <li><a href="<?php echo base64_encode('pembayaran_pkl_perpanjang'); ?>">Perpanjang Sewa</a></li>
                    	</ul>
                    
                    
                    </li>                                       
                <!--    <li><a href="<?php echo base64_encode('pembayaran_ksp'); ?>">Pembayaran Kegiatan Shooting / Pemotretan</a></li>
                    <li><a href="<?php echo base64_encode('pembayaran_psp'); ?>">Pembayaran Pembukaan Sarana Prasarana Lingkungan</a></li> -->
				</ul>
			</li>
		</ul>
	</li>
	
	<li><a href="">Laporan <span></span></a>
		<ul>
			<li><a href="">Fasilitas<span></span></a>
				<ul>
					<li><a href="">Rencana <span></span></a>
						<ul>
							<li><a href="<?php echo base64_encode('ai_rencana_rincian'); ?>">Rincian</a></li>
							<li><a href="<?php echo base64_encode('ai_rencana_rekap'); ?>">Rekap</a></li>
						</ul>
					</li>
					<li><a href="">Penerimaan <span></span></a>
						<ul>
							<li><a href="<?php echo base64_encode('ai_penerimaan_rincian'); ?>">Rincian</a></li>
							<li><a href="<?php echo base64_encode('ai_penerimaan_rekap'); ?>">Rekap</a></li>
						</ul>
					</li>
					<li><a href="">Piutang <span></span></a>
						<ul>
							<li><a href="<?php echo base64_encode('ai_piutang_rincian'); ?>">Rincian</a></li>
							<li><a href="<?php echo base64_encode('ai_piutang_rekap'); ?>">Rekap</a></li>
							<li><a href="<?php echo base64_encode('ai_piutang_umur'); ?>">Umur Piutang</a></li>
						</ul>
					</li>
					<li><a href="<?php echo base64_encode('ai_pemutusan'); ?>">Pemutusan</a></li>
				</ul>
			</li>
			<li><a href="">Pelanggan <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('pelanggan_rincian'); ?>">Rincian</a></li>
					<li><a href="<?php echo base64_encode('pelanggan_rekap'); ?>">Rekap</a></li>
					<li><a href="<?php echo base64_encode('pelanggan_daftar'); ?>">Daftar</a></li>
				</ul>
			</li>
			
			<li><a href="<?php echo base64_encode('daftar_faktur_pajak'); ?>">Daftar Faktur Pajak</a></li>
		</ul>
	</li>
	
	<li><a href="">Utilitas <span></span></a>
		<ul>
			<li><a href="">Invoice <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('ai_invoice'); ?>">Fasilitas</a></li>
					<li><a href="<?php echo base64_encode('dp_invoice'); ?>">Deposit</a></li>
				</ul>
			</li>
			<li><a href="<?php echo base64_encode('posting_air_ipl'); ?>">Posting Pembayaran</a></li>
			<li><a href="">Faktur Pajak <span></span></a>
				<ul>
					<li><a href="<?php echo base64_encode('fp_penomoran'); ?>">Penomoran</a></li>
					<li><a href="<?php echo base64_encode('fp_cetak'); ?>">Cetak</a></li>
					<li><a href="<?php echo base64_encode('fp_posting'); ?>">Posting</a></li>
				</ul>
			</li>
			
			<li><a href="<?php echo base64_encode('user_management'); ?>">User Management</a></li>
			<li><a href="<?php echo base64_encode(''); ?>">User's Log</a></li>
			
			<!--li><a href="< ?php echo base64_encode('audit_meter_air'); ?>">Audit Meter Air</a></li-->
		</ul>
	</li>
</ul>

<div id="profil">
	<a href="#"><?php echo $_SESSION['NAMA_USER']; ?></a> | <a href="<?php echo BASE_URL; ?>administrator/aut.php?do=logout">Logout</a>
</div>

<div class="clear"></div>
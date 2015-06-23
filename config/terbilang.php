<?php
/*
$bilangan = new Terbilang;
echo $bilangan -> eja(100000000000000012);
*/

Class Terbilang {
	function terbilang() {
		$this->dasar = array(1=>'satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan');
		$this->angka = array(1000000000000,1000000000,1000000,1000,100,10,1,0);
		$this->satuan = array('trilliun','milyar','juta','ribu','ratus','puluh','');
	}
	function eja($n) {
		$i=0;
		$str = '';
		while($n!=0){
			$count = (int)($n/$this->angka[$i]);
			if($count>=10) $str .= $this->eja($count). " ".$this->satuan[$i]." ";
			else if($count > 0 && $count < 10)
			$str .= $this->dasar[$count] . " ".$this->satuan[$i]." ";
			$n -= $this->angka[$i] * $count;
			$i++;
		}
		$str = preg_replace("/satu puluh (\w+)/i","\\1 belas",$str);
		$str = preg_replace("/satu (ribu|ratus|puluh|belas)/i","se\\1",$str);
		if (trim($str)==""){
		   $str="nol";
		} 
		return $str;
	}
	function romawi($bulan){
		$romawi = '';
		switch ($bulan) {
			case '1':
				$romawi = 'I';
				break;
			case '2':
				$romawi = 'II';
				break;
			case '3':
				$romawi = 'III';
				break;
			case '4':
				$romawi = 'IV';
				break;
			case '5':
				$romawi = 'V';
				break;
			case '6':
				$romawi = 'VI';
				break;
			case '7':
				$romawi = 'VII';
				break;
			case '8':
				$romawi = 'VIII';
				break;
			case '9':
				$romawi = 'IX';
				break;
			case '10':
				$romawi = 'X';
				break;
			case '11':
				$romawi = 'XI';
				break;
			case '12':
				$romawi = 'XII';
				break;
			default:
				break;
		}
		return $romawi;
	}

	function nama_bln($bulan){
	$nama = '';
		switch (intval($bulan)) {
			case '1':
				$nama = 'Januari';
				break;
			case '2':
				$nama = 'Februari';
				break;
			case '3':
				$nama = 'Maret';
				break;
			case '4':
				$nama = 'April';
				break;
			case '5':
				$nama = 'Mei';
				break;
			case '6':
				$nama = 'Juni';
				break;
			case '7':
				$nama = 'Juli';
				break;
			case '8':
				$nama = 'Agustus';
				break;
			case '9':
				$nama = 'September';
				break;
			case '10':
				$nama = 'Oktober';
				break;
			case '11':
				$nama = 'November';
				break;
			case '12':
				$nama = 'Desember';
				break;
			default:
				break;
		}
		return $nama;	

	}
}
?>
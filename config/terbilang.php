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
}
?>
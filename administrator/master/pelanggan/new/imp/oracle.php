<?php
class oracle{
	var $user_db ;	//user database
	var $pass_db ;	//password database
	var $db	;		//Database
	var $call_ora ;	//Calling database (telpon kalee...calling)
	var $host ;		//host
	var $service ;	//service

	function oracon($user_db,$pass_db,$host,$service){
		$this->user_db 	= $user_db;
		$this->pass_db 	= $pass_db;
		$this->db		= "(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=1521)))(CONNECT_DATA=(SERVICE_NAME=$service)))";

		$this->call_ora = ocilogon($this->user_db,$this->pass_db,$this->db);
		if(!$this->call_ora){
			echo "Teu tiasa konek ka server database!<br>";	
			//return false;
			$this->logoff();
		} else {
			return $this->call_ora;
		}
	}

	function oraselect($sql=""){
		if(!empty($sql)){
		OCIInternalDebug(0);
		//$this->oracon();
		$data = array();
		$stmt = ociparse($this->call_ora,$sql);
		$reslt = ociexecute($stmt);
		//if(!$reslt) $this->error("eleuh-eleuh SQL Select na lepat kang...<br>");
		$n = 0 ;
				while ( OCIFetch($stmt) ) {
					$ncols = OCINumCols($stmt);
						for ( $i = 1; $i <= $ncols; $i++ ) {
							$column_name  = OCIColumnName($stmt,$i);
							$column_value = OCIResult($stmt,$i);
							$data[$n][$column_name]= $column_value;
					}
					$n++;
				}
			$this->orafree($stmt);
			return $data;
		} else {
			echo "eleuh-eleuh SQL Select na lepat kang...<br>".$sql;
		}
	}
	
	function oracount($sql=""){
		if(!empty($sql))
		{
			$stmt = oci_parse($this->call_ora,$sql);
	        oci_define_by_name($stmt, 'NUM_ROWS', $num_rows);
	        oci_execute($stmt);
	        oci_fetch($stmt);
	        if($num_rows > 0) $this->error("Data telah ada.<br>");

	        return $num_rows;
		}
	}

	function oracount2($sql=""){
		if(!empty($sql))
		{
			$stmt = oci_parse($this->call_ora,$sql);
	        oci_define_by_name($stmt, 'NUM_ROWS', $num_rows);
	        oci_execute($stmt);
	        oci_fetch($stmt);
	        if($num_rows > 0) $this->error("");

	        return $num_rows;
		}
	}

	function orainsert($sql=""){
		if(!empty($sql)){
			//$this->oracon();
			$stmt = ociparse($this->call_ora,$sql);
			$reslt = ociexecute($stmt,OCI_DEFAULT);
			//if(!$reslt) $this->error("eleuh-eleuh SQL Insert na lepat kang.<br>");
			$this->oracommit();
			return $stmt;
		} else {
			echo "eleuh-eleuh SQL Insert na lepat kang..<br>".$sql;	
		}
	}

	function oraupdate($sql=""){
		if(!empty($sql)){
			//$this->oracon();
			$stmt = ociparse($this->call_ora,$sql);
			$reslt = ociexecute($stmt,OCI_DEFAULT);
			//if(!$reslt) $this->error("eleuh-eleuh SQL Update na lepat kang...<br>");
			$this->oracommit();
			return $stmt;
		} else {
			echo "eleuh-eleuh SQL Update na lepat kang...<br>".$sql;	
		}	
	}

	function oradelete($sql=""){
		if(!empty($sql)){
			//$this->oracon();
			$stmt = ociparse($this->call_ora,$sql);
			$reslt = ociexecute($stmt,OCI_DEFAULT);
			//if(!$reslt) $this->error("eleuh-eleuh SQL Delete na lepat kang...<br>");
			$this->oracommit();
			return $stmt;
		} else {
			echo "eleuh-eleuh SQL Delete na lepat kang...<br>".$sql;	
		}	
	}

	function oracommit(){
		$retrn = ocicommit($this->call_ora);
		return $retrn;
	}

	function orafree($stmt){
		$retrn = OCIFreeStatement($stmt);  	
		return $retrn;
	}

	function error($text){
		$this->logoff();
		echo $text;
	}

	function logoff(){
		$retrn = ocilogoff($this->call_ora);
		return $retrn;	
	}

	function rollback(){
		$reslt = ocirollback($this->call_ora);
		$this->oracommit();
		return $reslt;
	}
}
?>
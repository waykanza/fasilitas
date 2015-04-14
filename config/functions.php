<?php
require_once('functions.inc.php');

/* ====== DATA BASE ====== */
function conn()
{
	if (DNS)
	{
		$conn =&ADONewConnection('odbc_'.DRIVER);
		$conn->SetFetchMode(ADODB_FETCH_BOTH);
		$dsn = 'Driver={SQL Server};Server='.HOST.';Database='.DB.';';
		$conn->Connect($dsn, USR, PWD) or die('Failed Connected to SQL Server');
	}
	else
	{
		$conn =&ADONewConnection(DRIVER);
		$conn->SetFetchMode(ADODB_FETCH_BOTH);
		$conn->Connect(HOST, USR, PWD, DB) or die('Failed Connected to SQL Server');
	}
	
	return $conn;
}

function close($conn = FALSE)
{
	if ($conn) { $conn->close(); }
}

function clogin()
{
	if ( ! isset($_SESSION['ID_USER'])) { header('location: ' . BASE_URL); }
}

function cmodul($idm)
{
	/*if ( ! in_array($idm, $_SESSION['ID_MODUL'])) 
	{
		echo '
		<script type="text/javascript">
		alert("Anda tidak memiliki hak akses modul ini!");
		location.href = "' . BASE_URL . '";
		</script>';
		exit;
	}*/
}
?>
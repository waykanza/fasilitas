<!DOCTYPE html>
<head>
<?php 
require_once('config/config.php');
$conn = conn();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Program Pembayaran Fasilitas </title>
<link type="image/x-icon" rel="icon" href="images/favicon.ico">

<!-- CSS -->
<link type="text/css" href="config/css/style.css" rel="stylesheet">
<link type="text/css" href="config/css/menu.css" rel="stylesheet">
<link type="text/css" href="plugin/css/zebra/default.css" rel="stylesheet">
<link type="text/css" href="plugin/window/themes/default.css" rel="stylesheet">
<link type="text/css" href="plugin/window/themes/mac_os_x.css" rel="stylesheet">

<!-- JS -->
<script type="text/javascript" src="plugin/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="plugin/js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="config/js/menu.js"></script>
<script type="text/javascript" src="plugin/js/jquery.inputmask.custom.js"></script>
<script type="text/javascript" src="plugin/js/keymaster.js"></script>
<script type="text/javascript" src="plugin/js/zebra_datepicker.js"></script>
<script type="text/javascript" src="plugin/js/jquery.ajaxfileupload.js"></script>
<script type="text/javascript" src="plugin/window/javascripts/prototype.js"></script>
<script type="text/javascript" src="plugin/window/javascripts/window.js"></script>
<script type="text/javascript" src="config/js/main.js"></script>
<script type="text/javascript">
$(function() {
	$('#id_user').focus();
});
</script>
<style type="text/css">
html { height:100%; }
body {
	position:relative;
	background:#990000;
	margin:0;
}
body { height:100%; }
</style>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<span class="pkb">
			<span class="big">P</span>engelola <span class="big">K</span>awasan <span class="big">B</span>intaro
			<span class="desc">Program Fasilitas </span>
		</span>
	</div>
	<div id="content">
		<div class="clear"></div>
		<br>
		<form id="form-login" method="post" action="<?php echo BASE_URL; ?>administrator/aut.php?do=login">
			<div class="title-page text-left">Login Form</div>
			<table class="t-control">
			<tr>
				<td width="120px">User ID</td>
				<td><input type="text" id="id_user" name="id_user"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" id="pass_user" name="pass_user"></td>
			</tr>
			<tr>
			
				<td colspan=2><button type="submit" id="login" style="width:260px;"><b>Login</b></button></td>
			</tr>
			</table>
		</form>
		<div class="clear"></div>
	</div>
</div>
<div id="footer">&copy; 2014 - PT. Jaya Real Property, Tbk<br>Built By ASYS IT Consultant</div>
</body>
</html>
<?php close($conn); ?>
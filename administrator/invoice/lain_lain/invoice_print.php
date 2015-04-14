<?php
require_once('../../../config/config.php');
require_once('../../../config/terbilang.php');
die_login();
die_mod('U10');
$conn = conn();
die_conn($conn);

?>

<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Excel.Sheet>
<meta name=Generator content="Microsoft Excel 14">
<style id="KVMBDPLL_Styles">
@media print {
	.wrap {
		margin: 0;
		width: initial;
		min-height: initial;
		background: initial;
		page-break-after: always;
	}
}
	
table {
	mso-displayed-decimal-separator:"\.";
	mso-displayed-thousand-separator:"\,";
}

.xl15{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Arial;mso-generic-font-family:auto;mso-font-charset:1;mso-number-format:General;text-align:general;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl63{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl64{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:8.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl65{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl66{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl67{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl68{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl69{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:"_\(* \#\,\#\#0_\)\;_\(* \\\(\#\,\#\#0\\\)\;_\(* \0022-\0022_\)\;_\(\@_\)";text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl70{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:"_\(* \#\,\#\#0_\)\;_\(* \\\(\#\,\#\#0\\\)\;_\(* \0022-\0022_\)\;_\(\@_\)";text-align:general;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl71{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl72{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Arial;mso-generic-font-family:auto;mso-font-charset:1;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl73{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl74{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl75{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl76{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Arial;mso-generic-font-family:auto;mso-font-charset:1;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl77{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl78{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:1.0pt solid windowtext;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl79{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl80{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:"_\(* \#\,\#\#0_\)\;_\(* \\\(\#\,\#\#0\\\)\;_\(* \0022-\0022_\)\;_\(\@_\)";text-align:general;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl81{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;border:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl82{color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;padding-left:12px;mso-char-indent-count:1;}
.xl83{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:"mmm\\-yy";text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl84{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl85{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl86{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl87{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl88{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:7.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl89{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:7.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl90{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:7.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl91{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl92{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl93{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:14.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl94{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl95{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:.5pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl96{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:.5pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:.5pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl97{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:.5pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl98{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:middle;border-top:1.0pt solid windowtext;border-right:.5pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl99{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:middle;border-top:1.0pt solid windowtext;border-right:.5pt solid windowtext;border-bottom:none;border-left:.5pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl100{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:middle;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:.5pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl101{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl102{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl103{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl104{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Arial;mso-generic-font-family:auto;mso-font-charset:1;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl105{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:8.0pt;font-weight:700;font-style:italic;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl106{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:8.0pt;font-weight:700;font-style:italic;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl107{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl108{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl109{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl110{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:underline;text-underline-style:single;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl111{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:8.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:general;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl112{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:8.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl113{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:1.0pt solid windowtext;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl114{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl115{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:9.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl116{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:left;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl117{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Arial;mso-generic-font-family:auto;mso-font-charset:1;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl118{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl119{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:1.0pt solid windowtext;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl120{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl121{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:1.0pt solid windowtext;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl122{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl123{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl124{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:1.0pt solid windowtext;border-bottom:none;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl125{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:1.0pt solid windowtext;border-left:1.0pt solid windowtext;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl126{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:1.0pt solid windowtext;border-right:none;border-bottom:1.0pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl127{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:10.0pt;font-weight:400;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;border-top:none;border-right:none;border-bottom:1.5pt solid windowtext;border-left:none;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
.xl128{padding-top:1px;padding-right:1px;padding-left:1px;mso-ignore:padding;color:windowtext;font-size:12.0pt;font-weight:700;font-style:normal;text-decoration:none;font-family:Verdana, sans-serif;mso-font-charset:0;mso-number-format:General;text-align:center;vertical-align:bottom;mso-background-source:auto;mso-pattern:auto;white-space:nowrap;}
</style>
</head>

<body onload="window.print()">
<!--[if !excel]>&nbsp;&nbsp;<![endif]-->
<!--The following information was generated by Microsoft Excel's Publish as Web Page wizard.-->
<!--If the same item is republished from Excel, all information between the DIV tags will be replaced.-->
<!----------------------------->
<!--START OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD -->
<!----------------------------->

<div id="KVMBDPLL_" align=center x:publishsource="Excel">
<?php
$terbilang = new Terbilang;

$obj = $conn->Execute("SELECT DENDA_RUPIAH, DENDA_PERSEN FROM KWT_PARAMETER");

$denda_rupiah	= $obj->fields['DENDA_RUPIAH'];
$denda_persen	= $obj->fields['DENDA_PERSEN'];

$cb_data	= (isset($_REQUEST['cb_data'])) ? $_REQUEST['cb_data'] : array();
$trx		= (isset($_REQUEST['trx'])) ? $_REQUEST['trx'] : '';

$in_id_pembayaran = array();
foreach ($cb_data as $x) { $in_id_pembayaran[] = base64_decode($x); }
$in_id_pembayaran = implode("' ,'", $in_id_pembayaran);

$query = "
SELECT 
	b.ID_PEMBAYARAN, 
	b.TRX, 
	b.NO_INVOICE, 
	(CASE WHEN p.AKTIF_SM = 1 THEN p.SM_NAMA_PELANGGAN ELSE p.NAMA_PELANGGAN END) AS NAMA_PELANGGAN, 
	(CASE WHEN p.AKTIF_SM = 1 THEN p.SM_ALAMAT ELSE p.ALAMAT END) AS ALAMAT, 
	s.NAMA_SEKTOR, 
	c.NAMA_CLUSTER,
	b.KODE_BLOK, 
	b.NO_PELANGGAN, 
	CONVERT(VARCHAR(10), b.TGL_JATUH_TEMPO, 120) AS TGL_JATUH_TEMPO, 
	CONVERT(VARCHAR(10), b.CREATED_DATE, 120) AS TGL_IVC, 
	
	b.PERIODE_TAG,
	b.PERIODE_IPL_AWAL,
	b.PERIODE_IPL_AKHIR,
	
	b.JUMLAH_IPL,
	b.DISKON_IPL,
	b.DENDA,
	(b.JUMLAH_IPL + b.DENDA - b.DISKON_IPL) AS JUMLAH_BAYAR, 
	
	b.PERSEN_PPN,
	
	b.KET_IVC
FROM 
	KWT_PEMBAYARAN_AI b 
	LEFT JOIN KWT_PELANGGAN p ON b.NO_PELANGGAN = p.NO_PELANGGAN 
	LEFT JOIN KWT_SEKTOR s ON b.KODE_SEKTOR = s.KODE_SEKTOR 
	LEFT JOIN KWT_CLUSTER c ON b.KODE_CLUSTER = c.KODE_CLUSTER 
	LEFT JOIN KWT_USER uc ON b.USER_CETAK_KWT = uc.ID_USER 
WHERE 
	$where_trx_lain_lain AND 
	b.STATUS_BAYAR = 0 AND 
	p.INFO_TAGIHAN = 1 AND 
	b.ID_PEMBAYARAN IN ('$in_id_pembayaran') AND 
	b.TRX = $trx
	
ORDER BY b.KODE_BLOK
";
	
$obj = $conn->Execute($query);

while( ! $obj->EOF)
{
	$id_pembayaran		= $obj->fields['ID_PEMBAYARAN'];
	$trx				= $obj->fields['TRX'];
	$no_invoice 		= $obj->fields['NO_INVOICE'];
	
	$nama_pelanggan		= $obj->fields['NAMA_PELANGGAN'];
	$alamat				= $obj->fields['ALAMAT'];
	
	$nama_sektor		= $obj->fields['NAMA_SEKTOR']; 
	$nama_cluster		= $obj->fields['NAMA_CLUSTER'];
	$kode_blok			= $obj->fields['KODE_BLOK']; 
	$no_pelanggan		= $obj->fields['NO_PELANGGAN']; 
	$tgl_jatuh_tempo	= $obj->fields['TGL_JATUH_TEMPO'];
	$tgl_ivc			= $obj->fields['TGL_IVC'];
	
	$periode_tag		= $obj->fields['PERIODE_TAG'];
	$periode_ipl_awal	= $obj->fields['PERIODE_IPL_AWAL'];
	$periode_ipl_akhir	= $obj->fields['PERIODE_IPL_AKHIR'];
	
	$jumlah_ipl			= $obj->fields['JUMLAH_IPL'];
	$diskon_ipl			= $obj->fields['DISKON_IPL'];
	$denda				= $obj->fields['DENDA'];
	$jumlah_bayar		= $obj->fields['JUMLAH_BAYAR']; 
	
	$persen_ppn			= $obj->fields['PERSEN_PPN'];
	$ket_ivc			= $obj->fields['KET_IVC'];
	
	$text_st = '';
	
	if ($trx == $trx_lbg) { $text_st = 'MEMBANGUN'; }
	elseif ($trx == $trx_lrv) { $text_st = 'RENOVASI'; }

	?>
	<div class="wrap">
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
		<td colspan=9 class=xl84>IURAN PEMELIHARAAN LINGKUNGAN <?php echo $text_st; ?></td>
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
		<td colspan=9 class=xl84>Biaya lain-lain</td>
		<td class=xl75>=</td>
		<td class=xl63>Rp.</td>
		<td class=xl70 style="text-align:right;"><span style='mso-spacerun:yes'></span><?php echo to_money($jumlah_bayar); ?></td>
		</tr>
		<tr height=17 style='height:12.75pt'>
		<td height=17 class=xl15 style='height:12.75pt'></td>
		<td class=xl71>&nbsp;</td>
		<td colspan=9 class=xl84><?echo $ket_ivc; ?></td>
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
		<td colspan=11 class=xl105 style='border-right:1.0pt solid black'># <?php echo ucfirst($terbilang->eja($jumlah_bayar)); ?> #</td>
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
		
	</div>
	<?php
	$obj->movenext();
}

$conn->Execute("
UPDATE KWT_PEMBAYARAN_AI 
SET 
	STATUS_CETAK_KWT = 1, 
	TGL_CETAK_KWT = GETDATE(), 
	USER_CETAK_KWT = '$sess_id_user', 
					
	USER_MODIFIED = '$sess_id_user', 
	MODIFIED_DATE = GETDATE() 
WHERE ID_PEMBAYARAN IN ('$in_id_pembayaran')");
?>

</div>
<!----------------------------->
<!--END OF OUTPUT FROM EXCEL PUBLISH AS WEB PAGE WIZARD-->
<!----------------------------->
</body>
</html>

<?php close($conn); ?>

<html>
<head><title>Contoh Koneksi Mesin Absensi Mengunakan SOAP Web Service</title></head>
<body bgcolor="#caffcb">

<H3>Upload Sidik Jari</H3>

<?php
$IP=$HTTP_GET_VARS["ip"];
$Key=$HTTP_GET_VARS["key"];
$id=$HTTP_GET_VARS["id"];
$fn=$HTTP_GET_VARS["fn"];
$temp=$HTTP_GET_VARS["temp"];

if($IP=="") $IP="192.168.1.20";
if($Key=="") $Key="0";
if($id=="") $id="1";
if($fn=="") $fn="0";
?>

<form action="upload-sidik-jari.php">
IP Address: <input type="Text" name="ip" value="<?php echo $IP?>" size=15><BR>
Comm Key: <input type="Text" name="key" size="5" value="<?php echo $Key?>"><BR><BR>

UserID: <input type="Text" name="id" size="5" value="<?php echo $id?>"><BR>
Finger No: <input type="Text" name="fn" size="1" value="<?php echo $fn?>"><BR><BR>
Template Sidik jari: <BR>
<textarea name="temp" rows="7" name="isi" cols="40"><?php echo $temp?></textarea><BR><BR>

<input type="Submit" value="Upload">
</form>
<BR>

<?php
if($HTTP_GET_VARS["ip"]!=""){?>

	<?php
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<SetUserTemplate><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">".$id."</PIN><FingerID xsi:type=\"xsd:integer\">".$fn."</FingerID><Size>".strlen($temp)."</Size><Valid>1</Valid><Template>".$temp."</Template></Arg></SetUserTemplate>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
	}else echo "Koneksi Gagal";
	include("parse.php");
//	echo $buffer;
	$buffer=Parse_Data($buffer,"<SetUserTemplateResponse>","</SetUserTemplateResponse>");
	$buffer=Parse_Data($buffer,"<Information>","</Information>");
	echo "<B>Result:</B><BR>".$buffer;
	
	//Refresh DB
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	$soap_request="<RefreshDB><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey></RefreshDB>";
	$newLine="\r\n";
	fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
    fputs($Connect, "Content-Type: text/xml".$newLine);
    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
    fputs($Connect, $soap_request.$newLine);

}?>


</body>
</html>
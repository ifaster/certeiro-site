<?php
/*******************************************************
*   File     : gens3key.php                            *
*   Package  : AVLock Basic Online key generator       *
*   Author   : Alcides Valega                          *
*   Version  : 3.0 - May 12, 2008                      *
*   Copyright: (c) Alcides Valega, 2002 - 2008         *
*                                                      *
*   Part of the AVLock SIMPLE package                  *
*******************************************************/

  require_once("./genlib.php");


$key = 'NO DATA POSTED';

if (isset($_POST['data']))
{
  $data = $_POST['data'];
  $data = decryptstr($data);
  $arr=explode("|",$data);
  if (count($arr)==10)
  {
    $icsum    = trim($arr[0],"\x00..\x20");
    $icode    = trim($arr[1],"\x00..\x20");
    $appid    = trim($arr[2],"\x00..\x20");
    $enckey   = trim($arr[3],"\x00..\x20");
    $ver      = trim($arr[4],"\x00..\x20");
    $module   = trim($arr[5],"\x00..\x20");
    $kind     = trim($arr[6],"\x00..\x20");
    $instances= trim($arr[7],"\x00..\x20");
    $days     = trim($arr[8],"\x00..\x20");
    $vals     = trim($arr[9],"\x00..\x20");

    if (strlen($icode) != 27)  $key = 'ICODE LENGTH MISMATCH';
    else if (strlen($icsum) != 4)  $key = 'ICSUM LENGTH MISMATCH';
    else
    {
      if (!preg_match("([0-9A-F]{3})",$vals)) $vals = $trialvalues;
      if ($days == 0) $days = $trialdays;
      if ($instances == 0) $instances = $trialinstances;
      if ($kind > 2) $kind = 0;

      $udate = (int) (date("U")/86400)+25569; //current local date in delphi mode (days from 12/31/1899)
      //  date("U") = seconds from 01/01/1970 up to the current date
      //  86400 = seconds by one day (60 * 60 * 24)
      //  25569 = days from 12/31/1899 to 01/01/1970
      //  -1 = to ensure a local date even greather than the $startdate value

      // create file
      $fil = './temp/'.$module.$icode.$appid.'.txt';
      if (file_exists($fil))
      {
        if ($fp = fopen($fil,"r"))
        {
          $key = fread($fp,31);
          fclose($fp);
        }
      } else
      {
        $key = calculatekey($kind,$appid,$icsum,$enckey,1,$instances,$module,$days,$udate,$vals);

        if ($fp = fopen($fil,"w"))
        {
          fwrite($fp,$key,strlen($key));
          fclose($fp);
        } else $key = 'COULD NOT SAVE FILE'.$fil;
      }
    }
  }
}

echo (encryptstr($key));

?>

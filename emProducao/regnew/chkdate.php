
<?php

 //if the difference between the greenwich time with the local time is
 //minor than 2, then return 0, else return 1.

 require_once("./genlib.php");

 $result='';

 if (isset($_POST['data'])) {
   $ldate = $_POST['data'];
   $ldate = (int) decryptstr($ldate);

   $udate = (int) (date("U")/86400)+25569; //current local date in delphi mode (days from 12/31/1899)
   $dife = abs($udate - $ldate);
   if ($dife < 2) $result = '0'; else $result = '1';

 } else $result = '2';

 echo (encryptstr($result));

?>


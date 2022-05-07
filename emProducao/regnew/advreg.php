<?php
/*******************************************************
*   File     : avlock_gensimple3key.php                *
*   Package  : AVLOCK Online License Manager           *
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

//Error constants

//Error constants
 error_reporting(0);
 $NO_ERROR                              = '50';
 $VALUES_POSTED_COUNT_MISMATCH          = '51';
 $NO_DATA_POSTED                        = '52';
 $ICODE_LENGTH_MISMATCH                 = '53';
 $ICSUM_LENGTH_MISMATCH                 = '54';
 $SECONDARY_LINKED_TO_SECONDARY         = '55';
 $PRIMARY_NOT_EXISTS                    = '56';
 $KEY_LENGTH_MISMATCH                   = '57';
 $COULD_NOT_UPDATE_RECORDS              = '58';
 $COULD_NOT_CONNECT_TO_DATABASE         = '59';
 $NOT_EXTENSION_ALLOWED                 = '60';
 $NOT_MORE_USERS_ALLOWED                = '61';
 $COULD_NOT_CONNECT_TO_DATABASE         = '62';
 $NOT_PAID                              = '63';
 $COULD_NOT_DELETE_RECORDS              = '64';
 $RECORD_ALREADY_EXISTS                 = '65';
 $RECORD_NOT_EXISTS                     = '66';
 $RECORD_NOT_SAVED                      = '67';
 $RECORD_IS_NOT_PRIMARY                 = '68';
 $RECORD_IS_NOT_SECONDARY               = '69';
 $RECORD_IS_NOT_MOVED                   = '70';
 $RECORD_IS_MOVED                       = '71';
 $COUNT_OF_RECORDS_IS_ZERO              = '72';
 $HARDICODE_LENGTH_MISMATCH             = '73';


require_once("./genlib.php");
require_once("./reg_inc.php");

//action|appname|icsum|icode|icodep|appid|enckey|ver|username|company|module|kind|users|moved|startdate|days|values|regKey

$result = '';//encryptstr('NO DATA POSTED');

if (!isset($_POST['data'])){
  $data = $_GET['data'];
} else {
  $data = $_POST['data'];
}

//echo 'valor de data :'.$data.'<br>';
if (isset($data))
{
  $data = decryptstr($data);
  $arr=explode("|",$data);
  //print_r($arr);

  if (count($arr)==32)
  {
    $action   = trim($arr[0], "\x00..\x20");
    $appname  = trim($arr[1], "\x00..\x20");
    $icsum    = trim($arr[2], "\x00..\x20");
    $icode    = trim($arr[3], "\x00..\x20");
    $icodep   = trim($arr[4], "\x00..\x20");
    $appid    = trim($arr[5], "\x00..\x20");
    $enckey   = trim($arr[6], "\x00..\x20");
    $email    = trim($arr[7], "\x00..\x20");
    $username = trim($arr[8], "\x00..\x20");
    $company  = trim($arr[9], "\x00..\x20");
    $module   = trim($arr[10],"\x00..\x20");
    $kind     = trim($arr[11],"\x00..\x20");
    $users    = trim($arr[12],"\x00..\x20");
    $instances= trim($arr[13],"\x00..\x20");
    $moved    = trim($arr[14],"\x00..\x20");
    $startdate= trim($arr[15],"\x00..\x20");
    $days     = trim($arr[16],"\x00..\x20");
    $vals     = trim($arr[17],"\x00..\x20");
    $regkey   = trim($arr[18],"\x00..\x20");
    $paid     = trim($arr[19],"\x00..\x20");
    $extend   = trim($arr[20],"\x00..\x20");
    $icodeh   = trim($arr[21],"\x00..\x20");
    $tppessoa = trim($arr[22],"\x00..\x20");
    $endereco = trim($arr[23],"\x00..\x20");
    $bairro   = trim($arr[24],"\x00..\x20");
    $cidade   = trim($arr[25],"\x00..\x20");
    $uf       = trim($arr[26],"\x00..\x20");
    $contato  = trim($arr[27],"\x00..\x20");
    $telefone = trim($arr[28],"\x00..\x20");
    $cpfcnpj  = trim($arr[29],"\x00..\x20");
    $rgie     = trim($arr[30],"\x00..\x20");
    $cep      = trim($arr[31],"\x00..\x20");

    if (strlen($icode) != 27){
      $result = $ICODE_LENGTH_MISMATCH;
    } else if ((strlen($icodeh) != 27) && (strlen($icodeh) != 0)) {
      $result = $HARDICODE_LENGTH_MISMATCH;
    } else if (strlen($icsum) != 4) {
      $result = $ICSUM_LENGTH_MISMATCH;
    } else {
      if (!preg_match("([0-9A-F]{3})",$vals)){
        $vals = $trialvalues;
      }
      if ($days == 0){
        $days = $trialdays;
      }
      if ($users == 0){
        $users = $trialusers;
      }
      if (($kind != 2) && ($kind != 0)){
        $kind = 0;
      }
      $udate = (int) (date("U")/86400) + 25569; //current local date in delphi mode (days from 12/31/1899)

      //if (abs($startdate - $udate) >1) $startdate = $udate;

      $recordexists = ( readrecord( $appid, $appname, $icode, $module, $username1,
                                    $company1, $email1, $exten1, $paid1, $lastkey1,
                                    $days1, $keydate1, $move1, $instances1, $users1,
                                    $icsum1, $icodep1, $primar1, $vals1, $kind1,
                                    $startdate1, $icodeh1, $tppessoa1, $endereco1,
                                    $bairro1, $cidade1, $uf1, $contato1, $telefone1,
                                    $cpfcnpj1, $rgie1, $cep1));

	  $upd = True;
      $add = False;

      switch($action)
      {
        case '0':  //SYNSCHRONIZE   OLM -> LOCAL DATA
                   //For primary and secondary records
                   //Only if record exists. No data changed in database
                   //Return (Key|Moved|Primary|icode)

          if ($recordexists)
          {
            if ($primar1 == 'Y'){
              $result = $lastkey1.$move1.'Y'.$icode.$icodeh1; //Return primary key
            } else {
              $primaryexists = (readrecord($appid, $appname, $icodep1, $module, $username2, $company2, $email2,
                $exten2, $paid2, $lastkey2, $days2, $keydate2, $move2, $instances2, $users2, $icsum2, $icodep2,
                $primar2, $vals2, $kind2, $startdate2, $icodeh2, $tppessoa2, $endereco2,
                $bairro2, $cidade2, $uf2, $contato2, $telefone2, $cpfcnpj2, $rgie2, $cep2));
              if ($primaryexists)
              {
                if ($primar2 == 'Y') $result = $lastkey2.$move2.'N'.$icodep1.$icodeh1; //return secondary key
                else $result = $SECONDARY_LINKED_TO_SECONDARY;
              } else $result = $PRIMARY_NOT_EXISTS;
            }
          } else $result = $RECORD_NOT_EXISTS;
          break;



        case '1':  //START ONLINE TRIAL:
                 //If record exists the local registration data will be sinchronized from the OLM.
                 //Otherwise a new trial primary record will be added into the OLM
                 //Return (Key|Moved|Primary|icode)
          $key0='';
          if ($recordexists)
          { //SYNCHRONIZE
            if ($primar1 == 'Y') $key0 = $lastkey1.$move1.$primar1.$icode.$icodeh;
            else $key0 = $RECORD_IS_NOT_PRIMARY;
          }
          else
          {
            $startdate = $udate;
            $key0 = calculatekey(0, $appid, $icsum, $enckey, $trialusers, $trialinstances, $module, $trialdays,
            $startdate, $trialvalues);
            if (strlen($key0) == 31)
            { //Create record
              saverecord($add, $appid, $appname, $icode, $module, $username, $company, $email,
                'N', 'N', $key0, $trialdays, date("d/m/y"), 'N', '0', $trialusers, $icsum,
                $icode, 'Y', $trialvalues, 0, $startdate, $icodeh, $tppessoa, $endereco,
                $bairro, $cidade, $uf, $contato, $telefone, $cpfcnpj, $rgie, $cep);
                $key0 = $key0.'NY'.$icode.$icodeh;
            } else $key0 = $KEY_LENGTH_MISMATCH;
          }

          $result = $key0;

          break;


        case '2': //EXTEND TRIAL ONLINE:
                //Read a primary record and check the Exten field, if 'Y' then generate
                //and return the new key and spread it to all associated secondary records
                 //Return (Key|Moved|Primary|icode)

          $key0='';
          if ($recordexists)
          {
            if ($primar1 == 'Y')
            {
              if ($exten1 == 'Y')
              {
                //if (abs($startdate - $udate) >1)
                $startdate = $udate;
                $key0 = calculatekey(0, $appid, $icsum, $enckey, $trialusers, $trialinstances, $module, $extendays,
                  $startdate, $trialvalues);
                if (strlen($key0) == 31)
                { //Update record
                  if (getmyconnection())
                  {
                    $kdate =  date("d/m/y");
                    $qry = "UPDATE $sql_table set EXTEN = 'N', MOVED = 'N', LASTKEY = '$key0',
                            KEYDATE = '$kdate'
                            WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                    if (mysql_query($qry, $sql_link)) $key0 = $key0.'NY'.$icode.$icodeh;
                    else $key0 = $COULD_NOT_UPDATE_RECORDS;
                    cleanupdb();
                  } else $result = $COULD_NOT_CONNECT_TO_DATABASE;

                } else $key0 = $KEY_LENGTH_MISMATCH;
              } else $key0 = $NOT_EXTENSION_ALLOWED;
            } else $key0 = $RECORD_IS_NOT_PRIMARY;
          } else $key0 = $RECORD_NOT_EXISTS;

          $result = $key0;

          break;


        case '3': //REGISTER KEY OFFLINE:     KEY -> OLM
                //At first time or for renewal. If record not exists then creates
                //a new one. Saves Key and registration data into the record
                //Return 'OK' if succedd.

          $len = strlen($regkey);
          if ($len == 31)
          {
            if ($recordexists) $flg = $upd;  //RENEWAL
            else $flg = $add; //FIRST TIME

            $ok = saverecord($flg, $appid, $appname, $icode, $module, $username, $company, $email,
              'N', 'N', $regkey, $days, date("d/m/y"), 'N', $instances, $users, $icsum,
              $icode, 'Y', $vals, $kind, $startdate, $icodeh, $tppessoa, $endereco,
                $bairro, $cidade, $uf, $contato, $telefone, $cpfcnpj, $rgie, $cep);

            if ($ok) $result = $NO_ERROR; else $result = $RECORD_NOT_SAVED;
          } else $result = $KEY_LENGTH_MISMATCH;
          break;


        case '4': //SECONDARY REGISTRATION: If are users availables then create a new
                //secondary record for the given icode and icodep and return the
                //regkey from the primary record
                //Return (Key|Moved|Primary|icode)

          if ($recordexists) $result = $RECORD_ALREADY_EXISTS;
          else
          {
            $primaryexists = (readrecord($appid, $appname, $icodep, $module, $username2, $company2, $email2,
              $exten2, $paid2, $lastkey2, $days2, $keydate2, $move2, $instances2, $users2, $icsum2, $icodep2,
              $primar2, $vals2, $kind2, $startdate2, $icodeh2, $tppessoa2, $endereco2,
                $bairro2, $cidade2, $uf2, $contato2, $telefone2, $cpfcnpj2, $rgie2, $cep2));
            if ($primaryexists)
            {
              if ($primar2 == 'Y')
              {
                $cnt = countrecords($module, $appid, $icodep); //count of records with the same $icodep
                if ($cnt > 0)
                {
                  if ($cnt < $users2) //compare with authorized users $users2
                  {
                    $ok = saverecord($add, $appid, $appname, $icode, $module, $username2, $company2, '',
                      'N', 'N', $lastkey2, $days2, date("d/m/y"), 'N', '0', $users2, $icsum,
                      $icodep2, 'N', $vals2, $kind2, $startdate2, $icodeh2, $tppessoa2, $endereco2,
                      $bairro2, $cidade2, $uf2, $contato2, $telefone2, $cpfcnpj2, $rgie2, $cep2);
                    if ($ok)
                    {
                      if (strlen($lastkey2) == 31) $result = $lastkey2.'NN'.$icodep; //SUCCESS
                      else $result = $KEY_LENGTH_MISMATCH;
                    } else $result = $RECORD_NOT_SAVED;

                  } else $result = $NOT_MORE_USERS_ALLOWED;
                } else $result = $COUNT_OF_RECORDS_IS_ZERO;
              } else $result = $RECORD_IS_NOT_PRIMARY;
            } else $result = $PRIMARY_NOT_EXISTS;
          }
          break;


        case '5': //RENEW REGISTRATION ONLINE: read a primary record and check the Paid
                //field, if 'Y' then generate and return the new key and spread it to
                //all associated secondary records
                //Return (Key|Moved|Primary|icode)


          $key0='';
          if ($recordexists)
          {
            if ($primar1 == 'Y')
            {
              if ($paid1 == 'Y')
              {
                //if (abs($startdate - $udate) >1)
                $startdate = $udate;
                if ($days1 == 65535) $kind = 2; //Permanent
                else $kind = 1;                 //Temporary
                $key0 = calculatekey($kind,$appid,$icsum,$enckey,$users1,$instances1,$modul1,$days1,
                $startdate,$vals1);
                if (strlen($key0) == 31)
                { //Update record
                  if (getmyconnection())
                  {
                    $kdate =date("d/m/y");
                    $qry = "UPDATE $sql_table set PAID = 'N', MOVED='N', LASTKEY = '$key0', STARTDATE = '$startdate',
                           KEYDATE = '$kdate', KIND = '$kind'
                          WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                    if (mysql_query($qry, $sql_link)) $key0 = $key0.'NY'.$icode.$icodeh;
                    else $key0 = $COULD_NOT_UPDATE_RECORDS;
                    cleanupdb();
                  } else $result = $COULD_NOT_CONNECT_TO_DATABASE;

                } else $key0 = $KEY_LENGTH_MISMATCH;
              } else $key0 = $NOT_PAID;
            } else $key0 = $RECORD_IS_NOT_PRIMARY;
          } else $key0 = $RECORD_NOT_EXISTS;

          $result = $key0;

          break;


        case '6': //MOVE PRIMARY STEP1:
                //From the current PC. Access to all records with (icodep = current icode)
                //and set (Moved=Y). All associated licenses will be disabled.
                //Return (Key|Moved|Primary|icode)

          $result = '';
          if ($recordexists)
          {
            if ($primar1 == 'Y')
            {
              if ($move1 == 'N')
              {
                if (getmyconnection())
                {
                  $qry = "UPDATE $sql_table set MOVED = 'Y'
                          WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                  if (mysql_query($qry, $sql_link)) $result = $result = $lastkey1.'YY'.$icode;
                  else $result = $COULD_NOT_UPDATE_RECORDS;
                  cleanupdb();
                } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
              } else $result = $RECORD_IS_MOVED;
            } else $result = $RECORD_IS_NOT_PRIMARY;
          } else $result = $RECORD_NOT_EXISTS;

          break;

        case '7': //MOVE PRIMARY STEP2: only if record not exists
                //From the new PC. Will need to give the old icodep. Calculate the new key
                //based in the new icsum and old startdate, days, kind and vals
                //Access to all records with the same given icodep, set the new Key and change
                //the icodes with the new ones and change to (Moved=N).Enabling All licenses
                //Return (Key|Moved|Primary|icode)

          if ($recordexists) $result = $RECORD_ALREADY_EXISTS;
          else
          {
            $primaryexists = (readrecord($appid, $appname, $icodep, $module, $username2, $company2, $email2,
              $exten2, $paid2, $lastkey2, $days2, $keydate2, $move2, $instances2, $users2, $icsum2, $icodep2,
              $primar2, $vals2, $kind2, $startdate2, $icodeh2, $tppessoa2, $endereco2,
                $bairro2, $cidade2, $uf2, $contato2, $telefone2, $cpfcnpj2, $rgie2, $cep2));
            if ($primaryexists)
            {
              if ($primar2 == 'Y')
              {
                if ($move2 =='Y')
                {
                  $key0 = calculatekey($kind1,$appid,$icsum,$enckey,$users1,$instances1,$module,$days1,
                    $startdate2,$vals1);
                  if (strlen($key0) == 31)
                  { //Update record
                    if (getmyconnection())
                    { //Update primary record
                      $qry = "UPDATE $sql_table set ICODE = '$icode', LASTKEY= '$key0',
                              WHERE MODULE = '$module' and APPID = '$appid' and ICODE= '$icodep'";
                      if (mysql_query($qry, $sql_link))
                      { //update secondary records
                        $qry = "UPDATE $sql_table set MOVED = 'N', ICODEP = '$icode', LASTKEY = '$key0',
                                WHERE ICODEP= '$icodep' and MODULE = '$module' and APPID = '$appid'";
                        if (mysql_query($qry, $sql_link)) $result = $key0.'NY'.$icode.$icodeh;
                        else $result = $COULD_NOT_UPDATE_RECORDS;
                      } else $result = $COULD_NOT_UPDATE_RECORDS;
                      cleanupdb();
                    } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
                  } else $result = $KEY_LENGTH_MISMATCH;
                } else $result = $RECORD_IS_NOT_MOVED;
              } else $result = $RECORD_IS_NOT_PRIMARY;
            } else $result = $RECORD_NOT_EXISTS;
          }
          break;


        case '8': //MOVE SECONDARY STEP1:
                //From the current PC. Access to the record where (icode=current icode)
                //then set (Moved=Y). Disabling the license.
                //Return (Key|Moved|Primary|icode)

          $result = '';
          if ($recordexists)
          {
            if ($primar1 == 'N')
            {
              if  ($move1 =='N')
              {
                if (getmyconnection())
                {
                  $qry = "UPDATE $sql_table set MOVED = 'Y'
                          WHERE MODULE= $module and ICODE= '$icode' and APPID= '$appid'";
                  if (mysql_query($qry, $sql_link)) $result = $lastkey1.'YN'.$icodep1;
                  else $result = $COULD_NOT_UPDATE_RECORDS;
                  cleanupdb();
                } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
              } else $result = $RECORD_IS_MOVED;
            } else $result = $RECORD_IS_NOT_SECONDARY;
          } else $result = $RECORD_NOT_EXISTS;


          break;


        case '9': //MOVE SECONDARY STEP2: only if record exists
                //From the new PC. Will need to give the old icode into icode, and the
                //current icode into icodep. Then access to the record with icode and
                //change the old icode to icodep (the current) and (Moved=Y) to (Moved=N).
                //Enabling the license.
                //Return 'OK' if succeed

          if ($recordexists) //The old record
          {
            if ($primar1 == 'N')
            {
              if ($move1 =='Y')
              {
                if (getmyconnection())
                {
                  $qry = "UPDATE $sql_table set MOVED = 'N', ICODE = '$icodep'
                          WHERE MODULE= '$module' and APPID= '$appid' and ICODE= '$icode'";
                  if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
                  else $result = $COULD_NOT_UPDATE_RECORDS;
                  cleanupdb();
                } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
              } else $result = $RECORD_IS_NOT_MOVED;
            } else $result = $RECORD_IS_NOT_SECONDARY;
          } else $result = $RECORD_NOT_EXISTS;


          break;

        case 'A': //RESTORE PRIMARY:
                //From the moved PC. Access to all records with (icodep=current icode) then
                //change all matching records from (Moved=Y) to (Moved=N).
                //Return 'OK' if succeed

          if ($recordexists)
          {
            if ($primar1 == 'Y')
            {
              if ($move1 =='Y')
              {
                if (getmyconnection())
                {
                  $qry = "UPDATE $sql_table set MOVED = 'N'
                          WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                  if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
                  else $result = $COULD_NOT_UPDATE_RECORDS;
                  cleanupdb();
                } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
              } else $result = $RECORD_IS_NOT_MOVED;
            } else $result = $RECORD_IS_NOT_PRIMARY;
          } else $result = $RECORD_NOT_EXISTS;


          break;

        case 'B': //RESTORE SECONDARY:
                //From the moved PC. Access to the record from the current icode then
                //change from (Moved=Y) to (Moved=N).

          if ($recordexists)
          {
            if ($primar1 == 'N')
            {
              if ($move1 =='Y')
              {
                if (getmyconnection())
                {
                  $qry = "UPDATE $sql_table set MOVED = 'N'
                          WHERE MODULE= $module and ICODE= '$icode' and APPID= '$appid'";
                  if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
                  else $result = $COULD_NOT_UPDATE_RECORDS;
                  cleanupdb();
                } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
              } else $result = $RECORD_IS_NOT_MOVED;
            } else $result = $RECORD_IS_NOT_SECONDARY;
          } else $result = $RECORD_NOT_EXISTS;

          break;


        case 'C':  //SAVE REGISTRATION DATA:
                 //For primary and secondary records
                 //Only if record exists. Data changed in database
                 //Return: NO_ERROR if success

          if ($recordexists)
          {
            $ok = saverecord($upd, $appid, $appname, $icode, $module, $username, $company, $email,
              $exten, $paid, '', $days, '', $moved, $instances, $users, '',
              '', '', $vals, $kind1, $startdate, $icodeh, $tppessoa, $endereco,
              $bairro, $cidade, $uf, $contato, $telefone, $cpfcnpj, $rgie, $cep);
            if ($ok) $result = $NO_ERROR;
            else $result = $COULD_NOT_UPDATE_RECORDS;
          } else $result = $RECORD_NOT_EXISTS;
          break;

        case 'D': //SAVE USER DATA TO OLM:     USER DATA -> OLM
                  //If record exists then saves User Data into the record else no action
                  //Return NO_ERROR if succedd.

          if ($recordexists)
          {

            if (getmyconnection())
            {
              $qry = "UPDATE $sql_table set USERNAME = '$username', COMPANY = '$company',
                      EMAIL = '$email'
                      WHERE ICODE= '$icode' and APPID= '$appid'";
              if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
              else $result = $COULD_NOT_UPDATE_RECORDS;
              cleanupdb();
            } else $result = $COULD_NOT_CONNECT_TO_DATABASE;
          } else $result = $RECORD_NOT_EXISTS;
          break;


        case 'E': //GENERATE KEY:
                //Generate a RegKey according with the posted data
                //No database action accomplished
          $key0 = calculatekey($kind,$appid,$icsum,$enckey,$users,$instances,$module,$days,
          $startdate,$vals);
          if (strlen($key0) != 31) $key0 = $KEY_LENGTH_MISMATCH;
          $result = $key0;

          break;


        case 'F': //REMOVE RECORD:
                  //IF Primary: Remove Primary and associated secondary
                  //IF Secondary: Remove only this record.

          if ($recordexists)
          {
            if ($primar1 == 'Y')
            {
              if (getmyconnection())
              {
                $qry = "DELETE FROM $sql_table
                        WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
                else $result = $COULD_NOT_DELETE_RECORDS;
                cleanupdb();
              }  else $result = $COULD_NOT_CONNECT_TO_DATABASE;
            }
            else
            {
              if (getmyconnection())
              {
                $qry = "DELETE FROM $sql_table
                        WHERE MODULE= $module and ICODE= '$icode' and APPID= '$appid'";
                if (mysql_query($qry, $sql_link)) $result = $NO_ERROR;
                else $result = $COULD_NOT_DELETE_RECORDS;
                cleanupdb();
              }  else $result = $COULD_NOT_CONNECT_TO_DATABASE;
            }
          } else $result = $RECORD_NOT_EXISTS;
          break;

        case 'G':  //RETRIEVE REGISTRATION DATA:
                   //For primary and secondary records
                   //Only if record exists. No data changed in database
                   //          00-1     01-2   02-3  03-4  04-5  05-6
                   //Return: (username|company|email|exten|paid1|lastkey|
				           // 06-7  07-8  08-9   09-10   10-11 11-12  12-13  13-14
                   //days|keydate|move|instances|users|icsum|icodep|primar|
				           //14-15 15-16  16-17     17-18    18-19  19-20  20-21
                   //vals|kind|startdate|tppessoa|endereco|bairro|cidade|
				           //21-22 22-23   23-24    24-25  25-26 26-27 27-28
				           //uf |contato|telefone|cpfcnppj|regie|cep|icodeh)

          if ($recordexists)
          {
            if (($startdate != '') && ($startdate != $startdate1)  && ($kind == 0)) {
              if (($udate - $startdate1 ) > 7){
                $startdate1 = $startdate;
              }
            }
            $result =
              $username1.'|'.$company1.'|'.$email1.'|'.$exten1.'|'.$paid1.'|'.$lastkey1.'|'.
              $days1.'|'.$keydate1.'|'.$move1.'|'.$instances1.'|'.$users1.'|'.$icsum1.'|'.
              $icodep1.'|'.$primar1.'|'.$vals1.'|'.$kind1.'|'.$startdate1.'|'.
			        $tppessoa1.'|'.$endereco1.'|'.$bairro1.'|'.$cidade1.'|'.$uf1.'|'.$contato1.'|'.
			        $telefone1.'|'.$cpfcnpj1.'|'.$rgie1.'|'.$cep1.'|'.$icodeh1;

          } else $result = $RECORD_NOT_EXISTS;
          break;


          //=====================================================
          //  FULL SYNCHRONIZE
          //  If Record exists //Try to Renew (case 5)
          //    If Primary
          //       If Paid Generates new key
          //       Else if Extend Generates new key
          //       Else Return last key
          //    Else Synchronize Secondary
          //  Else Start Trial
          //
          // 0: (synch) $result = $lastkey1.$move1.'Y'.$icode.$icodeh;
          // 1: (start) $result = $lastkey1.$move1.$primar1.$icode.$icodeh;
          // 2: (exten) $result = $key0.'NY'.$icode.$icodeh;
          // 5: (renew) $result = $key0.'NY'.$icode.$icodeh;
          //=====================================================
        case 'H':  //FULL SYNCHRONIZE

          $key0='';
          if ($recordexists)
          { //Try to Renew (case 5)
            if ($primar1 == 'Y')
            {
              if ($paid1 == 'Y')
              {
                //if (abs($startdate - $udate) >1)
                $startdate = $udate;
                if ($days1 == 65535) $kind = 2; //Permanent
                else $kind = 1;                 //Temporary
                $key0 = calculatekey($kind,$appid,$icsum,$enckey,$users1,$instances1,$modul1,$days1,$startdate,$vals1);

                if (strlen($key0) == 31)
                { //Update record
                  if (getmyconnection())
                  {
                    $kdate =date("d/m/y");
                    $qry = "UPDATE $sql_table set PAID = 'N', MOVED='N', LASTKEY = '$key0', STARTDATE = '$startdate',
                            KEYDATE = '$kdate', KIND = '$kind'
                            WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                    if (mysql_query($qry, $sql_link)) $key0 = $key0.'NY'.$icode.$icodeh;
                    //else $key0 = $COULD_NOT_UPDATE_RECORDS;
                    cleanupdb();
                  } //else $result = $COULD_NOT_CONNECT_TO_DATABASE;

                } //else $key0 = $KEY_LENGTH_MISMATCH;
              } else
              { //NOT_PAID, Try to EXTEND (case 1)
                if ($exten1 == 'Y')
                {
                  //if (abs($startdate - $udate) >1)
                  $startdate = $udate;
                  $key0 = calculatekey(0,$appid,$icsum,$enckey,$trialusers,$trialinstances,$module,$extendays,
                  $startdate,$trialvalues);
                  if (strlen($key0) == 31)
                  { //Update record
                    if (getmyconnection())
                    {
                      $kdate =date("d/m/y");
                      $qry = "UPDATE $sql_table set EXTEN = 'N', MOVED = 'N', LASTKEY = '$key0',
                              KEYDATE = '$kdate'
                              WHERE MODULE= $module and ICODEP= '$icode' and APPID= '$appid'";
                      if (mysql_query($qry, $sql_link)) $key0 = $key0.'NY'.$icode.$icodeh;
                      else $key0 = $COULD_NOT_UPDATE_RECORDS;
                      cleanupdb();
                    } else $result = $COULD_NOT_CONNECT_TO_DATABASE;

                  } else $key0 = $KEY_LENGTH_MISMATCH;
                } else $key0 = $NOT_EXTENSION_ALLOWED;
              }

              if (strlen($key0) <= 31)
              {//Synchronize Primary
                // retornar todos os campos da tabela. (Nome/Telefone/Endereco)
                $key0 = $lastkey1.$move1.$primar1.$icode.$icodeh;
              }

            }
            else //SECONDARY  (Not PRYMARY)
          { //Synchronize Secondary
            $primaryexists = (readrecord($appid, $appname, $icodep1, $module, $username2, $company2, $email2,
              $exten2, $paid2, $lastkey2, $days2, $keydate2, $move2, $instances2, $users2, $icsum2, $icodep2,
              $primar2, $vals2, $kind2, $startdate2, $icodeh2, $tppessoa2, $endereco2,
                $bairro2, $cidade2, $uf2, $contato2, $telefone2, $cpfcnpj2, $rgie2, $cep2));
            if ($primaryexists)
            {
              if ($primar2 == 'Y') $key0 = $lastkey2.$move2.'N'.$icodep1.$icodeh1; //return secondary key
              else $key0 = $SECONDARY_LINKED_TO_SECONDARY;
            } else $key0 = $PRIMARY_NOT_EXISTS;
          }
        }
        else //Record not exists
        { //Start Trial
          $startdate = $udate;
          $key0 = calculatekey(0,$appid,$icsum,$enckey,$trialusers,$trialinstances,$module,$trialdays,
          $startdate,$trialvalues);
          if (strlen($key0) == 31)
          { //Create record
            saverecord($add, $appid, $appname, $icode, $module, $username, $company, $email,
            'N', 'N', $key0, $trialdays, date("d/m/y"), 'N', '0', $trialusers, $icsum,
            $icode, 'Y', $trialvalues, 0, $startdate, $icodeh, $tppessoa, $endereco,
                $bairro, $cidade, $uf, $contato, $telefone, $cpfcnpj, $rgie, $cep);
            $key0 = $key0.'NY'.$icode.$icodeh;
          } else $key0 = $KEY_LENGTH_MISMATCH;

        }

        $result = $key0;

        break;

      }
    }
  } else $result=$VALUES_POSTED_COUNT_MISMATCH;

} else $result= $NO_DATA_POSTED;

echo (encryptstr($result));


?>

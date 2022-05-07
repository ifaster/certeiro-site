<?php
/*******************************************************
*   File     : avlocks3.inc.php                        *
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

// Modify below the configuration variables with your own values

//*****************CONFIGURATION SECTION************************
$sql_host="172.106.0.110";     // Host for the MySql server. Normally localhost
$sql_user="licenca"; // Mysql user name
$sql_pass="str50103";        // Mysql password
$sql_db="licenca";     // your MySql database
$sql_table="LICENCA";    // table for the avlock simple data
//**************************************************************

$sql_link = false;

$sql_create="CREATE TABLE IF NOT EXISTS $sql_table (
  ID        int(9) unsigned NOT NULL auto_increment,
  APPID     int(9) unsigned NOT NULL,
  STARTDATE int(9) unsigned NOT NULL,
  DAYS      int(5) unsigned NOT NULL,
  USERS     int(5) unsigned NOT NULL,
  INSTANCES int(5) unsigned NOT NULL,
  KIND      int(5) unsigned NOT NULL,
  APPNAME   tinytext NOT NULL,
  ICSUM     tinytext NOT NULL,
  ICODE     tinytext NOT NULL,
  ICODEP    tinytext NOT NULL,
  MODULE    tinytext NOT NULL,
  USERNAME  tinytext NOT NULL,
  COMPANY   tinytext,
  EMAIL     tinytext,
  EXTEN     tinytext NOT NULL,
  PAID      tinytext NOT NULL,
  MOVED     tinytext NOT NULL,
  PRIMAR    tinytext NOT NULL,
  LASTKEY   tinytext NOT NULL,
  KEYDATE   tinytext NOT NULL,
  VALS      tinytext NOT NULL,
  ICODEH    tinytext,
  TPPESSOA  tinytext,
  ENDERECO  tinytext,
  BAIRRO    tinytext,
  CIDADE    tinytext,
  UF        tinytext,
  CONTATO   tinytext,
  TELEFONE  tinytext,
  CPFCNPJ   tinytext,
  RGIE      tinytext,
  CEP       tinytext,
  PRIMARY KEY  (ID)
) TYPE=MyISAM PACK_KEYS=1;";


$appid0 = '';
$appname0 = '';


function cleanupdb()
{
  global $sql_link;

  if( $sql_link != false ) mysql_close($sql_link);
  $sql_link = false;
}


function create_table()
{
  $result=False;
  global $sql_link;
  global $sql_create;
  global $sql_host;
  global $sql_user;
  global $sql_pass;
  global $sql_db;

  if( !$sql_link ) $sql_link = mysql_connect( "$sql_host", "$sql_user", "$sql_pass");
  if ($sql_link)
  {
    mysql_select_db("$sql_db", $sql_link) or die('Error:  Could not select database '.mysql_error());
    //echo 'query='.$sql_create;
    mysql_query($sql_create) or die ('Error: ' . mysql_error());
    cleanupdb();
    $result=True;
  } else echo 'Error:  Could not connect to database '.$sql_host.' - '.$sql_user.' - '.$sql_pass.' - '.mysql_error();
    //die('Error:  Could not connect to database '.mysql_error());
  return $result;
}


function upgrade_table($sql_upgrade)
{
  $result=False;
  global $sql_link;
  global $sql_create;
  global $sql_host;
  global $sql_user;
  global $sql_pass;
  global $sql_db;

  if( !$sql_link ) $sql_link = mysql_connect( "$sql_host", "$sql_user", "$sql_pass");
  if ($sql_link)
  {
    mysql_select_db("$sql_db", $sql_link) or die('Error:  Could not select database '.mysql_error());
    mysql_query($sql_upgrade) or die ('Error: ' . mysql_error());
    cleanupdb();
    $result=True;
  } else echo 'Error:  Could not connect to database '.$sql_host.' - '.$sql_user.' - '.$sql_pass.' - '.mysql_error();
  return $result;
}


function getmyconnection()
{
  global $sql_link;
  //global $sql_create;
  global $sql_host;
  global $sql_user;
  global $sql_pass;
  global $sql_db;

  $s = "";

  if( !$sql_link ) $sql_link = mysql_connect( "$sql_host", "$sql_user", "$sql_pass");
  if ($sql_link)
  {
    mysql_select_db("$sql_db", $sql_link) or die('Error:  Could not select database '.mysql_error());

  } else die('Error:  Could not connect to server '.mysql_error());

  return $sql_link;
}



// Aplicar comillas sobre la variable para hacerla segura
function smart_quotes($value)
{
  // Remove slashes
  if (get_magic_quotes_gpc()) $value = stripslashes($value);

  // fence in quotes if not integer
  if (!is_numeric($value)) $value = "'" . mysql_real_escape_string($value) . "'";
  return $value;
}


function readrecord($appid, $appname, $icode, $module, &$username, &$company, &$email,
&$exten, &$paid, &$lastkey, &$days, &$keydate, &$moved, &$instances, &$users, &$icsum,
&$icodep, &$primar, &$vals, &$kind, &$startdate, &$icodeh, &$tppessoa, &$endereco,
&$bairro, &$cidade, &$uf, &$contato, &$telefone, &$cpfcnpj, &$rgie, &$cep)
{
  global $sql_link;
  global $sql_table;

  $result = False;

  if (getmyconnection())
  {
    //check if record exists
    $qry = sprintf("SELECT * FROM $sql_table WHERE APPID=%s AND APPNAME=%s AND ICODE=%s AND MODULE=%s",
      smart_quotes($appid), smart_quotes($appname), smart_quotes($icode), smart_quotes($module));
    $data = mysql_query($qry, $sql_link);
    $qry = "";
    if (mysql_num_rows($data) != 0)
    { //record found
      $rec = mysql_fetch_object($data);
      $id=   $rec->ID;
      $appi=   $rec->APPID;
      $appn=   $rec->APPNAME;
      $icod=   $rec->ICODE;
      $modul=  $rec->MODULE;
      //check fields, retrieved values must be equal to input values
      if (($appi != $appid) || ($appn != $appname) || ($icod != $icode) || ($modul != $module))
        die ('Error: fields mismatch');
      //$icode =    $rec['icode'];
      $username   = $rec->USERNAME;
      $company    = $rec->COMPANY;
      $email      = $rec->EMAIL;
      $exten      = $rec->EXTEN;
      $paid       = $rec->PAID;
      $lastkey    = $rec->LASTKEY;
      $days       = $rec->DAYS;
      $keydate    = $rec->KEYDATE;
      $moved      = $rec->MOVED;
      $users      = $rec->USERS;
      $instances  = $rec->INSTANCES;
      $icsum      = $rec->ICSUM;
      $icodep     = $rec->ICODEP;
      $primar     = $rec->PRIMAR;
      $vals       = $rec->VALS;
      $kind       = $rec->KIND;
      $startdate  = $rec->STARTDATE;
      $icodeh     = $rec->ICODEH;
      $tppessoa   = $rec->TPPESSOA;
      $endereco   = $rec->ENDERECO;
      $bairro     = $rec->BAIRRO;
      $cidade     = $rec->CIDADE;
      $uf         = $rec->UF;
      $contato    = $rec->CONTATO;
      $telefone   = $rec->TELEFONE;
      $cpfcnpj    = $rec->CPFCNPJ;
      $rgie       = $rec->RGIE;
      $cep        = $rec->CEP;

	  //colocando data/hora ultimo acesso
      $qry = sprintf("UPDATE LICENCA SET LAST_ACCESS=now() WHERE ID=%s", smart_quotes($id));
      $data = mysql_query($qry, $sql_link);

      $qry = sprintf("SELECT INTE_CLIE_ID FROM INTEGRADORA WHERE INTE_LICE_ID=%s", smart_quotes($id));
      $data = mysql_query($qry, $sql_link);
      $id_empresa = 0;
      if (mysql_num_rows($data) != 0)
      {
        $rec = mysql_fetch_object($data);
		$id_cliente = $rec->INTE_CLIE_ID;
	  } else {
	    $id_cliente = 0;
	  }

	  if ($id_cliente != 0) {
  	    //colocando a data/hora do ultimo acesso
	    $qry = sprintf("INSERT INTO LICENCA_USO (LIUS_LICE_ID, LIUS_CLIE_ID, LIUS_CLIE_EMPR_ID, LIUS_LAS_ACCESS) VALUES (%s, %s, %s, %s)", smart_quotes($id), 
		    smart_quotes($id_cliente), smart_quotes($id_empresa), smart_quotes(date("Y-m-d H:i")));
        $data = mysql_query($qry, $sql_link);

		//verificando se o cliente tem boletos em aberto a mais de 10 dias
        #$qry = sprintf("SELECT count(*) AS ABERTOS FROM BOLETO WHERE BOLETO.BOLT_CLIE_ID =%s AND BOLETO.BOLT_PAGO = 'A' AND (DATEDIFF(NOW(), BOLETO.BOLT_DATA_VENC) > 10)", smart_quotes($id_cliente));
		$qry = sprintf("SELECT count(*) AS ABERTOS FROM BOLETO WHERE BOLETO.BOLT_CLIE_ID = %s AND BOLETO.BOLT_PAGO = 'A' AND (DATEDIFF(NOW(), BOLETO.BOLT_DATA_VENC) > 14) AND ( DATE_ADD(NOW(), INTERVAL -1 DAY) > (SELECT COCL_DTA_PROMESSA_PGTO  FROM CONFIGURACAO_CLIE WHERE COCL_CLIE_ID = BOLT_CLIE_ID AND COCL_CLIE_EMPR_ID = BOLT_CLIE_EMPR_ID) OR (SELECT COCL_DTA_PROMESSA_PGTO  FROM CONFIGURACAO_CLIE WHERE COCL_CLIE_ID = BOLT_CLIE_ID AND COCL_CLIE_EMPR_ID = BOLT_CLIE_EMPR_ID) IS NULL)",	smart_quotes($id_cliente));
        $data = mysql_query($qry, $sql_link);
        if (mysql_num_rows($data) != 0)
        {
          $rec = mysql_fetch_object($data);
		  $qtde_abertos = $rec->ABERTOS;
	    } else {
	      $qtde_abertos = 0;
	    }
		
		#echo "sql:".$qry.'<br>';
		#echo "abertos:".$qtde_abertos.'<br>';
		if ($qtde_abertos > 0) {
		  $moved = 'Y';
		  $icodeh = $icodeh.'0';
		}
      }
      $result = True;
    }
    cleanupdb();
  }
  return $result;
}


//add or update db record
//if ($update==0) add record else update record
function saverecord($update, $appid, $appname, $icode, $module, $username, $company,
  $email, $exten, $paid, $lastkey, $days, $keydate, $moved, $instances, $users, $icsum,
  $icodep, $primar, $vals, $kind, $startdate, $icodeh, $tppessoa, $endereco,
  $bairro, $cidade, $uf, $contato, $telefone, $cpfcnpj, $rgie, $cep)
{
  global $sql_link;
  global $sql_table;

  $result = False;

  if (getmyconnection())
  {
    //check if record exists
    $qry = sprintf("SELECT ID, APPID, ICODE, APPNAME, MODULE FROM $sql_table
    WHERE APPID=%s AND APPNAME=%s AND ICODE=%s AND MODULE=%s",
    smart_quotes($appid), smart_quotes($appname), smart_quotes($icode), smart_quotes($module));

    $data = mysql_query($qry, $sql_link);
    $qry = "";
    if (mysql_num_rows($data) == 0)
    { //record not found
      if (!$update)
      { //add record
        $qry = "INSERT into $sql_table (APPID, APPNAME, ICODE, MODULE, USERNAME, COMPANY,
        EMAIL, EXTEN, PAID, LASTKEY, DAYS, KEYDATE, MOVED, INSTANCES, USERS, ICSUM, ICODEP,
        PRIMAR, VALS, KIND, STARTDATE, ICODEH, TPPESSOA, ENDERECO, BAIRRO, CIDADE,
        UF, CONTATO, TELEFONE, CPFCNPJ, RGIE, CEP)
        values ('$appid', '$appname', '$icode', '$module', '$username', '$company',
        '$email', '$exten', '$paid', '$lastkey', '$days', '$keydate', '$moved', '$instances',
        '$users', '$icsum', '$icodep', '$primar', '$vals', '$kind', '$startdate', '$icodeh',
        '$tppessoa', '$endereco', '$bairro', '$cidade', '$uf', '$contato',
        '$telefone', '$cpfcnpj', '$rgie', '$cep')";
      }

    } else
    {//record found
      if ($update)
      { //update record
        $rec =  mysql_fetch_object($data);
        $id  =  $rec->ID;
        $appi=  $rec->APPID;
        $appn=  $rec->APPNAME;
        $icod=  $rec->ICODE;
        $modul= $rec->MODULE;
        //check fields, retrieved values must be equal to input values
        if (($appi != $appid) || ($appn != $appname) || ($icod != $icode) || ($modul != $module))
           die ('Error: fields mismatch');

        $qry = "UPDATE $sql_table set appid = '$appid'";
        if ($username  != "") $qry .= ", USERNAME = '$username'";
        if ($company   != "") $qry .= ", COMPANY  = '$company'";
        if ($email     != "") $qry .= ", EMAIL    = '$email'";
        if ($exten     != "") $qry .= ", EXTEN    = '$exten'";
        if ($paid      != "") $qry .= ", PAID     = '$paid'";
        if ($lastkey   != "") $qry .= ", LASTKEY  = '$lastkey'";
        if ($days      != "") $qry .= ", DAYS     = '$days'";
        if ($keydate   != "") $qry .= ", KEYDATE  = '$keydate'";
        if ($moved     != "") $qry .= ", MOVED    = '$moved'";
        if ($users     != "") $qry .= ", USERS    = '$users'";
        if ($instances != "") $qry .= ", INSTANCES= '$instances'";
        if ($icsum     != "") $qry .= ", ICSUM    = '$icsum'";
        if ($icodep    != "") $qry .= ", ICODEP   = '$icodep'";
        if ($primar    != "") $qry .= ", PRIMAR   = '$primar'";
        if ($vals      != "") $qry .= ", VALS     = '$vals'";
        if ($kind      != "") $qry .= ", KIND     = '$kind'";
        if ($startdate != "") $qry .= ", STARTDATE= '$startdate'";
        if ($icodeh    != "") $qry .= ", ICODEH   = '$icodeh'";
        if ($tppessoa  != "") $qry .= ", TPPESSOA = '$tppessoa'";
        if ($endereco  != "") $qry .= ", ENDERECO = '$endereco'";
        if ($bairro    != "") $qry .= ", BAIRRO   = '$bairro'";
        if ($cidade    != "") $qry .= ", CIDADE   = '$cidade'";
        if ($uf        != "") $qry .= ", UF       = '$uf'";
        if ($contato   != "") $qry .= ", CONTATO  = '$contato'";
        if ($telefone  != "") $qry .= ", TELEFONE = '$telefone'";
        if ($cpfcnpj   != "") $qry .= ", CPFCNPJ  = '$cpfcnpj'";
        if ($rgie      != "") $qry .= ", RGIE     = '$rgie'";
        if ($cep       != "") $qry .= ", CEP      = '$cep'";
        $qry .= " WHERE id = '$id'";

      }
    }
    mysql_query($qry, $sql_link) or die ('Error: ' . mysql_error());
    cleanupdb();
    $result= True;
  }
  return $result;
}


//count of records with $icodep
function countrecords($module, $appid, $icodep)
{

  global $sql_link;
  global $sql_table;


  $result = 0;
  if (getmyconnection())
  {
    //get number of records
    //$value='N';
    $qry = "SELECT COUNT(ID) FROM $sql_table
            WHERE MODULE= $module and ICODEP= '$icodep' and APPID= '$appid'";
    $data = mysql_query($qry, $sql_link); //or die(mysql_error());

    if ($data)
    {
      $row = mysql_fetch_row($data);
      $result = $row[0];
    }

    $qry = "";
    cleanupdb();
  }

  return $result;
}


?>



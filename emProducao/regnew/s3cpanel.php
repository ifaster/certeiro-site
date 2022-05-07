<?php
/*******************************************************
*   File     : s3cpanel.php                            *
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


header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
require_once "./reg_inc.php";
error_reporting(E_ALL);

function getagent() {
  $usragent = $_SERVER['HTTP_USER_AGENT'];
  if (strstr($usragent, 'Firefox')) return 'FIREFOX';
  else return 'OTHER';
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
<HEAD><TITLE></TITLE>

<style type=text/css>

input {
	margin:.01em .5em;
}
th {
	cursor: default;
}

<!--
 .Estilo0 {
 font-family: Verdana, Arial, Helvetica, sans-serif;
 font-size: 12px;
 color:#000000;
 background-color:#FFFFFF;
 font-weight: bold;
 line-height:16px;
 }
 .Estilo1 {
 font-family: Verdana, Arial, Helvetica, sans-serif;
 font-size: 12px;
 color:#000000;
 background-color:#D5D5E8;
 font-weight: bold;
 line-height:16px;
 }
 .Estilo2 {
 font-family: Verdana, Arial, Helvetica, sans-serif;
 font-size: 10px;
 color:#000000;
 background-color:#EAEAFA;
 font-weight: normal;
 line-height:14px;
-->

</style>
<script type="text/javascript" src="sorttable.js"></script>
<script type="text/javascript">
function SendForm(how) {
	document.forms[0]['mode'].value = how;
	document.forms[0].submit();
}

function setField(id, appi, appnam) {
	document.forms[0]['id'].value = id;
	document.forms[0]['appid'].value = appi;
	document.forms[0]['appname'].value = appnam;
}
</script>
</HEAD>
<BODY style="margin:1em auto 1em auto;text-align:center;">

<div><!-- wrapper -->
<form method="POST" action="s3cpanel.php">
<input type="hidden" name="mode" value="extend">
<table align="center" class="Estilo1" style="background-color:#E0E0F0;text-align:middle; width:84em;border:1px solid black;">
  <tr>
    <td height="40" valign="top" colspan="4"><span>
       AppID<input name="appid" size="10" />&nbsp;&nbsp;AppName
       <input name="appname" size="14" /> &nbsp;&nbsp;UserName
       <input name="searchfor" size="14" />
       <input type="button" value="-> Go" onclick="SendForm('refresh');"  />
    </span></td>
  </tr>
  <tr>
    <td width="16%"><span>ID
        <input name="id" size="2" readonly />
        <input type="button" value="Delete" onclick="SendForm('delete');"  />
    </span></td>
    <td width="16%"><span>Extend
        <input type="button" value=" Y " onclick="SendForm('extend');"  />
        <input type="button" value=" N " onclick="SendForm('unextend');"  />
    </span></td>
    <td width="52%"><span>
        Users<input name="users" size="1" value="1" />
        Inst<input name="inst" size="1" value="1" />
        Days<input name="days" size="4" value="65535" />
        Val<input name="values" size="2" value="000" />
        Paid<input type="button" value=" Y " onclick="SendForm('setpaid');"  />
        <input type="button" value=" N " onclick="SendForm('unsetpaid');"  />
    </span></td>
    <td width="16%"><span>Moved
        <input type="button" value=" Y " onclick="SendForm('setmov');"  />
        <input type="button" value=" N " onclick="SendForm('unsetmov');"  />
    </span></td>
  </tr>
</table>
</form>
</div><!-- end of wrapper -->

<table class='Estilo2 sortable' align='center' cellpadding='2' cellspacing=0 bordercolor="#FFFFFF" border='3' bgcolor='#E9E9F9'>
<tr class='Estilo1'>
<th>ID</th><th>AppID</th><th>AppName</th><th>M</th><th>PR</th><th>SUM</th><th>Installcode</th><th>Primary Installcode</th>
<th>Ext</th><th>Users</th><th>Inst</th><th>Days</th><th>Val</th><th>Paid</th><th>Mov</th><th>KeyDate</th><th>UserName</th><th>Company</th><th>UserEmail</th>
<th>K</th><th>Key</th></tr>


<?php

$msg='';

if (isset($_POST['appid']) && isset($_POST['appname']) && isset($_POST['mode'])) {
  $appid0 = $_POST['appid'];
  $appname0 = $_POST['appname'];
  $mode = $_POST['mode'];
  $searchfor='';
  if (isset($_POST['searchfor'])) $searchfor = $_POST['searchfor'];

  if (isset($_POST['id']))
  {
    $id = $_POST['id'];
    $users = $_POST['users'];
    $values = $_POST['values'];
    $days = $_POST['days'];
    $inst = $_POST['inst'];
  }

  if ($mode == 'delete') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("DELETE FROM $sql_table WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not delete record';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if ($mode == 'extend') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("UPDATE $sql_table set EXTEN = 'Y' WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not update EXTEN field';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if ($mode == 'unextend') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("UPDATE $sql_table set EXTEN = 'N' WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not update EXTEN field';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if ($mode == 'setpaid') {

    if(preg_match("([0-9A-F]{3})",$values))
    {

      if (getmyconnection())
      {
        if (isset($_POST['id']))
        {
          $qry = sprintf("UPDATE $sql_table set PAID = 'Y' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update PAID field';
          $qry = sprintf("UPDATE $sql_table set USERS = '$users' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update USERS field';
          $qry = sprintf("UPDATE $sql_table set VALS = '$values' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update VALS field';
          $qry = sprintf("UPDATE $sql_table set DAYS = '$days' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update DAYS field';
          $qry = sprintf("UPDATE $sql_table set INSTANCES = '$inst' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update INSTANCES field';
          $qry = sprintf("UPDATE $sql_table set MOVED = 'N' WHERE ID=%s", smart_quotes($id));
          if (!mysql_query($qry, $sql_link)) echo 'Could not update MOVED field';
          cleanupdb();
        } else echo 'UNDEFINED VARIABLE id';
      }
    } else echo 'Values must be a 3 digit hexadecimal string ( e.g. 1AE )';
  }

  if ($mode == 'unsetpaid') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("UPDATE $sql_table set PAID = 'N' WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not update PAID field';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if ($mode == 'setmov') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("UPDATE $sql_table set MOVED = 'Y' WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not update MOVED field';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if ($mode == 'unsetmov') {
    if (getmyconnection())
    {
      if (isset($_POST['id']))
      {
        $qry = sprintf("UPDATE $sql_table set MOVED = 'N' WHERE ID=%s", smart_quotes($id));
        if (!mysql_query($qry, $sql_link)) echo 'Could not update MOVED field';
        cleanupdb();
      } else echo 'UNDEFINED VARIABLE id';
    }
  }

  if (getmyconnection())
  {
    //check if record exists

    $appi=smart_quotes($appid0);
    $appn=smart_quotes($appname0);

    $qry ="SELECT * FROM $sql_table WHERE appid = $appi AND appname = $appn";
    if ($searchfor!='') {
      $searchfor=smart_quotes("%".$searchfor."%");
      $qry.=" AND username LIKE $searchfor";
    }

    $data = mysql_query($qry, $sql_link);
    $qry = "";
    $count=mysql_num_rows($data);
    if ($count != 0) //record found
    {
      $msg = "Select a row below, then hit a button above.";
      while ($rec = mysql_fetch_object($data)) {
        $appid1 = $rec->APPID;
        $appname1 = $rec->APPNAME;
        if (($appid0 != $appid1) || ($appname0 != $appname1)) die ('Error: Fields mismatch');
        $id       = $rec->ID;
        $icode    = $rec->ICODE;
        $module   = $rec->MODULE;
        $username = $rec->USERNAME;
        $company  = $rec->COMPANY;
        $email    = $rec->EMAIL;
        $exten    = $rec->EXTEN;
        $paid     = $rec->PAID;
        $lastkey  = $rec->LASTKEY;
        $keydate  = $rec->KEYDATE;
        $days     = $rec->DAYS;
        $moved    = $rec->MOVED;
        $users    = $rec->USERS;
        $instances= $rec->INSTANCES;
        $icsum    = $rec->ICSUM;
        $icodep   = $rec->ICODEP;
        $primary  = $rec->PRIMAR;
        $values   = $rec->VALS;
        $kind     = $rec->KIND;


        if (getagent() == 'FIREFOX')
        echo "<tr onclick='setField(this.childNodes[0].textContent,this.childNodes[1].textContent,
        this.childNodes[2].textContent)' align='center'>";
        else
        echo "<tr onclick='setField(this.childNodes[0].innerText,this.childNodes[1].innerText,
        this.childNodes[2].innerText)' align='center'>";
        echo
        "<td>$id</td><td>$appid1</td><td>$appname1</td><td>$module</td><td>$primary</td><td>$icsum</td>
        <td>$icode</td><td>$icodep</td><td>$exten</td><td>$users</td><td>$instances</td><td>$days</td>
        <td>$values</td><td>$paid</td><td>$moved</td><td>$keydate</td><td>$username</td>
        <td>$company</td><td>$email</td><td>$kind</td><td>$lastkey</td></tr>\n";

//ID-AppID-AppName-M-PR-SUM-Installcode-Primary Installcode-Ext-Users-
//Days-Val-Paid-K-LastKey-KeyDate-Un-Mov-UserName-Company-UserEmail

      }
      mysql_free_result($data);
    } else $msg="$count records for [$appname0] [$appid0]";
      cleanupdb();
  }

} else $msg= "Please enter the AppID and AppName fields above, then hit the [-> Go] button.";

if ($msg!='') echo "<b class=\"Estilo0\">$msg</b>";

?>
</table>
</BODY>
</HTML>

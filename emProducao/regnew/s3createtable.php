<HTML>
<HEAD><TITLE></TITLE>
</HEAD>
<BODY>

<?php
/*******************************************************
*   File     : s3createtable.php                       *
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

  require_once "./reg_inc.php";

  if (create_table()) echo 'table created successfully';
  else echo 'could not create table';


?>

</BODY>
</HTML>



<?php
    error_reporting(1);
	require_once("./genlib.php");
	#echo 'bastiao';

class Exemplo
{
	// Guarda uma instância da classe
    static private $instance;
   
    // Um construtor privado
    private function __construct() 
    {
		$host  = 'localhost';
		$user  = 'licenca';
		$pass  = 'str50103';
		$banco = 'licenca';
        $link = mysql_connect($host, $user, $pass);
        if (!$link) {
            die('Não foi possível conectar: ' . mysql_error()."<br><br>");
        }

        $db_selected = mysql_select_db($banco, $link);
        if (!$db_selected) {
            die ('Não foi possível selecionar : ' . mysql_error());
        }
    }

    // O método singleton 
    static public function singleton() 
    {
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
   
    // Método exemplo
    public function consultar($sql, $tipo = 0)
    {
		
        $result = mysql_query($sql);
        if (!$result) {
			#echo 'erro ao executar sql  '; 
			$erro = mysql_error();
			if ($erro != "") {
			    $result = $erro;
			} else {
				$result = '55';
			}
            return $result;
        }else{
			#echo 'executar sql';
			if ($tipo == 2) { 
				$result = '55';
			}
			return $result;
		}
    }
}

// Isso sempre vai recuperar uma instância da classe
$sql_local=str_replace('\\','',$_GET['sql']);
if (!isset($sql_local)){$sql_local=str_replace('\\','',$_POST['sql']);}
#echo 'SQL PASSADA :'.$sql_local;
$conection = Exemplo::singleton();

if ($sql_local!='') {
    $retorno = $conection->consultar($sql_local);
	#echo 'retorno sql :'.$retorno;

	if ($retorno!='54') {
		while ($line = mysql_fetch_array($retorno, MYSQL_ASSOC)) {
			foreach ($line as $col_value) {
				$res = $res . "$col_value"."§"; 
			}
			$res = $res .   chr(13) . chr(10);
		}
	} else {$res = $retorno;}
	$result = $res;
	#echo $result.'<br>';
	#echo '£'.encryptstr($result).'£';
	exit;
} else {
	//return constants
	$NO_ERROR	= '50';
	$CONEXAO    = '51';
	$TABELA		= '52';
	$SQL_ERROR	= '53';
	$EXEC_ERROR	= '54';
	$EXEC_OK	= '55';

	//action|sql
	//{action = 1-select,2-update/insert}

	$result = '';//encryptstr('NO DATA POSTED');
	$data = $_POST['data'];
	if (!isset($data)){
		$data=$_GET['data'];
	}
	#echo 'pacote:'.$data;
	if (isset($data))
	{
		$databk = $data;	
		$data   = decryptstr($data);
     	#echo 'bastiao dados2 :'.$data.'<br>';
		#echo 'databk :'.$databk;

		$arr=explode("|",$data);
		#exit;
		if (count($arr)==2)
		{
			$action = trim($arr[0],"\x00..\x20");
			$sql    = trim($arr[1],"\x00..\x20");
			switch($action)
				{
					case '1':  //SELECT 
					{
						$retorno = $conection->consultar($sql,1);
						$res='';
						while ($line = mysql_fetch_array($retorno, MYSQL_ASSOC)) {
							foreach ($line as $col_value) {
								$res = $res . "$col_value"."§";
							};
							$res = $res .   chr(13) . chr(10);
						};
						$result = $res;
						#echo 'RETORNO:'.$result.'<BR>';
						echo ('£'.encryptstr($result).'£');
						exit;

					}
					
                    case '2':{
						$retorno = $conection->consultar($sql,2);
						#echo 'retorno :'.$retorno.'<br>';
						/*if ($retorno=='54'){
							#echo 'entrou 54';
							$retorno=$retorno;
						}else{	$retorno = $EXEC_OK; echo 'entrou'; } */
						#echo 'RESULTADO :'.$retorno;
						echo ('£'.encryptstr($retorno).'£');
						exit;
					}
				}
		}
	}
}
?>
